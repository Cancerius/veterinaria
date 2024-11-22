<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

use App\Models\Propietario;

class Mascota extends Model
{
    use HasRoles;
    
    protected $table = 'mascotas';
    protected $fillable = [
        'id_veterinario',
        'id_propietario',
        'nombre_mascota',
        'raza',
        'sexo',
        'fecha_nacimiento',
        'peso',
        'color',
        'imagen',
    ];

    // Relación con Propietario
    public function propietario()
    {
        return $this->belongsTo(Propietario::class, 'id_propietario');
    }

    // Relación con Consultas
    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'id_mascota');
    }

    // Relación con Cirugias
    public function cirugias()
    {
        return $this->hasMany(Cirugia::class, 'id_mascota');
    }

    // Relación con Vacunaciones
    public function vacunaciones()
    {
        return $this->hasMany(Vacunacion::class, 'id_mascota');
    }
    public function veterinario(){
        
        return $this->belongsTo(User::class, 'id_veterinario');
    }
    
}
