<?php

use Illuminate\Support\Facades\Route;
use rlessi\FilamentMultiSideSelect\Http\Controllers\MultiSideSelectController;

Route::post('/api/filament-multi-side-select/search', [MultiSideSelectController::class, 'search'])
    ->middleware(['web', 'auth'])
    ->name('filament-multi-side-select.search');