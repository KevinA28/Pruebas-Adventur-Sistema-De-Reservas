<?php
// =====================================================================
// ARCHIVO: ReniecService.php
// UBICACIÓN: app/Services/Integrations/ReniecService.php
// =====================================================================
// SERVICIO USADO: apis.net.pe (plan gratuito — 100 consultas/día)
// DOCUMENTACIÓN: https://apis.net.pe/api-dni-gratis
// CONFIGURAR EN .env:
//   RENIEC_API_TOKEN=tu_token_aqui
// OBTENER TOKEN GRATIS EN: https://apis.net.pe
// =====================================================================

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReniecService
{
    private string $baseUrl = 'https://api.apis.net.pe/v2';
    private string $token;

    public function __construct()
    {
        $this->token = config('services.reniec.token', '');
    }

    // -----------------------------------------------------------------
    // MÉTODO PRINCIPAL
    // Detecta automáticamente si es DNI o RUC y consulta el endpoint
    // correcto. Retorna array con datos normalizados o null si falla.
    // -----------------------------------------------------------------
    public function consultar(string $numero, string $tipo): ?array
    {
        return match (strtoupper($tipo)) {
            'DNI'  => $this->consultarDni($numero),
            'RUC'  => $this->consultarRuc($numero),
            default => null,
        };
    }

    // -----------------------------------------------------------------
    // CONSULTAR DNI
    // Endpoint: GET /reniec/dni?numero=12345678
    // Respuesta: { "nombres", "apellidoPaterno", "apellidoMaterno", ... }
    // -----------------------------------------------------------------
    public function consultarDni(string $dni): ?array
    {
        try {
            $response = Http::withToken($this->token)
                ->timeout(8)
                ->get("{$this->baseUrl}/reniec/dni", ['numero' => $dni]);

            if ($response->failed()) {
                Log::warning('ReniecService: DNI no encontrado o error', [
                    'dni'    => $dni,
                    'status' => $response->status(),
                ]);
                return null;
            }

            $data = $response->json();

            // Normalizar al formato interno del sistema
            return [
                'nombre_completo'  => trim(
                    ($data['nombres'] ?? '') . ' ' .
                    ($data['apellidoPaterno'] ?? '') . ' ' .
                    ($data['apellidoMaterno'] ?? '')
                ),
                'tipo_documento'   => 'DNI',
                'numero_documento' => $dni,
                'direccion'        => null, // RENIEC no retorna dirección
            ];

        } catch (\Exception $e) {
            Log::error('ReniecService: excepción consultando DNI', [
                'dni'     => $dni,
                'mensaje' => $e->getMessage(),
            ]);
            return null;
        }
    }

    // -----------------------------------------------------------------
    // CONSULTAR RUC
    // Endpoint: GET /sunat/ruc?numero=20123456789
    // Respuesta: { "razonSocial", "nombreComercial", "direccion", ... }
    // -----------------------------------------------------------------
    public function consultarRuc(string $ruc): ?array
    {
        try {
            $response = Http::withToken($this->token)
                ->timeout(8)
                ->get("{$this->baseUrl}/sunat/ruc", ['numero' => $ruc]);

            if ($response->failed()) {
                Log::warning('ReniecService: RUC no encontrado o error', [
                    'ruc'    => $ruc,
                    'status' => $response->status(),
                ]);
                return null;
            }

            $data = $response->json();

            return [
                'nombre_completo'  => $data['razonSocial'] ?? $data['nombreComercial'] ?? '',
                'tipo_documento'   => 'RUC',
                'numero_documento' => $ruc,
                'direccion'        => $data['direccion'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('ReniecService: excepción consultando RUC', [
                'ruc'     => $ruc,
                'mensaje' => $e->getMessage(),
            ]);
            return null;
        }
    }
}