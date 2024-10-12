<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\BarangResource\RelationManagers\BarangKeluarRelationManager;
use App\Filament\Resources\BarangResource\RelationManagers\BarangMasukRelationManager;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Barang')->schema([
                    Forms\Components\Group::make([
                        Forms\Components\FileUpload::make('image')
                            ->required()
                            ->label('Gambar')
                            ->image()
                            ->disk('public')
                            ->maxSize(1024)
                            ->imageCropAspectRatio('1:1')
                            ->directory('images/products'),
                    ])->columns(1)->columnSpanFull(),
                    Forms\Components\Group::make(
                        [
                            Forms\Components\Select::make('category_id')
                                ->relationship('category', 'name')
                                ->label('Pilih Kategori Barang')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Barang')
                                ->placeholder('Masukan Nama Barang')
                                ->required()
                                ->live(500)
                                ->maxLength(255)
                                ->afterStateUpdated(fn(Forms\Set $set, $state) => $set('barcode', 'BR-' . random_int(10000, 99999))),
                            Forms\Components\TextInput::make('barcode')
                                ->placeholder('Generated setelah isi Nama Barang')
                                ->label('Barcode')
                                ->required()
                                ->maxLength(255)
                                ->live(500)
                                ->suffixAction(function (Forms\Set $set, Forms\Get $get) {
                                    return Forms\Components\Actions\Action::make('generateBarcode')
                                        ->icon('heroicon-o-arrow-path')
                                        ->hidden(! $get('name'))
                                        ->action(fn() => $set('barcode', 'BR-' . random_int(10000, 99999)));
                                }),
                        ]
                    )->columns(3)->columnSpanFull(),

                    Forms\Components\MarkdownEditor::make('description')
                        ->maxLength(65535)
                        ->required()
                        ->default('Deskripsi singkat')
                        ->label('Deskripsi')
                        ->placeholder('Masukan Deskripsi')
                        ->columnSpanFull(),
                    Forms\Components\Group::make([
                        Forms\Components\TextInput::make('stock_quantity')
                            ->required()
                            ->default('100')
                            ->label('Stock Barang')
                            ->placeholder('Stock Barang, ex: 100')
                            ->numeric(),
                        Forms\Components\TextInput::make('cost_price')
                            ->required()
                            ->default('5000')
                            ->label('Harga Pokok')
                            ->placeholder('ex: 5000')
                            ->numeric()
                            ->prefix('Rp '),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->default('7000')
                            ->label('Harga Jual')
                            ->placeholder('ex: 7000') // mengambil 20% dari harga pokok
                            ->numeric()
                            ->prefix('Rp ')
                            ->live(500),
                        Forms\Components\DatePicker::make('expiration_date')
                            ->label('Tanggal Kadaluwarsa')
                            ->required(),
                    ])->columns(4)->columnSpanFull(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('barcode')
                    ->label('Barcode')
                    ->formatStateUsing(function ($state) {
                        return view('components.barcode', ['barcode' => $state]);
                    }),
                Tables\Columns\ImageColumn::make('image')->circular()->label('Gambar'),
                Tables\Columns\TextColumn::make('name')->label('Nama Barang')->searchable(),
                Tables\Columns\TextColumn::make('category.name')->label('Kategori'),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock Barang')
                    ->numeric()
                    ->sortable()
                    ->suffix(' Quantity')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('cost_price')->label('Harga Pokok')
                    ->formatStateUsing(fn($state) => formatPrice($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')->label('Harga Jual')
                    ->formatStateUsing(fn($state) => formatPrice($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')->label('Deskripsi')
                    ->limit(500)
                    ->color('gray')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('expiration_date')->label('Tanggal Kadaluwarsa')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label('Diubah Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            BarangKeluarRelationManager::class,
            BarangMasukRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}
