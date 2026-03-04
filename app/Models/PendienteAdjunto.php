<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendienteAdjunto extends Model
{
    use HasFactory;

    protected $table = 'pendiente_adjuntos';

    protected $fillable = [
        'pendiente_id',
        'nombre_original',
        'ruta_archivo',
        'mime_type',
        'tamano_bytes',
    ];

    public function pendiente(): BelongsTo
    {
        return $this->belongsTo(Pendiente::class, 'pendiente_id');
    }
}
