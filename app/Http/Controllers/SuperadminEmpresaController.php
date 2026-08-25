<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;

class SuperadminEmpresaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:superadmin']);
    }

    public function index(Request $request)
    {
        $query = Empresa::withoutGlobalScopes()->withCount('sucursales');

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('razon_social', 'like', "%{$buscar}%")
                    ->orWhere('identificacion_fiscal', 'like', "%{$buscar}%");
            });
        }

        $empresas = $query->orderByDesc('creado_en')->paginate(15)->withQueryString();

        // Cantidad de usuarios por empresa, para mostrar en el listado
        $usuariosPorEmpresa = User::selectRaw('id_empresa, COUNT(*) as total')
            ->whereNotNull('id_empresa')
            ->groupBy('id_empresa')
            ->pluck('total', 'id_empresa');

        return view('superadmin.empresas.index', compact('empresas', 'usuariosPorEmpresa'));
    }

    public function show(Empresa $empresa)
    {
        $empresa = Empresa::withoutGlobalScopes()->findOrFail($empresa->id_empresa);
        $usuarios = User::withoutGlobalScopes()->where('id_empresa', $empresa->id_empresa)->get();

        return view('superadmin.empresas.show', compact('empresa', 'usuarios'));
    }

    public function suspender(Empresa $empresa)
    {
        $empresa = Empresa::withoutGlobalScopes()->findOrFail($empresa->id_empresa);
        $empresa->update(['estado' => false]);

        return back()->with('success', 'Empresa suspendida. Sus usuarios no podrán iniciar sesión hasta que se reactive.');
    }

    public function activar(Empresa $empresa)
    {
        $empresa = Empresa::withoutGlobalScopes()->findOrFail($empresa->id_empresa);
        $empresa->update(['estado' => true]);

        return back()->with('success', 'Empresa reactivada correctamente.');
    }
}