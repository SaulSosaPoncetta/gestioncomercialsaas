<?php

namespace App\Http\Controllers;
use App\Traits\ExportaExcel;
use App\Models\Almacen;
use App\Models\Compra;
use App\Models\CuentaPorPagar;
use App\Models\DetalleCompra;
use App\Models\MovimientoInventario;
use App\Models\Persona;
use App\Models\Producto;
use App\Models\Stock;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    use ExportaExcel;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Compra::with(['proveedor', 'sucursal']);

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where('numero_comprobante', 'like', "%{$buscar}%")
                ->orWhereHas('proveedor', fn($q) => $q->where('razon_social_nombre', 'like', "%{$buscar}%"));
        }

        $compras = $query->orderByDesc('fecha_recepcion')->paginate(10)->withQueryString();
        return view('compras.index', compact('compras'));
    }

        public function exportar(Request $request)
    {
        $query = Compra::with(['proveedor', 'sucursal']);

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where('numero_comprobante', 'like', "%{$buscar}%")
                ->orWhereHas('proveedor', fn($q) => $q->where('razon_social_nombre', 'like', "%{$buscar}%"));
        }

        $compras = $query->orderByDesc('fecha_recepcion')->get();

        $encabezados = ['Fecha', 'Comprobante', 'Proveedor', 'Sucursal', 'Condición', 'Subtotal', 'Impuestos', 'Total', 'Estado'];

        $filas = $compras->map(fn($c) => [
            $c->fecha_emision->format('d/m/Y'),
            $c->tipo_comprobante . ' ' . $c->numero_comprobante,
            $c->proveedor->razon_social_nombre ?? '',
            $c->sucursal->nombre ?? '',
            $c->condicion_pago,
            number_format($c->subtotal, 2),
            number_format($c->total_impuestos, 2),
            number_format($c->total_compra, 2),
            $c->estado,
        ]);

        return $this->exportarExcel('compras_' . now()->format('Y-m-d'), $encabezados, $filas);
    }

    public function create()
    {
        $proveedores = Persona::proveedores()->where('estado', true)->orderBy('razon_social_nombre')->get();
        $sucursales = Sucursal::where('estado', true)->orderBy('nombre')->get();
        $productos = Producto::where('estado', true)->orderBy('nombre')->get();
        $almacenes = Almacen::where('estado', true)->orderBy('nombre')->get();

        return view('compras.create', compact('proveedores', 'sucursales', 'productos', 'almacenes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_proveedor' => 'required|exists:personas,id_persona',
            'id_sucursal' => 'required|exists:sucursales,id_sucursal',
            'id_almacen' => 'required|exists:almacenes,id_almacen',
            'tipo_comprobante' => 'required|string|max:20',
            'numero_comprobante' => 'required|string|max:50',
            'fecha_emision' => 'required|date',
            'condicion_pago' => 'required|in:CONTADO,CREDITO',
            'dias_vencimiento' => 'nullable|integer|min:0',
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|numeric|min:0.001',
            'productos.*.costo_unitario' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data) {
            $subtotal = 0;
            foreach ($data['productos'] as $linea) {
                $subtotal += $linea['cantidad'] * $linea['costo_unitario'];
            }

            $compra = Compra::create([
                'id_proveedor' => $data['id_proveedor'],
                'id_sucursal' => $data['id_sucursal'],
                'tipo_comprobante' => $data['tipo_comprobante'],
                'numero_comprobante' => $data['numero_comprobante'],
                'fecha_emision' => $data['fecha_emision'],
                'fecha_recepcion' => now(),
                'condicion_pago' => $data['condicion_pago'],
                'subtotal' => $subtotal,
                'total_impuestos' => 0,
                'total_compra' => $subtotal,
                'estado' => 'RECIBIDO',
            ]);

            foreach ($data['productos'] as $linea) {
                DetalleCompra::create([
                    'id_compra' => $compra->id_compra,
                    'id_producto' => $linea['id_producto'],
                    'cantidad' => $linea['cantidad'],
                    'costo_unitario' => $linea['costo_unitario'],
                ]);

                $stock = Stock::firstOrNew([
                    'id_producto' => $linea['id_producto'],
                    'id_almacen' => $data['id_almacen'],
                ]);
                $stock->cantidad = ($stock->cantidad ?? 0) + $linea['cantidad'];
                $stock->save();

                MovimientoInventario::create([
                    'id_producto' => $linea['id_producto'],
                    'id_almacen_origen' => null,
                    'id_almacen_destino' => $data['id_almacen'],
                    'tipo_movimiento' => 'ENTRADA_COMPRA',
                    'cantidad' => $linea['cantidad'],
                    'costo_unitario' => $linea['costo_unitario'],
                    'referencia_documento' => $data['numero_comprobante'],
                    'motivo' => 'Ingreso por compra ' . $data['numero_comprobante'],
                    'id_usuario' => auth()->id(),
                ]);

                Producto::where('id_producto', $linea['id_producto'])->update(['precio_costo' => $linea['costo_unitario']]);
            }

            if ($data['condicion_pago'] === 'CREDITO') {
                CuentaPorPagar::create([
                    'id_compra' => $compra->id_compra,
                    'id_proveedor' => $data['id_proveedor'],
                    'fecha_vencimiento' => now()->addDays($data['dias_vencimiento'] ?? 30),
                    'monto_total' => $subtotal,
                    'monto_pagado' => 0,
                    'estado' => 'PENDIENTE',
                ]);
            }
        });

        return redirect()->route('compras.index')->with('success', 'Compra registrada correctamente. Se actualizó el stock automáticamente.');
    }

    public function show(Compra $compra)
    {
        $compra->load(['proveedor', 'sucursal', 'detalles.producto', 'cuentaPorPagar']);
        return view('compras.show', compact('compra'));
    }
        public function imprimirRemito(Compra $compra)
    {
        $compra->load(['proveedor', 'sucursal.empresa', 'detalles.producto']);
        return view('compras.remito-imprimir', compact('compra'));
    }
}