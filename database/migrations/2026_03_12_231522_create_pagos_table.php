<?php
// =====================================================================
// ARCHIVO: 2026_03_12_000006_create_pagos_table.php
// UBICACIÓN: database/migrations/
// =====================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    public function up()
    {
        Schema::create('metodos_pago', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reservas')->cascadeOnDelete();
            $table->foreignId('metodo_pago_id')->constrained('metodos_pago');
            $table->foreignId('registrado_por')->constrained('usuarios_admin');
            $table->decimal('monto', 10, 2);
            $table->string('numero_operacion', 50)->nullable();
            $table->string('archivo_baucher')->nullable();
            $table->enum('tipo_pago', ['adelanto', 'saldo', 'pago_completo'])->default('adelanto');
            $table->enum('estado_validacion', ['pendiente', 'verificado', 'rechazado'])->default('pendiente');
            $table->date('fecha_pago');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('metodos_pago');
    }
}