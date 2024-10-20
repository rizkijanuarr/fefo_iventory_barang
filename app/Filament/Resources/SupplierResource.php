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
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Master Data';
    use \App\Traits\HasNavigationBadge;

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
                    ->before(function (Supplier $suppliers) {
                        $suppliers->barangMasuks()->delete();
                        $suppliers->delete();
                    }),
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
