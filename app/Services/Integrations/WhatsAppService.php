<?php
// =====================================================================
// ARCHIVO: WhatsAppService.php
// UBICACIÓN: app/Services/Integrations/WhatsAppService.php
// =====================================================================
// SERVICIO USADO: Meta Cloud API (gratis hasta 1000 msgs/mes)
// DOCUMENTACIÓN: https://developers.facebook.com/docs/whatsapp/cloud-api
//
// PASOS PARA ACTIVAR GRATIS:
//   1. Ir a developers.facebook.com → crear app → tipo "Business"
//   2. Agregar producto "WhatsApp"
//   3. En WhatsApp > Configuración de API obtener:
//      - Phone Number ID
//      - Token de acceso temporal (o permanente con cuenta Business)
//   4. Configurar en .env:
//      WHATSAPP_TOKEN=tu_token_meta
//      WHATSAPP_PHONE_ID=tu_phone_number_id
//      WHATSAPP_FROM=51XXXXXXXXX   <- número registrado en Meta
//
// NOTA: El número destino debe estar en la lista de prueba
//       hasta que la app sea aprobada por Meta (1-3 días)
// =====================================================================

namespace App\Services\Integrations;

use App\Models\Reserva;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $token;
    private string $phoneId;
    private string $apiUrl;

    public function __construct()
    {
        $this->token   = config('services.whatsapp.token', '');
        $this->phoneId = config('services.whatsapp.phone_id', '');
        $this->apiUrl  = "https://graph.facebook.com/v19.0/{$this->phoneId}/messages";
    }

    // -----------------------------------------------------------------
    // ENVÍA MENSAJE DE CONFIRMACIÓN DE RESERVA AL CLIENTE
    // Número destino: telefono_whatsapp del cliente (con código país)
    // Ejemplo: 51987654321 (Perú = 51)
    // -----------------------------------------------------------------
    public function enviarConfirmacionReserva(Reserva $reserva): void
    {
        $telefono = $this->normalizarTelefono($reserva->cliente->telefono_whatsapp);

        if (! $telefono) {
            Log::info('WhatsAppService: cliente sin teléfono, omitiendo', [
                'reserva_id' => $reserva->id,
            ]);
            return;
        }

        $mensaje = $this->armarMensajeConfirmacion($reserva);
        $this->enviar($telefono, $mensaje, $reserva->id);
    }

    // -----------------------------------------------------------------
    // ENVÍA MENSAJE CUANDO UN PAGO ES VERIFICADO
    // -----------------------------------------------------------------
    public function enviarPagoVerificado(\App\Models\Pago $pago): void
    {
        $telefono = $this->normalizarTelefono(
            $pago->reserva->cliente->telefono_whatsapp
        );

        if (! $telefono) {
            return;
        }

        $reserva = $pago->reserva;
        $mensaje = "✅ *Pago confirmado — Adventur*\n\n"
                 . "Hola {$reserva->cliente->nombre_completo},\n"
                 . "tu pago de S/. {$pago->monto} ha sido verificado.\n\n"
                 . "Reserva: *{$reserva->codigo_reserva}*\n"
                 . "Tour: {$reserva->fechaTour->tour->nombre}\n"
                 . "Fecha: {$reserva->fechaTour->fecha}\n\n"
                 . "¡Gracias por confiar en Adventur! 🌿";

        $this->enviar($telefono, $mensaje, $reserva->id);
    }

    // -----------------------------------------------------------------
    // MÉTODO BASE — envía el mensaje a la API de Meta
    // -----------------------------------------------------------------
    private function enviar(string $telefono, string $mensaje, int $contextId = 0): void
    {
        try {
            $response = Http::withToken($this->token)
                ->timeout(10)
                ->post($this->apiUrl, [
                    'messaging_product' => 'whatsapp',
                    'to'                => $telefono,
                    'type'              => 'text',
                    'text'              => ['body' => $mensaje],
                ]);

            if ($response->failed()) {
                Log::warning('WhatsAppService: envío fallido', [
                    'telefono'   => $telefono,
                    'context_id' => $contextId,
                    'status'     => $response->status(),
                    'body'       => $response->body(),
                ]);
                return;
            }

            Log::info('WhatsAppService: mensaje enviado', [
                'telefono'   => $telefono,
                'context_id' => $contextId,
            ]);

        } catch (\Exception $e) {
            // Falla silenciosamente — WhatsApp NO debe romper el flujo
            Log::error('WhatsAppService: excepción', [
                'telefono' => $telefono,
                'mensaje'  => $e->getMessage(),
            ]);
        }
    }

    // -----------------------------------------------------------------
    // NORMALIZA el número a formato internacional sin +
    // Agrega 51 (Perú) si el número tiene 9 dígitos
    // Ejemplos: "987654321" → "51987654321"
    //           "+51987654321" → "51987654321"
    //           "51987654321" → "51987654321"
    // -----------------------------------------------------------------
    private function normalizarTelefono(?string $telefono): ?string
    {
        if (! $telefono) {
            return null;
        }

        // Quitar todo lo que no sea número
        $limpio = preg_replace('/\D/', '', $telefono);

        // Si tiene 9 dígitos (móvil peruano sin código país)
        if (strlen($limpio) === 9) {
            return '51' . $limpio;
        }

        // Si ya tiene código de país (11+ dígitos)
        if (strlen($limpio) >= 11) {
            return $limpio;
        }

        return null;
    }

    // -----------------------------------------------------------------
    // ARMA EL MENSAJE DE CONFIRMACIÓN DE RESERVA
    // -----------------------------------------------------------------
    private function armarMensajeConfirmacion(Reserva $reserva): string
    {
        $tour     = $reserva->fechaTour->tour->nombre;
        $fecha    = \Carbon\Carbon::parse($reserva->fechaTour->fecha)->format('d/m/Y');
        $adultos  = $reserva->cantidad_adultos;
        $ninos    = $reserva->cantidad_ninos;
        $total    = number_format($reserva->precio_total, 2);

        return "🌿 *Reserva confirmada — Adventur*\n\n"
             . "Hola *{$reserva->cliente->nombre_completo}*,\n"
             . "tu reserva ha sido registrada exitosamente.\n\n"
             . "📋 *Detalles:*\n"
             . "Código: *{$reserva->codigo_reserva}*\n"
             . "Tour: {$tour}\n"
             . "Fecha: {$fecha}\n"
             . "Pasajeros: {$adultos} adulto(s)"
             . ($ninos > 0 ? ", {$ninos} niño(s)" : '') . "\n"
             . "Total: *S/. {$total}*\n\n"
             . "Para cualquier consulta escríbenos.\n"
             . "¡Gracias por elegir Adventur! 🎒";
    }
}