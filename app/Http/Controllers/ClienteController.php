<?php
// =====================================================================
// ARCHIVO: ClienteController.php
// UBICACIÓN: app/Http/Controllers/ClienteController.php
// =====================================================================

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Models\Cliente;
use App\Services\ClienteService;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function __construct(private ClienteService $clienteService) {}

    public function index(Request $request)
    {
        $clientes = Cliente::when($request->buscar, function ($q, $buscar) {
                $q->where('nombre_completo', 'like', "%$buscar%")
                  ->orWhere('numero_documento', 'like', "%$buscar%");
            })
            ->withCount('reservas')
            ->latest()
            ->paginate(20);

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(StoreClienteRequest $request)
    {
        $cliente = $this->clienteService->crear($request->validated());

        if ($request->expectsJson()) {
            return response()->json(['cliente' => $cliente]);
        }

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        $cliente = $this->clienteService->cargarDetalle($cliente);

        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre_completo'   => 'required|string|max:200',
            'email'             => 'nullable|email',
            'telefono_whatsapp' => 'nullable|string|max:20',
        ]);

        $this->clienteService->actualizar($cliente, $request->validated());

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Cliente actualizado.');
    }

    /**
     * AJAX: busca en BD local primero, si no consulta RENIEC.
     * Usado en el formulario de nueva reserva para autocompletar.
     */
    public function buscarDocumento(Request $request)
    {
        $resultado = $this->clienteService->buscarOConsultarDocumento(
            $request->numero,
            $request->tipo ?? 'DNI'
        );

        return response()->json($resultado);
    }
}