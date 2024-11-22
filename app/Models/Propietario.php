<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Mascota;

use App\Models\Rol;
use Spatie\Permission\Traits\HasRoles;

class Propietario extends Model
{
    use HasFactory;
    use HasRoles;

    protected $guard = 'propietarios';
    protected $table = 'propietarios'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'id_veterinario',
        'nombre_completo',
        'celular',
        'direccion',
    ];

    public function veterinario(){
        
        return $this->belongsTo(User::class, 'id_veterinario');
    }
}
