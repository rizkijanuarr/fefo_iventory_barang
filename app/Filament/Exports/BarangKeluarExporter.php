<?php

namespace App\Filament\Exports;

use App\Models\BarangKeluar;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Carbon;
use Filament\Actions\Exports\Enums\ExportFormat;

class BarangKeluarExporter extends Exporter
{
    protected static ?string $model = BarangKeluar::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('customer.name'),
            ExportColumn::make('barang.name'),
            ExportColumn::make('quantity'),
            ExportColumn::make('date_sold')
                ->formatStateUsing(fn($state) => Carbon::parse($state)->translatedFormat('d F Y')),
            ExportColumn::make('created_at')
                ->formatStateUsing(fn($state) => Carbon::parse($state)->translatedFormat('d F Y')),
        ];
    }

    public function getFormats(): array
    {
        return [
            ExportFormat::Xlsx,
            ExportFormat::Csv
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your barang keluar export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
