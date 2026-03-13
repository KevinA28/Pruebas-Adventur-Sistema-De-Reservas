<?php
// =====================================================================
// ARCHIVO: DatabaseSeeder.php
// UBICACIÓN: database/seeders/DatabaseSeeder.php
// =====================================================================

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Estados de reserva
        $estados = [
            ['nombre' => 'consulta',     'color_hex' => '#6b7280'],
            ['nombre' => 'pre-reserva',  'color_hex' => '#f59e0b'],
            ['nombre' => 'confirmada',   'color_hex' => '#10b981'],
            ['nombre' => 'lista espera', 'color_hex' => '#8b5cf6'],
            ['nombre' => 'cancelada',    'color_hex' => '#ef4444'],
            ['nombre' => 'no show',      'color_hex' => '#f97316'],
            ['nombre' => 'finalizada',   'color_hex' => '#1d4ed8'],
        ];
        foreach ($estados as $e) {
            DB::table('estados_reserva')->insert([...$e, 'created_at' => now(), 'updated_at' => now()]);
        }

        // Métodos de pago
        foreach (['Yape', 'Plin', 'Transferencia bancaria', 'Tarjeta de crédito', 'Efectivo'] as $nombre) {
            DB::table('metodos_pago')->insert(['nombre' => $nombre, 'activo' => true, 'created_at' => now(), 'updated_at' => now()]);
        }

        // Usuario administrador inicial
        DB::table('usuarios_admin')->insert([
            'nombre'     => 'Admin',
            'apellido'   => 'ADVENTUR',
            'email'      => 'admin@adventur.pe',
            'password'   => Hash::make('password'),
            'rol'        => 'administrador',
            'activo'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tours de ejemplo — Cajamarca
        $tours = [
            ['nombre' => 'Cumbemayo',            'precio_adulto' => 35.00, 'precio_nino' => 20.00, 'duracion_horas' => 4],
            ['nombre' => 'Baños del Inca',        'precio_adulto' => 25.00, 'precio_nino' => 15.00, 'duracion_horas' => 3],
            ['nombre' => 'Ventanillas de Otuzco', 'precio_adulto' => 30.00, 'precio_nino' => 18.00, 'duracion_horas' => 5],
            ['nombre' => 'Granja Porcón',         'precio_adulto' => 40.00, 'precio_nino' => 25.00, 'duracion_horas' => 6],
        ];
        foreach ($tours as $t) {
            DB::table('tours')->insert([...$t, 'activo' => true, 'created_at' => now(), 'updated_at' => now()]);
        }
    }
}