<?php
// =====================================================================
// ARCHIVO: ExcelService.php
// UBICACIÓN: app/Services/Integrations/ExcelService.php
// =====================================================================
// LIBRERÍA USADA: maatwebsite/excel (gratis, open source)
// INSTALAR CON: composer require maatwebsite/excel
// DOCUMENTACIÓN: https://laravel-excel.com
// =====================================================================

namespace App\Services\Integrations;

use App\Exports\ReservasExport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelService
{
    // -----------------------------------------------------------------
    // EXPORTA RESERVAS A EXCEL
    // Usa la clase: app/Exports/ReservasExport.php (crearla aparte)
    // Retorna respuesta de descarga directa al navegador
    // -----------------------------------------------------------------
    public function exportarReservas(array $filtros)
    {
        $nombreArchivo = 'reservas-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new ReservasExport($filtros),
            $nombreArchivo
        );
    }
}