<?php

namespace App\Filament\Exports;

use App\Models\BarangMasuk;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Carbon;
use Filament\Actions\Exports\Enums\ExportFormat;

class BarangMasukExporter extends Exporter
{
    protected static ?string $model = BarangMasuk::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('supplier.name'),
            ExportColumn::make('barang.name'),
            ExportColumn::make('quantity'),
            ExportColumn::make('expiration_date')
                ->formatStateUsing(fn($state) => Carbon::parse($state)->translatedFormat('d F Y')),
            ExportColumn::make('date_received')
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
        $body = 'Your barang masuk export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
