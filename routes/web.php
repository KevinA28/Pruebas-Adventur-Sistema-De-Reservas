<?php
// =====================================================================
// ARCHIVO: web.php
// UBICACIÓN: routes/web.php
// =====================================================================

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PagoController;

// Página raíz
Route::get('/', fn() => view('welcome'));

// Dashboard
Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

// ── Clientes ──────────────────────────────────────────────────────────
Route::resource('clientes', ClienteController::class);
Route::get('/clientes/buscar-documento', [ClienteController::class, 'buscarDocumento'])
    ->name('clientes.buscar-documento');

// ── Reservas ──────────────────────────────────────────────────────────
Route::resource('reservas', ReservaController::class);
Route::patch('/reservas/{reserva}/estado', [ReservaController::class, 'cambiarEstado'])
    ->name('reservas.cambiar-estado');

// ── Pagos ─────────────────────────────────────────────────────────────
Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
Route::patch('/pagos/{pago}/verificar', [PagoController::class, 'verificar'])->name('pagos.verificar');
Route::patch('/pagos/{pago}/rechazar', [PagoController::class, 'rechazar'])->name('pagos.rechazar');

// ── Tours (ruta temporal para que el sidebar no rompa) ────────────────
Route::get('/tours', fn() => view('welcome'))->name('tours.index');