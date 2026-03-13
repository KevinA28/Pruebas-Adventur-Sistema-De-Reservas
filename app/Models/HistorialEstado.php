<?php
// =====================================================================
// ARCHIVO: HistorialEstado.php
// UBICACIÓN: app/Models/HistorialEstado.php
// =====================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialEstado extends Model
{
    public $timestamps = false;

    protected $table = 'historial_estados';

    protected $fillable = [
        'reserva_id', 'estado_anterior_id', 'estado_nuevo_id',
        'cambiado_por', 'motivo', 'fecha_cambio',
    ];

    protected $casts = [
        'fecha_cambio' => 'datetime',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

    public function estadoAnterior()
    {
        return $this->belongsTo(EstadoReserva::class, 'estado_anterior_id');
    }

    public function estadoNuevo()
    {
        return $this->belongsTo(EstadoReserva::class, 'estado_nuevo_id');
    }

    public function cambiadorPor()
    {
        return $this->belongsTo(UsuarioAdmin::class, 'cambiado_por');
    }
}