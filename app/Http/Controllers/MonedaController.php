<?php

namespace App\Http\Controllers;

use App\Models\Moneda;
use Illuminate\Http\Request;

class MonedaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $monedas = Moneda::orderBy('codigo')->paginate(10);
        return view('monedas.index', compact('monedas'));
    }

    public function create()
    {
        return view('monedas.create');
    }

    public function store(Request $request)
    {
        $data = $this->validarDatos($request);
        $data['es_moneda_base'] = $request->boolean('es_moneda_base');

        if ($data['es_moneda_base']) {
            Moneda::where('es_moneda_base', true)->update(['es_moneda_base' => false]);
        }

        Moneda::create($data);
        return redirect()->route('monedas.index')->with('success', 'Moneda creada correctamente.');
    }

    public function edit(Moneda $moneda)
    {
        return view('monedas.edit', compact('moneda'));
    }

    public function update(Request $request, Moneda $moneda)
    {
        $data = $this->validarDatos($request, $moneda->id_moneda);
        $data['es_moneda_base'] = $request->boolean('es_moneda_base');

        if ($data['es_moneda_base']) {
            Moneda::where('id_moneda', '!=', $moneda->id_moneda)->update(['es_moneda_base' => false]);
        }

        $moneda->update($data);
        return redirect()->route('monedas.index')->with('success', 'Moneda actualizada correctamente.');
    }

    public function destroy(Moneda $moneda)
    {
        $this->authorize('eliminar-registros');
        $moneda->delete();
        return redirect()->route('monedas.index')->with('success', 'Moneda eliminada correctamente.');
    }

    private function validarDatos(Request $request, $idMoneda = null)
    {
        return $request->validate([
            'codigo' => 'required|string|max:5|unique:monedas,codigo,' . $idMoneda . ',id_moneda',
            'nombre' => 'required|string|max:50',
            'simbolo' => 'required|string|max:5',
            'tipo_cambio' => 'required|numeric|min:0',
        ]);
    }
}