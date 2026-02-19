<?php

use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
Route::get('/importar', fn () => view('import'))->name('import');
Route::get('/graficos', fn () => view('graficos'))->name('graficos');

Route::prefix('exportar')->name('export.')->group(function () {
    Route::get('/excel', [ExportController::class, 'excel'])->name('excel');
    Route::get('/pdf', [ExportController::class, 'pdf'])->name('pdf');
    Route::get('/modelo-csv', [ExportController::class, 'modeloCsv'])->name('modelo-csv');
});
