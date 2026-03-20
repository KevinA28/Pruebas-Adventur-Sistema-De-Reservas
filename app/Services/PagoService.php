<?php
// =====================================================================
// ARCHIVO: PagoService.php
// UBICACIÓN: app/Services/PagoService.php
// =====================================================================

namespace App\Services;

use App\Models\Pago;
use App\Services\Integrations\PdfService;
use Illuminate\Http\UploadedFile;

class PagoService
{
    public function __construct(private PdfService $pdfService) {}

    /**
     * Registra un nuevo pago con su baucher (si viene archivo).
     * Incrementa el monto pagado acumulado en la reserva.
     */
    public function registrar(array $datos, ?UploadedFile $baucher = null): Pago
    {
        $datos['registrado_por'] = auth()->id();

        if ($baucher) {
            $datos['archivo_baucher'] = $baucher->store('bauchers', 'public');
        }

        $pago = Pago::create($datos);

        // Acumular monto en la reserva
        $pago->reserva->increment('monto_pagado', $pago->monto);

        return $pago;
    }

    /**
     * El admin verifica que el baucher es válido.
     */
    public function verificar(Pago $pago): void
    {
        $pago->update(['estado_validacion' => 'verificado']);
    }

    /**
     * El admin rechaza el baucher y revierte el monto acumulado.
     */
    public function rechazar(Pago $pago): void
    {
        $pago->update(['estado_validacion' => 'rechazado']);
        $pago->reserva->decrement('monto_pagado', $pago->monto);
    }

    /**
     * Genera el PDF del comprobante de pago verificado.
     * Retorna la ruta del archivo generado.
     */
    public function generarComprobante(Pago $pago): string
    {
        $pago->load(['reserva.cliente', 'reserva.fechaTour.tour', 'metodoPago']);
        return $this->pdfService->generarComprobantePago($pago);
    }
}