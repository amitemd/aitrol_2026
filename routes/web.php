<?php

use App\Http\Controllers\SesionController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::view('/', 'adminlte.dashboard')->name('dashboard');
Route::view('/panel-anterior', 'dashboard')->name('dashboard.previous');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [SesionController::class, 'create'])->name('login');
    Route::post('/login', [SesionController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
});

Route::post('/logout', [SesionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/adminlte/{page}', function (string $page): Response {
    $file = resource_path('views/adminlte/demo/'.$page);

    abort_unless(is_file($file), Response::HTTP_NOT_FOUND);

    return response(file_get_contents($file), headers: ['Content-Type' => 'text/html; charset=UTF-8']);
})->where('page', '(?:[A-Za-z0-9_-]+/)*[A-Za-z0-9_-]+\.html')->name('adminlte.demo');
