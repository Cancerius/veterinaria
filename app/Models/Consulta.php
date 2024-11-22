<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Mascota;
use App\Models\User;
use App\Models\Propietario;
use Spatie\Permission\Traits\HasRoles;

class Consulta extends Model
{
    use HasFactory;
    use HasRoles;

    protected $guard = 'consultas';
    protected $table = 'consultas';

    protected $fillable = [
        'id_mascota',
        'id_propietario',
        'id_veterinario',
        'fecha_consulta',
        'fecha_cita',
        'temperatura',
        'peso',
        'fre_cardiaca',
        'fre_respiratoria',
        'dolores_localizados',
        'diagnostica',
    ];

    protected static function boot()
    {
        parent::boot();

        // Validación antes de guardar la consulta
        static::saving(function ($consulta) {
            // Verificar si la fecha_cita es anterior a la fecha_consulta
            if ($consulta->fecha_cita && $consulta->fecha_consulta) {
                if ($consulta->fecha_cita < $consulta->fecha_consulta) {
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

    public function veterinario()
    {
        return $this->belongsTo(User::class, 'id_veterinario');
    }
}
