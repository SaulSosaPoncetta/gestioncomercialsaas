<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Services\InicializarEmpresa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Datos de la empresa.
 * - Cliente del SaaS: solo ve y edita SU empresa.
 * - Superadmin: ve todas, crea y elimina.
 */
class EmpresaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (! $this->esSuperadmin()) {
            $propia = auth()->user()->id_empresa;
            abort_unless($propia, 403, 'Su usuario no está asociado a una empresa.');

            return redirect()->route('empresas.edit', $propia);
        }

        $empresas = Empresa::orderBy('razon_social')->paginate(10);
        return view('empresas.index', compact('empresas'));
    }

    public function create()
    {
        abort_unless($this->esSuperadmin(), 403);

        return view('empresas.create');
    }

    public function store(Request $request, InicializarEmpresa $inicializar)
    {
        abort_unless($this->esSuperadmin(), 403);

        $data = $this->validarDatos($request);
        $empresa = Empresa::create($data);
        $inicializar->ejecutar($empresa);

        return redirect()->route('empresas.index')->with('success', 'Empresa creada correctamente.');
    }

    public function edit(Empresa $empresa)
    {
        $this->autorizar($empresa);

        return view('empresas.edit', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa)
    {
        $this->autorizar($empresa);

        $data = $this->validarDatos($request, $empresa->id_empresa);
        $empresa->update($data);

        return $this->esSuperadmin()
            ? redirect()->route('empresas.index')->with('success', 'Empresa actualizada correctamente.')
            : redirect()->route('empresas.edit', $empresa)->with('success', 'Datos de la empresa actualizados.');
    }

    public function destroy(Empresa $empresa)
    {
        abort_unless($this->esSuperadmin(), 403);
        $this->authorize('eliminar-registros');

        $empresa->delete();
        return redirect()->route('empresas.index')->with('success', 'Empresa eliminada correctamente.');
    }

    private function autorizar(Empresa $empresa): void
    {
        abort_unless($this->esSuperadmin() || (int) auth()->user()->id_empresa === (int) $empresa->id_empresa, 403);
    }

    private function esSuperadmin(): bool
    {
        return auth()->user()->hasRole('superadmin');
    }

    private function validarDatos(Request $request, $idEmpresa = null)
    {
        return $request->validate([
            'razon_social' => 'required|string|max:150',
            'nombre_fantasia' => 'nullable|string|max:150',
            'identificacion_fiscal' => ['required', 'string', 'max:20', Rule::unique('empresas', 'identificacion_fiscal')->ignore($idEmpresa, 'id_empresa')],
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
        ]);
    }
}
