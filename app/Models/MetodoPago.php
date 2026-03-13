<?php
// =====================================================================
// ARCHIVO: MetodoPago.php
// UBICACIÓN: app/Models/MetodoPago.php
// =====================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'metodos_pago';

    protected $fillable = ['nombre', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}