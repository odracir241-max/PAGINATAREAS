<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pendiente_seguimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendiente_id')->constrained('pendientes')->cascadeOnDelete();
            $table->unsignedInteger('usuario_id');
            $table->enum('estatus_anterior', ['pendiente', 'seguimiento', 'pausa', 'programado', 'finalizado'])->nullable();
            $table->enum('estatus_nuevo', ['pendiente', 'seguimiento', 'pausa', 'programado', 'finalizado']);
            $table->text('comentario')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')
                ->references('idUsuario')
                ->on('usuarios')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendiente_seguimientos');
    }
};
