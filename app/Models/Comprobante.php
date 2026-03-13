<?php
// =====================================================================
// ARCHIVO: Comprobante.php
// UBICACIÓN: app/Models/Comprobante.php
// =====================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    protected $fillable = [
        'reserva_id', 'emitido_por', 'tipo', 'serie', 'numero',
        'subtotal', 'igv', 'total', 'ruc_receptor',
        'razon_social_receptor', 'direccion_receptor', 'archivo_pdf',
        'correo_envio', 'telefono_whatsapp', 'estado_envio', 'fecha_envio',
    ];

    protected $casts = [
        'subtotal'    => 'decimal:2',
        'igv'         => 'decimal:2',
        'total'       => 'decimal:2',
        'fecha_envio' => 'datetime',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

    // Devuelve número completo: B001-00025
    public function getNumeroCompletoAttribute(): string
    {
        return $this->serie . '-' . str_pad($this->numero, 5, '0', STR_PAD_LEFT);
    }
}