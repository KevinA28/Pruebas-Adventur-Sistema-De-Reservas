<?php
// =====================================================================
// ARCHIVO: SaludPasajero.php
// UBICACIÓN: app/Models/SaludPasajero.php
// =====================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaludPasajero extends Model
{
    protected $table = 'salud_pasajero';

    protected $fillable = [
        'pasajero_id', 'alergias', 'restricciones_alimentarias',
        'condiciones_medicas', 'medicamentos',
    ];

    public function pasajero()
    {
        return $this->belongsTo(Pasajero::class);
    }
}