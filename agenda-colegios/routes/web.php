<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, UsuarioController, ColegioController, TallerController, AgendaController};

Route::get('login', function(){ return view('auth.login'); })->name('login');
Route::post('login',[AuthController::class,'login']);
Route::post('logout',[AuthController::class,'logout'])->name('logout');

Route::resource('usuarios', UsuarioController::class);
Route::resource('colegios', ColegioController::class);
Route::resource('talleres', TallerController::class);
Route::resource('agenda', AgendaController::class);

Route::post('agenda/{agenda}/asignar-tallerista', [AgendaController::class,'asignarTallerista'])->name('agenda.asignar');
Route::post('agenda/{agenda}/eliminar-tallerista', [AgendaController::class,'eliminarTallerista'])->name('agenda.eliminar');

