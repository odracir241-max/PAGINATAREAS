<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendienteSeguimiento extends Model
{
    use HasFactory;

    protected $table = 'pendiente_seguimientos';

    protected $fillable = [
        'pendiente_id',
        'usuario_id',
        'estatus_anterior',
        'estatus_nuevo',
        'comentario',
    ];

    public function pendiente(): BelongsTo
    {
        return $this->belongsTo(Pendiente::class, 'pendiente_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'idUsuario');
    }
}
