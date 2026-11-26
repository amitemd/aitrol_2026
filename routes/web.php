<?php

use App\Http\Controllers\RecuperacionClaveController;
use App\Http\Controllers\SesionController;
use App\Services\OpenApiDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'auth.session'])->group(function (): void {
    Route::view('/', 'adminlte.dashboard')->name('dashboard');
    Route::view('/panel-anterior', 'dashboard')->name('dashboard.previous');
    Route::post('/logout', [SesionController::class, 'destroy'])->name('logout');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [SesionController::class, 'create'])->name('login');
    Route::post('/login', [SesionController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/forgot-password', [RecuperacionClaveController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [RecuperacionClaveController::class, 'store'])
        ->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [RecuperacionClaveController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [RecuperacionClaveController::class, 'update'])
        ->middleware('throttle:6,1')->name('password.update');
});

Route::view('/docs/api', 'api.swagger')->name('swagger.index');

Route::get('/openapi.json', function (Request $request, OpenApiDocument $document): JsonResponse {
    return response()->json($document->build($request->getBaseUrl()));
})->name('swagger.spec');

Route::get('/adminlte/{page}', function (string $page): Response {
    $file = resource_path('views/adminlte/demo/'.$page);

    abort_unless(is_file($file), Response::HTTP_NOT_FOUND);

    return response(file_get_contents($file), headers: ['Content-Type' => 'text/html; charset=UTF-8']);
})->where('page', '(?:[A-Za-z0-9_-]+/)*[A-Za-z0-9_-]+\.html')->name('adminlte.demo');
