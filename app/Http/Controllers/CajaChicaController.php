<?php

namespace App\Http\Controllers;

use App\Models\AperturaCierreCaja;
use App\Models\CajaChica;
use App\Models\MovimientoCaja;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CajaChicaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cajas = CajaChica::with(['sucursal', 'sesionAbierta'])->orderBy('nombre')->paginate(10);
        return view('cajas.index', compact('cajas'));
    }

    public function create()
    {
        $sucursales = Sucursal::where('estado', true)->orderBy('nombre')->get();
        return view('cajas.create', compact('sucursales'));
    }

    public function store(Request $request)
    {
        $data = $this->validarDatos($request);
        $data['estado'] = $request->boolean('estado', true);
        CajaChica::create($data);
        return redirect()->route('cajas.index')->with('success', 'Caja creada correctamente.');
    }

    public function edit(CajaChica $caja)
    {
        $sucursales = Sucursal::where('estado', true)->orderBy('nombre')->get();
        return view('cajas.edit', compact('caja', 'sucursales'));
    }

    public function update(Request $request, CajaChica $caja)
    {
        $data = $this->validarDatos($request);
        $data['estado'] = $request->boolean('estado', true);
        $caja->update($data);
        return redirect()->route('cajas.index')->with('success', 'Caja actualizada correctamente.');
    }

    public function destroy(CajaChica $caja)
    {
        $this->authorize('eliminar-registros');
        if ($caja->sesionAbierta()->exists()) {
            return back()->with('error', 'No se puede eliminar: la caja tiene una sesión abierta.');
        }
        $caja->delete();
        return redirect()->route('cajas.index')->with('success', 'Caja eliminada correctamente.');
    }

    public function abrir(CajaChica $caja)
    {
        if ($caja->sesionAbierta()->exists()) {
            return redirect()->route('cajas.index')->with('error', 'Esta caja ya tiene una sesión abierta.');
        }
        return view('cajas.abrir', compact('caja'));
    }

    public function guardarApertura(Request $request, CajaChica $caja)
    {
        $data = $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
        ]);

        AperturaCierreCaja::create([
            'id_caja' => $caja->id_caja,
            'id_usuario_apertura' => auth()->id(),
            'fecha_apertura' => now(),
            'monto_inicial' => $data['monto_inicial'],
            'estado' => 'ABIERTA',
        ]);

        return redirect()->route('cajas.index')->with('success', 'Caja abierta correctamente.');
    }

    public function sesion(AperturaCierreCaja $sesion)
    {
        $sesion->load(['caja', 'movimientos', 'usuarioApertura']);

        $ingresos = $sesion->movimientos->where('tipo', 'INGRESO')->sum('monto');
        $egresos = $sesion->movimientos->where('tipo', 'EGRESO')->sum('monto');
        $saldoSistema = $sesion->monto_inicial + $ingresos - $egresos;

        return view('cajas.sesion', compact('sesion', 'ingresos', 'egresos', 'saldoSistema'));
    }

    public function guardarMovimiento(Request $request, AperturaCierreCaja $sesion)
    {
        $data = $request->validate([
            'tipo' => 'required|in:INGRESO,EGRESO',
            'concepto' => 'required|string|max:150',
            'monto' => 'required|numeric|min:0.01',
            'id_forma_pago' => 'required|integer',
        ]);

        MovimientoCaja::create(array_merge($data, ['id_sesion_caja' => $sesion->id_sesion_caja]));

        return redirect()->route('cajas.sesion', $sesion)->with('success', 'Movimiento registrado correctamente.');
    }

    public function formularioCierre(AperturaCierreCaja $sesion)
    {
        $ingresos = $sesion->movimientos->where('tipo', 'INGRESO')->sum('monto');
        $egresos = $sesion->movimientos->where('tipo', 'EGRESO')->sum('monto');
        $saldoSistema = $sesion->monto_inicial + $ingresos - $egresos;

        return view('cajas.cerrar', compact('sesion', 'saldoSistema'));
    }

    public function guardarCierre(Request $request, AperturaCierreCaja $sesion)
    {
        $data = $request->validate([
            'monto_final_real' => 'required|numeric|min:0',
        ]);

        $ingresos = $sesion->movimientos->where('tipo', 'INGRESO')->sum('monto');
        $egresos = $sesion->movimientos->where('tipo', 'EGRESO')->sum('monto');
        $saldoSistema = $sesion->monto_inicial + $ingresos - $egresos;

        $sesion->update([
            'id_usuario_cierre' => auth()->id(),
            'fecha_cierre' => now(),
            'monto_final_sistema' => $saldoSistema,
            'monto_final_real' => $data['monto_final_real'],
            'diferencia' => $data['monto_final_real'] - $saldoSistema,
            'estado' => 'CERRADA',
        ]);

        return redirect()->route('cajas.index')->with('success', 'Caja cerrada correctamente.');
    }

    private function validarDatos(Request $request)
    {
        return $request->validate([
            'id_sucursal' => 'required|exists:sucursales,id_sucursal',
            'nombre' => 'required|string|max:50',
        ]);
    }
}