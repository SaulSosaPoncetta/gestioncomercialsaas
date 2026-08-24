<?php

namespace App\Http\Controllers;
use App\Traits\ExportaExcel;
use App\Models\Almacen;
use App\Models\CuentaPorCobrar;
use App\Models\DetalleVenta;
use App\Models\Factura;
use App\Models\ListaPrecio;
use App\Models\MovimientoInventario;
use App\Models\Persona;
use App\Models\PrecioProducto;
use App\Models\Producto;
use App\Models\Stock;
use App\Models\Sucursal;
use App\Models\TipoComprobante;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    use ExportaExcel;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'sucursal', 'vendedor']);

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->whereHas('cliente', fn($q) => $q->where('razon_social_nombre', 'like', "%{$buscar}%"));
        }

        $ventas = $query->orderByDesc('fecha_venta')->paginate(10)->withQueryString();
        return view('ventas.index', compact('ventas'));
    }

        public function exportar(Request $request)
    {
        $query = Venta::with(['cliente', 'sucursal', 'vendedor']);

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->whereHas('cliente', fn($q) => $q->where('razon_social_nombre', 'like', "%{$buscar}%"));
        }

        $ventas = $query->orderByDesc('fecha_venta')->get();

        $encabezados = ['Fecha', 'Cliente', 'Vendedor', 'Condición', 'Subtotal', 'Descuento', 'Impuesto', 'Total', 'Estado'];

        $filas = $ventas->map(fn($v) => [
            $v->fecha_venta->format('d/m/Y H:i'),
            $v->cliente->razon_social_nombre ?? '',
            $v->vendedor->name ?? '',
            $v->tipo_venta,
            number_format($v->subtotal, 2),
            number_format($v->descuento_total, 2),
            number_format($v->impuesto_total, 2),
            number_format($v->total_venta, 2),
            $v->estado,
        ]);

        return $this->exportarExcel('ventas_' . now()->format('Y-m-d'), $encabezados, $filas);
    }

    public function create()
    {
        $clientes = Persona::clientes()->where('estado', true)->orderBy('razon_social_nombre')->get();
        $sucursales = Sucursal::where('estado', true)->orderBy('nombre')->get();
        $almacenes = Almacen::where('estado', true)->orderBy('nombre')->get();
        $listasPrecios = ListaPrecio::orderBy('nombre')->get();
        $productos = Producto::with('impuesto')->where('estado', true)->orderBy('nombre')->get();

        return view('ventas.create', compact('clientes', 'sucursales', 'almacenes', 'listasPrecios', 'productos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_cliente' => 'required|exists:personas,id_persona',
            'id_sucursal' => 'required|exists:sucursales,id_sucursal',
            'id_almacen' => 'required|exists:almacenes,id_almacen',
            'tipo_venta' => 'required|in:CONTADO,CREDITO',
            'dias_vencimiento' => 'nullable|integer|min:0',
            'emitir_factura' => 'nullable|boolean',
            'id_tipo_comprobante' => 'required_if:emitir_factura,1|nullable|exists:tipos_comprobante,id_tipo_comprobante',
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|numeric|min:0.001',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'productos.*.descuento' => 'nullable|numeric|min:0',
        ]);

        try {
            $venta = DB::transaction(function () use ($data) {
                // Validar stock disponible antes de descontar nada
                foreach ($data['productos'] as $linea) {
                    $producto = Producto::find($linea['id_producto']);
                    if ($producto->aplica_inventario) {
                        $stock = Stock::where('id_producto', $linea['id_producto'])
                            ->where('id_almacen', $data['id_almacen'])
                            ->first();

                        $disponible = $stock->cantidad ?? 0;
                        if ($disponible < $linea['cantidad']) {
                            throw ValidationException::withMessages([
                                'productos' => "Stock insuficiente de \"{$producto->nombre}\" (disponible: {$disponible}).",
                            ]);
                        }
                    }
                }

                $subtotal = 0;
                $descuentoTotal = 0;
                $impuestoTotal = 0;

                foreach ($data['productos'] as $linea) {
                    $producto = Producto::with('impuesto')->find($linea['id_producto']);
                    $descuento = $linea['descuento'] ?? 0;
                    $baseLinea = ($linea['cantidad'] * $linea['precio_unitario']) - $descuento;
                    $porcentajeImpuesto = $producto->impuesto->porcentaje ?? 0;
                    $impuestoLinea = round($baseLinea * ($porcentajeImpuesto / 100), 2);

                    $subtotal += $linea['cantidad'] * $linea['precio_unitario'];
                    $descuentoTotal += $descuento;
                    $impuestoTotal += $impuestoLinea;
                }

                $totalVenta = $subtotal - $descuentoTotal + $impuestoTotal;

                $venta = Venta::create([
                    'id_sucursal' => $data['id_sucursal'],
                    'id_cliente' => $data['id_cliente'],
                    'id_vendedor' => auth()->id(),
                    'tipo_venta' => $data['tipo_venta'],
                    'subtotal' => $subtotal,
                    'descuento_total' => $descuentoTotal,
                    'impuesto_total' => $impuestoTotal,
                    'total_venta' => $totalVenta,
                    'estado' => 'COMPLETADA',
                ]);

                foreach ($data['productos'] as $linea) {
                    $producto = Producto::with('impuesto')->find($linea['id_producto']);
                    $descuento = $linea['descuento'] ?? 0;
                    $baseLinea = ($linea['cantidad'] * $linea['precio_unitario']) - $descuento;
                    $porcentajeImpuesto = $producto->impuesto->porcentaje ?? 0;
                    $impuestoLinea = round($baseLinea * ($porcentajeImpuesto / 100), 2);
                    $subtotalLinea = $baseLinea + $impuestoLinea;

                    DetalleVenta::create([
                        'id_venta' => $venta->id_venta,
                        'id_producto' => $linea['id_producto'],
                        'cantidad' => $linea['cantidad'],
                        'precio_unitario' => $linea['precio_unitario'],
                        'descuento' => $descuento,
                        'monto_impuesto' => $impuestoLinea,
                        'subtotal' => $subtotalLinea,
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
                            'tipo_movimiento' => 'SALIDA_VENTA',
                            'cantidad' => $linea['cantidad'],
                            'costo_unitario' => $producto->precio_costo,
                            'referencia_documento' => 'Venta #' . $venta->id_venta,
                            'motivo' => 'Salida por venta #' . $venta->id_venta,
                            'id_usuario' => auth()->id(),
                        ]);
                    }
                }

                if ($data['tipo_venta'] === 'CREDITO') {
                    CuentaPorCobrar::create([
                        'id_venta' => $venta->id_venta,
                        'id_cliente' => $data['id_cliente'],
                        'fecha_vencimiento' => now()->addDays($data['dias_vencimiento'] ?? 30),
                        'monto_total' => $totalVenta,
                        'monto_cobrado' => 0,
                        'estado' => 'PENDIENTE',
                    ]);
                }

                if (!empty($data['emitir_factura'])) {
                    $tipoComprobante = TipoComprobante::lockForUpdate()->find($data['id_tipo_comprobante']);
                    $tipoComprobante->correlativo_actual += 1;
                    $tipoComprobante->save();

                    $numeroFactura = $tipoComprobante->serie_prefijo . '-' . str_pad($tipoComprobante->correlativo_actual, 8, '0', STR_PAD_LEFT);

                    Factura::create([
                        'id_venta' => $venta->id_venta,
                        'id_tipo_comprobante' => $tipoComprobante->id_tipo_comprobante,
                        'numero_factura' => $numeroFactura,
                        'fecha_emision' => now(),
                        'subtotal' => $subtotal - $descuentoTotal,
                        'monto_iva' => $impuestoTotal,
                        'total_facturado' => $totalVenta,
                        'estado_fiscal' => 'EMITIDA',
                    ]);
                }

                return $venta;
            });
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        return redirect()->route('ventas.show', $venta)->with('success', 'Venta registrada correctamente.');
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'sucursal', 'vendedor', 'detalles.producto', 'cuentaPorCobrar', 'factura.tipoComprobante']);
        return view('ventas.show', compact('venta'));
    }

    public function preciosPorLista(Request $request)
    {
        $idLista = $request->input('id_lista_precio');
        $precios = PrecioProducto::where('id_lista_precio', $idLista)->pluck('precio_venta', 'id_producto');
        return response()->json($precios);
    }
        public function imprimirFactura(Venta $venta)
    {
        $venta->load(['cliente', 'sucursal.empresa', 'detalles.producto', 'factura.tipoComprobante']);

        if (!$venta->factura) {
            return back()->with('error', 'Esta venta no tiene factura emitida.');
        }

        return view('ventas.factura-imprimir', compact('venta'));
    }

    public function imprimirRemito(Venta $venta)
    {
        $venta->load(['cliente', 'sucursal.empresa', 'detalles.producto']);
        return view('ventas.remito-imprimir', compact('venta'));
    }
}