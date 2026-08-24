<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $sucursales = Sucursal::with('empresa')->orderBy('nombre')->paginate(10);
        return view('sucursales.index', compact('sucursales'));
    }

    public function create()
    {
        $empresas = Empresa::orderBy('razon_social')->get();
        return view('sucursales.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $data = $this->validarDatos($request);
        $data['es_casa_matriz'] = $request->boolean('es_casa_matriz');
        $data['estado'] = $request->boolean('estado', true);
        Sucursal::create($data);
        return redirect()->route('sucursales.index')->with('success', 'Sucursal creada correctamente.');
    }

    public function edit(Sucursal $sucursale)
    {
        $empresas = Empresa::orderBy('razon_social')->get();
        return view('sucursales.edit', ['sucursal' => $sucursale, 'empresas' => $empresas]);
    }

    public function update(Request $request, Sucursal $sucursale)
    {
        $data = $this->validarDatos($request, $sucursale->id_sucursal);
        $data['es_casa_matriz'] = $request->boolean('es_casa_matriz');
        $data['estado'] = $request->boolean('estado', true);
        $sucursale->update($data);
        return redirect()->route('sucursales.index')->with('success', 'Sucursal actualizada correctamente.');
    }

    public function destroy(Sucursal $sucursale)
    {
        $this->authorize('eliminar-registros');
        $sucursale->delete();
        return redirect()->route('sucursales.index')->with('success', 'Sucursal eliminada correctamente.');
    }

    private function validarDatos(Request $request, $idSucursal = null)
    {
        return $request->validate([
            'id_empresa' => 'required|exists:empresas,id_empresa',
            'nombre' => 'required|string|max:100',
            'codigo' => 'required|string|max:10|unique:sucursales,codigo,' . $idSucursal . ',id_sucursal',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string|max:30',
        ]);
    }
}