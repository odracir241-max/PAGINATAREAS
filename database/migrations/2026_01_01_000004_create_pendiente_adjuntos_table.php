<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pendiente_adjuntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendiente_id')->constrained('pendientes')->cascadeOnDelete();
            $table->string('nombre_original', 255);
            $table->string('ruta_archivo', 255);
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('tamano_bytes')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendiente_adjuntos');
    }
};
