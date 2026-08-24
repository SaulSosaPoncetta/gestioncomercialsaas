<?php

namespace App\Http\Controllers;

use App\Models\CuentaPorCobrar;
use App\Models\CuentaPorPagar;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Stock;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $hoy = now()->toDateString();

        $ventasHoyQuery = Venta::whereDate('fecha_venta', $hoy)->where('estado', 'COMPLETADA');
        $totalVentasHoy = (clone $ventasHoyQuery)->sum('total_venta');
        $cantidadVentasHoy = (clone $ventasHoyQuery)->count();

        $stockBajo = Stock::with(['producto', 'almacen'])
            ->get()
            ->filter(fn($s) => $s->producto && $s->producto->estado && $s->cantidad <= $s->producto->stock_minimo)
            ->take(8);

        $cxcPorVencer = CuentaPorCobrar::with('cliente')
            ->where('estado', '!=', 'PAGADO')
            ->orderBy('fecha_vencimiento')
            ->take(6)
            ->get();

        $cxpPorVencer = CuentaPorPagar::with('proveedor')
            ->where('estado', '!=', 'PAGADO')
            ->orderBy('fecha_vencimiento')
            ->take(6)
            ->get();

        $masVendidos = DetalleVenta::select('id_producto', DB::raw('SUM(cantidad) as total_cantidad'))
            ->groupBy('id_producto')
            ->orderByDesc('total_cantidad')
            ->with('producto')
            ->take(5)
            ->get();

        $menosVendidos = Producto::query()
            ->whereHas('detalleCompras')
            ->withSum('detalleVentas as total_vendido', 'cantidad')
            ->orderByRaw('COALESCE(total_vendido, 0) asc')
            ->take(5)
            ->get();

        return view('home', compact(
            'totalVentasHoy',
            'cantidadVentasHoy',
            'stockBajo',
            'cxcPorVencer',
            'cxpPorVencer',
            'masVendidos',
            'menosVendidos'
        ));
    }
}