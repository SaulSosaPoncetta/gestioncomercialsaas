<?php

namespace App\Http\Controllers;

use App\Traits\ExportaExcel;
use App\Models\Categoria;
use App\Models\Impuesto;
use App\Models\Marca;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    use ExportaExcel;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'marca', 'impuesto']);

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('sku', 'like', "%{$buscar}%")
                    ->orWhere('codigo_barras', 'like', "%{$buscar}%");
            });
        }

        $productos = $query->orderBy('nombre')->paginate(10)->withQueryString();
        return view('productos.index', compact('productos'));
    }

        public function exportar(Request $request)
    {
        $query = Producto::with(['categoria', 'marca', 'impuesto']);

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('sku', 'like', "%{$buscar}%")
                    ->orWhere('codigo_barras', 'like', "%{$buscar}%");
            });
        }

        $productos = $query->orderBy('nombre')->get();

        $puedeVerCostos = auth()->user()->can('ver-costos');

        $encabezados = ['SKU', 'Nombre', 'Categoría', 'Marca', 'Impuesto'];
        if ($puedeVerCostos) {
            $encabezados[] = 'Costo';
        }
        $encabezados[] = 'Estado';

        $filas = $productos->map(function ($p) use ($puedeVerCostos) {
            $fila = [
                $p->sku,
                $p->nombre,
                $p->categoria->nombre ?? '',
                $p->marca->nombre ?? '',
                $p->impuesto->nombre ?? '',
            ];
            if ($puedeVerCostos) {
                $fila[] = number_format($p->precio_costo, 2);
            }
            $fila[] = $p->estado ? 'Activo' : 'Inactivo';
            return $fila;
        });

        return $this->exportarExcel('productos_' . now()->format('Y-m-d'), $encabezados, $filas);
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();
        $impuestos = Impuesto::where('estado', true)->orderBy('nombre')->get();
        return view('productos.create', compact('categorias', 'marcas', 'impuestos'));
    }

    public function store(Request $request)
    {
        $data = $this->validarDatos($request);
        $data['aplica_inventario'] = $request->boolean('aplica_inventario', true);
        $data['estado'] = $request->boolean('estado', true);
        Producto::create($data);
        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $marcas = Marca::orderBy('nombre')->get();
        $impuestos = Impuesto::where('estado', true)->orderBy('nombre')->get();
        return view('productos.edit', compact('producto', 'categorias', 'marcas', 'impuestos'));
    }

    public function update(Request $request, Producto $producto)
    {
        $data = $this->validarDatos($request, $producto->id_producto);
        $data['aplica_inventario'] = $request->boolean('aplica_inventario', true);
        $data['estado'] = $request->boolean('estado', true);
        $producto->update($data);
        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $this->authorize('eliminar-registros');
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }

    private function validarDatos(Request $request, $idProducto = null)
    {
        return $request->validate([
            'sku' => 'required|string|max:50|unique:productos,sku,' . $idProducto . ',id_producto',
            'codigo_barras' => 'nullable|string|max:50|unique:productos,codigo_barras,' . $idProducto . ',id_producto',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'id_marca' => 'nullable|exists:marcas,id_marca',
            'id_impuesto' => 'required|exists:impuestos,id_impuesto',
            'unidad_medida' => 'required|string|max:20',
            'precio_costo' => 'required|numeric|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'stock_maximo' => 'nullable|integer|min:0',
        ]);
    }
}