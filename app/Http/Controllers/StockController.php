<?php

namespace App\Http\Controllers;
use App\Traits\ExportaExcel;
use App\Models\Almacen;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    use ExportaExcel;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Stock::with(['producto', 'almacen']);

        if ($request->filled('id_almacen')) {
            $query->where('id_almacen', $request->input('id_almacen'));
        }

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->whereHas('producto', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")->orWhere('sku', 'like', "%{$buscar}%");
            });
        }

        $stock = $query->orderBy('id_producto')->paginate(15)->withQueryString();
        $almacenes = Almacen::where('estado', true)->orderBy('nombre')->get();

        return view('stock.index', compact('stock', 'almacenes'));
    }

        public function exportar(Request $request)
    {
        $query = Stock::with(['producto', 'almacen']);

        if ($request->filled('id_almacen')) {
            $query->where('id_almacen', $request->input('id_almacen'));
        }

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->whereHas('producto', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")->orWhere('sku', 'like', "%{$buscar}%");
            });
        }

        $stock = $query->orderBy('id_producto')->get();

        $encabezados = ['SKU', 'Producto', 'Almacén', 'Cantidad', 'Stock Mínimo', 'Alerta'];

        $filas = $stock->map(function ($item) {
            $bajoMinimo = $item->producto && $item->cantidad <= $item->producto->stock_minimo;
            return [
                $item->producto->sku ?? '',
                $item->producto->nombre ?? '',
                $item->almacen->nombre ?? '',
                rtrim(rtrim(number_format($item->cantidad, 3, '.', ''), '0'), '.'),
                $item->producto->stock_minimo ?? 0,
                $bajoMinimo ? 'BAJO MÍNIMO' : 'OK',
            ];
        });

        return $this->exportarExcel('stock_' . now()->format('Y-m-d'), $encabezados, $filas);
    }
    public function ajustar()
    {
        $productos = Producto::where('estado', true)->where('aplica_inventario', true)->orderBy('nombre')->get();
        $almacenes = Almacen::where('estado', true)->orderBy('nombre')->get();
        return view('stock.ajustar', compact('productos', 'almacenes'));
    }

    public function guardarAjuste(Request $request)
    {
        $data = $request->validate([
            'id_producto' => 'required|exists:productos,id_producto',
            'id_almacen' => 'required|exists:almacenes,id_almacen',
            'cantidad_nueva' => 'required|numeric|min:0',
            'motivo' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($data) {
            $stock = Stock::firstOrNew([
                'id_producto' => $data['id_producto'],
                'id_almacen' => $data['id_almacen'],
            ]);

            $cantidadActual = $stock->cantidad ?? 0;
            $diferencia = $data['cantidad_nueva'] - $cantidadActual;

            if ($diferencia == 0) {
                return;
            }

            $producto = Producto::find($data['id_producto']);

            MovimientoInventario::create([
                'id_producto' => $data['id_producto'],
                'id_almacen_origen' => $diferencia < 0 ? $data['id_almacen'] : null,
                'id_almacen_destino' => $diferencia > 0 ? $data['id_almacen'] : null,
                'tipo_movimiento' => $diferencia > 0 ? 'AJUSTE_POSITIVO' : 'AJUSTE_NEGATIVO',
                'cantidad' => abs($diferencia),
                'costo_unitario' => $producto->precio_costo ?? 0,
                'motivo' => $data['motivo'],
                'id_usuario' => auth()->id(),
            ]);

            $stock->cantidad = $data['cantidad_nueva'];
            $stock->save();
        });

        return redirect()->route('stock.index')->with('success', 'Stock ajustado correctamente.');
    }

    public function historial(Request $request)
    {
        $query = MovimientoInventario::with(['producto', 'almacenOrigen', 'almacenDestino', 'usuario']);

        if ($request->filled('id_producto')) {
            $query->where('id_producto', $request->input('id_producto'));
        }

        $movimientos = $query->orderByDesc('fecha')->paginate(15)->withQueryString();
        $productos = Producto::orderBy('nombre')->get();

        return view('stock.historial', compact('movimientos', 'productos'));
    }
}