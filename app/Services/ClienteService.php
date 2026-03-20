<?php
// =====================================================================
// ARCHIVO: ClienteService.php
// UBICACIÓN: app/Services/ClienteService.php
// =====================================================================

namespace App\Services;

use App\Models\Cliente;
use App\Services\Integrations\ReniecService;

class ClienteService
{
    public function __construct(private ReniecService $reniecService) {}

    /**
     * Crea un nuevo cliente.
     */
    public function crear(array $datos): Cliente
    {
        return Cliente::create($datos);
    }

    /**
     * Actualiza los datos de un cliente existente.
     */
    public function actualizar(Cliente $cliente, array $datos): Cliente
    {
        $cliente->update($datos);
        return $cliente->fresh();
    }

    /**
     * Busca un cliente por número de documento en la BD local.
     * Retorna el cliente si existe, null si no.
     */
    public function buscarPorDocumento(string $numero): ?Cliente
    {
        return Cliente::where('numero_documento', $numero)->first();
    }

    /**
     * Primero busca en BD local.
     * Si no existe, consulta RENIEC/SUNAT para autocompletar datos.
     * Retorna array con datos listos para prellenar el formulario.
     */
    public function buscarOConsultarDocumento(string $numero, string $tipo): array
    {
        // 1. Buscar en BD local primero
        $clienteLocal = $this->buscarPorDocumento($numero);
        if ($clienteLocal) {
            return [
                'encontrado_local' => true,
                'cliente'          => $clienteLocal,
            ];
        }

        // 2. Si no existe, consultar API externa
        $datosReniec = $this->reniecService->consultar($numero, $tipo);

        return [
            'encontrado_local' => false,
            'datos_reniec'     => $datosReniec,
        ];
    }

    /**
     * Carga las relaciones necesarias para la vista de detalle del cliente.
     */
    public function cargarDetalle(Cliente $cliente): Cliente
    {
        $cliente->load([
            'reservas.fechaTour.tour',
            'reservas.estado',
        ]);

        return $cliente;
    }
}