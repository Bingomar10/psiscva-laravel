<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    // Le decimos a Laravel que use TU tabla
    protected $table = 'usuarios';

    // Los campos que se pueden llenar masivamente
    protected $fillable = [
        'nombre_usuario',
        'password',
        'activo',
        'empleado_id',
        'rol_id',
    ];

    // Los campos que se ocultan al convertir a JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }
}