<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::view('/', 'adminlte.dashboard')->name('dashboard');
Route::view('/panel-anterior', 'dashboard')->name('dashboard.previous');

Route::get('/adminlte/{page}', function (string $page): Response {
    $file = resource_path('views/adminlte/demo/'.$page);

    abort_unless(is_file($file), Response::HTTP_NOT_FOUND);

    return response(file_get_contents($file), headers: ['Content-Type' => 'text/html; charset=UTF-8']);
})->where('page', '(?:[A-Za-z0-9_-]+/)*[A-Za-z0-9_-]+\.html')->name('adminlte.demo');
