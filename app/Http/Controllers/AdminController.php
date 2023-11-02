<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Paciente;
use App\Models\Vacuna;
use App\Models\Peluqueria;
use App\Models\Dato;


class AdminController extends Controller
{
    public function index(){

        return view('admin.index');
    }
    //Doctores

    public function doctor(){
        $users = User::all();
        return view('admin.doctores.doctor',['users' => $users]);
    }
    //pacientes
    public function paciente(){
        $pacientes = Paciente::all();
        return view('admin.pacientes.paciente',['pacientes' => $pacientes]);
    }

    public function add_paciente(){
        return view('admin.pacientes.add_paciente',);
    }

    public function store_Paciente(Request $request) {
        $request->validate([
            'nombre_propietario' => 'required',
            'telefono' => 'required',
            'nombre_mascota' => 'required',
            'sexo_mascota' => 'required',
            'raza_mascota'=>'required',
            'edad' => 'required',
            'peso' => 'required',
            'color' => 'required',
            'fecha'=>'required|date',
            
        ]);

        // Crear una nueva instancia de Medico y asignar valores
        $pacientes = new Paciente();
        $pacientes->nombre_propietario = $request->input('nombre_propietario');
        $pacientes->telefono = $request->input('telefono');
        $pacientes->nombre_mascota = $request->input('nombre_mascota');
        $pacientes->sexo_mascota = $request->input('sexo_mascota');
        $pacientes->raza_mascota = $request->input('raza_mascota');
        $pacientes->edad = $request->input('edad');
        $pacientes->peso = $request->input('peso');
        $pacientes->color = $request->input('color');
        $pacientes->fecha = $request->date('fecha');
        

        // Guardar el paciente en la base de datos
        $pacientes->save();

        // Redireccionar a la lista de paciente
        return redirect()->route('admin.pacientes.paciente');
    }


    //Consultas
    public function consulta(){
        return view('admin.consultas.consulta');
    }

    public function add_consulta(){
        return view('admin.consultas.add_consulta');
    }
    //datos
    public function dato(){
       $users =User::all();
       $pacientes=Paciente::all();
       $datos=Dato::all();
        return view('admin.datos.dato',['users' => $users, 'pacientes' => $pacientes, 'datos' => $datos]);
    }

    
    public function add_dato(){
        $users =User::all();
        $pacientes=Paciente::all();
        $datos=Dato::all();
        return view('admin.datos.add_dato',['users' => $users, 'pacientes' => $pacientes, 'datos' => $datos]);
    }

    public function store_dato(Request $request){
        $request->validate([
            'id_medicos' => 'required|exists:users,id',
            'id_pacientes' => 'required|exists:pacientes,id',
            'temperatura' => 'required',
            'pulso' => 'required',
            'respiracion' => 'required',
            'desidratacion' => 'required',
            'pupilas' => 'required',

        ]);

        $datos = new Dato();
        $datos->id_medicos = $request->input('id_medicos');
        $datos->id_pacientes = $request->input('id_pacientes');
        $datos->temperatura = $request->input('temperatura');
        $datos->pulso = $request->input('pulso');
        $datos->respiracion = $request->input('respiracion');
        $datos->desidratacion = $request->input('desidratacion');
        $datos->pupilas = $request->input('pupilas');
    

        $datos->save();

        return redirect()->route('admin.datos.dato');

    }
    
    //vacunas
    public function vacuna(){
        $users= User::all();
        $vacunas = Vacuna::all();
        return view('admin.vacunas.vacuna',['users' => $users, 'vacunas' => $vacunas]);
    }
    public function add_vacuna(){
        $users= User::all();
        $vacunas = Vacuna::all();
        return view('admin.vacunas.add_vacuna',['users' => $users, 'vacunas' => $vacunas]);
    }

    public function store_vacuna(Request $request){
        $request->validate([
            'id_medicos' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'nombre_propietario' => 'required',
            'nombre_mascota' => 'required',
            'vacuna' => 'required',
            'edad' => 'required',
            'peso' => 'required',
            'celular' => 'required',

        ]);

        $vacunas = new Vacuna();
        $vacunas->id_medicos = $request->input('id_medicos');
        $vacunas->fecha = $request->date('fecha');
        $vacunas->nombre_propietario = $request->input('nombre_propietario');
        $vacunas->nombre_mascota = $request->input('nombre_mascota');
        $vacunas->vacuna = $request->input('vacuna');
        $vacunas->edad = $request->input('edad');
        $vacunas->peso = $request->input('peso');
        $vacunas->celular = $request->input('celular');

        $vacunas->save();

        return redirect()->route('admin.vacunas.vacuna');

    }

    //peluqueria
    public function peluqueria(){
        $users= User::all();
        $peluquerias = Peluqueria::all();
        return view('admin.peluquerias.peluqueria',['users' => $users, 'peluquerias' => $peluquerias]);
        
    }
    public function add_peluqueria(){
        $users= User::all();
        $peluquerias = Peluqueria::all();
        return view('admin.peluquerias.add_peluqueria',['users' => $users, 'peluquerias' => $peluquerias]);
    }

    public function store_peluqueria(Request $request){
        $request->validate([
            'id_medicos' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'nombre_propietario' => 'required',
            'nombre_mascota' => 'required',
            'peluqueria' => 'required',
            'edad' => 'required',
            'peso' => 'required',
            'celular' => 'required',

        ]);

        $peluquerias = new Peluqueria();
        $peluquerias->id_medicos = $request->input('id_medicos');
        $peluquerias->fecha = $request->date('fecha');
        $peluquerias->nombre_propietario = $request->input('nombre_propietario');
        $peluquerias->nombre_mascota = $request->input('nombre_mascota');
        $peluquerias->peluqueria = $request->input('peluqueria');
        $peluquerias->edad = $request->input('edad');
        $peluquerias->peso = $request->input('peso');
        $peluquerias->celular = $request->input('celular');

        $peluquerias->save();

        return redirect()->route('admin.peluquerias.peluqueria');

    }
}
