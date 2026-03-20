<?php
// =====================================================================
// ARCHIVO: PdfService.php
// UBICACIÓN: app/Services/Integrations/PdfService.php
// =====================================================================
// LIBRERÍA USADA: barryvdh/laravel-dompdf (gratis, open source)
// INSTALAR CON: composer require barryvdh/laravel-dompdf
// DOCUMENTACIÓN: https://github.com/barryvdh/laravel-dompdf
// =====================================================================

namespace App\Services\Integrations;

use App\Models\Pago;
use App\Models\Reserva;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    // -----------------------------------------------------------------
    // GENERA PDF DE CONFIRMACIÓN DE RESERVA
    // Usa la vista: resources/views/pdf/confirmacion-reserva.blade.php
    // Guarda en:    storage/app/public/reservas/confirmacion-RES-001.pdf
    // Retorna la ruta relativa dentro del disco 'public'
    // -----------------------------------------------------------------
    public function generarConfirmacion(Reserva $reserva): string
    {
        $pdf = Pdf::loadView('pdf.confirmacion-reserva', [
            'reserva' => $reserva,
        ]);

        // Orientación vertical, tamaño A4
        $pdf->setPaper('A4', 'portrait');

        $path = 'reservas/confirmacion-' . $reserva->codigo_reserva . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    // -----------------------------------------------------------------
    // GENERA PDF DE COMPROBANTE DE PAGO
    // Usa la vista: resources/views/pdf/comprobante-pago.blade.php
    // Guarda en:    storage/app/public/pagos/comprobante-123.pdf
    // -----------------------------------------------------------------
    public function generarComprobantePago(Pago $pago): string
    {
        $pdf = Pdf::loadView('pdf.comprobante-pago', [
            'pago' => $pago,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $path = 'pagos/comprobante-' . $pago->id . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    // -----------------------------------------------------------------
    // GENERA PDF DE REPORTE DE RESERVAS
    // Usa la vista: resources/views/pdf/reporte-reservas.blade.php
    // Se llama desde ReporteService con el rango de fechas ya filtrado
    // -----------------------------------------------------------------
    public function generarReporteReservas(array $reservas, array $filtros): string
    {
        $pdf = Pdf::loadView('pdf.reporte-reservas', [
            'reservas' => $reservas,
            'filtros'  => $filtros,
            'fecha'    => now()->format('d/m/Y H:i'),
        ]);

        // Horizontal para que entren todas las columnas
        $pdf->setPaper('A4', 'landscape');

        $path = 'reportes/reservas-' . now()->format('Y-m-d-His') . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}