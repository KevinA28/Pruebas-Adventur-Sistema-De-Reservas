<?php
// =====================================================================
// ARCHIVO: FechaTour.php
// UBICACIÓN: app/Models/FechaTour.php
// =====================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FechaTour extends Model
{
    protected $table = 'fechas_tour';

    protected $fillable = [
        'tour_id', 'fecha', 'hora_salida',
        'cupo_total', 'cupo_disponible', 'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    // Descuenta cupos al confirmar una reserva
    public function descontarCupos(int $cantidad): void
    {
        $this->decrement('cupo_disponible', $cantidad);
        if ($this->cupo_disponible <= 0) {
            $this->update(['estado' => 'lleno']);
        }
    }
}