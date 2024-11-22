<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleController;
use App\Http\Middleware\Admin;

Route::redirect('/', '/login');
Route::get('/login', function () {
    return view('auth.login');
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

Route::get('/veterinarios',[AdminController::class, 'doctor'])
->middleware('auth.admin')
->middleware('can:Tabla veterinario')
->name('admin.doctores.doctor');

Route::get('/agregar_veterinario',[AdminController::class, 'add_veterinario'])
->middleware('auth.admin')
->middleware('can:Crear veterinario')
->name('admin.register');

Route::post('/agregar_veterinario',[AdminController::class, 'store_veterinario'])
->middleware('auth.admin')
->middleware('can:Crear veterinario')
->name('register');

Route::get('/editar_veterinario/{user}', [AdminController::class, 'editar_veterinario'])
->middleware('auth.admin')
->middleware('can:Editar veterinario')
->name('admin.doctores.edit_doctor');

Route::put('/editar_veterinario/{user}', [AdminController::class, 'update_veterinario'])
->middleware('auth.admin')
->middleware('can:Editar veterinario')
->name('admin.doctor.update');

Route::delete('/eliminar-veterinario/{user}', [AdminController::class, 'eliminarVeterinario'])
->middleware('auth.admin')
->middleware('can:Editar veterinario')
->name('eliminar-veterinario');

//Roles

Route::get('/roles',[RoleController::class, 'index'])
->middleware('auth.admin')
->middleware('can:Tabla roles')
->name('admin.roles.index');

Route::get('/agregar_roles',[RoleController::class, 'create'])
->middleware('auth.admin')
->middleware('can:Crear roles')
->name('admin.roles.create');

Route::post('/roles',[RoleController::class, 'store'])
->middleware('auth.admin')
->middleware('can:Crear roles')
->name('admin.roles.store');

Route::get('/editar_roles/{role}',[RoleController::class, 'edit'])
->middleware('auth.admin')
->middleware('can:Editar roles')
->name('admin.roles.edit');

Route::put('/editar_roles/{role}',[RoleController::class, 'update'])
->middleware('auth.admin')
->middleware('can:Editar roles')
->name('admin.roles.update');

Route::delete('/eliminar_roles{role}',[RoleController::class, 'destroy'])
->middleware('auth.admin')
->middleware('can:Eliminar roles')
->name('admin.roles.destroy');



//propietario
Route::get('/propietarios',[AdminController::class, 'propietario'])
->middleware('auth.admin')
->middleware('can:Tabla propietario')
->name('admin.registros.propietarios.propietario');

Route::get('/agregar_propietario',[AdminController::class, 'add_propietario'])
->middleware('auth.admin')
->middleware('can:Crear propietario')
->name('admin.registros.propietarios.add_propietario');

Route::post('/propietarios', [AdminController::class, 'store_propietario'])
->middleware('auth.admin')
->middleware('can:Crear propietario')
->name('propietarios.store');

Route::get('/editar_prop/{id}', [AdminController::class, 'edit_propietario'])
->middleware('auth.admin')
->middleware('can:Editar propietario')
->name('admin.registros.propietarios.edit_propietario');

Route::put('/admin/registros/propietarios/{id}', [AdminController::class, 'update_prop'])
->middleware('auth.admin')
->middleware('can:Editar propietario')
->name('admin.registros.propietarios.update');


Route::delete('/eliminar-propietario/{propietario}', [AdminController::class, 'eliminarPropietario'])
->middleware('auth.admin')
->middleware('can:Eliminar propietario')
->name('eliminar-propietario');


//mascota
Route::get('/mascotas',[AdminController::class, 'mascota'])
->middleware('auth.admin')
->middleware('can:Tabla mascota')
->name('admin.registros.mascotas.mascota');

Route::get('/agregar_mascota',[AdminController::class, 'add_mascota'])
->middleware('auth.admin')
->middleware('can:Crear mascota')
->name('admin.registros.mascotas.add_mascota');

Route::post('/mascotas', [AdminController::class, 'store_mascota'])
->middleware('auth.admin')
->middleware('can:Crear mascota')
->name('mascotas.store');

Route::get('/editar_masco/{id}', [AdminController::class, 'edit_mascota'])
->middleware('auth.admin')
->middleware('can:Editar mascota')
->name('admin.registros.mascotas.edit_mascotas');

Route::put('/update_masco/{id}', [AdminController::class, 'update_mascota'])
->middleware('auth.admin')
->middleware('can:Editar mascota')
->name('admin.registros.mascotas.update');

Route::delete('/eliminar-mascota/{mascota}', [AdminController::class, 'eliminarMascota'])
->middleware('auth.admin')
->middleware('can:Eliminar mascota')
->name('eliminar-mascota');

Route::get('/cardex_pdf/{id}', [AdminController::class, 'cardex_pdf'])
->middleware('auth.admin')
->middleware('can:Generar Kardex mascota')
->name('cardex_pdf');

//consulta

Route::get('/consultas',[AdminController::class, 'consulta'])
->middleware('auth.admin')
->middleware('can:Tabla consulta')
->name('admin.registros.consultas.consulta');

Route::get('/agregar_consulta',[AdminController::class, 'add_consulta'])
->middleware('auth.admin')
->middleware('can:Crear consulta')
->name('admin.registros.consultas.add_consulta');


Route::post('/consultas', [AdminController::class, 'store_consulta'])
->middleware('auth.admin')
->middleware('can:Crear consulta')
->name('consultas.store');

Route::get('/editar_consultas/{id}',[AdminController::class, 'edit_consulta'])
->middleware('auth.admin')
->middleware('can:Editar consulta')
->name('admin.registros.consultas.edit_consulta');

Route::put('/update_consulta/{id}', [AdminController::class, 'update_consulta'])
->middleware('auth.admin')
->middleware('can:Editar consulta')
->name('admin.registros.consulta.update');

Route::get('/consulta_pdf/{id}', [AdminController::class, 'consulta_pdf'])
->middleware('auth.admin')
->middleware('can:Reporte consulta')
->name('consulta_pdf');

Route::delete('/eliminar-consulta/{consulta}', [AdminController::class, 'eliminarConsulta'])
->middleware('auth.admin')
->middleware('can:Eliminar consulta')
->name('eliminar-consulta');

Route::get('/reporte_consulta', [AdminController::class, 'ReporteFechasConsultas'])
->middleware('auth.admin')
->middleware('can:Reporte por fechas consulta')
->name('consulta_fechas');

//cirugia
Route::get('/cirugias',[AdminController::class, 'cirugia'])
->middleware('auth.admin')
->middleware('can:Tabla cirugia')
->name('admin.registros.cirugias.cirugia');

Route::get('/agregar_cirugia',[AdminController::class, 'add_cirugia'])
->middleware('auth.admin')
->middleware('can:Crear cirugia')
->name('admin.registros.cirugias.add_cirugia');

Route::post('/cirugias', [AdminController::class, 'store_cirugia'])
->middleware('auth.admin')
->middleware('can:Crear cirugia')
->name('cirugias.store');

Route::get('/editar_cirugia/{id}',[AdminController::class, 'edit_cirugia'])
->middleware('auth.admin')
->middleware('can:Editar cirugia')
->name('admin.registros.cirugias.edit_cirugia');

Route::put('/update_cirugia/{id}', [AdminController::class, 'update_cirugia'])
->middleware('auth.admin')
->middleware('can:Editar cirugia')
->name('admin.registros.cirugia.update');

Route::get('/cirugia_pdf/{id}', [AdminController::class, 'cirugia_pdf'])
->middleware('auth.admin')
->middleware('can:Reporte cirugia')
->name('cirugia_pdf');

Route::delete('/eliminar-cirugia/{cirugia}', [AdminController::class, 'eliminarCirugia'])
->middleware('auth.admin')
->middleware('can:Eliminar cirugia')
->name('eliminar-cirugia');

Route::get('/reporte_cirugia', [AdminController::class, 'ReporteFechasCirugia'])
->middleware('auth.admin')
->middleware('can:Reporte por fechas cirugia')
->name('cirugia_fechas'); 

//vacuna
Route::get('/vacunas',[AdminController::class, 'vacuna'])
->middleware('auth.admin')
->middleware('can:Tabla vacunacion')
->name('admin.registros.vacunacion.vacuna');

Route::get('/agregar_vacuna',[AdminController::class, 'add_vacuna'])
->middleware('auth.admin')
->middleware('can:Crear vacunacion')
->name('admin.registros.vacunacion.add_vacuna');

Route::post('/vacunas', [AdminController::class, 'store_vacuna'])
->middleware('auth.admin')
->middleware('can:Crear vacunacion')
->name('vacunas.store');

Route::get('/editar_vacunas/{id}',[AdminController::class, 'edit_vacuna'])
->middleware('auth.admin')
->middleware('can:Editar vacunacion')
->name('admin.registros.vacunacion.edit_vacuna');

Route::put('/update_vacuna/{id}', [AdminController::class, 'update_vacuna'])
->middleware('auth.admin')
->middleware('can:Editar vacunacion')
->name('admin.registros.vacuna.update');

Route::get('/vacuna_pdf/{id}', [AdminController::class, 'vacuna_pdf'])
->middleware('auth.admin')
->middleware('can:Reporte vacunacion')
->name('vacuna_pdf');

Route::delete('/eliminar-vacuna/{vacunacion}', [AdminController::class, 'eliminarVacuna'])
->middleware('auth.admin')
->middleware('can:Eliminar vacunacion')
->name('eliminar-vacunacion');

Route::get('/reporte_vacuna', [AdminController::class, 'ReporteFechasVacuna'])
->middleware('auth.admin')
->middleware('can:Reporte por fechas vacuna')
->name('vacuna_fechas'); 

//servicio
Route::get('/servicios',[AdminController::class, 'servicio'])
->middleware('auth.admin')
->middleware('can:Tabla peluqueria y baño')
->name('admin.servicios.servicio');

Route::get('/agregar_servicios',[AdminController::class, 'add_servicio'])
->middleware('auth.admin')
->middleware('can:Crear peluqueria y baño')
->name('admin.servicios.add_servicio');

Route::post('/servicios', [AdminController::class, 'store_servicio'])
->middleware('auth.admin')
->middleware('can:Crear peluqueria y baño')
->name('servicios.store');

Route::get('/editar_servicio/{id}',[AdminController::class, 'edit_servicio'])
->middleware('auth.admin')
->middleware('can:Editar peluqueira y baño')
->name('admin.servicios.edit_servicio');

Route::put('/update_servicio/{id}', [AdminController::class, 'update_servicio'])
->middleware('auth.admin')
->middleware('can:Editar peluqueira y baño')
->name('admin.registros.servicio.update');

Route::get('/servicio_pdf/{id}', [AdminController::class, 'servicio_pdf'])
->middleware('auth.admin')
->middleware('can:Reporte peluqueria y baño')
->name('servicio_pdf');

Route::delete('/eliminar-servicio/{servicio}', [AdminController::class, 'eliminarServicio'])
->middleware('auth.admin')
->middleware('can:Eliminar peluqueriay baño')
->name('eliminar-servicio');

Route::get('/reporte_servicio', [AdminController::class, 'ReporteFechasServicio'])
->middleware('auth.admin')
->middleware('can:Reporte por fechas peluqueria y baño')
->name('servicio_fechas'); 

//productos

Route::get('/productos',[AdminController::class, 'producto'])
->middleware('auth.admin')
->middleware('can:Tabla productos')
->name('admin.productos.producto');

Route::get('/agregar_productos',[AdminController::class, 'add_producto'])
->middleware('auth.admin')
->middleware('can:Crear producto')
->name('admin.productos.add_producto');

Route::post('/productos', [AdminController::class, 'store_producto'])
->middleware('auth.admin')
->middleware('can:Crear producto')
->name('productos.store');

Route::get('/editar_productos/{id}',[AdminController::class, 'edit_producto'])
->middleware('auth.admin')
->middleware('can:Editar producto')
->name('admin.productos.edit_producto');

Route::put('/update_producto/{id}', [AdminController::class, 'update_producto'])
->middleware('auth.admin')
->middleware('can:Editar producto')
->name('admin.registros.producto.update');

Route::delete('/eliminar-producto/{producto}', [AdminController::class, 'eliminarProducto'])
->middleware('auth.admin')
->middleware('can:Eliminar producto')
->name('eliminar-producto');

Route::get('/reporte_producto', [AdminController::class, 'ReporteFechasProducto'])
->middleware('auth.admin')
->middleware('can:Eliminar producto')
->name('producto_fechas'); 

