<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Mascota;
use App\Models\User;
use App\Models\Propietario;
use Spatie\Permission\Traits\HasRoles;

class Cirugia extends Model
{
    use HasFactory;
    use HasRoles;

    protected $guard = 'cirugias';
    protected $table = 'cirugias';

    protected $fillable = [
        'id_mascota',
        'id_propietario',
        'id_veterinario',
        'fecha_cirugia',
        'fecha_cita',
        'temperatura',
        'fre_cardiaca',
        'fre_respiratoria',
        'peso',
        'tipo_cirugia',
    ];

    protected static function boot()
    {
        parent::boot();

        // Validación antes de guardar la consulta
        static::saving(function ($cirugia) {
            // Verificar si la fecha_cita es anterior a la fecha_consulta
            if ($cirugia->fecha_cita && $cirugia->fecha_cirugia) {
                if ($cirugia->fecha_cita < $cirugia->fecha_cirugia) {
                    // Lanzar una excepción o error en caso de que la fecha_cita sea anterior
                    throw new \Exception('La fecha de la cita no puede ser anterior a la fecha de la consulta.');
                }
            }
        });
    }

    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'id_mascota');
    }

    public function propietario()
    {
        return $this->belongsTo(Propietario::class, 'id_propietario');
    }

    public function veterinario(){
        
        return $this->belongsTo(User::class, 'id_veterinario');
    }
}
