<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SuperAdminController;
use App\Http\Controllers\Admin\CuponController;
use App\Http\Controllers\Tienda\AuthController;
use App\Http\Controllers\Tienda\ApartadoController;
use App\Http\Controllers\Tienda\RenovacionController;
use App\Http\Controllers\Tienda\ClienteController;  // ← NUEVO: importar ClienteController
use App\Http\Controllers\Publico\BuscadorController;
use App\Http\Controllers\Tienda\ConfiguracionController;
use App\Http\Controllers\Publico\LandingController;
use App\Http\Controllers\Publico\RegistroController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;


// ============================================
// PÁGINA PÚBLICA - LANDING (PRINCIPAL)
// ============================================
Route::get('/', [LandingController::class, 'index'])->name('landing');

// ============================================
// RUTAS DE RECUPERACIÓN DE CONTRASEÑA
// ============================================
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// ============================================
// PÁGINA PÚBLICA - BUSCADOR DE APARTADOS
// ============================================
Route::get('/buscar', [BuscadorController::class, 'index'])->name('publico.index');
Route::post('/buscar-apartado', [BuscadorController::class, 'buscar'])->name('publico.buscar');
Route::get('/apartado/{codigo}', [BuscadorController::class, 'mostrar'])->name('publico.mostrar');

// ============================================
// RUTAS DE REGISTRO Y PAGO
// ============================================
Route::post('/registro/procesar', [RegistroController::class, 'procesar'])->name('registro.procesar');
Route::get('/validar-cupon', [RegistroController::class, 'validarCupon'])->name('validar.cupon');
Route::get('/pago/iniciar', [RegistroController::class, 'iniciarPago'])->name('pago.iniciar');
Route::get('/pago/exitoso', [RegistroController::class, 'pagoExitoso'])->name('pago.exitoso');
Route::get('/pago/fallido', [RegistroController::class, 'pagoFallido'])->name('pago.fallido');
Route::get('/pago/pendiente', [RegistroController::class, 'pagoPendiente'])->name('pago.pendiente');
Route::post('/webhook/mercadopago', [RegistroController::class, 'webhook'])->name('webhook.mercadopago');

// ============================================
// LOGIN
// ============================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================================
// RUTAS DE SUPER ADMIN
// ============================================
Route::prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    
    // Gestión de Tiendas
    Route::get('/tiendas', [SuperAdminController::class, 'listarTiendas'])->name('tiendas');
    Route::get('/tiendas/crear', [SuperAdminController::class, 'crearTienda'])->name('tiendas.crear');
    Route::post('/tiendas', [SuperAdminController::class, 'guardarTienda'])->name('tiendas.guardar');
    Route::get('/tiendas/{id}', [SuperAdminController::class, 'verTienda'])->name('tiendas.ver');
    Route::get('/tiendas/{id}/editar', [SuperAdminController::class, 'editarTienda'])->name('tiendas.editar');
    Route::put('/tiendas/{id}', [SuperAdminController::class, 'actualizarTienda'])->name('tiendas.actualizar');
    Route::post('/tiendas/{id}/suspender', [SuperAdminController::class, 'suspenderTienda'])->name('tiendas.suspender');
    Route::post('/tiendas/{id}/activar', [SuperAdminController::class, 'activarTienda'])->name('tiendas.activar');
    
    // Gestión de Cupones
    Route::get('/cupones', [CuponController::class, 'index'])->name('cupones');
    Route::get('/cupones/crear', [CuponController::class, 'crear'])->name('cupones.crear');
    Route::post('/cupones', [CuponController::class, 'guardar'])->name('cupones.guardar');
    Route::get('/cupones/{id}/editar', [CuponController::class, 'editar'])->name('cupones.editar');
    Route::put('/cupones/{id}', [CuponController::class, 'actualizar'])->name('cupones.actualizar');
    Route::delete('/cupones/{id}', [CuponController::class, 'eliminar'])->name('cupones.eliminar');
    Route::post('/cupones/{id}/toggle', [CuponController::class, 'toggleEstado'])->name('cupones.toggle');
    Route::get('/cupones/{id}/estadisticas', [CuponController::class, 'estadisticas'])->name('cupones.estadisticas');
});

// ============================================
// RUTAS DE TIENDA (PROTEGIDAS)
// ============================================
Route::prefix('tienda')->middleware(['auth'])->name('tienda.')->group(function () {
    
    // ========================================
    // RUTAS DE CLIENTES (NUEVAS)
    // ========================================
    Route::get('/clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');
    Route::post('/clientes/nuevo-apartado', [ClienteController::class, 'nuevoApartado'])->name('clientes.nuevo-apartado');
    Route::get('/clientes/historial/{clienteId}', [ClienteController::class, 'historial'])->name('clientes.historial');
    
    // ========================================
    // RUTAS DE RENOVACIÓN (SIN MIDDLEWARE DE SUSCRIPCIÓN)
    // ========================================
    Route::get('/renovar', [RenovacionController::class, 'index'])->name('renovar');
    Route::post('/renovar/procesar', [RenovacionController::class, 'procesar'])->name('renovar.procesar');
    
    // Callbacks de Mercado Pago
    Route::get('/renovar/exitoso', [RenovacionController::class, 'exitoso'])->name('renovar.exitoso');
    Route::get('/renovar/fallido', [RenovacionController::class, 'fallido'])->name('renovar.fallido');
    Route::get('/renovar/pendiente', [RenovacionController::class, 'pendiente'])->name('renovar.pendiente');
    Route::post('/renovar/webhook', [RenovacionController::class, 'webhook'])->name('renovar.webhook');
    
    // ========================================
    // RUTAS PROTEGIDAS POR SUSCRIPCIÓN
    // ========================================
    Route::middleware([\App\Http\Middleware\VerificarSuscripcionTienda::class])->group(function () {
        
        Route::get('/dashboard', [ApartadoController::class, 'dashboard'])->name('dashboard');
        Route::get('/apartados', [ApartadoController::class, 'index'])->name('apartados');
        Route::get('/apartados/crear', [ApartadoController::class, 'crear'])->name('apartados.crear');
        Route::post('/apartados', [ApartadoController::class, 'guardar'])->name('apartados.guardar');
        Route::get('/apartados/{id}', [ApartadoController::class, 'mostrar'])->name('apartados.mostrar');
        Route::get('/apartados/{id}/editar', [ApartadoController::class, 'editar'])->name('apartados.editar');
        Route::put('/apartados/{id}', [ApartadoController::class, 'actualizar'])->name('apartados.actualizar');
        Route::post('/apartados/{id}/pago', [ApartadoController::class, 'registrarPago'])->name('apartados.pago');
        Route::post('/apartados/{id}/pago-producto', [ApartadoController::class, 'registrarPagoProducto'])->name('apartados.pago-producto');
        Route::get('/apartados/buscar', [ApartadoController::class, 'buscar'])->name('apartados.buscar');
        Route::delete('/apartados/{id}', [ApartadoController::class, 'eliminar'])->name('apartados.eliminar');
        Route::get('/apartados/{id}/agregar-producto', [ApartadoController::class, 'formularioAgregarProducto'])->name('apartados.agregar-producto');
        Route::post('/apartados/{id}/agregar-producto', [ApartadoController::class, 'agregarProducto'])->name('apartados.agregar-producto.store');
        
        Route::get('/apartados/{id}/imprimir-producto/{productoId}', [ApartadoController::class, 'imprimirTicketProducto'])->name('apartados.imprimir-producto');
        
        // Buscador específico
        Route::get('/buscar', [ApartadoController::class, 'buscar'])->name('buscar');
        Route::get('/buscar/codigo', [ApartadoController::class, 'buscarPorCodigo'])->name('buscar.codigo');
        
        // Configuración
        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion');
        Route::post('/configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');
    });
});