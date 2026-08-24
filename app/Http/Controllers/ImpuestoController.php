<?php

namespace App\Http\Controllers;

use App\Models\Impuesto;
use Illuminate\Http\Request;

class ImpuestoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $impuestos = Impuesto::orderBy('nombre')->paginate(10);
        return view('impuestos.index', compact('impuestos'));
    }

    public function create()
    {
        return view('impuestos.create');
    }

    public function store(Request $request)
    {
        $data = $this->validarDatos($request);
        $data['es_predeterminado'] = $request->boolean('es_predeterminado');
        $data['estado'] = $request->boolean('estado', true);

        if ($data['es_predeterminado']) {
            Impuesto::where('es_predeterminado', true)->update(['es_predeterminado' => false]);
        }

        Impuesto::create($data);
        return redirect()->route('impuestos.index')->with('success', 'Impuesto creado correctamente.');
    }

    public function edit(Impuesto $impuesto)
    {
        return view('impuestos.edit', compact('impuesto'));
    }

    public function update(Request $request, Impuesto $impuesto)
    {
        $data = $this->validarDatos($request);
        $data['es_predeterminado'] = $request->boolean('es_predeterminado');
        $data['estado'] = $request->boolean('estado', true);

        if ($data['es_predeterminado']) {
            Impuesto::where('id_impuesto', '!=', $impuesto->id_impuesto)->update(['es_predeterminado' => false]);
        }

        $impuesto->update($data);
        return redirect()->route('impuestos.index')->with('success', 'Impuesto actualizado correctamente.');
    }

    public function destroy(Impuesto $impuesto)
    {
        $this->authorize('eliminar-registros');
        $impuesto->delete();
        return redirect()->route('impuestos.index')->with('success', 'Impuesto eliminado correctamente.');
    }

    private function validarDatos(Request $request)
    {
        return $request->validate([
            'nombre' => 'required|string|max:50',
            'porcentaje' => 'required|numeric|min:0|max:100',
        ]);
    }
}