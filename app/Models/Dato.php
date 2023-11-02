<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Paciente;


class Dato extends Model
{
    use HasFactory;

    protected $table = 'datos';

    protected $fillable = [
        'id_medicos',
        'id_pacientes',
        'temperatura',
        'pulso',
        'respiracion',
        'desidratacion',        
        'pupilas',
        

    ];

    public function medico()
    {
        return $this->belongsTo(User::class, 'id_medicos');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_pacientes');
    }
}
