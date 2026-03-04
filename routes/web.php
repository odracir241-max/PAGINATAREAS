<?php

use App\Http\Controllers\PendienteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PendienteController::class, 'index'])->name('pendientes.index');
Route::get('/pendientes', [PendienteController::class, 'index']);
Route::post('/pendientes', [PendienteController::class, 'store'])->name('pendientes.store');
Route::patch('/pendientes/{pendiente}/estatus', [PendienteController::class, 'updateStatus'])->name('pendientes.update-status');
Route::get('/pendientes/adjuntos/{adjunto}/descargar', [PendienteController::class, 'downloadAdjunto'])->name('pendientes.adjuntos.download');
Route::get('/pendientes/{pendiente}', [PendienteController::class, 'show'])->whereNumber('pendiente')->name('pendientes.show');
Route::get('/pendientes/calendario.ics', [PendienteController::class, 'calendarIcs'])->name('pendientes.calendar.ics');
