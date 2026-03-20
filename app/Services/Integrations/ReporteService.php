<?php
// =====================================================================
// ARCHIVO: ReporteService.php
// UBICACIÓN: app/Services/ReporteService.php
// =====================================================================
// Orquesta la generación de reportes de reservas.
// Filtra por rango de fechas (hoy, semana, mes, rango personalizado)
// y delega la generación a PdfService o ExcelService.
// =====================================================================

namespace App\Services;

use App\Models\Reserva;
use App\Services\Integrations\ExcelService;
use App\Services\Integrations\PdfService;
use Illuminate\Support\Collection;

class ReporteService
{
    public function __construct(
        private PdfService   $pdfService,
        private ExcelService $excelService,
    ) {}

    // -----------------------------------------------------------------
    // OBTIENE RESERVAS FILTRADAS POR RANGO
    // Rango: 'hoy' | 'semana' | 'mes' | 'personalizado'
    // Para 'personalizado' pasar fecha_inicio y fecha_fin en $filtros
    // -----------------------------------------------------------------
    public function obtenerReservas(array $filtros): Collection
    {
        $query = Reserva::with([
            'cliente',
            'fechaTour.tour',
            'estado',
            'pagos',
        ]);

        // Filtro por rango de fechas
        switch ($filtros['rango'] ?? 'mes') {
            case 'hoy':
                $query->whereDate('created_at', today());
                break;
            case 'semana':
                $query->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek(),
                ]);
                break;
            case 'mes':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
            case 'personalizado':
                if (! empty($filtros['fecha_inicio']) && ! empty($filtros['fecha_fin'])) {
                    $query->whereBetween('created_at', [
                        $filtros['fecha_inicio'] . ' 00:00:00',
                        $filtros['fecha_fin']    . ' 23:59:59',
                    ]);
                }
                break;
        }

        // Filtros opcionales adicionales
        if (! empty($filtros['estado_id'])) {
            $query->where('estado_id', $filtros['estado_id']);
        }
        if (! empty($filtros['tour_id'])) {
            $query->whereHas('fechaTour', fn($q) => $q->where('tour_id', $filtros['tour_id']));
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    // -----------------------------------------------------------------
    // GENERA REPORTE EN PDF
    // Retorna ruta del archivo generado en storage/public
    // -----------------------------------------------------------------
    public function generarPdf(array $filtros): string
    {
        $reservas = $this->obtenerReservas($filtros);
        return $this->pdfService->generarReporteReservas($reservas->toArray(), $filtros);
    }

    // -----------------------------------------------------------------
    // GENERA REPORTE EN EXCEL
    // Retorna respuesta de descarga directa
    // -----------------------------------------------------------------
    public function generarExcel(array $filtros)
    {
        return $this->excelService->exportarReservas($filtros);
    }

    // -----------------------------------------------------------------
    // TOTALES PARA EL RESUMEN DEL REPORTE
    // -----------------------------------------------------------------
    public function calcularTotales(Collection $reservas): array
    {
        return [
            'total_reservas'  => $reservas->count(),
            'total_ingresos'  => $reservas->sum('precio_total'),
            'total_cobrado'   => $reservas->sum('monto_pagado'),
            'total_pendiente' => $reservas->sum('precio_total') - $reservas->sum('monto_pagado'),
            'total_pasajeros' => $reservas->sum(fn($r) => $r->cantidad_adultos + $r->cantidad_ninos),
        ];
    }
}