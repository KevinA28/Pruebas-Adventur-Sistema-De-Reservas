<?php
// =====================================================================
// ARCHIVO: MailService.php
// UBICACIÓN: app/Services/Integrations/MailService.php
// =====================================================================
// SERVICIO USADO: Gmail SMTP (gratis con cuenta Google)
// CONFIGURAR EN .env:
//   MAIL_MAILER=smtp
//   MAIL_HOST=smtp.gmail.com
//   MAIL_PORT=587
//   MAIL_USERNAME=tucorreo@gmail.com
//   MAIL_PASSWORD=xxxx_xxxx_xxxx_xxxx   <- contraseña de aplicación Gmail
//   MAIL_ENCRYPTION=tls
//   MAIL_FROM_ADDRESS=tucorreo@gmail.com
//   MAIL_FROM_NAME="Adventur Reservas"
//
// OBTENER CONTRASEÑA DE APLICACIÓN:
//   1. Ir a myaccount.google.com
//   2. Seguridad → Verificación en 2 pasos (activar)
//   3. Seguridad → Contraseñas de aplicaciones → Generar
// =====================================================================

namespace App\Services\Integrations;

use App\Models\Reserva;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class MailService
{
    // -----------------------------------------------------------------
    // ENVÍA CORREO DE CONFIRMACIÓN AL CLIENTE
    // Plantilla: resources/views/emails/confirmacion-reserva.blade.php
    // Adjunta el PDF de confirmación si existe
    // Si el cliente no tiene email, registra en log y NO falla
    // -----------------------------------------------------------------
    public function enviarConfirmacion(Reserva $reserva, ?string $pdfPath = null): void
    {
        if (! $reserva->cliente->email) {
            Log::info('MailService: cliente sin email, omitiendo envío', [
                'reserva_id' => $reserva->id,
                'cliente_id' => $reserva->cliente_id,
            ]);
            return;
        }

        try {
            Mail::send(
                'emails.confirmacion-reserva',
                ['reserva' => $reserva],
                function ($message) use ($reserva, $pdfPath) {
                    $message
                        ->to($reserva->cliente->email, $reserva->cliente->nombre_completo)
                        ->subject('Confirmación de Reserva — ' . $reserva->codigo_reserva);

                    // Adjuntar PDF si existe
                    if ($pdfPath && Storage::disk('public')->exists($pdfPath)) {
                        $message->attachData(
                            Storage::disk('public')->get($pdfPath),
                            'confirmacion-' . $reserva->codigo_reserva . '.pdf',
                            ['mime' => 'application/pdf']
                        );
                    }
                }
            );

            Log::info('MailService: confirmación enviada', [
                'reserva_id' => $reserva->id,
                'email'      => $reserva->cliente->email,
            ]);

        } catch (\Exception $e) {
            // Falla silenciosamente — el correo NO debe romper el flujo
            Log::error('MailService: error al enviar correo', [
                'reserva_id' => $reserva->id,
                'mensaje'    => $e->getMessage(),
            ]);
        }
    }

    // -----------------------------------------------------------------
    // ENVÍA CORREO CUANDO UN PAGO ES VERIFICADO
    // Plantilla: resources/views/emails/pago-verificado.blade.php
    // -----------------------------------------------------------------
    public function enviarPagoVerificado(\App\Models\Pago $pago): void
    {
        if (! $pago->reserva->cliente->email) {
            return;
        }

        try {
            Mail::send(
                'emails.pago-verificado',
                ['pago' => $pago],
                function ($message) use ($pago) {
                    $message
                        ->to($pago->reserva->cliente->email, $pago->reserva->cliente->nombre_completo)
                        ->subject('Pago confirmado — ' . $pago->reserva->codigo_reserva);
                }
            );
        } catch (\Exception $e) {
            Log::error('MailService: error enviando confirmación de pago', [
                'pago_id' => $pago->id,
                'mensaje' => $e->getMessage(),
            ]);
        }
    }
}