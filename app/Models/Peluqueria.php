<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Peluqueria extends Model
{
    use HasFactory;
    protected $table = 'peluquerias';
    protected $guard = 'peluquerias';
    
    protected $fillable = [
        'id_medicos',
        'fecha',
        'nombre_propietario',
        'nombre_mascota',
        'peluqueria',
        'edad',
        'peso',        
        'celular',
        
    ];

    public function medico()
    {
        return $this->belongsTo(User::class, 'id_medicos');
    }

}
