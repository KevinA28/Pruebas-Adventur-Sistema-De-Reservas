<?php
// =====================================================================
// ARCHIVO: Reserva.php
// UBICACIÓN: app/Models/Reserva.php
// =====================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $fillable = [
        'codigo_reserva', 'cliente_id', 'fecha_tour_id',
        'estado_id', 'usuario_admin_id', 'cantidad_adultos',
        'cantidad_ninos', 'precio_total', 'monto_pagado',
        'canal_contacto', 'observaciones',
    ];

    protected $casts = [
        'precio_total' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
    ];

    // ── Relaciones ────────────────────────────────────────────────

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function fechaTour()
    {
        return $this->belongsTo(FechaTour::class);
    }

    public function estado()
    {
        return $this->belongsTo(EstadoReserva::class, 'estado_id');
    }

    public function usuarioAdmin()
    {
        return $this->belongsTo(UsuarioAdmin::class, 'usuario_admin_id');
    }

    public function pasajeros()
    {
        return $this->hasMany(Pasajero::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function comprobantes()
    {
        return $this->hasMany(Comprobante::class);
    }

    public function logistica()
    {
        return $this->hasOne(LogisticaReserva::class);
    }

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstado::class);
    }

    // ── Helpers ───────────────────────────────────────────────────

    // Cuánto falta por pagar
    public function getSaldoPendienteAttribute(): float
    {
        return $this->precio_total - $this->monto_pagado;
    }

    // Genera código tipo ADV-2026-014
    public static function generarCodigo(): string
    {
        $año    = date('Y');
        $ultimo = self::whereYear('created_at', $año)->count() + 1;
        return 'ADV-' . $año . '-' . str_pad($ultimo, 3, '0', STR_PAD_LEFT);
    }
}