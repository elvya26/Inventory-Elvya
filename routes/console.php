<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inventory:service', function () {
    $this->info('Service aktif: '.env('SERVICE_NAME', 'inventory'));
});
