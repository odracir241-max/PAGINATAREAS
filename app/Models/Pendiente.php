<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pendiente extends Model
{
    use HasFactory;

    protected $table = 'pendientes';

    protected $fillable = [
        'titulo',
        'descripcion',
        'area_origen',
        'area_destino',
        'usuario_responsable_id',
        'prioridad',
        'estatus',
        'fecha_inicio',
        'fecha_programada',
        'fecha_finalizacion',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_programada' => 'date',
        'fecha_finalizacion' => 'datetime',
    ];

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_responsable_id', 'idUsuario');
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(PendienteSeguimiento::class, 'pendiente_id');
    }

    public function adjuntos(): HasMany
    {
        return $this->hasMany(PendienteAdjunto::class, 'pendiente_id');
    }
}
