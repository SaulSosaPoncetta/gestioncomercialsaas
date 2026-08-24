<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\ImpuestoController;
use App\Http\Controllers\MonedaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ListaPrecioController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CajaChicaController;
use App\Http\Controllers\NotaCreditoVentaController;
use App\Http\Controllers\NotaCreditoCompraController;
use App\Http\Controllers\TransferenciaController;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');


Route::middleware('auth')->group(function () {
    
Route::middleware('permission:gestionar-configuracion')->group(function () {
    Route::resource('empresas', EmpresaController::class);
    Route::resource('sucursales', SucursalController::class);
    Route::resource('impuestos', ImpuestoController::class);
    Route::resource('monedas', MonedaController::class);
});
    Route::resource('categorias', CategoriaController::class);
    Route::resource('marcas', MarcaController::class);
    Route::get('productos/exportar', [ProductoController::class, 'exportar'])->name('productos.exportar');
    Route::resource('productos', ProductoController::class);
    Route::resource('listas-precios', ListaPrecioController::class);
    Route::get('listas-precios/{listas_precio}/precios', [ListaPrecioController::class, 'precios'])->name('listas-precios.precios');
    Route::post('listas-precios/{listas_precio}/precios', [ListaPrecioController::class, 'guardarPrecios'])->name('listas-precios.guardarPrecios');
    Route::resource('personas', PersonaController::class);
    Route::resource('almacenes', AlmacenController::class);
    Route::get('stock/exportar', [StockController::class, 'exportar'])->name('stock.exportar');
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('stock/ajustar', [StockController::class, 'ajustar'])->name('stock.ajustar');
    Route::post('stock/ajustar', [StockController::class, 'guardarAjuste'])->name('stock.guardarAjuste');
    Route::get('stock/historial', [StockController::class, 'historial'])->name('stock.historial');
    Route::get('compras/exportar', [CompraController::class, 'exportar'])->name('compras.exportar');
    Route::get('compras', [CompraController::class, 'index'])->name('compras.index');
    Route::get('compras/crear', [CompraController::class, 'create'])->name('compras.create');
    Route::post('compras', [CompraController::class, 'store'])->name('compras.store');
    Route::get('compras/{compra}', [CompraController::class, 'show'])->name('compras.show');
    Route::get('ventas/exportar', [VentaController::class, 'exportar'])->name('ventas.exportar');
    Route::get('ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::get('ventas/crear', [VentaController::class, 'create'])->name('ventas.create');
    Route::post('ventas', [VentaController::class, 'store'])->name('ventas.store');
    Route::get('ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');
    Route::get('ventas-ajax/precios-por-lista', [VentaController::class, 'preciosPorLista'])->name('ventas.preciosPorLista');
    Route::resource('cajas', CajaChicaController::class)->except(['show']);
    Route::get('cajas/{caja}/abrir', [CajaChicaController::class, 'abrir'])->name('cajas.abrir');
    Route::post('cajas/{caja}/abrir', [CajaChicaController::class, 'guardarApertura'])->name('cajas.guardarApertura');
    Route::get('cajas-sesion/{sesion}', [CajaChicaController::class, 'sesion'])->name('cajas.sesion');
    Route::post('cajas-sesion/{sesion}/movimiento', [CajaChicaController::class, 'guardarMovimiento'])->name('cajas.guardarMovimiento');
    Route::get('cajas-sesion/{sesion}/cerrar', [CajaChicaController::class, 'formularioCierre'])->name('cajas.formularioCierre');
    Route::post('cajas-sesion/{sesion}/cerrar', [CajaChicaController::class, 'guardarCierre'])->name('cajas.guardarCierre');
    Route::get('notas-credito-venta', [NotaCreditoVentaController::class, 'index'])->name('notas-credito-venta.index');
    Route::get('notas-credito-venta/crear', [NotaCreditoVentaController::class, 'create'])->name('notas-credito-venta.create');
    Route::get('notas-credito-venta/datos-venta/{venta}', [NotaCreditoVentaController::class, 'datosVenta'])->name('notas-credito-venta.datosVenta');
    Route::post('notas-credito-venta', [NotaCreditoVentaController::class, 'store'])->name('notas-credito-venta.store');
    Route::get('notas-credito-venta/{notasCreditoVentum}', [NotaCreditoVentaController::class, 'show'])->name('notas-credito-venta.show');
    Route::get('ventas/{venta}/factura-imprimir', [VentaController::class, 'imprimirFactura'])->name('ventas.factura.imprimir');
    Route::get('ventas/{venta}/remito-imprimir', [VentaController::class, 'imprimirRemito'])->name('ventas.remito.imprimir');
    Route::get('notas-credito-compra', [NotaCreditoCompraController::class, 'index'])->name('notas-credito-compra.index');
    Route::get('notas-credito-compra/crear', [NotaCreditoCompraController::class, 'create'])->name('notas-credito-compra.create');
    Route::get('notas-credito-compra/datos-compra/{compra}', [NotaCreditoCompraController::class, 'datosCompra'])->name('notas-credito-compra.datosCompra');
    Route::post('notas-credito-compra', [NotaCreditoCompraController::class, 'store'])->name('notas-credito-compra.store');
    Route::get('notas-credito-compra/{notasCreditoComprum}', [NotaCreditoCompraController::class, 'show'])->name('notas-credito-compra.show');
    Route::get('compras/{compra}/remito-imprimir', [CompraController::class, 'imprimirRemito'])->name('compras.remito.imprimir');
    Route::get('transferencias', [TransferenciaController::class, 'index'])->name('transferencias.index');
    Route::get('transferencias/pendientes', [TransferenciaController::class, 'pendientes'])->name('transferencias.pendientes');
    Route::get('transferencias/crear', [TransferenciaController::class, 'create'])->name('transferencias.create');
    Route::get('transferencias-ajax/stock-por-almacen', [TransferenciaController::class, 'stockPorAlmacen'])->name('transferencias.stockPorAlmacen');
    Route::post('transferencias', [TransferenciaController::class, 'store'])->name('transferencias.store');
    Route::get('transferencias/{transferencia}', [TransferenciaController::class, 'show'])->name('transferencias.show');
    Route::get('transferencias/{transferencia}/recibir', [TransferenciaController::class, 'recibir'])->name('transferencias.recibir');
    Route::post('transferencias/{transferencia}/recibir', [TransferenciaController::class, 'guardarRecepcion'])->name('transferencias.guardarRecepcion');
    Route::get('transferencias/{transferencia}/remito-imprimir', [TransferenciaController::class, 'imprimirRemito'])->name('transferencias.remito.imprimir');

    });

require __DIR__.'/auth.php';