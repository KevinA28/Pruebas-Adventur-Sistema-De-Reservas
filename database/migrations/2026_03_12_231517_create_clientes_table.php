<?php
// =====================================================================
// ARCHIVO: 2026_03_12_000002_create_clientes_table.php
// UBICACIÓN: database/migrations/
// =====================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_documento', ['DNI', 'RUC', 'CE', 'PASAPORTE']);
            $table->string('numero_documento', 20)->unique();
            $table->string('nombre_completo');
            $table->string('razon_social')->nullable();
            $table->string('direccion_fiscal')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('telefono_whatsapp', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
}