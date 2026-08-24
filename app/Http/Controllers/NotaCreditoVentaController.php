<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\DetalleNotaCreditoVenta;
use App\Models\MovimientoInventario;
use App\Models\NotaCreditoVenta;
use App\Models\Producto;
use App\Models\Stock;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NotaCreditoVentaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = NotaCreditoVenta::with(['venta.cliente']);

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where('numero_nota', 'like', "%{$buscar}%");
        }

        $notas = $query->orderByDesc('fecha')->paginate(10)->withQueryString();
        return view('notas-credito-venta.index', compact('notas'));
    }

    public function create()
    {
        $ventas = Venta::with('cliente')->where('estado', 'COMPLETADA')->orderByDesc('fecha_venta')->get();
        $almacenes = Almacen::where('estado', true)->orderBy('nombre')->get();
        return view('notas-credito-venta.create', compact('ventas', 'almacenes'));
    }

    public function datosVenta(Venta $venta)
    {
        $venta->load('detalles.producto');

        $lineas = $venta->detalles->map(function ($detalle) {
            $yaDevuelto = DetalleNotaCreditoVenta::whereHas('notaCredito', function ($q) use ($detalle) {
                $q->where('id_venta', $detalle->id_venta)->where('estado', 'EMITIDA');
            })->where('id_producto', $detalle->id_producto)->sum('cantidad');

            return [
                'id_producto' => $detalle->id_producto,
                'sku' => $detalle->producto->sku ?? '',
                'nombre' => $detalle->producto->nombre ?? '',
                'cantidad_vendida' => (float) $detalle->cantidad,
                'cantidad_disponible' => max(0, (float) $detalle->cantidad - (float) $yaDevuelto),
                'precio_unitario' => (float) $detalle->precio_unitario,
                'porcentaje_impuesto' => $detalle->cantidad > 0
                    ? round(((float) $detalle->monto_impuesto / (((float) $detalle->cantidad * (float) $detalle->precio_unitario) - (float) $detalle->descuento)) * 100, 2)
                    : 0,
            ];
        });

        return response()->json($lineas);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_venta' => 'required|exists:ventas,id_venta',
            'id_almacen' => 'required|exists:almacenes,id_almacen',
            'motivo' => 'required|string|max:255',
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|numeric|min:0.001',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        try {
            $nota = DB::transaction(function () use ($data) {
                $venta = Venta::findOrFail($data['id_venta']);

                foreach ($data['productos'] as $linea) {
                    $detalleVenta = $venta->detalles()->where('id_producto', $linea['id_producto'])->first();
                    if (!$detalleVenta) {
                        throw ValidationException::withMessages(['productos' => 'El producto no pertenece a esta venta.']);
                    }

                    $yaDevuelto = DetalleNotaCreditoVenta::whereHas('notaCredito', function ($q) use ($data) {
                        $q->where('id_venta', $data['id_venta'])->where('estado', 'EMITIDA');
                    })->where('id_producto', $linea['id_producto'])->sum('cantidad');

                    $disponible = $detalleVenta->cantidad - $yaDevuelto;

                    if ($linea['cantidad'] > $disponible) {
                        $producto = Producto::find($linea['id_producto']);
                        throw ValidationException::withMessages([
                            'productos' => "No se puede devolver más de lo vendido de \"{$producto->nombre}\" (disponible para devolución: {$disponible}).",
                        ]);
                    }
                }

                $subtotal = 0;
                $montoImpuesto = 0;

                foreach ($data['productos'] as $linea) {
                    $producto = Producto::with('impuesto')->find($linea['id_producto']);
                    $baseLinea = $linea['cantidad'] * $linea['precio_unitario'];
                    $porcentajeImpuesto = $producto->impuesto->porcentaje ?? 0;
                    $impuestoLinea = round($baseLinea * ($porcentajeImpuesto / 100), 2);

                    $subtotal += $baseLinea;
                    $montoImpuesto += $impuestoLinea;
                }

                $total = $subtotal + $montoImpuesto;
                $numeroNota = 'NCV-' . str_pad((NotaCreditoVenta::max('id_nota_credito_venta') + 1), 8, '0', STR_PAD_LEFT);

                $nota = NotaCreditoVenta::create([
                    'id_venta' => $data['id_venta'],
                    'numero_nota' => $numeroNota,
                    'fecha' => now()->toDateString(),
                    'motivo' => $data['motivo'],
                    'subtotal' => $subtotal,
                    'monto_impuesto' => $montoImpuesto,
                    'total' => $total,
                    'estado' => 'EMITIDA',
                    'id_usuario' => auth()->id(),
                ]);

                foreach ($data['productos'] as $linea) {
                    $producto = Producto::with('impuesto')->find($linea['id_producto']);
                    $baseLinea = $linea['cantidad'] * $linea['precio_unitario'];
                    $porcentajeImpuesto = $producto->impuesto->porcentaje ?? 0;
                    $impuestoLinea = round($baseLinea * ($porcentajeImpuesto / 100), 2);

                    DetalleNotaCreditoVenta::create([
                        'id_nota_credito_venta' => $nota->id_nota_credito_venta,
                        'id_producto' => $linea['id_producto'],
                        'cantidad' => $linea['cantidad'],
                        'precio_unitario' => $linea['precio_unitario'],
                        'monto_impuesto' => $impuestoLinea,
                        'subtotal' => $baseLinea + $impuestoLinea,
                    ]);

                    if ($producto->aplica_inventario) {
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
                            'tipo_movimiento' => 'DEVOLUCION_VENTA',
                            'cantidad' => $linea['cantidad'],
                            'costo_unitario' => $producto->precio_costo,
                            'referencia_documento' => $numeroNota,
                            'motivo' => 'Devolución por nota de crédito ' . $numeroNota,
                            'id_usuario' => auth()->id(),
                        ]);
                    }
                }

                $venta = Venta::with('cuentaPorCobrar')->find($data['id_venta']);
                if ($venta->cuentaPorCobrar) {
                    $cxc = $venta->cuentaPorCobrar;
                    $cxc->monto_cobrado += min($total, $cxc->saldo_pendiente);
                    $cxc->estado = $cxc->monto_cobrado >= $cxc->monto_total ? 'PAGADO' : 'PAGADO_PARCIAL';
                    $cxc->save();
                }

                return $nota;
            });
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        return redirect()->route('notas-credito-venta.show', $nota)->with('success', 'Nota de crédito emitida correctamente. Se repuso el stock automáticamente.');
    }

    public function show(NotaCreditoVenta $notasCreditoVentum)
    {
        $notasCreditoVentum->load(['venta.cliente', 'detalles.producto', 'usuario']);
        return view('notas-credito-venta.show', ['nota' => $notasCreditoVentum]);
    }
}