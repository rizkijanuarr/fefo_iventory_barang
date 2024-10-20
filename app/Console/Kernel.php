<?php

protected function schedule(Schedule $schedule)
{
    // Jadwalkan command untuk menjalankan setiap hari
    $schedule->command('barang:check-expired')->daily();
}
