<?php
// =====================================================================
// ARCHIVO: ReservaController.php
// UBICACIÓN: app/Http/Controllers/ReservaController.php
// =====================================================================

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservaRequest;
use App\Models\EstadoReserva;
use App\Models\FechaTour;
use App\Models\Reserva;
use App\Services\ReservaService;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function __construct(private ReservaService $reservaService) {}

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

    public function create()
    {
        $fechas = FechaTour::with('tour')
            ->where('estado', 'disponible')
            ->where('fecha', '>=', now())
            ->orderBy('fecha')
            ->get();
        $estados = EstadoReserva::all();

        return view('reservas.create', compact('fechas', 'estados'));
    }

    public function store(StoreReservaRequest $request)
    {
        $this->reservaService->crear($request->validated());

        return redirect()->route('reservas.index')
            ->with('success', 'Reserva creada correctamente.');
    }

    public function show(Reserva $reserva)
    {
        $reserva = $this->reservaService->cargarDetalle($reserva);

        return view('reservas.show', compact('reserva'));
    }

    public function cambiarEstado(Request $request, Reserva $reserva)
    {
        $request->validate([
            'estado_id' => 'required|exists:estados_reserva,id',
            'motivo'    => 'nullable|string|max:500',
        ]);

        $this->reservaService->cambiarEstado($reserva, $request->estado_id, $request->motivo);

        return back()->with('success', 'Estado actualizado.');
    }
}