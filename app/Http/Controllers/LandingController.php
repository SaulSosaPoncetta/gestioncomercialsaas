<?php

namespace App\Http\Controllers;

use App\Mail\ActivacionEmpresaMail;
use App\Mail\BienvenidaEmpresaMail;
use App\Models\Empresa;
use App\Models\Plan;
use App\Models\User;
use App\Services\HubEstadoClienteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        // Si ya está logueado, va directo al sistema
        if ($request->user()) {
            return redirect()->route('dashboard');
        }

        $planes = Plan::where('activo', true)->orderBy('precio')->get();

        return view('landing.index', compact('planes'));
    }

    public function registrar(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validate([
                'plan_id' => 'required|exists:planes,id',
                'razon_social' => 'required|string|max:150',
                'identificacion_fiscal' => 'required|string|max:20|unique:empresas,identificacion_fiscal',
                'nombre_admin' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
            ]);

            $plan = Plan::findOrFail($data['plan_id']);

            $empresa = Empresa::create([
                'razon_social' => $data['razon_social'],
                'identificacion_fiscal' => $data['identificacion_fiscal'],
                'email' => $data['email'],
                'plan_id' => $plan->id,
                'estado' => true,
                'estado_pago' => 'sin_verificar',
            ]);

            $token = Str::random(64);

            $user = User::create([
                'name' => $data['nombre_admin'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'id_empresa' => $empresa->id_empresa,
                'activation_token' => $token,
                'email_activado' => false,
            ]);

            $user->assignRole('admin');

            DB::commit();

            $activationUrl = route('landing.activar', ['token' => $token]);

            Mail::to($user->email)->send(new BienvenidaEmpresaMail($empresa, $plan, $activationUrl, $user->name));

            return response()->json([
                'success' => true,
                'message' => 'Te enviamos un mail de activación a '.$user->email,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?? 'Los datos ingresados no son válidos.',
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('LandingController@registrar: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado. Intentá de nuevo.',
            ], 500);
        }
    }

    public function activar(string $token, HubEstadoClienteService $hub)
    {
        try {
            DB::beginTransaction();

            $user = User::where('activation_token', $token)->first();

            if (! $user) {
                return redirect()->route('landing.index')
                    ->with('error', 'El link de activación es inválido o ya fue usado.');
            }

            $user->update([
                'activation_token' => null,
                'email_activado' => true,
                'email_verified_at' => now(),
            ]);

            $empresa = $user->empresa;

            DB::commit();

            Mail::to($user->email)->send(new ActivacionEmpresaMail($empresa));

            $hub->registrar($empresa, $empresa->plan?->nombre, $empresa->plan?->precio);

            return redirect()->route('login')
                ->with('success', 'Cuenta activada correctamente. Ya podés iniciar sesión.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('LandingController@activar: '.$e->getMessage());

            return redirect()->route('landing.index')
                ->with('error', 'Ocurrió un error al activar la cuenta.');
        }
    }
}
