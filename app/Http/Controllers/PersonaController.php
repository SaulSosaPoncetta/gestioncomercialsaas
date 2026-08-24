<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Persona::query();

        if ($request->filled('tipo')) {
            $query->where('tipo_persona', $request->input('tipo'));
        }

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('razon_social_nombre', 'like', "%{$buscar}%")
                    ->orWhere('numero_documento', 'like', "%{$buscar}%");
            });
        }

        $personas = $query->orderBy('razon_social_nombre')->paginate(10)->withQueryString();
        return view('personas.index', compact('personas'));
    }

    public function create()
    {
        return view('personas.create');
    }

    public function store(Request $request)
    {
        $data = $this->validarDatos($request);
        $data['estado'] = $request->boolean('estado', true);
        Persona::create($data);
        return redirect()->route('personas.index')->with('success', 'Persona creada correctamente.');
    }

    public function edit(Persona $persona)
    {
        return view('personas.edit', compact('persona'));
    }

    public function update(Request $request, Persona $persona)
    {
        $data = $this->validarDatos($request, $persona->id_persona);
        $data['estado'] = $request->boolean('estado', true);
        $persona->update($data);
        return redirect()->route('personas.index')->with('success', 'Persona actualizada correctamente.');
    }

    public function destroy(Persona $persona)
    {
        $this->authorize('eliminar-registros');
        $persona->delete();
        return redirect()->route('personas.index')->with('success', 'Persona eliminada correctamente.');
    }

    private function validarDatos(Request $request, $idPersona = null)
    {
        return $request->validate([
            'tipo_persona' => 'required|in:CLIENTE,PROVEEDOR,EMPLEADO,AMBOS',
            'razon_social_nombre' => 'required|string|max:150',
            'tipo_documento' => 'required|in:DNI,CUIT,CUIL,RUC,PASAPORTE,OTRO',
            'numero_documento' => 'required|string|max:25|unique:personas,numero_documento,' . $idPersona . ',id_persona',
            'condicion_iva' => 'required|string|max:50',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'limite_credito' => 'required|numeric|min:0',
            'dias_credito' => 'required|integer|min:0',
        ]);
    }
}