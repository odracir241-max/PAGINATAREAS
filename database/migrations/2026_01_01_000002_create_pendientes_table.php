<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pendientes', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 255);
            $table->text('descripcion')->nullable();
            $table->string('area_origen', 255);
            $table->string('area_destino', 255);
            $table->unsignedInteger('usuario_responsable_id');
            $table->enum('prioridad', ['alta', 'media', 'baja'])->default('media');
            $table->enum('estatus', ['pendiente', 'seguimiento', 'pausa', 'programado', 'finalizado'])->default('pendiente');
            $table->date('fecha_programada')->nullable();
            $table->dateTime('fecha_finalizacion')->nullable();
            $table->timestamps();

            $table->foreign('usuario_responsable_id')
                ->references('idUsuario')
                ->on('usuarios')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendientes');
    }
};
