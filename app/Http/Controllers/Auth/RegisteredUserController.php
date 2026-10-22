<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\User;
use App\Services\HubClientes;
use App\Services\InicializarEmpresa;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'razon_social' => ['required', 'string', 'max:150'],
            'identificacion_fiscal' => ['required', 'string', 'max:20', 'unique:empresas,identificacion_fiscal'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($request) {
            $empresa = Empresa::withoutGlobalScopes()->create([
                'razon_social' => $request->razon_social,
                'identificacion_fiscal' => $request->identificacion_fiscal,
                'estado' => true,
            ]);

            // Datos iniciales propios de la empresa: sucursal, depósito, IVA, moneda y tipos de comprobante
            app(InicializarEmpresa::class)->ejecutar($empresa);

            $user = User::create([
                'id_empresa' => $empresa->id_empresa,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('admin');

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        // Alta en MiGestión Panel (si ya usa la Contabilidad, el hub vincula los dos sistemas solo)
        dispatch(fn () => app(HubClientes::class)->registrar($user->empresa, $user->email))->afterResponse();

        return redirect(route('dashboard'));
    }
}