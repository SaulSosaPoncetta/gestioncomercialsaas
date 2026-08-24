<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Stock;
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
        $query = MovimientoInventario::with(['producto', 'almacenOrigen', 'almacenDestino', 'usuario'])
            ->where('tipo_movimiento', 'TRANSFERENCIA');

        if ($request->filled('id_almacen')) {
            $idAlmacen = $request->input('id_almacen');
            $query->where(function ($q) use ($idAlmacen) {
                $q->where('id_almacen_origen', $idAlmacen)->orWhere('id_almacen_destino', $idAlmacen);
            });
        }

        $transferencias = $query->orderByDesc('fecha')->paginate(15)->withQueryString();
        $almacenes = Almacen::where('estado', true)->orderBy('nombre')->get();

        return view('transferencias.index', compact('transferencias', 'almacenes'));
    }

    public function create()
    {
        $almacenes = Almacen::where('estado', true)->orderBy('nombre')->get();
        $productos = Producto::where('estado', true)->where('aplica_inventario', true)->orderBy('nombre')->get();

        return view('transferencias.create', compact('almacenes', 'productos'));
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

        DB::transaction(function () use ($data) {
            foreach ($data['productos'] as $linea) {
                $stockOrigen = Stock::where('id_producto', $linea['id_producto'])
                    ->where('id_almacen', $data['id_almacen_origen'])
                    ->first();

                $disponible = $stockOrigen->cantidad ?? 0;

                if ($linea['cantidad'] > $disponible) {
                    $producto = Producto::find($linea['id_producto']);
                    abort(422, "Stock insuficiente de \"{$producto->nombre}\" en el almacén de origen (disponible: {$disponible}).");
                }

                $stockOrigen->cantidad -= $linea['cantidad'];
                $stockOrigen->save();

                $stockDestino = Stock::firstOrNew([
                    'id_producto' => $linea['id_producto'],
                    'id_almacen' => $data['id_almacen_destino'],
                ]);
                $stockDestino->cantidad = ($stockDestino->cantidad ?? 0) + $linea['cantidad'];
                $stockDestino->save();

                $producto = Producto::find($linea['id_producto']);

                MovimientoInventario::create([
                    'id_producto' => $linea['id_producto'],
                    'id_almacen_origen' => $data['id_almacen_origen'],
                    'id_almacen_destino' => $data['id_almacen_destino'],
                    'tipo_movimiento' => 'TRANSFERENCIA',
                    'cantidad' => $linea['cantidad'],
                    'costo_unitario' => $producto->precio_costo ?? 0,
                    'motivo' => $data['motivo'],
                    'id_usuario' => auth()->id(),
                ]);
            }
        });

        return redirect()->route('transferencias.index')->with('success', 'Transferencia registrada correctamente.');
    }
}