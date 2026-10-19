<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends Model
{
      use HasFactory, SoftDeletes;

    protected $table = 'empleados'; // Tu tabla real
    
    protected $fillable = [
        'nombre', 'apellido', 'dui', 'correo', 'telefono', 'foto', 'direccion', 'activo'
    ];
}
