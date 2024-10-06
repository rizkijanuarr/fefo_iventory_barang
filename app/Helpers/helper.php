<?php

if (! function_exists('formatPrice')) {
    /**
     * formatPrice
     *
     * @param  mixed $str
     * @return void
     */
    function formatPrice($str)
    {
        return 'Rp. ' . number_format($str, '0', '', '.');
    }
}


if (! function_exists('generateBarangKeluarNumber')) {
    function generateBarangKeluarNumber(string $model, ?string $initials = 'BK', string $column = 'barang_keluar_number'): string
    {
        $lastRecord = $model::latest('id')->first();
        $lastNumber = $lastRecord ? intval(substr($lastRecord->$column, strlen($initials))) : 0;
        $newNumber = $lastNumber + 1;

        return $initials . str_pad($newNumber, 8, '0', STR_PAD_LEFT);
    }
}
