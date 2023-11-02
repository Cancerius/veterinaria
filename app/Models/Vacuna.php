<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Vacuna extends Model
{
    use HasFactory;

    protected $table = 'vacunas';
    protected $guard = 'vacunas';
    
    protected $fillable = [
        'id_medicos',
        'fecha',
        'nombre_propietario',
        'nombre_mascota',
        'vacuna',
        'edad',
        'peso',        
        'celular',
        
    ];

    public function medico()
    {
        return $this->belongsTo(User::class, 'id_medicos');
    }
}
