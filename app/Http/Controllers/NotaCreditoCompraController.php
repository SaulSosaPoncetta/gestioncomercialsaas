<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Compra;
use App\Models\DetalleNotaCreditoCompra;
use App\Models\MovimientoInventario;
use App\Models\NotaCreditoCompra;
use App\Models\Producto;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NotaCreditoCompraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = NotaCreditoCompra::with(['compra.proveedor']);

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where('numero_nota', 'like', "%{$buscar}%");
        }

        $notas = $query->orderByDesc('fecha')->paginate(10)->withQueryString();
        return view('notas-credito-compra.index', compact('notas'));
    }

    public function create()
    {
        $compras = Compra::with('proveedor')->where('estado', 'RECIBIDO')->orderByDesc('fecha_recepcion')->get();
        $almacenes = Almacen::where('estado', true)->orderBy('nombre')->get();
        return view('notas-credito-compra.create', compact('compras', 'almacenes'));
    }

    public function datosCompra(Compra $compra)
    {
        $compra->load('detalles.producto');

        $lineas = $compra->detalles->map(function ($detalle) {
            $yaDevuelto = DetalleNotaCreditoCompra::whereHas('notaCredito', function ($q) use ($detalle) {
                $q->where('id_compra', $detalle->id_compra)->where('estado', 'EMITIDA');
            })->where('id_producto', $detalle->id_producto)->sum('cantidad');

            return [
                'id_producto' => $detalle->id_producto,
                'sku' => $detalle->producto->sku ?? '',
                'nombre' => $detalle->producto->nombre ?? '',
                'cantidad_comprada' => (float) $detalle->cantidad,
                'cantidad_disponible' => max(0, (float) $detalle->cantidad - (float) $yaDevuelto),
                'costo_unitario' => (float) $detalle->costo_unitario,
            ];
        });

        return response()->json($lineas);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_compra' => 'required|exists:compras,id_compra',
            'id_almacen' => 'required|exists:almacenes,id_almacen',
            'motivo' => 'required|string|max:255',
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|numeric|min:0.001',
            'productos.*.costo_unitario' => 'required|numeric|min:0',
        ]);

        try {
            $nota = DB::transaction(function () use ($data) {
                $compra = Compra::findOrFail($data['id_compra']);

                foreach ($data['productos'] as $linea) {
                    $detalleCompra = $compra->detalles()->where('id_producto', $linea['id_producto'])->first();
                    if (!$detalleCompra) {
                        throw ValidationException::withMessages(['productos' => 'El producto no pertenece a esta compra.']);
                    }

                    $yaDevuelto = DetalleNotaCreditoCompra::whereHas('notaCredito', function ($q) use ($data) {
                        $q->where('id_compra', $data['id_compra'])->where('estado', 'EMITIDA');
                    })->where('id_producto', $linea['id_producto'])->sum('cantidad');

                    $disponible = $detalleCompra->cantidad - $yaDevuelto;

                    if ($linea['cantidad'] > $disponible) {
                        $producto = Producto::find($linea['id_producto']);
                        throw ValidationException::withMessages([
                            'productos' => "No se puede devolver más de lo comprado de \"{$producto->nombre}\" (disponible para devolución: {$disponible}).",
                        ]);
                    }

                    // Validar que haya stock físico suficiente para devolver al proveedor
                    $stock = Stock::where('id_producto', $linea['id_producto'])
                        ->where('id_almacen', $data['id_almacen'])
                        ->first();
                    $stockDisponible = $stock->cantidad ?? 0;

                    if ($linea['cantidad'] > $stockDisponible) {
                        $producto = Producto::find($linea['id_producto']);
                        throw ValidationException::withMessages([
                            'productos' => "No hay stock suficiente de \"{$producto->nombre}\" en ese almacén para devolver al proveedor (disponible: {$stockDisponible}).",
                        ]);
                    }
                }

                $subtotal = 0;
                foreach ($data['productos'] as $linea) {
                    $subtotal += $linea['cantidad'] * $linea['costo_unitario'];
                }

                $numeroNota = 'NCC-' . str_pad((NotaCreditoCompra::max('id_nota_credito_compra') + 1), 8, '0', STR_PAD_LEFT);

                $nota = NotaCreditoCompra::create([
                    'id_compra' => $data['id_compra'],
                    'numero_nota' => $numeroNota,
                    'fecha' => now()->toDateString(),
                    'motivo' => $data['motivo'],
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                    'estado' => 'EMITIDA',
                    'id_usuario' => auth()->id(),
                ]);

                foreach ($data['productos'] as $linea) {
                    $producto = Producto::find($linea['id_producto']);

                    DetalleNotaCreditoCompra::create([
                        'id_nota_credito_compra' => $nota->id_nota_credito_compra,
                        'id_producto' => $linea['id_producto'],
                        'cantidad' => $linea['cantidad'],
                        'costo_unitario' => $linea['costo_unitario'],
                        'subtotal' => $linea['cantidad'] * $linea['costo_unitario'],
                    ]);

                    if ($producto->aplica_inventario) {
                        $stock = Stock::where('id_producto', $linea['id_producto'])
                            ->where('id_almacen', $data['id_almacen'])
                            ->first();
                        $stock->cantidad -= $linea['cantidad'];
                        $stock->save();

                        MovimientoInventario::create([
                            'id_producto' => $linea['id_producto'],
                            'id_almacen_origen' => $data['id_almacen'],
                            'id_almacen_destino' => null,
                            'tipo_movimiento' => 'DEVOLUCION_COMPRA',
                            'cantidad' => $linea['cantidad'],
                            'costo_unitario' => $linea['costo_unitario'],
                            'referencia_documento' => $numeroNota,
                            'motivo' => 'Devolución a proveedor por nota de crédito ' . $numeroNota,
                            'id_usuario' => auth()->id(),
                        ]);
                    }
                }

                $compra = Compra::with('cuentaPorPagar')->find($data['id_compra']);
                if ($compra->cuentaPorPagar) {
                    $cxp = $compra->cuentaPorPagar;
                    $cxp->monto_pagado += min($subtotal, $cxp->saldo_pendiente);
                    $cxp->estado = $cxp->monto_pagado >= $cxp->monto_total ? 'PAGADO' : 'PAGADO_PARCIAL';
                    $cxp->save();
                }

                return $nota;
            });
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        return redirect()->route('notas-credito-compra.show', $nota)->with('success', 'Nota de crédito registrada correctamente. Se descontó el stock automáticamente.');
    }

    public function show(NotaCreditoCompra $notasCreditoComprum)
    {
        $notasCreditoComprum->load(['compra.proveedor', 'detalles.producto', 'usuario']);
        return view('notas-credito-compra.show', ['nota' => $notasCreditoComprum]);
    }
}