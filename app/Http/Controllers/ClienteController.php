<?php
// =====================================================================
// ARCHIVO: ClienteController.php
// UBICACIÓN: app/Http/Controllers/ClienteController.php
// =====================================================================

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
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

    public function store(Request $request)
    {
        $request->validate([
            'tipo_documento'    => 'required|in:DNI,RUC,CE,PASAPORTE',
            'numero_documento'  => 'required|string|unique:clientes',
            'nombre_completo'   => 'required|string|max:200',
            'email'             => 'nullable|email',
            'telefono_whatsapp' => 'nullable|string|max:20',
        ]);

        $cliente = Cliente::create($request->all());

        // Si viene de un modal AJAX devuelve JSON
        if ($request->expectsJson()) {
            return response()->json(['cliente' => $cliente]);
        }

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['reservas.fechaTour.tour', 'reservas.estado']);
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

        $cliente->update($request->all());

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Cliente actualizado.');
    }

    // AJAX: busca cliente por DNI o RUC antes de crearlo
    // En el futuro conectar con SUNAT/RENIEC aquí
    public function buscarDocumento(Request $request)
    {
        $cliente = Cliente::where('numero_documento', $request->numero)->first();

        if ($cliente) {
            return response()->json(['encontrado' => true, 'cliente' => $cliente]);
        }

        return response()->json(['encontrado' => false]);
    }
}