<?php

namespace App\Filament\Resources\StatsOverviewResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Categories', \App\Models\Category::count()),
            Stat::make('Total Suppliers', \App\Models\Supplier::count()),
            Stat::make('Total Customer', \App\Models\Customer::count()),
            Stat::make('Total Barang', \App\Models\Barang::count()),
            Stat::make('Total Barang Masuk', \App\Models\BarangMasuk::count()),
            Stat::make('Total Barang Keluar', \App\Models\BarangKeluar::count()),
        ];
    }
}
