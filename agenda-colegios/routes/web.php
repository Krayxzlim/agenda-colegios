<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, UsuarioController, ColegioController, TallerController, AgendaController};

// Solo admin accede a usuarios
Route::middleware(['rol:admin'])->group(function () {
    Route::resource('usuarios', UsuarioController::class);
});

// Admin y talleristas pueden ver colegios y talleres
Route::middleware(['rol:admin,tallerista'])->group(function () {
    Route::resource('colegios', ColegioController::class);
    Route::resource('talleres', TallerController::class);
});

// Todos los roles autenticados pueden ver agenda
Route::middleware(['auth'])->group(function () {
    Route::get('/agenda', [\App\Http\Controllers\AgendaController::class, 'index'])->name('agenda.index');
    Route::post('/agenda/store', [\App\Http\Controllers\AgendaController::class, 'store']);
    Route::delete('/agenda/{id}', [\App\Http\Controllers\AgendaController::class, 'destroy']);
});

Route::get('/', [AgendaController::class, 'index'])->name('agenda.index');
Route::get('login', function(){ return view('auth.login'); })->name('login');
Route::post('login',[AuthController::class,'login']);
Route::post('logout',[AuthController::class,'logout'])->name('logout');

Route::post('agenda/{agenda}/asignar-tallerista', [AgendaController::class,'asignarTallerista'])->name('agenda.asignar');
Route::post('agenda/{agenda}/eliminar-tallerista', [AgendaController::class,'eliminarTallerista'])->name('agenda.eliminar');

