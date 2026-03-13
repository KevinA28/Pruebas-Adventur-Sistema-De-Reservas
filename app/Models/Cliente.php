<?php
// =====================================================================
// ARCHIVO: Cliente.php
// UBICACIÓN: app/Models/Cliente.php
// =====================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'tipo_documento',
        'numero_documento',
        'nombre_completo',
        'razon_social',
        'direccion_fiscal',
        'telefono',
        'email',
        'telefono_whatsapp',
    ];

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    // Devuelve razón social si es empresa, nombre completo si es persona
    public function getNombreMostrarAttribute(): string
    {
        return $this->razon_social ?? $this->nombre_completo;
    }

    public function getEsEmpresaAttribute(): bool
    {
        return $this->tipo_documento === 'RUC';
    }
}