<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';
    protected $guard = 'pacientes'; // Define el guard específico para médicos

    protected $fillable = [
        'nombre_propietario',
        'telefono',
        'nombre_mascota',
        'sexo_mascota',
        'raza_mascota',
        'edad',        
        'peso',
        'color',
        'fecha',

        // Otros campos del modelo de médico
    ];
}
