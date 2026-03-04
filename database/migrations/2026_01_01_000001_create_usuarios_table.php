<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('idUsuario');
            $table->integer('idactivacion')->nullable();
            $table->string('Nombre', 255);
            $table->string('Telefono', 50)->nullable();
            $table->string('Correo', 50);
            $table->string('Usuario', 50);
            $table->string('Password', 50);
            $table->string('Tipo', 50);
            $table->string('Activo', 50);
            $table->string('Imagen', 100)->nullable();
            $table->string('area', 255)->nullable();

            $table->index('idUsuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
