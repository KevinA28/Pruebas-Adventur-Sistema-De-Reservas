<?php
// =====================================================================
// ARCHIVO: 2026_03_12_000005_create_pasajeros_table.php
// UBICACIÓN: database/migrations/
// =====================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasajerosTable extends Migration
{
    public function up()
    {
        Schema::create('pasajeros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reservas')->cascadeOnDelete();
            $table->string('nombre_completo');
            $table->enum('tipo_documento', ['DNI', 'CE', 'PASAPORTE'])->nullable();
            $table->string('numero_documento', 20)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('tipo', ['adulto', 'nino'])->default('adulto');
            $table->timestamps();
        });

        Schema::create('salud_pasajero', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasajero_id')->constrained('pasajeros')->cascadeOnDelete();
            $table->text('alergias')->nullable();
            $table->text('restricciones_alimentarias')->nullable();
            $table->text('condiciones_medicas')->nullable();
            $table->text('medicamentos')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('salud_pasajero');
        Schema::dropIfExists('pasajeros');
    }
}