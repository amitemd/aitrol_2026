<?php

use App\Http\Controllers\ModuleCatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('modules.index');
});

Route::get('/mapa-modulos', ModuleCatalogController::class)->name('modules.index');
