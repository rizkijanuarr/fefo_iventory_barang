<?php

namespace App\Filament\Pages;

use App\Filament\Resources\StatsOverviewResource\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static ?string $title = "Dashboard";

    public function getWidgets(): array
    {
        return [
            StatsOverview::class
        ];
    }
}
