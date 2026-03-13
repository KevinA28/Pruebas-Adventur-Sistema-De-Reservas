<?php
// =====================================================================
// ARCHIVO: ReservaController.php
// UBICACIÓN: app/Http/Controllers/ReservaController.php
// =====================================================================

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\FechaTour;
use App\Models\EstadoReserva;
use App\Models\HistorialEstado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservaController extends Controller
{
    // Lista de reservas con filtros
    public function index(Request $request)
    {
        $query = Reserva::with(['cliente', 'fechaTour.tour', 'estado'])->latest();

        if ($request->filled('estado')) {
            $query->where('estado_id', $request->estado);
        }
        if ($request->filled('canal')) {
            $query->where('canal_contacto', $request->canal);
        }
        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('codigo_reserva', 'like', '%' . $request->buscar . '%')
                  ->orWhereHas('cliente', fn($c) => $c->where('nombre_completo', 'like', '%' . $request->buscar . '%'));
            });
        }

        $reservas = $query->paginate(20)->withQueryString();
        $estados  = EstadoReserva::all();

        return view('reservas.index', compact('reservas', 'estados'));
    }

    // Formulario para crear nueva reserva
    public function create()
    {
        $fechas  = FechaTour::with('tour')
            ->where('estado', 'disponible')
            ->where('fecha', '>=', now())
            ->orderBy('fecha')
            ->get();
        $estados = EstadoReserva::all();

        return view('reservas.create', compact('fechas', 'estados'));
    }

    // Guardar nueva reserva
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'                  => 'required|exists:clientes,id',
            'fecha_tour_id'               => 'required|exists:fechas_tour,id',
            'cantidad_adultos'            => 'required|integer|min:1',
            'cantidad_ninos'              => 'required|integer|min:0',
            'canal_contacto'              => 'required|in:whatsapp,presencial,llamada,redes_sociales,web',
            'pasajeros'                   => 'required|array|min:1',
            'pasajeros.*.nombre_completo' => 'required|string',
            'pasajeros.*.tipo'            => 'required|in:adulto,nino',
        ]);

        DB::transaction(function () use ($request) {
            $fechaTour     = FechaTour::findOrFail($request->fecha_tour_id);
            $estadoInicial = EstadoReserva::where('nombre', 'pre-reserva')->first();
            $total         = ($request->cantidad_adultos * $fechaTour->tour->precio_adulto)
                           + ($request->cantidad_ninos  * ($fechaTour->tour->precio_nino ?? $fechaTour->tour->precio_adulto * 0.5));

            $reserva = Reserva::create([
                'codigo_reserva'   => Reserva::generarCodigo(),
                'cliente_id'       => $request->cliente_id,
                'fecha_tour_id'    => $request->fecha_tour_id,
                'estado_id'        => $estadoInicial->id,
                'usuario_admin_id' => Auth::id(),
                'cantidad_adultos' => $request->cantidad_adultos,
                'cantidad_ninos'   => $request->cantidad_ninos,
                'precio_total'     => $total,
                'canal_contacto'   => $request->canal_contacto,
                'observaciones'    => $request->observaciones,
            ]);

            foreach ($request->pasajeros as $data) {
                $reserva->pasajeros()->create($data);
            }

            HistorialEstado::create([
                'reserva_id'      => $reserva->id,
                'estado_nuevo_id' => $estadoInicial->id,
                'cambiado_por'    => Auth::id(),
                'motivo'          => 'Reserva creada — canal: ' . $request->canal_contacto,
                'fecha_cambio'    => now(),
            ]);

            $fechaTour->descontarCupos($request->cantidad_adultos + $request->cantidad_ninos);
        });

        return redirect()->route('reservas.index')->with('success', 'Reserva creada correctamente.');
    }

    // Ver detalle completo de una reserva
    public function show(Reserva $reserva)
    {
        $reserva->load([
            'cliente', 'fechaTour.tour', 'estado',
            'pasajeros.salud', 'pagos.metodoPago',
            'logistica', 'historialEstados.estadoAnterior',
            'historialEstados.estadoNuevo',
            'historialEstados.cambiadorPor',
            'comprobantes',
        ]);

        return view('reservas.show', compact('reserva'));
    }

    // Cambiar estado y guardar en historial
    public function cambiarEstado(Request $request, Reserva $reserva)
    {
        $request->validate([
            'estado_id' => 'required|exists:estados_reserva,id',
            'motivo'    => 'nullable|string|max:500',
        ]);

        $estadoAnterior = $reserva->estado_id;
        $reserva->update(['estado_id' => $request->estado_id]);

        HistorialEstado::create([
            'reserva_id'         => $reserva->id,
            'estado_anterior_id' => $estadoAnterior,
            'estado_nuevo_id'    => $request->estado_id,
            'cambiado_por'       => Auth::id(),
            'motivo'             => $request->motivo,
            'fecha_cambio'       => now(),
        ]);

        return back()->with('success', 'Estado actualizado.');
    }
}