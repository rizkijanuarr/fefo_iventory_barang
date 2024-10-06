<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama Customer')
                    ->placeholder('Masukan Nama Customer')
                    ->default('Cita')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email Customer')
                    ->placeholder('Masukan Email Customer')
                    ->default('cita@gmail.com')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->label('No. HP Customer')
                    ->placeholder('Masukan No.HP Customer ex: 6285749463854')
                    ->default('6285749463854')
                    ->required()
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->label('Alamat Customer')
                    ->required()
                    ->placeholder('Masukan Alamat Customer ex: Jl. Perjuangan No.11')
                    ->default('Jl. Perjuangan No.11'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('No.HP Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat Customer')
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
            'index' => Pages\ManageCustomers::route('/'),
        ];
    }
}
