<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BarangMasuk;

class CheckExpiredItems extends Command
{
    // Nama command
    protected $signature = 'barang:check-expired';

    // Deskripsi command
    protected $description = 'Check expired items and mark them as returned';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Memanggil fungsi yang sudah dibuat di model BarangMasuk
        BarangMasuk::checkExpiredItems();

        // Tampilkan pesan sukses
        $this->info('Expired items have been checked and marked as returned.');
    }
}
