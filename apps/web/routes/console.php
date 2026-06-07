<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('about:finproppt', function (): void {
    $this->info('FINPROPPT TIKI Denpasar Laravel frontend.');
});
