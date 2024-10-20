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
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Master Data';
    use \App\Traits\HasNavigationBadge;

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
                    ->before(function (Customer $customer) {
                        $customer->barangKeluars()->delete();
                        $customer->delete();
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
            'index' => Pages\ManageCustomers::route('/'),
        ];
    }
}
