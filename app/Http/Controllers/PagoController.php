<?php
// =====================================================================
// ARCHIVO: PagoController.php
// UBICACIÓN: app/Http/Controllers/PagoController.php
// =====================================================================

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    // Registrar baucher de pago (el sistema NO procesa pagos, solo los valida)
    public function store(Request $request)
    {
        $request->validate([
            'reserva_id'      => 'required|exists:reservas,id',
            'metodo_pago_id'  => 'required|exists:metodos_pago,id',
            'monto'           => 'required|numeric|min:1',
            'tipo_pago'       => 'required|in:adelanto,saldo,pago_completo',
            'fecha_pago'      => 'required|date',
            'archivo_baucher' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $data                   = $request->except('archivo_baucher');
        $data['registrado_por'] = auth()->id();

        if ($request->hasFile('archivo_baucher')) {
            $data['archivo_baucher'] = $request->file('archivo_baucher')
                ->store('bauchers', 'public');
        }

        $pago = Pago::create($data);

        // Actualizar monto pagado acumulado en la reserva
        $pago->reserva->increment('monto_pagado', $pago->monto);

        return back()->with('success', 'Pago registrado. Pendiente de verificación.');
    }

    // El admin confirma que el baucher es válido
    public function verificar(Pago $pago)
    {
        $pago->update(['estado_validacion' => 'verificado']);
        return back()->with('success', 'Pago verificado correctamente.');
    }

    // El admin rechaza el baucher y se revierte el monto
    public function rechazar(Pago $pago)
    {
        $pago->update(['estado_validacion' => 'rechazado']);
        $pago->reserva->decrement('monto_pagado', $pago->monto);
        return back()->with('success', 'Pago rechazado. Monto revertido.');
    }
}