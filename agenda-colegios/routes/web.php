<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, UsuarioController, ColegioController, TallerController, AgendaController, ReporteController};
use App\Http\Middleware\CheckRole;

Route::get('/', [AgendaController::class, 'index'])->name('agenda.index');

Route::get('login', function(){ return view('auth.login'); })->name('login');
Route::post('login',[AuthController::class,'login']);
Route::post('logout',[AuthController::class,'logout'])->name('logout');

Route::get('/agenda/events', [AgendaController::class, 'getEvents'])->name('agenda.events');
Route::post('/agenda/store', [AgendaController::class, 'store'])->name('agenda.store');
Route::delete('/agenda/{id}', [AgendaController::class, 'destroy'])->name('agenda.destroy');

Route::post('agenda/{agenda}/asignar-tallerista', [AgendaController::class,'asignarTallerista'])->name('agenda.asignar');
Route::post('agenda/{agenda}/eliminar-tallerista', [AgendaController::class,'eliminarTallerista'])->name('agenda.eliminar');

// Rutas protegidas por rol usando la clase completa del middleware

// Usuarios → solo admin
Route::middleware(['auth', CheckRole::class.':admin'])->group(function () {
    Route::resource('usuarios', UsuarioController::class);
});

// Reportes → admin y supervisor
Route::middleware(['auth', CheckRole::class.':admin,supervisor'])->group(function () {
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::post('/reportes/filtrar', [ReporteController::class, 'filtrar'])->name('reportes.filtrar');
    Route::post('/reportes/export', [ReporteController::class, 'export'])->name('reportes.export');
});

// Colegios y Talleres → todos los roles
Route::middleware(['auth', CheckRole::class.':admin,supervisor,tallerista'])->group(function () {
    Route::resource('colegios', ColegioController::class);
    Route::resource('talleres', TallerController::class);
});
