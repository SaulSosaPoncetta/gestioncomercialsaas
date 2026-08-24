<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\DetalleTransferencia;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Stock;
use App\Models\Transferencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferenciaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Transferencia::with(['almacenOrigen', 'almacenDestino', 'usuarioEnvio']);

        if ($request->filled('id_almacen')) {
            $idAlmacen = $request->input('id_almacen');
            $query->where(function ($q) use ($idAlmacen) {
                $q->where('id_almacen_origen', $idAlmacen)->orWhere('id_almacen_destino', $idAlmacen);
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        $transferencias = $query->orderByDesc('fecha_envio')->paginate(15)->withQueryString();
        $almacenes = Almacen::where('estado', true)->orderBy('nombre')->get();

        return view('transferencias.index', compact('transferencias', 'almacenes'));
    }

    public function create()
    {
        $almacenes = Almacen::where('estado', true)->orderBy('nombre')->get();
        return view('transferencias.create', compact('almacenes'));
    }

    public function stockPorAlmacen(Request $request)
    {
        $idAlmacen = $request->input('id_almacen');

        $stock = Stock::with('producto')
            ->where('id_almacen', $idAlmacen)
            ->where('cantidad', '>', 0)
            ->get()
            ->filter(fn($s) => $s->producto && $s->producto->estado)
            ->map(fn($s) => [
                'id_producto' => $s->id_producto,
                'nombre' => $s->producto->nombre,
                'sku' => $s->producto->sku,
                'disponible' => (float) $s->cantidad,
            ])
            ->values();

        return response()->json($stock);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_almacen_origen' => 'required|exists:almacenes,id_almacen|different:id_almacen_destino',
            'id_almacen_destino' => 'required|exists:almacenes,id_almacen',
            'motivo' => 'required|string|max:255',
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|numeric|min:0.001',
        ], [
            'id_almacen_origen.different' => 'El almacén de origen y destino deben ser distintos.',
        ]);

        $transferencia = DB::transaction(function () use ($data) {
            foreach ($data['productos'] as $linea) {
                $stockOrigen = Stock::where('id_producto', $linea['id_producto'])
                    ->where('id_almacen', $data['id_almacen_origen'])
                    ->first();

                $disponible = $stockOrigen->cantidad ?? 0;

                if ($linea['cantidad'] > $disponible) {
                    $producto = Producto::find($linea['id_producto']);
                    abort(422, "Stock insuficiente de \"{$producto->nombre}\" en el almacén de origen (disponible: {$disponible}).");
                }
            }

            $numeroRemito = 'TRF-' . str_pad((Transferencia::max('id_transferencia') + 1), 8, '0', STR_PAD_LEFT);

            $transferencia = Transferencia::create([
                'id_almacen_origen' => $data['id_almacen_origen'],
                'id_almacen_destino' => $data['id_almacen_destino'],
                'numero_remito' => $numeroRemito,
                'motivo' => $data['motivo'],
                'estado' => 'ENVIADA',
                'fecha_envio' => now(),
                'id_usuario_envio' => auth()->id(),
            ]);

            foreach ($data['productos'] as $linea) {
                $stockOrigen = Stock::where('id_producto', $linea['id_producto'])
                    ->where('id_almacen', $data['id_almacen_origen'])
                    ->first();
                $stockOrigen->cantidad -= $linea['cantidad'];
                $stockOrigen->save();

                DetalleTransferencia::create([
                    'id_transferencia' => $transferencia->id_transferencia,
                    'id_producto' => $linea['id_producto'],
                    'cantidad_enviada' => $linea['cantidad'],
                ]);

                $producto = Producto::find($linea['id_producto']);

                MovimientoInventario::create([
                    'id_producto' => $linea['id_producto'],
                    'id_almacen_origen' => $data['id_almacen_origen'],
                    'id_almacen_destino' => null,
                    'tipo_movimiento' => 'TRANSFERENCIA',
                    'cantidad' => $linea['cantidad'],
                    'costo_unitario' => $producto->precio_costo ?? 0,
                    'referencia_documento' => $numeroRemito,
                    'motivo' => 'Envío por transferencia ' . $numeroRemito,
                    'id_usuario' => auth()->id(),
                ]);
            }

            return $transferencia;
        });

        return redirect()->route('transferencias.show', $transferencia)->with('success', 'Transferencia enviada correctamente. Se generó el remito ' . $transferencia->numero_remito . '.');
    }

    public function show(Transferencia $transferencia)
    {
        $transferencia->load(['almacenOrigen', 'almacenDestino', 'detalles.producto', 'usuarioEnvio', 'usuarioRecepcion']);
        return view('transferencias.show', compact('transferencia'));
    }

    public function pendientes(Request $request)
    {
        $query = Transferencia::with(['almacenOrigen', 'almacenDestino'])->where('estado', 'ENVIADA');

        if ($request->filled('id_almacen')) {
            $query->where('id_almacen_destino', $request->input('id_almacen'));
        }

        $pendientes = $query->orderBy('fecha_envio')->paginate(15)->withQueryString();
        $almacenes = Almacen::where('estado', true)->orderBy('nombre')->get();

        return view('transferencias.pendientes', compact('pendientes', 'almacenes'));
    }

    public function recibir(Transferencia $transferencia)
    {
        if ($transferencia->estado !== 'ENVIADA') {
            return redirect()->route('transferencias.show', $transferencia)->with('error', 'Esta transferencia ya fue recibida.');
        }

        $transferencia->load(['almacenOrigen', 'almacenDestino', 'detalles.producto']);
        return view('transferencias.recibir', compact('transferencia'));
    }

    public function guardarRecepcion(Request $request, Transferencia $transferencia)
    {
        if ($transferencia->estado !== 'ENVIADA') {
            return redirect()->route('transferencias.show', $transferencia)->with('error', 'Esta transferencia ya fue recibida.');
        }

        $data = $request->validate([
            'observaciones_recepcion' => 'nullable|string|max:255',
            'cantidades' => 'required|array',
            'cantidades.*' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data, $transferencia) {
            $huboDiferencia = false;

            foreach ($transferencia->detalles as $detalle) {
                $cantidadRecibida = $data['cantidades'][$detalle->id_detalle_transferencia] ?? 0;
                $detalle->cantidad_recibida = $cantidadRecibida;
                $detalle->save();

                if (bccomp((string) $cantidadRecibida, (string) $detalle->cantidad_enviada, 3) !== 0) {
                    $huboDiferencia = true;
                }

                if ($cantidadRecibida > 0) {
                    $stockDestino = Stock::firstOrNew([
                        'id_producto' => $detalle->id_producto,
                        'id_almacen' => $transferencia->id_almacen_destino,
                    ]);
                    $stockDestino->cantidad = ($stockDestino->cantidad ?? 0) + $cantidadRecibida;
                    $stockDestino->save();

                    $producto = Producto::find($detalle->id_producto);

                    MovimientoInventario::create([
                        'id_producto' => $detalle->id_producto,
                        'id_almacen_origen' => null,
                        'id_almacen_destino' => $transferencia->id_almacen_destino,
                        'tipo_movimiento' => 'TRANSFERENCIA',
                        'cantidad' => $cantidadRecibida,
                        'costo_unitario' => $producto->precio_costo ?? 0,
                        'referencia_documento' => $transferencia->numero_remito,
                        'motivo' => 'Recepción por transferencia ' . $transferencia->numero_remito,
                        'id_usuario' => auth()->id(),
                    ]);
                }
            }

            $transferencia->update([
                'estado' => $huboDiferencia ? 'RECIBIDA_CON_DIFERENCIA' : 'RECIBIDA',
                'fecha_recepcion' => now(),
                'observaciones_recepcion' => $data['observaciones_recepcion'] ?? null,
                'id_usuario_recepcion' => auth()->id(),
            ]);
        });

        return redirect()->route('transferencias.show', $transferencia)->with('success', 'Recepción registrada correctamente.');
    }

    public function imprimirRemito(Transferencia $transferencia)
    {
        $transferencia->load(['almacenOrigen.sucursal.empresa', 'almacenDestino', 'detalles.producto']);
        return view('transferencias.remito-imprimir', compact('transferencia'));
    }
}