<?php
// =====================================================================
// ARCHIVO: Pasajero.php
// UBICACIÓN: app/Models/Pasajero.php
// =====================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasajero extends Model
{
    protected $fillable = [
        'reserva_id', 'nombre_completo', 'tipo_documento',
        'numero_documento', 'fecha_nacimiento', 'tipo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

    public function salud()
    {
        return $this->hasOne(SaludPasajero::class);
    }
}