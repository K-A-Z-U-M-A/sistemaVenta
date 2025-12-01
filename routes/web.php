<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\categoriaController;
use App\Http\Controllers\clienteController;
use App\Http\Controllers\compraController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\homeController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\logoutController;
use App\Http\Controllers\marcaController;
use App\Http\Controllers\presentacioneController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\proveedorController;
use App\Http\Controllers\roleController;
use App\Http\Controllers\userController;
use App\Http\Controllers\ventaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return redirect()->route('login');
});

// Ruta de panel (home después de login)
Route::get('/panel', [homeController::class, 'index'])->name('panel');

Route::resources([
    'categorias' => categoriaController::class,
    'presentaciones' => presentacioneController::class,
    'marcas' => marcaController::class,
    'productos' => ProductoController::class,
    'clientes' => clienteController::class,
    'proveedores' => proveedorController::class,
    'compras' => compraController::class,
    'ventas' => ventaController::class,
    'users' => userController::class,
    'roles' => roleController::class,
    'profile' => profileController::class,
    'devoluciones' => DevolucionController::class
]);

// Rutas adicionales para ventas
Route::get('/ventas/{venta}/imprimir-ticket', [ventaController::class, 'imprimirTicket'])->name('ventas.imprimir-ticket');
Route::get('/ventas/{venta}/imprimir-cocina', [ventaController::class, 'imprimirTicketCocina'])->name('ventas.imprimir-cocina');
Route::patch('/ventas/{venta}/cambiar-estado', [ventaController::class, 'cambiarEstado'])->name('ventas.cambiar-estado');

// Rutas adicionales para devoluciones
Route::get('/devoluciones/venta/{venta}/productos', [DevolucionController::class, 'getVentaProductos'])->name('devoluciones.venta-productos');

// Rutas de Estadísticas e Inventario
Route::get('/estadisticas', [EstadisticaController::class, 'index'])->name('estadisticas.index');
Route::get('/estadisticas/exportar-excel', [EstadisticaController::class, 'exportarExcel'])->name('estadisticas.exportar-excel');
Route::get('/estadisticas/pdf', [EstadisticaController::class, 'pdf'])->name('estadisticas.pdf');
Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');

// Rutas de Caja
Route::get('/caja', [CajaController::class, 'index'])->name('caja.index');
Route::post('/caja/abrir', [CajaController::class, 'abrir'])->name('caja.abrir');
Route::patch('/caja/{caja}/cerrar', [CajaController::class, 'cerrar'])->name('caja.cerrar');
Route::get('/caja/{caja}', [CajaController::class, 'show'])->name('caja.show');
Route::get('/caja-balance', [CajaController::class, 'balance'])->name('caja.balance');

Route::get('/login',[loginController::class,'index'])->name('login');
Route::post('/login',[loginController::class,'login']);
Route::get('/logout',[logoutController::class,'logout'])->name('logout');

Route::get('/401', function () {
    return view('pages.401');
});
Route::get('/404', function () {
    return view('pages.404');
});
Route::get('/500', function () {
    return view('pages.500');
});

// Rutas de Backup
Route::group(['middleware' => ['auth']], function () {
    Route::get('/backups', [App\Http\Controllers\BackupController::class, 'index'])->name('backup.index');
    Route::post('/backups/create', [App\Http\Controllers\BackupController::class, 'create'])->name('backup.create');
    Route::get('/backups/download/{file_name}', [App\Http\Controllers\BackupController::class, 'download'])->name('backup.download');
    Route::delete('/backups/delete/{file_name}', [App\Http\Controllers\BackupController::class, 'destroy'])->name('backup.destroy');
});

// Rutas de Mantenimiento del Sistema y Registro de Actividad (Solo Administradores)
Route::group(['middleware' => ['auth', 'role:administrador']], function () {
    Route::get('/system', [App\Http\Controllers\SystemController::class, 'index'])->name('system.index');
    Route::post('/system/optimize', [App\Http\Controllers\SystemController::class, 'optimize'])->name('system.optimize');
    Route::post('/system/cache-config', [App\Http\Controllers\SystemController::class, 'cacheConfig'])->name('system.cache-config');
    Route::post('/system/cache-route', [App\Http\Controllers\SystemController::class, 'cacheRoute'])->name('system.cache-route');
    Route::post('/system/cache-view', [App\Http\Controllers\SystemController::class, 'cacheView'])->name('system.cache-view');
    
    // Registro de Actividad
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('/activity-log/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-log.show');
    Route::post('/activity-log/clean', [ActivityLogController::class, 'clean'])->name('activity-log.clean');
});
