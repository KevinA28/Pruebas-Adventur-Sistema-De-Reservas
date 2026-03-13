<?php
// =====================================================================
// ARCHIVO: 2026_03_12_000008_create_logistica_historial_table.php
// UBICACIÓN: database/migrations/
// =====================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogisticaHistorialTable extends Migration
{
    public function up()
    {
        Schema::create('logistica_reserva', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reservas')->cascadeOnDelete();
            $table->string('punto_encuentro')->nullable();
            $table->string('direccion_recojo')->nullable();
            $table->time('hora_recojo')->nullable();
            $table->string('hotel')->nullable();
            $table->string('nombre_guia')->nullable();
            $table->string('telefono_guia', 20)->nullable();
            $table->text('instrucciones_especiales')->nullable();
            $table->timestamps();
        });

        Schema::create('historial_estados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reservas')->cascadeOnDelete();
            $table->foreignId('estado_anterior_id')->nullable()->constrained('estados_reserva');
            $table->foreignId('estado_nuevo_id')->constrained('estados_reserva');
            $table->foreignId('cambiado_por')->constrained('usuarios_admin');
            $table->text('motivo')->nullable();
            $table->timestamp('fecha_cambio')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('historial_estados');
        Schema::dropIfExists('logistica_reserva');
    }
}