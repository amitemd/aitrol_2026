<?php

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\EmpresaInicialController;
use App\Http\Controllers\RecuperacionClaveController;
use App\Http\Controllers\SesionController;
use App\Services\OpenApiDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', [SesionController::class, 'inicio'])->middleware('auth.session')->name('dashboard');
Route::get('/login', fn (): RedirectResponse => to_route('dashboard'))->name('login');

Route::middleware(['auth', 'auth.session'])->group(function (): void {
    Route::get('/auditoria/bitacora', [AuditoriaController::class, 'index'])
        ->middleware(['empresa', 'permiso:auditoria.logs.index,leer'])->name('auditoria.logs.index');
    Route::view('/panel-anterior', 'dashboard')->middleware('empresa')->name('dashboard.previous');
    Route::post('/empresa-inicial', [EmpresaInicialController::class, 'store'])->middleware('throttle:10,1')->name('empresa.inicial');
    Route::post('/logout', [SesionController::class, 'destroy'])->name('logout');
});

Route::middleware('guest')->group(function (): void {
    Route::post('/', [SesionController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
    Route::post('/login', [SesionController::class, 'store'])->middleware('throttle:6,1');
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
