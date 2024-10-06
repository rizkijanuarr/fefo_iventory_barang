<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama Supplier')
                    ->placeholder('Masukan Nama Supplier')
                    ->default('PT. Harapan Jaya')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email Supplier')
                    ->placeholder('Masukan Email Supplier')
                    ->default('laura@gmail.com')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->label('No. HP Supplier')
                    ->placeholder('Masukan No.HP Supplier ex: 6285749463854')
                    ->default('6285749463854')
                    ->required()
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->label('Alamat Supplier')
                    ->required()
                    ->placeholder('Masukan Alamat Supplier ex: Jl. Perjuangan No.11')
                    ->default('Jl. Perjuangan No.11'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Supplier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email Supplier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('No.HP Supplier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat Supplier')
                    ->searchable(),
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
            'index' => Pages\ManageSuppliers::route('/'),
        ];
    }
}
