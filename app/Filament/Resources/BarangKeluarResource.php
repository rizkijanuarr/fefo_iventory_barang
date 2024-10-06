<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BarangKeluar;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BarangKeluarResource\Pages;
use App\Filament\Resources\BarangKeluarResource\RelationManagers;


class BarangKeluarResource extends Resource
{
    protected static ?string $model = BarangKeluar::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Barang Keluar')->schema([

                    // Nomor transaksi Barang Keluar (Auto Generate dan ReadOnly)
                    Forms\Components\TextInput::make('barang_keluar_number')
                        ->label('Nomor Transaksi')
                        ->default(generateBarangKeluarNumber(\App\Models\BarangKeluar::class))
                        ->readOnly()
                        ->required(),

                    // Group untuk field customer, barang
                    Forms\Components\Group::make([
                        // Select untuk memilih Customer
                        Forms\Components\Select::make('customer_id')
                            ->label('Pilih Customer')
                            ->preload()
                            ->relationship('customer', 'name')
                            ->required(),

                    ])->columns(3)->columnSpanFull(),

                    // Group untuk field quantity, date_sold, payment_method, dan status
                    Forms\Components\Group::make([
                        Forms\Components\DatePicker::make('date_sold')
                            ->required()
                            ->label('Tanggal Barang Keluar'),
                        Forms\Components\Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->enum(\App\Enums\PaymentMethod::class)
                            ->options(\App\Enums\PaymentMethod::class)
                            ->default(\App\Enums\PaymentMethod::CASH)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status Transaksi')
                            ->required()
                            ->enum(\App\Enums\Status::class)
                            ->options(\App\Enums\Status::class)
                            ->default(\App\Enums\Status::PENDING),
                    ])->columns(4)->columnSpanFull(),

                    // Toggle untuk barang dikembalikan
                    Forms\Components\Toggle::make('is_returned')
                        ->label('Apakah Barang Dikembalikan?')
                        ->onColor('success')
                        ->offColor('danger'),

                    // Total (ReadOnly, auto calculate)
                    Forms\Components\TextInput::make('total')
                        ->label('Total')
                        // ->hiddenOn('create')
                        ->readOnly()
                        ->default(0)
                        ->numeric(),

                    // Profit (ReadOnly, auto calculate)
                    Forms\Components\TextInput::make('profit')
                        ->label('Profit')
                        ->hiddenOn('create')
                        ->numeric()
                        ->readOnly(),
                ])
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('barang_keluar_number')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('barang_keluar_name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount')
                    ->label('Disc')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable()
                    ->summarize(
                        Tables\Columns\Summarizers\Sum::make('total')
                            ->money('IDR'),
                    ),
                Tables\Columns\TextColumn::make('profit')
                    ->numeric()
                    ->summarize(
                        Tables\Columns\Summarizers\Sum::make('profit')
                            ->money('IDR'),
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => $state->getColor()),
                Tables\Columns\IconColumn::make('is_returned')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(\App\Enums\Status::class),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->multiple()
                    ->options(\App\Enums\PaymentMethod::class),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->maxDate(fn(Forms\Get $get) => $get('end_date') ?: now())
                            ->native(false),
                        Forms\Components\DatePicker::make('created_until')
                            ->native(false)
                            ->maxDate(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->button()
                    ->color('gray')
                    ->icon('heroicon-o-printer')
                    ->action(function (\App\Models\BarangKeluar $record) {
                        $pdf = Pdf::loadView('pdf.print-barang-keluar', [
                            'barangKeluar' => $record,
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'receipt-' . $record->barang_keluar_number . '.pdf');
                    }),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->color('gray'),
                    Tables\Actions\Action::make('edit-transaction')
                        ->visible(fn(\App\Models\BarangKeluar $record) => $record->status === \App\Enums\Status::PENDING)
                        ->label('Edit Transaction')
                        ->icon('heroicon-o-pencil')
                        ->url(fn($record) => "/barang-keluars/{$record->barang_keluar_number}"),
                    Tables\Actions\Action::make('mark-as-complete')
                        ->visible(fn(\App\Models\BarangKeluar $record) => $record->status === \App\Enums\Status::PENDING)
                        ->requiresConfirmation()
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn(\App\Models\BarangKeluar $record) => $record->markAsComplete())
                        ->label('Mark as Complete'),
                    Tables\Actions\Action::make('divider')->label('')->disabled(),
                    Tables\Actions\DeleteAction::make()
                        ->before(function (\App\Models\BarangKeluar $barangKeluar) {
                            foreach ($barangKeluar->barangKeluarDetails as $detail) {
                                $barang = \App\Models\Barang::find($detail->barang_id);
                                if ($barang) {
                                    $barang->stock_quantity += $detail->quantity;
                                    $barang->save();
                                }
                            }
                            $barangKeluar->barangKeluarDetails()->delete();
                        }),
                ])
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (\Illuminate\Support\Collection $records) {
                            $records->each(fn(\App\Models\BarangKeluar $barangKeluar) => $barangKeluar->barangKeluarDetails()->delete());
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangKeluars::route('/'),
            'create' => Pages\CreateBarangKeluar::route('/create'),
            'edit' => Pages\EditBarangKeluar::route('/{record}/edit'),
            'create-transaction' => Pages\CreateTransaction::route('{record}'),
        ];
    }
}
