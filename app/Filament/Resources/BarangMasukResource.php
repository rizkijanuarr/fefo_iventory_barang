<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BarangMasuk;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BarangMasukResource\Pages;
use App\Filament\Resources\BarangMasukResource\RelationManagers;

class BarangMasukResource extends Resource
{
    protected static ?string $model = BarangMasuk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(4) // Membuat grid dengan 4 kolom
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
                            ->live(500)
                            ->label('Pilih Barang')
                            ->placeholder('Pilih Barang')
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                // Ambil barang berdasarkan barang_id yang dipilih
                                $barang = \App\Models\Barang::find($state);
                                // Set stock_quantity berdasarkan barang yang dipilih
                                $set('stock_quantity', $barang ? $barang->stock_quantity : 0); // Set ke 0 jika tidak ada barang
                            }),

                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('Stock Quantity')
                            ->required()
                            ->live(500)
                            ->numeric()
                            ->disabled() // Menandai sebagai disabled
                            ->placeholder('Stock Quantity'),

                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->live(500)
                            ->label('Qty')
                            ->placeholder('Qty')
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                // Ambil nilai stock_quantity saat ini
                                $currentStock = $get('stock_quantity');

                                // Jika Qty diisi, tambahkan ke stock_quantity
                                if ($state !== '') {
                                    $set('stock_quantity', $currentStock + $state);
                                } else {
                                    // Jika Qty dikosongkan, ambil nilai stock_quantity dari barang yang dipilih
                                    $barang = \App\Models\Barang::find($get('barang_id'));
                                    $set('stock_quantity', $barang ? $barang->stock_quantity : 0);
                                }
                            }),
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




            ]);
    }

    public static function create(array $data): Model
    {
        Log::info('Creating BarangMasuk with data:', $data);

        $barang = \App\Models\Barang::find($data['barang_id']);
        if (!$barang) {
            throw new \Exception('Barang not found for ID: ' . $data['barang_id']);
        }

        // Logika untuk menambah stok barang
        $barang->stock_quantity += $data['quantity'];
        $barang->save();

        // Simpan data BarangMasuk
        $barangMasuk = parent::create($data);

        return $barangMasuk;
    }


    public static function update(array $data, Model $record): Model
    {
        // Simpan data BarangMasuk menggunakan parent::update
        $barangMasuk = parent::update($data, $record);

        // Logika untuk menambah stok barang (misalnya, jika quantity berubah)
        $barang = \App\Models\Barang::find($data['barang_id']);
        if ($barang) {
            // Anda mungkin ingin memeriksa apakah quantity berubah sebelum menambah
            $quantityDifference = $data['quantity'] - $record->quantity; // Perbedaan quantity
            $barang->stock_quantity += $quantityDifference;
            $barang->save();
        }

        return $barangMasuk;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->sortable(),
                Tables\Columns\TextColumn::make('barang.name')
                    ->label('Barang')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qty')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label('Kadaluarsa')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_received')
                    ->label('Barang Masuk')
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBarangMasuks::route('/'),
        ];
    }
}
