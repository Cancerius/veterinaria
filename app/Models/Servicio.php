<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Mascota;
use App\Models\User;
use App\Models\Propietario;
use Spatie\Permission\Traits\HasRoles;

class Servicio extends Model
{
    
    use HasFactory;
    use HasRoles;
    
    protected $guard = 'servicios';
    protected $table = 'servicios';

    protected $fillable = [
        'id_mascota',
        'id_propietario',
        'id_veterinario',
        'fecha_servicio',
        'tipo_servicio',
        'costo',
      
    ];

    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'id_mascota');
    }

    public function propietario()
    {
        return $this->belongsTo(Propietario::class, 'id_propietario');
    }

    public function veterinario()
    {
        return $this->belongsTo(User::class, 'id_veterinario');
    }
}