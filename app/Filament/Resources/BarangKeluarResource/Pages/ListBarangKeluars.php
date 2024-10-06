<?php

namespace App\Filament\Resources\BarangKeluarResource\Pages;

use App\Filament\Resources\BarangKeluarResource;
use App\Models\BarangKeluar;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListBarangKeluars extends ListRecords
{
    protected static string $resource = BarangKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $statuses = collect([
            'all' => ['label' => 'All', 'badgeColor' => 'primary', 'status' => null],
            \App\Enums\Status::PENDING->name => ['label' => 'Pending', 'badgeColor' => 'warning', 'status' => \App\Enums\Status::PENDING],
            \App\Enums\Status::COMPLETED->name => ['label' => 'Completed', 'badgeColor' => 'success', 'status' => \App\Enums\Status::COMPLETED],
            \App\Enums\Status::CANCELLED->name => ['label' => 'Cancelled', 'badgeColor' => 'danger', 'status' => \App\Enums\Status::CANCELLED],
        ]);

        return $statuses->mapWithKeys(function ($data, $key) {
            $badgeCount = is_null($data['status'])
                ? BarangKeluar::count()
                : BarangKeluar::where('status', $data['status'])->count();

            return [$key => Tab::make($data['label'])
                ->badge($badgeCount)
                ->modifyQueryUsing(fn($query) => is_null($data['status']) ? $query : $query->where('status', $data['status']))
                ->badgeColor($data['badgeColor'])];
        })->toArray();
    }
}
