<?php

namespace App\Filament\Resources\BarangResource\Pages;

use App\Filament\Resources\BarangResource;
use App\Models\Barang;
use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBarang extends EditRecord
{
    protected static string $resource = BarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(function (Barang $barang) {
                    try {
                        $barang->delete();
                        Notification::make()
                            ->success()
                            ->title('Barang Deleted')
                            ->body('The barang has been successfully deleted.')
                            ->send();
                    } catch (QueryException $e) {
                        Notification::make()
                            ->danger()
                            ->title('Failed to delete barang')
                            ->body('This barang is still being used.')
                            ->send();
                    }
                }),
        ];
    }
}
