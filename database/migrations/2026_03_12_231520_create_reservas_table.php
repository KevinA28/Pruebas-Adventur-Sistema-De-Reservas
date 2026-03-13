<?php
// =====================================================================
// ARCHIVO: 2026_03_12_000004_create_reservas_table.php
// UBICACIÓN: database/migrations/
// =====================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservasTable extends Migration
{
    public function up()
    {
        Schema::create('estados_reserva', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('color_hex', 7)->default('#6b7280');
            $table->timestamps();
        });

        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_reserva', 20)->unique();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('fecha_tour_id')->constrained('fechas_tour');
            $table->foreignId('estado_id')->constrained('estados_reserva');
            $table->foreignId('usuario_admin_id')->constrained('usuarios_admin');
            $table->integer('cantidad_adultos')->default(1);
            $table->integer('cantidad_ninos')->default(0);
            $table->decimal('precio_total', 10, 2);
            $table->decimal('monto_pagado', 10, 2)->default(0);
            $table->enum('canal_contacto', ['whatsapp', 'presencial', 'llamada', 'redes_sociales', 'web'])->default('whatsapp');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservas');
        Schema::dropIfExists('estados_reserva');
    }
}