<?php
// =====================================================================
// ARCHIVO: ReservaService.php
// UBICACIÓN: app/Services/ReservaService.php
// =====================================================================

namespace App\Services;

use App\Models\EstadoReserva;
use App\Models\FechaTour;
use App\Models\HistorialEstado;
use App\Models\Reserva;
use App\Services\Integrations\MailService;
use App\Services\Integrations\PdfService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservaService
{
    public function __construct(
        private PdfService  $pdfService,
        private MailService $mailService,
    ) {}

    /**
     * Crea una reserva completa con sus pasajeros y historial inicial.
     * Usa transacción para garantizar consistencia.
     */
    public function crear(array $datos): Reserva
    {
        return DB::transaction(function () use ($datos) {
            $fechaTour     = FechaTour::findOrFail($datos['fecha_tour_id']);
            $estadoInicial = EstadoReserva::where('nombre', 'pre-reserva')->firstOrFail();

            $total = ($datos['cantidad_adultos'] * $fechaTour->tour->precio_adulto)
                   + ($datos['cantidad_ninos']   * ($fechaTour->tour->precio_nino
                        ?? $fechaTour->tour->precio_adulto * 0.5));

            $reserva = Reserva::create([
                'codigo_reserva'   => Reserva::generarCodigo(),
                'cliente_id'       => $datos['cliente_id'],
                'fecha_tour_id'    => $datos['fecha_tour_id'],
                'estado_id'        => $estadoInicial->id,
                'usuario_admin_id' => Auth::id(),
                'cantidad_adultos' => $datos['cantidad_adultos'],
                'cantidad_ninos'   => $datos['cantidad_ninos'],
                'precio_total'     => $total,
                'canal_contacto'   => $datos['canal_contacto'],
                'observaciones'    => $datos['observaciones'] ?? null,
            ]);

            foreach ($datos['pasajeros'] as $pasajero) {
                $reserva->pasajeros()->create($pasajero);
            }

            HistorialEstado::create([
                'reserva_id'      => $reserva->id,
                'estado_nuevo_id' => $estadoInicial->id,
                'cambiado_por'    => Auth::id(),
                'motivo'          => 'Reserva creada — canal: ' . $datos['canal_contacto'],
                'fecha_cambio'    => now(),
            ]);

            $fechaTour->descontarCupos($datos['cantidad_adultos'] + $datos['cantidad_ninos']);

            return $reserva;
        });
    }

    /**
     * Cambia el estado de una reserva y registra el cambio en historial.
     * Si pasa a "confirmada", genera PDF y envía correo al cliente.
     */
    public function cambiarEstado(Reserva $reserva, int $nuevoEstadoId, ?string $motivo = null): void
    {
        $estadoAnterior = $reserva->estado_id;

        $reserva->update(['estado_id' => $nuevoEstadoId]);

        HistorialEstado::create([
            'reserva_id'         => $reserva->id,
            'estado_anterior_id' => $estadoAnterior,
            'estado_nuevo_id'    => $nuevoEstadoId,
            'cambiado_por'       => Auth::id(),
            'motivo'             => $motivo,
            'fecha_cambio'       => now(),
        ]);

        // Si el nuevo estado es "confirmada", enviar confirmación al cliente
        $estadoNuevo = EstadoReserva::find($nuevoEstadoId);
        if ($estadoNuevo && $estadoNuevo->nombre === 'confirmada') {
            $reserva->load(['cliente', 'fechaTour.tour', 'pasajeros']);
            $pdfPath = $this->pdfService->generarConfirmacion($reserva);
            $this->mailService->enviarConfirmacion($reserva, $pdfPath);
        }
    }

    /**
     * Carga todas las relaciones necesarias para la vista de detalle.
     */
    public function cargarDetalle(Reserva $reserva): Reserva
    {
        $reserva->load([
            'cliente',
            'fechaTour.tour',
            'estado',
            'pasajeros.salud',
            'pagos.metodoPago',
            'logistica',
            'historialEstados.estadoAnterior',
            'historialEstados.estadoNuevo',
            'historialEstados.cambiadorPor',
            'comprobantes',
        ]);

        return $reserva;
    }
}