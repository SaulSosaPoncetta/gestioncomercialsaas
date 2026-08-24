<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $almacenes = Almacen::with('sucursal')->orderBy('nombre')->paginate(10);
        return view('almacenes.index', compact('almacenes'));
    }

    public function create()
    {
        $sucursales = Sucursal::where('estado', true)->orderBy('nombre')->get();
        return view('almacenes.create', compact('sucursales'));
    }

    public function store(Request $request)
    {
        $data = $this->validarDatos($request);
        $data['estado'] = $request->boolean('estado', true);
        Almacen::create($data);
        return redirect()->route('almacenes.index')->with('success', 'Almacén creado correctamente.');
    }

    public function edit(Almacen $almacen)
    {
        $sucursales = Sucursal::where('estado', true)->orderBy('nombre')->get();
        return view('almacenes.edit', compact('almacen', 'sucursales'));
    }

    public function update(Request $request, Almacen $almacen)
    {
        $data = $this->validarDatos($request);
        $data['estado'] = $request->boolean('estado', true);
        $almacen->update($data);
        return redirect()->route('almacenes.index')->with('success', 'Almacén actualizado correctamente.');
    }

    public function destroy(Almacen $almacen)
    {
        $this->authorize('eliminar-registros');
        $almacen->delete();
        return redirect()->route('almacenes.index')->with('success', 'Almacén eliminado correctamente.');
    }

    private function validarDatos(Request $request)
    {
        return $request->validate([
            'id_sucursal' => 'required|exists:sucursales,id_sucursal',
            'nombre' => 'required|string|max:100',
            'ubicacion' => 'nullable|string',
        ]);
    }
}