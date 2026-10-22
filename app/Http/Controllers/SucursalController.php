<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $empresas = $this->empresasPermitidas();
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
        $empresas = $this->empresasPermitidas();
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

    /** El superadmin elige la empresa; un cliente solo puede usar la suya. */
    private function empresasPermitidas()
    {
        return auth()->user()->hasRole('superadmin')
            ? Empresa::orderBy('razon_social')->get()
            : Empresa::where('id_empresa', auth()->user()->id_empresa)->get();
    }

    private function validarDatos(Request $request, $idSucursal = null)
    {
        $esSuperadmin = auth()->user()->hasRole('superadmin');

        // Un cliente no puede crear ni mover sucursales a otra empresa
        if (! $esSuperadmin) {
            $request->merge(['id_empresa' => auth()->user()->id_empresa]);
        }

        return $request->validate([
            'id_empresa' => 'required|exists:empresas,id_empresa',
            'nombre' => 'required|string|max:100',
            'codigo' => [
                'required', 'string', 'max:10',
                Rule::unique('sucursales', 'codigo')->ignore($idSucursal, 'id_sucursal')->where('id_empresa', $request->input('id_empresa')),
            ],
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string|max:30',
        ]);
    }
}
