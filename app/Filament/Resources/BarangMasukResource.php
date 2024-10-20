<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BarangMasuk;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BarangMasukResource\Pages;
use App\Filament\Resources\BarangMasukResource\RelationManagers;
use Filament\Tables\Filters\Filter;
use Carbon\Carbon;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\IconColumn;

class BarangMasukResource extends Resource
{
    protected static ?string $model = BarangMasuk::class;
    protected static ?string $navigationIcon = 'heroicon-s-inbox-arrow-down';
    protected static ?string $navigationGroup = 'Transactions';
    use \App\Traits\HasNavigationBadge;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Grid::make(4)
                    ->schema([
                        Forms\Components\Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Pilih Supplier')
                            ->placeholder('Pilih Supplier'),

                        Forms\Components\Select::make('barang_id')
                            ->relationship('barang', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Pilih Barang')
                            ->placeholder('Pilih Barang')
                            ->live(500)
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {

                                $barang = \App\Models\Barang::find($state);

                                $set('stock_quantity', $barang ? $barang->stock_quantity : 0);
                            }),

                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->live(500)
                            ->label('Qty')
                            ->placeholder('Qty')
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {

                                $currentStock = $get('stock_quantity');


                                if ($state !== '') {
                                    $set('stock_quantity', $currentStock + $state);
                                } else {

                                    $barang = \App\Models\Barang::find($get('barang_id'));
                                    $set('stock_quantity', $barang ? $barang->stock_quantity : 0);
                                }
                            }),

                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('Qty')
                            ->required()
                            ->live(500)
                            ->numeric()
                            ->disabled()
                            ->placeholder('Stock Quantity'),

                    ]),

                Forms\Components\Group::make(
                    [
                        Forms\Components\TextInput::make('reason')
                            ->maxLength(255)
                            ->default('Restock.')
                            ->required()
                            ->label('Catatan')
                            ->placeholder('Write a reason for the stock adjustment'),
                    ]
                )->columns(1)->columnSpanFull(),

                Forms\Components\Group::make(
                    [
                        Forms\Components\DatePicker::make('expiration_date')
                            ->label('Tanggal Kadaluarsa')
                            ->required(),

                        Forms\Components\DatePicker::make('date_received')
                            ->label('Tanggal Barang Masuk')
                            ->required(),
                    ]
                )->columns(2)->columnSpanFull(),
                Forms\Components\Toggle::make('is_returned')
                    ->label('Apakah dikembalikan?')
                    ->disabled() // Disabled, karena otomatis diatur oleh sistem
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('barang.barcode')
                    ->label('Barcode')
                    ->formatStateUsing(function ($state) {
                        return view('components.barcode', ['barcode' => $state]);
                    }),
                Tables\Columns\ImageColumn::make('barang.image')->circular()->label('Gambar'),
                Tables\Columns\TextColumn::make('barang.name')
                    ->label('Barang')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qty')
                    ->numeric()
                    ->sortable()
                    ->suffix(' Quantity')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label('Kadaluarsa')
                    ->date()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('date_received')
                //     ->label('Barang Masuk')
                //     ->date()
                //     ->sortable(),
                Tables\Columns\IconColumn::make('is_returned')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark'),
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
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal')
                            ->placeholder('Pilih Tanggal Awal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal')
                            ->placeholder('Pilih Tanggal Akhir'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', Carbon::parse($date))
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', Carbon::parse($date))
                            );
                    })
                    ->label('Filter Tanggal Barang Masuk'),
                Filter::make('is_returned')
                    ->query(fn(Builder $query): Builder => $query->where('is_returned', true))
                    ->label('Barang Return'),
                Filter::make('expired')
                    ->query(fn(Builder $query): Builder => $query->where('expiration_date', '<', now()))
                    ->label('Barang Expired'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->label(false)
                    ->button()
                    ->color('primary'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->label(false)
                    ->button()
                    ->color('success'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label(false)
                    ->button()
                    ->color('danger')
                    ->before(function (BarangMasuk $barangMasuks) {
                        $barangMasuks->delete();
                    }),
                // PRINT PER RECORD
                Tables\Actions\Action::make('print')
                    ->button()
                    ->color('gray')
                    ->label(false)
                    ->button()
                    ->icon('heroicon-o-printer')
                    ->action(function (BarangMasuk $record) {
                        $imagePath = public_path('storage/' . $record->barang->image);

                        if (!file_exists($imagePath)) {
                            Log::error("Image not found at path: " . $imagePath);
                            return response()->json(['error' => 'Image not found'], 404);
                        }

                        $imageData = file_get_contents($imagePath);
                        $imageBase64 = base64_encode($imageData);

                        // Load PDF view and pass data
                        $pdf = Pdf::loadView('pdf.barang-masuk.record-barang-masuk', [
                            'order' => $record,
                            'imageBase64' => $imageBase64,
                        ]);

                        // Return PDF as a downloadable file
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'receipt-barang-masuk-' . $record->id . '.pdf');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                // Tombol untuk ekspor ke Excel
                Tables\Actions\ExportAction::make()
                    ->label('Export Excel')
                    ->fileDisk('public')
                    ->color('success')
                    ->icon('heroicon-o-document-text')
                    ->exporter(\App\Filament\Exports\BarangMasukExporter::class),

                // Tombol untuk ekspor ke CSV
                Tables\Actions\ExportAction::make('exportCsv')
                    ->label('Export CSV')
                    ->fileDisk('public')
                    ->color('warning')
                    ->icon('heroicon-o-document')
                    ->exporter(\App\Filament\Exports\BarangMasukExporter::class),

                // Tombol untuk ekspor ke PDF
                Tables\Actions\Action::make('printAllRecords')
                    ->label('Export PDF')
                    ->button()
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->action(function () {
                        $barangMasuks = BarangMasuk::with('barang')->paginate(10); // Load multiple records

                        // Load PDF view and pass multiple records
                        $pdf = Pdf::loadView('pdf.barang-masuk.record-barang-masuk-paginate', [
                            'barangMasuks' => $barangMasuks, // Pass all paginated records
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'barang-masuk-' . now()->format('Y-m-d_H-i-s') . '.pdf');
                    }),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBarangMasuks::route('/'),
        ];
    }
}
