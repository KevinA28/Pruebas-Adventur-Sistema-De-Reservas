<?php
// =====================================================================
// ARCHIVO: PagoController.php
// UBICACIÓN: app/Http/Controllers/PagoController.php
// =====================================================================

namespace App\Http\Controllers;

use App\Http\Requests\StorePagoRequest;
use App\Models\Pago;
use App\Services\PagoService;

class PagoController extends Controller
{
    public function __construct(private PagoService $pagoService) {}

    public function store(StorePagoRequest $request)
    {
        $this->pagoService->registrar(
            $request->validated(),
            $request->file('archivo_baucher')
        );

        return back()->with('success', 'Pago registrado. Pendiente de verificación.');
    }

    public function verificar(Pago $pago)
    {
        $this->pagoService->verificar($pago);

        return back()->with('success', 'Pago verificado correctamente.');
    }

    public function rechazar(Pago $pago)
    {
        $this->pagoService->rechazar($pago);

        return back()->with('success', 'Pago rechazado. Monto revertido.');
    }

    public function descargarComprobante(Pago $pago)
    {
        $path = $this->pagoService->generarComprobante($pago);

        return response()->download(storage_path('app/public/' . $path));
    }
}