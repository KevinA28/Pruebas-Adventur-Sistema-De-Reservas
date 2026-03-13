<?php
// =====================================================================
// ARCHIVO: UsuarioAdmin.php
// UBICACIÓN: app/Models/UsuarioAdmin.php
// =====================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioAdmin extends Model
{
    protected $table = 'usuarios_admin';

    protected $fillable = [
        'nombre', 'apellido', 'email', 'password', 'rol', 'activo',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function reservasRegistradas()
    {
        return $this->hasMany(Reserva::class, 'usuario_admin_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }
}