<?php

namespace App\Filament\Resources\BarangResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BarangKeluarRelationManager extends RelationManager
{
    protected static string $relationship = 'barangKeluars';

    public function form(Form $form): Form
    {
        return \App\Filament\Resources\BarangKeluarResource::form($form);
    }

    public function table(Table $table): Table
    {
        return \App\Filament\Resources\BarangKeluarResource::table($table);
    }
}
