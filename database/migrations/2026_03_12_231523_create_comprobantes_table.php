<?php
// =====================================================================
// ARCHIVO: 2026_03_12_000007_create_comprobantes_table.php
// UBICACIÓN: database/migrations/
// =====================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComprobantesTable extends Migration
{
    public function up()
    {
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reservas');
            $table->foreignId('emitido_por')->constrained('usuarios_admin');
            $table->enum('tipo', ['boleta', 'factura']);
            $table->string('serie', 10);
            $table->string('numero', 10);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('igv', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('ruc_receptor', 20)->nullable();
            $table->string('razon_social_receptor')->nullable();
            $table->string('direccion_receptor')->nullable();
            $table->string('archivo_pdf')->nullable();
            $table->string('correo_envio')->nullable();
            $table->string('telefono_whatsapp', 20)->nullable();
            $table->enum('estado_envio', ['pendiente', 'enviado', 'error'])->default('pendiente');
            $table->timestamp('fecha_envio')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('comprobantes');
    }
}