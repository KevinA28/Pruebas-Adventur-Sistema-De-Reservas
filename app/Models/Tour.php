<?php
// =====================================================================
// ARCHIVO: Tour.php
// UBICACIÓN: app/Models/Tour.php
// =====================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $fillable = [
        'nombre', 'descripcion', 'precio_adulto',
        'precio_nino', 'duracion_horas', 'activo',
    ];

    protected $casts = [
        'activo'        => 'boolean',
        'precio_adulto' => 'decimal:2',
        'precio_nino'   => 'decimal:2',
    ];

    public function fechas()
    {
        return $this->hasMany(FechaTour::class);
    }

    // Solo fechas futuras y con cupo disponible
    public function fechasDisponibles()
    {
        return $this->hasMany(FechaTour::class)
            ->where('estado', 'disponible')
            ->where('fecha', '>=', now()->toDateString())
            ->orderBy('fecha');
    }
}