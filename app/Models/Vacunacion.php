<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Mascota;
use App\Models\User;
use App\Models\Propietario;
use App\Models\Producto;
use Spatie\Permission\Traits\HasRoles;

class Vacunacion extends Model
{
    use HasFactory;
    use HasRoles;
    
    protected $guard = 'vacunacion';
    protected $table = 'vacunacion';

    protected $fillable = [
        'id_mascota',
        'id_veterinario',
        'id_propietario',
        'id_producto',
        'fecha',
        'fecha_cita',
        'costo',
      
    ];

    protected static function boot()
    {
        parent::boot();

        // Validación antes de guardar la consulta
        static::saving(function ($vacunacion) {
            // Verificar si la fecha_cita es anterior a la fecha
            if ($vacunacion->fecha_cita && $vacunacion->fecha) {
                if ($vacunacion->fecha_cita < $vacunacion->fecha) {
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

    public function veterinario()
    {
        return $this->belongsTo(User::class, 'id_veterinario');
    }

    public function propietario()
    {
        return $this->belongsTo(Propietario::class, 'id_propietario');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
