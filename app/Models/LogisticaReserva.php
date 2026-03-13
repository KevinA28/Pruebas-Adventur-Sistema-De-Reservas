<?php
// =====================================================================
// ARCHIVO: LogisticaReserva.php
// UBICACIÓN: app/Models/LogisticaReserva.php
// =====================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogisticaReserva extends Model
{
    protected $table = 'logistica_reserva';

    protected $fillable = [
        'reserva_id', 'punto_encuentro', 'direccion_recojo',
        'hora_recojo', 'hotel', 'nombre_guia',
        'telefono_guia', 'instrucciones_especiales',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }
}