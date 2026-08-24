<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $marcas = Marca::orderBy('nombre')->paginate(10);
        return view('marcas.index', compact('marcas'));
    }

    public function create()
    {
        return view('marcas.create');
    }

    public function store(Request $request)
    {
        $data = $this->validarDatos($request);
        $data['estado'] = $request->boolean('estado', true);
        Marca::create($data);
        return redirect()->route('marcas.index')->with('success', 'Marca creada correctamente.');
    }

    public function edit(Marca $marca)
    {
        return view('marcas.edit', compact('marca'));
    }

    public function update(Request $request, Marca $marca)
    {
        $data = $this->validarDatos($request, $marca->id_marca);
        $data['estado'] = $request->boolean('estado', true);
        $marca->update($data);
        return redirect()->route('marcas.index')->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy(Marca $marca)
    {
        $this->authorize('eliminar-registros');
        $marca->delete();
        return redirect()->route('marcas.index')->with('success', 'Marca eliminada correctamente.');
    }

    private function validarDatos(Request $request, $idMarca = null)
    {
        return $request->validate([
            'nombre' => 'required|string|max:100|unique:marcas,nombre,' . $idMarca . ',id_marca',
        ]);
    }
}