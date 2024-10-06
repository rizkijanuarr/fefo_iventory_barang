<?php

namespace App\Filament\Resources\BarangResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BarangMasukRelationManager extends RelationManager
{
    protected static string $relationship = 'barangMasuks';

    public function form(Form $form): Form
    {
        return \App\Filament\Resources\BarangMasukResource::form($form);
    }

    public function table(Table $table): Table
    {
        return \App\Filament\Resources\BarangMasukResource::table($table);
    }
}
