<?php

namespace App\Http\Controllers;

use App\Models\ListaPrecio;
use App\Models\PrecioProducto;
use App\Models\Producto;
use Illuminate\Http\Request;

class ListaPrecioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $listasPrecios = ListaPrecio::orderBy('nombre')->paginate(10);
        return view('listas-precios.index', compact('listasPrecios'));
    }

    public function create()
    {
        return view('listas-precios.create');
    }

    public function store(Request $request)
    {
        $data = $this->validarDatos($request);
        $data['es_predeterminada'] = $request->boolean('es_predeterminada');

        if ($data['es_predeterminada']) {
            ListaPrecio::where('es_predeterminada', true)->update(['es_predeterminada' => false]);
        }

        ListaPrecio::create($data);
        return redirect()->route('listas-precios.index')->with('success', 'Lista de precios creada correctamente.');
    }

    public function edit(ListaPrecio $listasPrecio)
    {
        return view('listas-precios.edit', ['listaPrecio' => $listasPrecio]);
    }

    public function update(Request $request, ListaPrecio $listasPrecio)
    {
        $data = $this->validarDatos($request);
        $data['es_predeterminada'] = $request->boolean('es_predeterminada');

        if ($data['es_predeterminada']) {
            ListaPrecio::where('id_lista_precio', '!=', $listasPrecio->id_lista_precio)->update(['es_predeterminada' => false]);
        }

        $listasPrecio->update($data);
        return redirect()->route('listas-precios.index')->with('success', 'Lista de precios actualizada correctamente.');
    }

    public function destroy(ListaPrecio $listasPrecio)
    {
        $this->authorize('eliminar-registros');
        $listasPrecio->delete();
        return redirect()->route('listas-precios.index')->with('success', 'Lista de precios eliminada correctamente.');
    }

    public function precios(ListaPrecio $listasPrecio)
    {
        $productos = Producto::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $preciosActuales = PrecioProducto::where('id_lista_precio', $listasPrecio->id_lista_precio)
            ->pluck('precio_venta', 'id_producto');

        return view('listas-precios.precios', [
            'listaPrecio' => $listasPrecio,
            'productos' => $productos,
            'preciosActuales' => $preciosActuales,
        ]);
    }

    public function guardarPrecios(Request $request, ListaPrecio $listasPrecio)
    {
        $request->validate([
            'precios' => 'array',
            'precios.*' => 'nullable|numeric|min:0',
        ]);

        foreach ($request->input('precios', []) as $idProducto => $precioVenta) {
            if ($precioVenta === null || $precioVenta === '') {
                continue;
            }

            PrecioProducto::updateOrCreate(
                ['id_lista_precio' => $listasPrecio->id_lista_precio, 'id_producto' => $idProducto],
                ['precio_venta' => $precioVenta]
            );
        }

        return redirect()->route('listas-precios.precios', $listasPrecio)->with('success', 'Precios actualizados correctamente.');
    }

    private function validarDatos(Request $request)
    {
        return $request->validate([
            'nombre' => 'required|string|max:50',
            'porcentaje_ganancia_base' => 'nullable|numeric|min:0',
        ]);
    }
}