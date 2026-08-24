<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $empresas = Empresa::orderBy('razon_social')->paginate(10);
        return view('empresas.index', compact('empresas'));
    }

    public function create()
    {
        return view('empresas.create');
    }

    public function store(Request $request)
    {
        $data = $this->validarDatos($request);
        Empresa::create($data);
        return redirect()->route('empresas.index')->with('success', 'Empresa creada correctamente.');
    }

    public function edit(Empresa $empresa)
    {
        return view('empresas.edit', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa)
    {
        $data = $this->validarDatos($request, $empresa->id_empresa);
        $empresa->update($data);
        return redirect()->route('empresas.index')->with('success', 'Empresa actualizada correctamente.');
    }

    public function destroy(Empresa $empresa)
    {
        $this->authorize('eliminar-registros');
        $empresa->delete();
        return redirect()->route('empresas.index')->with('success', 'Empresa eliminada correctamente.');
    }

    private function validarDatos(Request $request, $idEmpresa = null)
    {
        return $request->validate([
            'razon_social' => 'required|string|max:150',
            'nombre_fantasia' => 'nullable|string|max:150',
            'identificacion_fiscal' => 'required|string|max:20|unique:empresas,identificacion_fiscal,' . $idEmpresa . ',id_empresa',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
        ]);
    }
}