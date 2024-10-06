<?php

namespace App\Filament\Resources\BarangKeluarResource\Pages;

use App\Filament\Resources\BarangKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBarangKeluar extends CreateRecord
{
    protected static string $resource = BarangKeluarResource::class;

    protected function getRedirectUrl(): string
    {
        return route('filament.app.resources.barang-keluars.create-transaction', [
            'record' => $this->record->barang_keluar_number,
        ]);
    }
}
