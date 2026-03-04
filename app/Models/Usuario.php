<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';

    protected $primaryKey = 'idUsuario';

    public $timestamps = false;

    protected $fillable = [
        'idactivacion',
        'Nombre',
        'Telefono',
        'Correo',
        'Usuario',
        'Password',
        'Tipo',
        'Activo',
        'Imagen',
        'area',
    ];

    public function pendientesAsignados(): HasMany
    {
        return $this->hasMany(Pendiente::class, 'usuario_responsable_id', 'idUsuario');
    }
}
