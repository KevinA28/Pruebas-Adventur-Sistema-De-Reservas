<?php
// =====================================================================
// ARCHIVO: EstadoReserva.php
// UBICACIÓN: app/Models/EstadoReserva.php
// =====================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoReserva extends Model
{
    protected $table = 'estados_reserva';

    protected $fillable = ['nombre', 'color_hex'];
}