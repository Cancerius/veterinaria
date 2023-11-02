<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('home');
});

Route::get('/register',[RegisterController::class, 'create'])
->name('register.index');

Route::post('/register',[RegisterController::class, 'store'])
->name('register.store');

Route::get('/login',[SessionsController::class, 'create'])
->middleware('guest')
->name('login.index');


Route::post('/login',[SessionsController::class, 'store'])
->name('login.store');

Route::get('/logout',[SessionsController::class, 'destroy'])
->middleware('auth')
->name('login.destroy');


Route::get('/admin',[AdminController::class, 'index'])
->middleware('auth.admin')
->name('admin.index');
//doctor
Route::get('/doctor',[AdminController::class, 'doctor'])
->name('admin.doctores.doctor');

;
//paciente
Route::get('/pacientes',[AdminController::class, 'paciente'])
->name('admin.pacientes.paciente');

Route::post('/pacientes', [AdminController::class, 'store_Paciente'])
->name('paciente.store');

Route::get('/agregar_paciente',[AdminController::class, 'add_paciente'])
->name('admin.pacientes.add_paciente');
//consulta

Route::get('/consultas',[AdminController::class, 'consulta'])
->name('admin.consultas.consulta');

Route::get('/agregar_consulta',[AdminController::class, 'add_consulta'])
->name('admin.consultas.add_consulta');
//dato
Route::get('/datos',[AdminController::class, 'dato'])
->name('admin.datos.dato');

Route::post('/datos',[AdminController::class, 'store_dato'])
->name('dato.store');

Route::get('/agregar_datos',[AdminController::class, 'add_dato'])
->name('admin.datos.add_dato');
//vacuna
Route::get('/vacunas',[AdminController::class, 'vacuna'])
->name('admin.vacunas.vacuna');

Route::post('/vacunas',[AdminController::class, 'store_vacuna'])
->name('vacuna.store');

Route::get('/agregar_vacunas',[AdminController::class, 'add_vacuna'])
->name('admin.vacunas.add_vacuna');
//peluqueria
Route::get('/peluqueria_baño',[AdminController::class, 'peluqueria'])
->name('admin.peluquerias.peluqueria');

Route::get('/agregar_peluqueria_baño',[AdminController::class, 'add_peluqueria'])
->name('admin.peluquerias.add_peluqueria');

Route::post('/peluqueria_baño',[AdminController::class, 'store_peluqueria'])
->name('peluqueria.store');




