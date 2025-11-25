<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/init-db', function () {
    Artisan::call('migrate', [
        '--force' => true,
    ]);

    Artisan::call('db:seed', [
        '--force' => true,
    ]);

    return 'migrated & seeded';
});
