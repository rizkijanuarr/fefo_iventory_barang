<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BarangKeluar;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BarangKeluarResource\Pages;
use App\Filament\Resources\BarangKeluarResource\RelationManagers;
use Filament\Tables\Filters\Filter;
use Carbon\Carbon;

class BarangKeluarResource extends Resource
{
    protected static ?string $model = BarangKeluar::class;
    protected static ?string $navigationIcon = 'heroicon-s-inbox-stack';
    protected static ?string $navigationGroup = 'Transactions';
    use \App\Traits\HasNavigationBadge;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(4)
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Pilih Customer')
                            ->placeholder('Pilih Customer'),

                        Forms\Components\Select::make('barang_id')
                            ->relationship('barang', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Pilih Barang')
                            ->placeholder('Pilih Barang')
                            ->live(500)
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                // Ambil barang dari barangMasuk dengan expiration date terdekat
                                $barangMasukTerdekat = \App\Models\BarangMasuk::where('barang_id', $state)
                                    ->orderBy('expiration_date', 'asc')
                                    ->first();

                                if ($barangMasukTerdekat) {
                                    $set('stock_quantity', $barangMasukTerdekat->quantity);
                                } else {
                                    $set('stock_quantity', 0); // Jika tidak ada barang yang masuk
                                }
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
                                    $set('stock_quantity', $currentStock - $state);
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
                Forms\Components\TextInput::make('reason')
                    ->maxLength(255)
                    ->default('Outstock ..')
                    ->required()
                    ->label('Catatan')
                    ->placeholder('Write a reason for the stock adjustment'),
                Forms\Components\DatePicker::make('date_sold')
                    ->label('Tanggal Terjual')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barang.barcode')
                    ->label('Barcode')
                    ->formatStateUsing(function ($state) {
                        return view('components.barcode', ['barcode' => $state]);
                    }),
                Tables\Columns\ImageColumn::make('barang.image')->circular()->label('Gambar'),
                Tables\Columns\TextColumn::make('barang.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qty')
                    ->numeric()
                    ->sortable()
                    ->suffix(' Quantity')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('date_sold')
                    ->label('Terjual')
                    ->date()
                    ->sortable(),
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
                    ->before(function (BarangKeluar $barangKeluars) {
                        // $barangMasuks->tanggapans()->delete();
                        $barangKeluars->delete();
                    }),
                // PRINT PER RECORD
                Tables\Actions\Action::make('print')
                    ->button()
                    ->label(false)
                    ->button()
                    ->color('gray')
                    ->icon('heroicon-o-printer')
                    ->action(function (BarangKeluar $record) {

                        $imagePath = public_path('storage/' . $record->barang->image);


                        if (!file_exists($imagePath)) {
                            Log::error("Image not found at path: " . $imagePath);
                            return response()->json(['error' => 'Image not found'], 404);
                        }

                        $imageData = file_get_contents($imagePath);
                        $imageBase64 = base64_encode($imageData);

                        // Load PDF view and pass data
                        $pdf = Pdf::loadView('pdf.barang-keluar.record-barang-keluar', [
                            'order' => $record,
                            'imageBase64' => $imageBase64,
                        ]);

                        // Return PDF as a downloadable file
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'receipt-barang-keluar-' . $record->id . '.pdf');
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
                    ->exporter(\App\Filament\Exports\BarangKeluarExporter::class),

                // Tombol untuk ekspor ke CSV
                Tables\Actions\ExportAction::make('exportCsv')
                    ->label('Export CSV')
                    ->fileDisk('public')
                    ->color('warning')
                    ->icon('heroicon-o-document')
                    ->exporter(\App\Filament\Exports\BarangKeluarExporter::class),

                // Tombol untuk ekspor ke PDF
                Tables\Actions\Action::make('printAllRecords')
                    ->label('Export PDF')
                    ->button()
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->action(function () {
                        $barangKeluars = BarangKeluar::with('barang')->paginate(10);

                        $pdf = Pdf::loadView('pdf.barang-keluar.record-barang-keluar-paginate', [
                            'barangKeluars' => $barangKeluars,
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'barang-keluar-' . now()->format('Y-m-d_H-i-s') . '.pdf');
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBarangKeluars::route('/'),
        ];
    }
}
