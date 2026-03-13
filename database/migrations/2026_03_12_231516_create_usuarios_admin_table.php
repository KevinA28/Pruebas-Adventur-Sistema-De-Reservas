<?php
// =====================================================================
// ARCHIVO: 2026_03_12_000001_create_usuarios_admin_table.php
// UBICACIÓN: database/migrations/
// =====================================================================
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
class CreateUsuariosAdminTable extends Migration
{
    public function up()
    {
        Schema::create('usuarios_admin', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('rol', ['administrador', 'ventas', 'operador'])->default('ventas');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }
 
    public function down()
    {
        Schema::dropIfExists('usuarios_admin');
    }
}
 