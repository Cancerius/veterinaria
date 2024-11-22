<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Propietario;
use App\Models\Mascota;
use App\Models\Consulta;
use App\Models\Cirugia;
use App\Models\Vacunacion;
use App\Models\Servicio;
use App\Models\Producto;
use Carbon\Carbon;
use FPDF;
use HasRoles;
use PDF;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use GuzzleHttp\Handler\Proxy;
use Illuminate\Database\QueryException;

use Spatie\Permission\Models\Role;
use Svg\Tag\Rect;

class AdminController extends Controller
{
    public function index(){

        return view('admin.index');
    }
    //Doctores

    public function doctor(Request $request)
    {
        $buscar = trim($request->get('buscar'));
        $perPage = $request->input('perPage', 5); // Por defecto 5 registros por página
        $veterinarios = User::where('nombre_completo', 'LIKE', '%' . $buscar . '%')
                            ->orWhere('email', 'LIKE', '%' . $buscar . '%')
                            ->paginate($perPage);
        
        return view('admin.doctores.doctor', ['veterinarios' => $veterinarios, 'buscar' => $buscar]);
    }

    public function add_veterinario(){
        $roles = Role::all();
        $veterinarios = User::all();
        return view('admin.doctores.add_doctor',[ 'veterinarios'=>$veterinarios, 'roles'=>$roles]);
    }

    public function store_veterinario(Request $request)
    {
        $request->validate([
            'nombre_completo' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$/',
            'email'=> 'required|email',
            'password' =>'required|confirmed',
            
        ], [

            'nombre_completo.required' => 'El campo Nombre veterinario es obligatorio.',
            'nombre_completo.regex' => 'El campo Nombre veterinario solo permite letras y espacios.',
            'email.required' => 'El campo Email es obligatorio.',
            'email.email' => 'El campo Email debe ser una dirección de correo electrónico válida.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.confirmed' => 'Las contraseñas no coinciden..',

        ]);
        
        // Crea el veterinario y asigna el ID del rol de administrador
            $veterinarios = new User();
            $veterinarios->nombre_completo = $request->input('nombre_completo');
            $veterinarios->email = $request->input('email');
            $veterinarios->password = $request->input('password');
        
            $veterinarios->save();
    
        // Redirigir a la lista de citas o a donde desees
        return redirect()->route('admin.doctores.doctor');
    }

    public function editar_veterinario(User $user){
        $roles = Role::all();
        return view('admin.doctores.edit_doctor',compact('user','roles'))->with('editar', 'ok');

    }

    public function update_veterinario(Request $request, User $user)
{
        $user->update([
        'nombre_completo' => $request->input('nombre_completo')
    ]);

    // Sincronizar roles
    $user->roles()->sync($request->input('roles', []));

    return redirect()->route('admin.doctores.doctor', $user)->with('actualizado', 'ok');
}

public function eliminarVeterinario(User $user)
{
    try {
        $user->delete();
        return redirect()->route('admin.doctores.doctor', ['id' => $user->id])->with('eliminar', 'ok');
    } catch (QueryException $e) {
        if ($e->getCode() == 23000) { // Código de error para violación de clave externa
            return redirect()->route('admin.doctores.doctor', ['id' => $user->id])->with('error', 'ok');
        }    
    }
}
    //propietarios
    public function propietario(Request $request)
{
    $buscar = trim($request->get('buscar'));
    $perPage = $request->get('perPage', 5);
    
    $veterinarios = User::all();
    
    $user = Auth::user();
    
    // Definir la consulta predeterminada
    $propietariosQuery = Propietario::query();
    
    // Ajustar la consulta según el rol del usuario
    if (!$user->hasRole('Administrador')) {
        $propietariosQuery = $propietariosQuery->where('id_veterinario', $user->id);
    }
    
    // Aplicar filtro de búsqueda si está presente
    if ($buscar) {
        $propietariosQuery->where(function($query) use ($buscar) {
            $query->where('nombre_completo', 'like', '%' . $buscar . '%')
                  ->orWhere('celular', 'like', '%' . $buscar . '%')
                  ->orWhere('direccion', 'like', '%' . $buscar . '%')
                  ->orWhereHas('veterinario', function($subQuery) use ($buscar) {
                      $subQuery->where('nombre_completo', 'like', '%' . $buscar . '%');
                  });
        });
    }

    // Paginar los resultados
    $propietarios = $propietariosQuery->paginate($perPage);
    
    // Devolver la vista con los datos
    return view('admin.registros.propietarios.propietario', [
        'propietarios' => $propietarios,
        'veterinarios' => $veterinarios,
        'buscar' => $buscar
    ]);
}


    public function add_propietario(){
        $veterinarios = User::all();
        $propietarios = Propietario::all();
        return view('admin.registros.propietarios.add_propietario',
        ['propietarios' => $propietarios, 'veterinarios' => $veterinarios]);
    }

    public function store_propietario(Request $request) {
        $request->validate([
            'nombre_completo' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$/',
            'celular' => 'required|digits:8|numeric|unique:propietarios,celular',
            'direccion' => 'required',
        ], [
            'nombre_completo.required' => 'El campo Nombre propietario es obligatorio.',
            'nombre_completo.regex' => 'El campo Nombre propietario solo permite letras y espacios.',
            'celular.required' => 'El campo Celular es obligatorio.',
            'celular.digits' => 'El campo Celular debe tener exactamente 8 dígitos.',
            'celular.numeric' => 'El campo Celular debe contener solo números.',
            'celular.unique' => 'El número de teléfono ya está registrado.',
            'direccion.required' => 'El campo Dirección es obligatorio.',
        ]);


        // Crear una nueva instancia de Medico y asignar valores
        $propietarios = new Propietario();
        $propietarios->nombre_completo = $request->input('nombre_completo');
        $propietarios->id_veterinario = $request->input('id_veterinario');
        $propietarios->celular = $request->input('celular');
        $propietarios->direccion = $request->input('direccion');
        
        $propietarios->save();

        // Redireccionar a la lista de doctores o a donde desees
        return redirect()->route('admin.registros.propietarios.propietario');
    }

    public function edit_propietario($id){
        $veterinarios = User::all();
        $propietario = Propietario::find($id);
        $propietarios = Propietario::all();

        return view('admin.registros.propietarios.edit_propietario', [
            'propietario' => $propietario,
            'propietarios' => $propietarios,
            'veterinarios' => $veterinarios,
        ]);
    }

    public function update_prop(Request $request, $id)
    {
    // Validación de datos
    $request->validate([
            'nombre_completo' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$/',
            'celular' => 'required|digits:8|numeric',
            'direccion' => 'required',
        ], [
            'nombre_completo.regex' => 'El campo Nombre propietario solo permite letras y espacios.',
            'nombre_completo.required' => 'El campo Nombre propietario es obligatorio.',
            'celular.digits' => 'El campo Celular debe tener exactamente 8 dígitos.',
            'celular.numeric' => 'El campo Celular debe contener solo números.',
            'celular.required' => 'El campo Celular es obligatorio.',
            'direccion.required' => 'El campo Dirección es obligatorio.',
        ]);


    // Obtener el propietario por ID
    $propietario = Propietario::find($id);

    // Actualizar los datos del propietario con los datos del formulario
    $propietario->nombre_completo = $request->input('nombre_completo');
    $propietario->id_veterinario = $request->input('id_veterinario');
    $propietario->celular = $request->input('celular');
    $propietario->direccion = $request->input('direccion');
    
    // Guardar los cambios
    $propietario->save();

    // Redireccionar a la página de edición o a donde desees
    return redirect()->route('admin.registros.propietarios.propietario', ['id' => $propietario->id])->with('actualizado', 'ok');
}

public function eliminarPropietario(Propietario $propietario)
    {
        try {
            $propietario->delete();
            return redirect()->route('admin.registros.propietarios.propietario', ['id' => $propietario->id])->with('eliminar', 'ok');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) { // Código de error para violación de clave externa
                return redirect()->route('admin.registros.propietarios.propietario', ['id' => $propietario->id])->with('error', 'ok');
            }  
                
            
        }
    }


//mascotas
public function mascota(Request $request)
{
    $buscar = $request->get('buscar');
    $veterinarios = User::all();
    $propietarios = Propietario::all();
    $perPage = $request->get('perPage', 5);
    $user = Auth::user();
    
    // Definir la consulta predeterminada
    $mascotasQuery = Mascota::query();
    
    // Ajustar la consulta según el rol del usuario
    if (!$user->hasRole('Administrador')) {
        $mascotasQuery->where('id_veterinario', $user->id);
    }
    
    // Aplicar filtro de búsqueda si está presente
    if (!empty($buscar)) {
        $mascotasQuery->where(function ($query) use ($buscar) {
            $query->where('nombre_mascota', 'like', "%$buscar%")
                ->orWhere('raza', 'like', "%$buscar%")
                ->orWhere('sexo', 'like', "%$buscar%")
                ->orWhere('peso', 'like', "%$buscar%")
                ->orWhere('color', 'like', "%$buscar%")
                ->orWhereHas('propietario', function ($query) use ($buscar) {
                    $query->where('nombre_completo', 'like', "%$buscar%");
                })
                ->orWhereHas('veterinario', function ($query) use ($buscar) {
                    $query->where('nombre_completo', 'like', "%$buscar%");
                });
        });
    }
    
    // Paginar los resultados
    $mascotas = $mascotasQuery->paginate($perPage);
    
    // Devolver la vista con los datos
    return view('admin.registros.mascotas.mascota', [
        'propietarios' => $propietarios,
        'mascotas' => $mascotas,
        'veterinarios' => $veterinarios,
        'buscar' => $buscar
    ]);
}
    

public function add_mascota(){
    $user = Auth::user();
    $veterinarios = User::all();
    
    if ($user->hasRole('Administrador')) {
        // Si el usuario es administrador, mostramos todos los propietarios
        $propietarios = Propietario::all();
    } else {
        // Si el usuario no es administrador, mostramos solo los propietarios relacionados con él
        $propietarios = Propietario::where('id_veterinario', $user->id)->get();
    }

    $mascotas = Mascota::all();
    return view('admin.registros.mascotas.add_mascota', compact('propietarios', 'mascotas', 'veterinarios'));
}
    public function store_mascota(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'id_propietario' => 'required|exists:propietarios,id',
            'nombre_mascota' => 'required|regex:/^[A-Za-z ]+$/',
            'raza' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$/',
            'sexo' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$/',
            'fecha_nacimiento' => 'required|date',
            'peso' => 'required|numeric|between:1,99',
            'color' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$/',
            'imagen' => 'required',
        ], [
            'nombre_mascota.regex' => 'El campo Nombre de la mascota solo permite letras y espacios.',
            'nombre_mascota.required' => 'El campo Nombre mascota es obligatorio.',
            'raza.regex' => 'El campo Raza solo permite letras y espacios.',
            'raza.required' => 'El campo Raza es obligatorio.',
            'sexo.regex' => 'El campo Sexo solo permite letras y espacios.',
            'sexo.required' => 'El campo Sexo es obligatorio.',
            'fecha_nacimiento.required' => 'El campo Fecha Nacimiento es obligatorio.',
            'peso.between' => 'El campo peso debe ser un número de 1 o 2 dígitos.',
            'peso.numeric' => 'El campo Peso debe contener solo números.',
            'peso.required' => 'El campo Peso es obligatorio.',
            'color.regex' => 'El campo Color solo permite letras y espacios.',
            'color.required' => 'El campo Color es obligatorio.',
            'imagen.required' => 'El campo Imagen es obligatorio.',
            
        ]);
    
        // Crear una instancia de Mascota
        $mascotas = new Mascota();
    
        // Asignar valores a las propiedades del modelo
        $mascotas->id_propietario = $request->input('id_propietario');
        $mascotas->id_veterinario = $request->input('id_veterinario');
        $mascotas->nombre_mascota = $request->input('nombre_mascota');
        $mascotas->raza = $request->input('raza');
        $mascotas->sexo = $request->input('sexo');
        $mascotas->fecha_nacimiento = $request->input('fecha_nacimiento');
        $mascotas->peso = $request->input('peso');
        $mascotas->color = $request->input('color');
    
        // Guardar la mascota en la base de dato
        
        // Manejo de la carga de la imagen
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            $nombreArchivo = $imagen->getClientOriginalName(); // Obtiene el nombre original del archivo
            $rutaImagen = $imagen->storeAs('public/imagenes', $nombreArchivo); // Guarda la imagen con su nombre original
            
            // Para mostrar solo el nombre de la imagen en lugar de toda la ruta, puedes hacer lo siguiente:
            $nombreImagen = basename($rutaImagen);
            
            $mascotas->imagen = $nombreImagen;
        }
    
        // Guardar la mascota en la base de datos
        $mascotas->save();
    
        // Redirigir a la lista de citas o a donde desees
        return redirect()->route('admin.registros.mascotas.mascota');
    }

    public function edit_mascota($id) {
        $user = Auth::user();
        $veterinarios = User::all();
        $mascota = Mascota::find($id);
    
        if ($user->hasRole('Veterinario')) {
            // Si el usuario es un veterinario, solo mostramos los propietarios relacionados con él
            $propietarios = Propietario::where('id_veterinario', $user->id)->get();
        } else {
            // Si no es un veterinario, mostramos todos los propietarios
            $propietarios = Propietario::all();
        }
        
        // Pasar los datos a la vista de edición
        return view('admin.registros.mascotas.edit_mascota', compact('propietarios', 'mascota', 'veterinarios'));
    }
    
   public function update_mascota(Request $request, $id) {
        // Validar los datos del formulario
        $request->validate([
            'id_propietario' => 'required|exists:propietarios,id',
            'nombre_mascota' => 'required|regex:/^[A-Za-z ]+$/',
            'raza' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$/',
            'sexo' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$/',
            'fecha_nacimiento' => 'required|date',
            'peso' => 'required|numeric|between:1,99',
            'color' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$/',
        ], [
            'nombre_mascota.regex' => 'El campo Nombre de la mascota solo permite letras y espacios.',
            'nombre_mascota.required' => 'El campo Nombre mascota es obligatorio.',
            'raza.regex' => 'El campo Raza solo permite letras y espacios.',
            'raza.required' => 'El campo Raza es obligatorio.',
            'sexo.regex' => 'El campo Sexo solo permite letras y espacios.',
            'sexo.required' => 'El campo Sexo es obligatorio.',
            'fecha_nacimiento.required' => 'El campo Fecha Nacimiento es obligatorio.',
            'peso.between' => 'El campo peso debe ser un número de 1 o 2 dígitos.',
            'peso.numeric' => 'El campo Peso debe contener solo números.',
            'peso.required' => 'El campo Peso es obligatorio.',
            'color.regex' => 'El campo Color solo permite letras y espacios.',
            'color.required' => 'El campo Color es obligatorio.',
            
        ]);
    
        // Buscar la mascota por ID
        $mascota = Mascota::find($id);
    
        // Actualizar los datos de la mascota con los datos del formulario
        $mascota->id_propietario = $request->input('id_propietario');
        $mascota->id_veterinario = $request->input('id_veterinario');
        $mascota->nombre_mascota = $request->input('nombre_mascota');
        $mascota->raza = $request->input('raza');
        $mascota->sexo = $request->input('sexo');
        $mascota->fecha_nacimiento = $request->input('fecha_nacimiento');
        $mascota->peso = $request->input('peso');
        $mascota->color = $request->input('color');
   
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            $nombreArchivo = $imagen->getClientOriginalName(); // Obtiene el nombre original del archivo
            $rutaImagen = $imagen->storeAs('public/imagenes', $nombreArchivo); // Guarda la imagen con su nombre original
            
            $nombreImagen = basename($rutaImagen);
            
            $mascota->imagen = $nombreImagen;
        }
        $mascota->save();
    
        return redirect()->route('admin.registros.mascotas.mascota', ['id' => $mascota->id])->with('actualizado', 'ok');
    }

    public function eliminarMascota(Mascota $mascota)
    {
        try {
            $mascota->delete();
            return redirect()->route('admin.registros.mascotas.mascota', ['id' => $mascota->id])->with('eliminar', 'ok');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->route('admin.registros.mascotas.mascota', ['id' => $mascota->id])->with('error', 'ok');
            }  
                
            
        }
    }
    
    public function cardex_pdf($id)
{
    $mascota = Mascota::with('propietario')->find($id);
    $pdf = new FPDF('P', 'mm', array(95, 120));
    $pdf->AddPage();

    // Agregar imagen de fondo
    $pdf->Image('assetsadmin/img/marca1.png', 0, 0, 95, 120); // Reemplazar 'path_to_background_image.jpg' con la ruta correcta de tu imagen

    $pdf->SetTitle('Kardex');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFont('Arial', 'U', 11);
    $pdf->Cell(0, 5, 'KARDEX', 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 10, utf8_decode($mascota->nombre_mascota), 0, 1, 'C');
    
    // Agregar imagen de la mascota si existe
    if (!empty($mascota->imagen)) {
        $ruta_imagen = storage_path('app/public/imagenes/' . $mascota->imagen);
        if (file_exists($ruta_imagen)) {
            $pdf->Image($ruta_imagen, 26, 23, 40, 40);
        }
    }
    $pdf->Ln(43);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(20, 6, 'Propietario:', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(20, 6, utf8_decode($mascota->propietario->nombre_completo), 0);
    $pdf->Ln(6);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 6, 'Raza:', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(30, 6, utf8_decode($mascota->raza), 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(21, 6, 'Nacimiento:', 0);
    $pdf->SetFont('Arial', '', 10);
    $fecha_nacimiento = date('d-m-Y', strtotime($mascota->fecha_nacimiento));
    $pdf->Cell(31, 6, $fecha_nacimiento, 0);
    $pdf->Ln(6);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 6, 'Sexo:', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(30, 6, utf8_decode($mascota->sexo), 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(12, 6, 'Color:', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(30, 6, utf8_decode($mascota->color), 0);
    $pdf->Ln(6);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(19, 6, 'Peso (Kg):', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(30, 6, $mascota->peso, 0);
    $pdf->Ln(6);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(20, 6, 'Veterinario:', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(20, 6, utf8_decode($mascota->veterinario->nombre_completo), 0);
    $output = $pdf->Output('', 'S');

    return new Response($output, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename=Kardex.pdf',
    ]);
}


     //Consultas
     public function consulta(Request $request)
{
    $buscar = $request->get('buscar');
    $veterinarios = User::all();
    $mascotas = Mascota::all();
    $propietarios = Propietario::all();
    $perPage = $request->get('perPage', 5);
    $user = Auth::user();
    $consultasQuery = Consulta::query();
    
    if ($user->hasRole('Administrador')) {
        $consultasQuery->with(['mascota', 'mascota.propietario', 'veterinario']);
    } else {
        $consultasQuery->where('id_veterinario', $user->id)
                       ->with(['mascota', 'mascota.propietario', 'veterinario']);
    }

    if ($buscar) {
        $consultasQuery->where(function($query) use ($buscar) {
            $query->whereHas('mascota', function($subQuery) use ($buscar) {
                $subQuery->where('nombre_mascota', 'like', '%' . $buscar . '%');
            })
            ->orWhereHas('mascota.propietario', function($subQuery) use ($buscar) {
                $subQuery->where('nombre_completo', 'like', '%' . $buscar . '%');
            })
            ->orWhereHas('veterinario', function($subQuery) use ($buscar) {
                $subQuery->where('nombre_completo', 'like', '%' . $buscar . '%');
            });
        });
    }

    $consultas = $consultasQuery->paginate($perPage);
    
    return view('admin.registros.consultas.consulta', [
        'veterinarios' => $veterinarios, 
        'mascotas' => $mascotas,
        'propietarios' => $propietarios,
        'consultas' => $consultas,
        'buscar' => $buscar,
    ]);
}
   
public function add_consulta(){
    $user = Auth::user();
    $veterinarios = User::all();
    
    if ($user->hasRole('Administrador')) {
        // Si el usuario es administrador, mostramos todos los propietarios y mascotas
        $propietarios = Propietario::all();
        $mascotas = Mascota::all();
    } else {
        // Si el usuario no es administrador, mostramos solo los propietarios relacionados con él
        $propietarios = Propietario::where('id_veterinario', $user->id)->get();
        $mascotas = Mascota::whereIn('id_propietario', $propietarios->pluck('id'))->get();
    }

    $consultas = Consulta::all();
    
    return view('admin.registros.consultas.add_consulta', compact('veterinarios', 'mascotas', 'propietarios', 'consultas'));
}

    public function store_consulta(Request $request){
        // Validar los datos del formulario
        $request->validate([
            'id_mascota' => 'required|exists:mascotas,id',
            'id_propietario' => 'required|exists:propietarios,id',
            'id_veterinario' => 'required|exists:veterinarios,id',
            'fecha_consulta' => 'required|date',
            'fecha_cita' => 'required|date|after_or_equal:fecha_consulta',
            'temperatura' => 'required|numeric|digits:2',
            'peso' => 'required|numeric|between:1,99',
            'fre_cardiaca' => 'required|numeric|between:10,999',
            'fre_respiratoria' => 'required|numeric|between:10,999',
            'dolores_localizados' => 'required',
            'diagnostico' => 'required',
        ], [
            'id_mascota.required' => 'El campo Mascota es obligatorio.',
            'id_mascota.exists' => 'La mascota seleccionada no existe en nuestros registros.',
            'id_propietario.required' => 'El campo Propietario es obligatorio.',
            'id_propietario.exists' => 'El propietario seleccionado no existe en nuestros registros.',
            'id_veterinario.required' => 'El campo Veterinario es obligatorio.',
            'id_veterinario.exists' => 'El veterinario seleccionado no existe en nuestros registros.',
            'fecha_consulta.required' => 'El campo Fecha de Consulta es obligatorio.',
            'fecha_consulta.date' => 'El campo Fecha de Consulta debe ser una fecha válida.',
            'fecha_cita.required' => 'El campo Fecha de Cita es obligatorio.',
            'fecha_cita.date' => 'El campo Fecha de Cita debe ser una fecha válida.',
            'fecha_cita.after_or_equal' => 'La Fecha de Cita debe ser igual o posterior a la Fecha de Consulta.',
            'temperatura.required' => 'El campo Temperatura es obligatorio.',
            'temperatura.numeric' => 'El campo Temperatura debe contener solo números.',
            'temperatura.digits' => 'El campo Temperatura debe tener exactamente :digits dígitos.',
            'peso.required' => 'El campo Peso es obligatorio.',
            'peso.numeric' => 'El campo Peso debe contener solo números.',
            'peso.between' => 'El campo Peso debe estar entre :min y :max.',
            'fre_cardiaca.required' => 'El campo Frecuencia Cardíaca es obligatorio.',
            'fre_cardiaca.numeric' => 'El campo Frecuencia Cardíaca debe contener solo números.',
            'fre_cardiaca.between' => 'El campo Frecuencia Cardíaca debe estar entre :min y :max.',
            'fre_respiratoria.required' => 'El campo Frecuencia Respiratoria es obligatorio.',
            'fre_respiratoria.numeric' => 'El campo Frecuencia Respiratoria debe contener solo números.',
            'fre_respiratoria.between' => 'El campo Frecuencia Respiratoria debe estar entre :min y :max.',
            'dolores_localizados.required' => 'El campo Dolores Localizados es obligatorio.',
            'diagnostico.required' => 'El campo Diagnóstico es obligatorio.',
        ]);
    
        $consultas = new Consulta();
        $consultas->id_mascota = $request->input('id_mascota');
        $consultas->id_propietario = $request->input('id_propietario');
        $consultas->id_veterinario = $request->input('id_veterinario');
        $consultas->fecha_consulta = $request->date('fecha_consulta');
        $consultas->fecha_cita = $request->date('fecha_cita');
        $consultas->temperatura = $request->input('temperatura');
        $consultas->peso = $request->input('peso');
        $consultas->fre_cardiaca = $request->input('fre_cardiaca');
        $consultas->fre_respiratoria = $request->input('fre_respiratoria');
        $consultas->dolores_localizados = $request->input('dolores_localizados');
        $consultas->diagnostico = $request->input('diagnostico');

        // Guardar la consultas en la base de datos
        $consultas->save();

        // Redirigir a la lista de citas o a donde desees
        return redirect()->route('admin.registros.consultas.consulta');
    }

    public function edit_consulta($id){
        $consulta = Consulta::find($id);
        $user = Auth::user();
        
        // Si el usuario es un veterinario, filtramos los propietarios y mascotas
        if ($user->hasRole('Veterinario')) {
            $propietarios = Propietario::where('id_veterinario', $user->id)->get();
            $mascotas = Mascota::whereIn('id_propietario', $propietarios->pluck('id'))->get();
        } else {
            // Si el usuario es un administrador, mostramos todos los propietarios y mascotas
            $propietarios = Propietario::all();
            $mascotas = Mascota::all();
        }
    
        $veterinarios = User::all();
        
        // Pasar los datos a la vista de edición
        return view('admin.registros.consultas.edit_consulta', compact('veterinarios', 'mascotas', 'propietarios', 'consulta'));
    }

    public function update_consulta(Request $request, $id) {
        // Validar los datos del formulario
        $request->validate([
            'id_mascota' => 'required|exists:mascotas,id',
            'id_propietario' => 'required|exists:propietarios,id',
            'id_veterinario' => 'required|exists:veterinarios,id',
            'fecha_consulta' => 'required|date',
            'fecha_cita' => 'required|date|after_or_equal:fecha_consulta',
            'temperatura' => 'required|numeric|digits:2',
            'peso' => 'required|numeric|between:1,99',
            'fre_cardiaca' => 'required|numeric|between:10,999',
            'fre_respiratoria' => 'required|numeric|between:10,999',
            'dolores_localizados' => 'required',
            'diagnostico' => 'required',
        ], [
            'id_mascota.required' => 'El campo Mascota es obligatorio.',
            'id_mascota.exists' => 'La mascota seleccionada no existe en nuestros registros.',
            'id_propietario.required' => 'El campo Propietario es obligatorio.',
            'id_propietario.exists' => 'El propietario seleccionado no existe en nuestros registros.',
            'id_veterinario.required' => 'El campo Veterinario es obligatorio.',
            'id_veterinario.exists' => 'El veterinario seleccionado no existe en nuestros registros.',
            'fecha_consulta.required' => 'El campo Fecha de Consulta es obligatorio.',
            'fecha_consulta.date' => 'El campo Fecha de Consulta debe ser una fecha válida.',
            'fecha_cita.required' => 'El campo Fecha de Cita es obligatorio.',
            'fecha_cita.date' => 'El campo Fecha de Cita debe ser una fecha válida.',
            'fecha_cita.after_or_equal' => 'La Fecha de Cita debe ser igual o posterior a la Fecha de Consulta.',
            'temperatura.required' => 'El campo Temperatura es obligatorio.',
            'temperatura.numeric' => 'El campo Temperatura debe contener solo números.',
            'temperatura.digits' => 'El campo Temperatura debe tener exactamente :digits dígitos.',
            'peso.required' => 'El campo Peso es obligatorio.',
            'peso.numeric' => 'El campo Peso debe contener solo números.',
            'peso.between' => 'El campo Peso debe estar entre :min y :max.',
            'fre_cardiaca.required' => 'El campo Frecuencia Cardíaca es obligatorio.',
            'fre_cardiaca.numeric' => 'El campo Frecuencia Cardíaca debe contener solo números.',
            'fre_cardiaca.between' => 'El campo Frecuencia Cardíaca debe estar entre :min y :max.',
            'fre_respiratoria.required' => 'El campo Frecuencia Respiratoria es obligatorio.',
            'fre_respiratoria.numeric' => 'El campo Frecuencia Respiratoria debe contener solo números.',
            'fre_respiratoria.between' => 'El campo Frecuencia Respiratoria debe estar entre :min y :max.',
            'dolores_localizados.required' => 'El campo Dolores Localizados es obligatorio.',
            'diagnostico.required' => 'El campo Diagnóstico es obligatorio.',
        ]);

    
        // Buscar la consulta por ID
        $consulta = Consulta::find($id);
    
        // Actualizar los datos de la mascota con los datos del formulario
        $consulta->id_mascota = $request->input('id_mascota');
        $consulta->id_propietario = $request->input('id_propietario');
        $consulta->id_veterinario = $request->input('id_veterinario');
        $consulta->fecha_consulta = $request->date('fecha_consulta');
        $consulta->fecha_cita = $request->date('fecha_cita');
        $consulta->temperatura = $request->input('temperatura');
        $consulta->peso = $request->input('peso');
        $consulta->fre_cardiaca = $request->input('fre_cardiaca');
        $consulta->fre_respiratoria = $request->input('fre_respiratoria');
        $consulta->dolores_localizados = $request->input('dolores_localizados');
        $consulta->diagnostico = $request->input('diagnostico');
        // Actualiza otros campos según tus necesidades
    
        // Guardar los cambios
        $consulta->save();
    
        // Redirigir a la página de listado de mascotas con un mensaje de éxito
        return redirect()->route('admin.registros.consultas.consulta', ['id' => $consulta->id])->with('actualizado', 'ok');
    }

    public function consulta_pdf($id)
{
    date_default_timezone_set('America/La_Paz');

    $consulta = Consulta::with('mascota', 'veterinario')->find($id);
    $pdf = new FPDF('P', 'mm', 'letter');
    $pdf->SetMargins(20, 20, 20);
    $pdf->AddPage();
    $pdf->SetTitle('Reporte de Consulta');
    $pdf->Image('assetsadmin/img/marca.jpg', 0, 0, 215.9, 279.4);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Image('assetsadmin/img/logo.jpg', 22, 15, 40);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetY($pdf->GetY() - 10); // Ajusta esta línea para mover hacia arriba el texto
    $pdf->Cell(130,0); // Mueve el cursor de la celda hacia la derecha
    $pdf->Cell(25, 0, utf8_decode('Dirección:'), 0, 0, 'R'); // Texto "Dirección:" alineado a la derecha
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(23, 0, utf8_decode('Calle Tumusla'), 0, 1, 'R'); // Texto "Calle Tumusla" alineado a la derecha
    $pdf->Ln(2);
    function fecha() {
        $meses = array(
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre'
        );
    
        $dia = date('d'); // Día actual
        $mes = $meses[intval(date('m'))]; // Mes actual en literal
        $anio = date('Y'); // Año actual
    
        return $dia . ' de ' . $mes . ' de ' . $anio;
    }
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(136);
    $pdf->Cell(47, 3, 'Fecha: ', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(7, 3, fecha(), 0, 1, 'R'); // Fecha actual
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(136);
    $pdf->Cell(19, 2, 'Hora: ',0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(2,2, date('H:i'), 0, 1, 'R'); // Hora actua
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(136);
    $pdf->Cell(30, 3, utf8_decode('Celular:'),0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(2,3, ('74530647'), 0, 1, 'R');
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(157);
    $pdf->Cell(2, 3, 'TUPIZA', 0, 1, 'R');

    $pdf->Ln(8);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, 'CONSULTA', 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(43, 10, 'Numero de consulta:', 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(75, 10, utf8_decode($consulta->id), 0);
    $pdf->SetFont('Arial', 'B', 12); 
    $pdf->Cell(33, 10, 'Fecha consulta:', 0);
    $pdf->SetFont('Arial', '', 12);
    $fecha_consulta = date('d-m-Y', strtotime($consulta->fecha_consulta));
    $pdf->Cell(0, 10, $fecha_consulta, 0);
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(43, 23, 'Nombre propietario:', 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(75, 23, utf8_decode($consulta->propietario->nombre_completo), 0);
    $pdf->SetFont('Arial', 'B', 12); 
    $pdf->Cell(28, 23, 'Proxima cita:', 0);
    $pdf->SetFont('Arial', '', 12);
    $fecha_cita = date('d-m-Y', strtotime($consulta->fecha_cita));
    $pdf->Cell(0, 23, $fecha_cita, 0);
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(37, 36, 'Nombre mascota:', 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 36, utf8_decode($consulta->mascota->nombre_mascota), 0);
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(35, 49, 'Nombre medico:', 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 49, utf8_decode($consulta->veterinario->nombre_completo), 0);
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(37, 62, utf8_decode('Temperatura (°C):'), 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(82, 62, utf8_decode($consulta->temperatura), 0);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(23, 62, utf8_decode('Peso (Kg): '), 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 62, utf8_decode($consulta->peso), 0);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(52, 75, 'Frecuencia cardiaca (Fc):', 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(67, 75, utf8_decode($consulta->fre_cardiaca), 0);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 75, 'Frecuencia respiratoria (Fr):', 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 75, utf8_decode($consulta->fre_respiratoria), 0);
    $pdf->Ln(45);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 6, 'Dolores localizados:', 0, 1);
    $pdf->Ln(1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 5, utf8_decode($consulta->dolores_localizados), 0, 'L');
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode('Diagnóstico:'), 0, 1);
    $pdf->Ln(1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 5, utf8_decode($consulta->diagnostico), 0, 'L');

    // Generar salida del PDF
    $output = $pdf->Output('', 'S');
    
    return new Response($output, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename=consulta.pdf',
    ]);
}

    public function eliminarConsulta(Consulta $consulta)
    {
        $consulta->delete();
        return redirect()->route('admin.registros.consultas.consulta', ['id' => $consulta->id])->with('eliminar', 'ok');
    }

    public function ReporteFechasConsultas(Request $request)
{
    date_default_timezone_set('America/La_Paz');
    $request->validate([
        'desde' => 'required|date',
        'hasta' => 'required|date|after_or_equal:desde',
    ]);

    $startDate = $request->input('desde');
    $endDate = $request->input('hasta');
    
    $consultas = [];
    $user = Auth::user();
    if ($user->hasRole('Administrador')) {
        $consultas = Consulta::whereBetween('fecha_consulta', [$startDate, $endDate])->get();
    } elseif ($user->hasRole('Veterinario')) {
        $consultas = Consulta::where('id_veterinario', $user->id)
                            ->whereBetween('fecha_consulta', [$startDate, $endDate])
                            ->get();
    }

    $pdf = new FPDF('P', 'mm', 'letter');
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();
    $pdf->SetTitle('Reporte de Consulta');
    $pdf->Image('assetsadmin/img/logo.jpg',10, 10, 50);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetY($pdf->GetY() - 5); // Ajusta esta línea para mover hacia arriba el texto
    $pdf->Cell(145,0); // Mueve el cursor de la celda hacia la derecha
    $pdf->Cell(20, 0, utf8_decode('Dirección:'), 0, 0, 'R'); // Texto "Dirección:" alineado a la derecha
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(23, 0, utf8_decode('Calle Tumusla'), 0, 1, 'R'); // Texto "Calle Tumusla" alineado a la derecha
    $pdf->Ln(2);
    function obtenerFechaEnEspanol() {
        $meses = array(
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre'
        );
    
        $dia = date('d'); // Día actual
        $mes = $meses[intval(date('m'))]; // Mes actual en literal
        $anio = date('Y'); // Año actual
    
        return $dia . ' de ' . $mes . ' de ' . $anio;
    }
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(146);
    $pdf->Cell(47, 3, 'Fecha: ', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(7, 3, obtenerFechaEnEspanol(), 0, 1, 'R'); // Fecha actual con mes literal
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(146);
    $pdf->Cell(19, 2, 'Hora: ',0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(2,2, date('H:i'), 0, 1, 'R'); // Hora actua
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(146);
    $pdf->Cell(30, 3, utf8_decode('Celular:'),0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(2,3, ('74530647'), 0, 1, 'R');
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(169);
    $pdf->Cell(2, 3, 'TUPIZA', 0, 1, 'R');
    
    $pdf->Ln(15);

    // Encabezados de la tabla
    function fechaLiteral($fecha) {
        $meses = array(
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre'
        );
        
        $dia = date('d', strtotime($fecha));
        $mes = $meses[intval(date('m', strtotime($fecha)))];
        $anio = date('Y', strtotime($fecha));
        
        return $dia . ' de ' . $mes . ' de ' . $anio;
    }
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Reporte de Consultas desde '.fechaLiteral($startDate).' hasta '.fechaLiteral($endDate), 0, 1, 'C');
     
    $pdf->Ln(5);
    $pdf->SetFillColor(200, 220, 255);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 10, utf8_decode('Nº'), 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Nombre Mascota', 1, 0, 'C', true);
    $pdf->Cell(45, 10, 'Nombre Propietario', 1, 0, 'C', true);
    $pdf->Cell(45, 10, 'Veterinario', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Fecha Consulta', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Proxima cita', 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);

    $contador = 1;
    foreach ($consultas as $consulta) {
        $fechaConsulta = date('d-m-Y', strtotime($consulta->fecha_consulta));
        $fechaCita = date('d-m-Y', strtotime($consulta->fecha_cita));

        $pdf->Cell(10, 10, $contador, 1, 0, 'C');
        $pdf->Cell(40, 10, utf8_decode($consulta->mascota->nombre_mascota), 1, 0, 'C');
        $pdf->Cell(45, 10, utf8_decode($consulta->propietario->nombre_completo), 1, 0, 'C');
        $pdf->Cell(45, 10, utf8_decode($consulta->veterinario->nombre_completo), 1, 0, 'C');
        $pdf->Cell(30, 10, $fechaConsulta, 1, 0, 'C');
        $pdf->Cell(30, 10, $fechaCita, 1, 1, 'C');

        $contador++;
    }

    $pdf->Ln(1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Cantidad de Consultas: ' . count($consultas), 0, 1, '');
    $output = $pdf->Output('', 'S');
    return new Response($output, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename=reporte_consulta.pdf',
    ]);
}
    //cirugias

    public function cirugia(Request $request)
    {
        $buscar = $request->get('buscar');
        $veterinarios = User::all();
        $mascotas = Mascota::all();
        $propietarios = Propietario::all();
        $perPage = $request->get('perPage', 5);
        $user = Auth::user();
        $cirugiasQuery = Cirugia::query();
        
        if ($user->hasRole('Administrador')) {
            $cirugiasQuery->with(['mascota', 'mascota.propietario', 'veterinario']);
        } else {
            $cirugiasQuery->where('id_veterinario', $user->id)
                          ->with(['mascota', 'mascota.propietario', 'veterinario']);
        }
    
        if ($buscar) {
            $cirugiasQuery->where(function($query) use ($buscar) {
                $query->whereHas('mascota', function($subQuery) use ($buscar) {
                    $subQuery->where('nombre_mascota', 'like', '%' . $buscar . '%');
                })
                ->orWhereHas('mascota.propietario', function($subQuery) use ($buscar) {
                    $subQuery->where('nombre_completo', 'like', '%' . $buscar . '%');
                })
                ->orWhereHas('veterinario', function($subQuery) use ($buscar) {
                    $subQuery->where('nombre_completo', 'like', '%' . $buscar . '%');
                })
                ->orWhere('tipo_cirugia', 'like', '%' . $buscar . '%');
            });
        }
        
        $cirugias = $cirugiasQuery->paginate($perPage);
        
        return view('admin.registros.cirugias.cirugia', [
            'veterinarios' => $veterinarios, 
            'mascotas' => $mascotas,
            'propietarios' => $propietarios,
            'cirugias' => $cirugias,
            'buscar' => $buscar
        ]);
    }

    public function add_cirugia(){
        $user = Auth::user();
        $veterinarios = User::all();
        
        if ($user->hasRole('Administrador')) {
            // Si el usuario es administrador, mostramos todos los propietarios y mascotas
            $propietarios = Propietario::all();
            $mascotas = Mascota::all();
        } else {
            // Si el usuario no es administrador, mostramos solo los propietarios relacionados con él
            $propietarios = Propietario::where('id_veterinario', $user->id)->get();
            $mascotas = Mascota::whereIn('id_propietario', $propietarios->pluck('id'))->get();
        }
    
        $cirugias = Cirugia::all();
        
        return view('admin.registros.cirugias.add_cirugia', compact('veterinarios', 'propietarios', 'mascotas', 'cirugias'));
    }

    public function store_cirugia(Request $request){
        // Validar los datos del formulario
        $request->validate([
            'id_mascota' => 'required|exists:mascotas,id',
            'id_propietario' => 'required|exists:propietarios,id',
            'id_veterinario' => 'required|exists:veterinarios,id',
            'fecha_cirugia' => 'required|date',
            'fecha_cita' => 'required|date|after_or_equal:fecha_cirugia',
            'temperatura' => 'required|numeric|digits:2',
            'fre_cardiaca' => 'required|numeric|between:10,999',
            'fre_respiratoria' => 'required|numeric|between:10,999',
            'peso' => 'required|numeric|between:1,99',
            'tipo_cirugia' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$/',
        ], [
            'fecha_consulta.required' => 'El campo Fecha es obligatorio',
            'fecha_cita.required' => 'El campo Fecha de Cita es obligatorio.',
            'fecha_cita.date' => 'El campo Fecha de Cita debe ser una fecha válida.',
            'fecha_cita.after_or_equal' => 'La Fecha de Cita debe ser igual o posterior a la Fecha de Consulta.',
            'temperatura.digits' => 'El campo temperatura debe tener 2 dígitos.',
            'temperatura.numeric' => 'El campo temperatura debe contener solo números.',
            'temperatura.required' => 'El campo temperatura es obligatorio.',
            'fre_cardiaca.between' => 'El campo frecuencia cardiaca debe tener 2 o 3 dígitos.',
            'fre_cardiaca.numeric' => 'El campo frecuencia ardiaca debe contener solo números.',
            'fre_cardiaca.required' => 'El campo frecuencia cardiaca es obligatorio.',
            'fre_respiratoria.between' => 'El campo frecuencia respiratoria debe tener 2 o 3 dígitos.',
            'fre_respiratoria.numeric' => 'El campo frecuencia respiratoria debe contener solo números.',
            'fre_respiratoria.required' => 'El campo frecuencia respiratoria es obligatorio.',
            'peso.between' => 'El campo peso debe tener 1 o 2 dígitos.',
            'peso.numeric' => 'El campo peso debe contener solo números.',
            'peso.required' => 'El campo peso es obligatorio.',
            'tipo_cirugia.regex' => 'El campo tipo cirugia solo permite letras y espacios.',
            'tipo_cirugia.required' => 'El campo tipo cirugia es obligatorio.',
        ]);

    
        $cirugias = new Cirugia();
        $cirugias->id_mascota = $request->input('id_mascota');
        $cirugias->id_propietario = $request->input('id_propietario');
        $cirugias->id_veterinario = $request->input('id_veterinario');
        $cirugias->fecha_cirugia = $request->date('fecha_cirugia');
        $cirugias->fecha_cita = $request->date('fecha_cita');
        $cirugias->temperatura = $request->input('temperatura');
        $cirugias->fre_cardiaca = $request->input('fre_cardiaca');
        $cirugias->fre_respiratoria = $request->input('fre_respiratoria');
        $cirugias->peso = $request->input('peso');
        $cirugias->tipo_cirugia = $request->input('tipo_cirugia');

        // Guardar la cirugias en la base de datos
        $cirugias->save();

        // Redirigir a la lista de citas o a donde desees
        return redirect()->route('admin.registros.cirugias.cirugia');
    }

    public function edit_cirugia($id){
        $user = Auth::user();
        $cirugia = Cirugia::find($id);
        
        // Si el usuario es un veterinario, filtramos los propietarios y mascotas
        if ($user->hasRole('Veterinario')) {
            $propietarios = Propietario::where('id_veterinario', $user->id)->get();
            $mascotas = Mascota::whereIn('id_propietario', $propietarios->pluck('id'))->get();
        } else {
            // Si el usuario es un administrador, mostramos todos los propietarios y mascotas
            $propietarios = Propietario::all();
            $mascotas = Mascota::all();
        }
    
        $veterinarios = User::all();
        
        // Pasar los datos a la vista de edición
        return view('admin.registros.cirugias.edit_cirugia', compact('veterinarios', 'mascotas', 'propietarios', 'cirugia'));
    }

    public function update_cirugia(Request $request, $id) {
        // Validar los datos del formulario
        $request->validate([
            'id_mascota' => 'required|exists:mascotas,id',
            'id_propietario' => 'required|exists:propietarios,id',
            'id_veterinario' => 'required|exists:veterinarios,id',
            'fecha_cirugia' => 'required|date',
            'fecha_cita' => 'required|date|after_or_equal:fecha_cirugia',
            'temperatura' => 'required|numeric|digits:2',
            'fre_cardiaca' => 'required|numeric|between:10,999',
            'fre_respiratoria' => 'required|numeric|between:10,999',
            'peso' => 'required|numeric|between:1,99',
            'tipo_cirugia' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$/',
        ], [
            'fecha_consulta.required' => 'El campo Fecha es obligatorio',
            'fecha_cita.required' => 'El campo Fecha de Cita es obligatorio.',
            'fecha_cita.date' => 'El campo Fecha de Cita debe ser una fecha válida.',
            'fecha_cita.after_or_equal' => 'La Fecha de Cita debe ser igual o posterior a la Fecha de Consulta.',
            'temperatura.digits' => 'El campo temperatura debe tener 2 dígitos.',
            'temperatura.numeric' => 'El campo temperatura debe contener solo números.',
            'temperatura.required' => 'El campo temperatura es obligatorio.',
            'fre_cardiaca.between' => 'El campo frecuencia cardiaca debe tener 2 o 3 dígitos.',
            'fre_cardiaca.numeric' => 'El campo frecuencia ardiaca debe contener solo números.',
            'fre_cardiaca.required' => 'El campo frecuencia cardiaca es obligatorio.',
            'fre_respiratoria.between' => 'El campo frecuencia respiratoria debe tener 2 o 3 dígitos.',
            'fre_respiratoria.numeric' => 'El campo frecuencia respiratoria debe contener solo números.',
            'fre_respiratoria.required' => 'El campo frecuencia respiratoria es obligatorio.',
            'peso.between' => 'El campo peso debe tener 1 o 2 dígitos.',
            'peso.numeric' => 'El campo peso debe contener solo números.',
            'peso.required' => 'El campo peso es obligatorio.',
            'tipo_cirugia.regex' => 'El campo tipo cirugia solo permite letras y espacios.',
            'tipo_cirugia.required' => 'El campo tipo cirugia es obligatorio.',
        ]);
    
        // Buscar la consulta por ID
        $cirugia = Cirugia::find($id);
        
        // Actualizar los datos de la mascota con los datos del formulario
        $cirugia->id_mascota = $request->input('id_mascota');
        $cirugia->id_propietario = $request->input('id_propietario');
        $cirugia->id_veterinario = $request->input('id_veterinario');
        $cirugia->fecha_cirugia = $request->date('fecha_cirugia');
        $cirugia->fecha_cita = $request->date('fecha_cita');
        $cirugia->temperatura = $request->input('temperatura');
        $cirugia->fre_cardiaca = $request->input('fre_cardiaca');
        $cirugia->fre_respiratoria = $request->input('fre_respiratoria');
        $cirugia->peso = $request->input('peso');
        $cirugia->tipo_cirugia = $request->input('tipo_cirugia');
       
    
        // Guardar los cambios
        $cirugia->save();
    
        // Redirigir a la página de listado de mascotas con un mensaje de éxito
        return redirect()->route('admin.registros.cirugias.cirugia', ['id' => $cirugia->id])->with('actualizado', 'ok');
    }
    
    public function cirugia_pdf($id)
{
    date_default_timezone_set('America/La_Paz');
    $cirugia = Cirugia::with('mascota', 'veterinario')->find($id);

    // Inicializar el PDF
    $pdf = new FPDF('P', 'mm', 'letter');
    $pdf->SetMargins(20, 20, 20);
    $pdf->AddPage();
    $pdf->SetTitle('Reporte de Cirugia');
    $pdf->Image('assetsadmin/img/marca.jpg', 0, 0, 215.9, 279.4);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Image('assetsadmin/img/logo.jpg',22, 15, 40);
    $pdf->Ln(3);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetY($pdf->GetY() - 10); // Ajusta esta línea para mover hacia arriba el texto
    $pdf->Cell(130,0); // Mueve el cursor de la celda hacia la derecha
    $pdf->Cell(25, 0, utf8_decode('Dirección:'), 0, 0, 'R'); // Texto "Dirección:" alineado a la derecha
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(23, 0, utf8_decode('Calle Tumusla'), 0, 1, 'R'); // Texto "Calle Tumusla" alineado a la derecha
    $pdf->Ln(2);
    function fecha1() {
        $meses = array(
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre'
        );
    
        $dia = date('d'); // Día actual
        $mes = $meses[intval(date('m'))]; // Mes actual en literal
        $anio = date('Y'); // Año actual
    
        return $dia . ' de ' . $mes . ' de ' . $anio;
    }
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(136);
    $pdf->Cell(47, 3, 'Fecha: ', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(7, 3, fecha1(), 0, 1, 'R'); // Fecha actual
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(136);
    $pdf->Cell(19, 2, 'Hora: ',0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(2,2, date('H:i'), 0, 1, 'R'); // Hora actua
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(136);
    $pdf->Cell(30, 3, utf8_decode('Celular:'),0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(2,3, ('74530647'), 0, 1, 'R');
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(157);
    $pdf->Cell(2, 3, 'TUPIZA', 0, 1, 'R');
    // Información de la consulta
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'CIRUGIA', 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Numero de cirugia:',0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(75,10, utf8_decode($cirugia->id),0);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(29,10, 'Fecha cirugia:',0);
    $pdf->SetFont('Arial', '', 12);
    $fecha_cirugia = date('d-m-Y', strtotime($cirugia->fecha_cirugia));
    $pdf->Cell(0, 10, $fecha_cirugia, 0);
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(42, 25, 'Nombre propietario:',0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(73,25, utf8_decode( $cirugia->propietario->nombre_completo), 0);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(28,25, 'Proxima cita:',0);
    $pdf->SetFont('Arial', '', 12);
    $fecha_cita = date('d-m-Y', strtotime($cirugia->fecha_cita));
    $pdf->Cell(0, 25, $fecha_cita, 0);
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(37, 40, 'Nombre mascota:',0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0,40, utf8_decode($cirugia->mascota->nombre_mascota), 0);
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(35, 55, 'Nombre medico:',0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0,55, utf8_decode($cirugia->veterinario->nombre_completo), 0);
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(37, 70, utf8_decode ('Temperatura (°C):'),0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(78,70, utf8_decode($cirugia->temperatura),0);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(22,70, utf8_decode('Peso (Kg):'),0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0,70, utf8_decode($cirugia->peso), 0);
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(52, 85, utf8_decode('Frecuencia cardiaca (Fc):'),0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(63,85, utf8_decode($cirugia->fre_cardiaca),0);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(58,85, utf8_decode('Frecuencia respiratoria (Fr):'),0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0,85, utf8_decode($cirugia->fre_respiratoria), 0);
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(33, 100, utf8_decode('Tipo de cirugia:'),0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0,100, utf8_decode($cirugia->tipo_cirugia),0);
    

    // Generar salida del PDF
    $output = $pdf->Output('', 'S');
    
    return new Response($output, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename=cirugia.pdf',
    ]);
}

public function eliminarCirugia(Cirugia $cirugia)
        {
            $cirugia->delete();
            return redirect()->route('admin.registros.cirugias.cirugia', ['id' => $cirugia->id])->with('eliminar', 'ok');
        }

public function ReporteFechasCirugia(Request $request)
        {
            date_default_timezone_set('America/La_Paz');
            $request->validate([
                'desde' => 'required|date',
                'hasta' => 'required|date|after_or_equal:desde',
            ]);
            $startDate = $request->input('desde');
            $endDate = $request->input('hasta');
            
            $cirugias = [];
            $user = Auth::user();
                    if ($user->hasRole('Administrador')) {
                        $cirugias = Cirugia::whereBetween('fecha_cirugia', [$startDate, $endDate])->get();
                    } elseif ($user->hasRole('Veterinario')) {
                        $cirugias = Cirugia::where('id_veterinario', $user->id)
                                    ->whereBetween('fecha_cirugia', [$startDate, $endDate])
                                    ->get();
                    }
            $pdf = new FPDF('L', 'mm', 'letter'); 
            $pdf->SetMargins(7, 15, 7);
            $pdf->AddPage();
            $pdf->SetTitle('Reporte de Cirugia');
            $pdf->Image('assetsadmin/img/logo.jpg', 7, 10, 60);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetY($pdf->GetY() - 5); // Ajusta esta línea para mover hacia arriba el texto
            $pdf->Cell(205,0); // Mueve el cursor de la celda hacia la derecha
            $pdf->Cell(25, 0, utf8_decode('Dirección:'), 0, 0, 'R'); // Texto "Dirección:" alineado a la derecha
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(23, 0, utf8_decode('Calle Tumusla'), 0, 1, 'R'); // Texto "Calle Tumusla" alineado a la derecha
            $pdf->Ln(2);
            function fecha2() {
                $meses = array(
                    1 => 'enero',
                    2 => 'febrero',
                    3 => 'marzo',
                    4 => 'abril',
                    5 => 'mayo',
                    6 => 'junio',
                    7 => 'julio',
                    8 => 'agosto',
                    9 => 'septiembre',
                    10 => 'octubre',
                    11 => 'noviembre',
                    12 => 'diciembre'
                );
            
                $dia = date('d'); // Día actual
                $mes = $meses[intval(date('m'))]; // Mes actual en literal
                $anio = date('Y'); // Año actual
            
                return $dia . ' de ' . $mes . ' de ' . $anio;
            }
            
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(211);
            $pdf->Cell(47, 3, 'Fecha: ', 0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(7, 3, fecha2(), 0, 1, 'R'); // Fecha actual con mes literal
            $pdf->Ln(1);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(211);
            $pdf->Cell(19, 2, 'Hora: ',0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(2,2, date('H:i'), 0, 1, 'R'); // Hora actua
            $pdf->Ln(1);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(211);
            $pdf->Cell(30, 3, utf8_decode('Celular:'),0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(2,3, ('74530647'), 0, 1, 'R');
            $pdf->Ln(1);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(233);
            $pdf->Cell(2, 3, 'TUPIZA', 0, 1, 'R');
        
            $pdf->Ln(15);
        
            // Encabezados de la tabla
            function fechaLiteral1($fecha) {
                $meses = array(
                    1 => 'enero',
                    2 => 'febrero',
                    3 => 'marzo',
                    4 => 'abril',
                    5 => 'mayo',
                    6 => 'junio',
                    7 => 'julio',
                    8 => 'agosto',
                    9 => 'septiembre',
                    10 => 'octubre',
                    11 => 'noviembre',
                    12 => 'diciembre'
                );
                
                $dia = date('d', strtotime($fecha));
                $mes = $meses[intval(date('m', strtotime($fecha)))];
                $anio = date('Y', strtotime($fecha));
                
                return $dia . ' de ' . $mes . ' de ' . $anio;
            }
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Reporte de Cirugias desde '.fechaLiteral1($startDate).' hasta '.fechaLiteral1($endDate), 0, 1, 'C'); // Modificar el título
            $pdf->Ln(5);
            $pdf->SetFillColor(200, 220, 255);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(10, 10, utf8_decode('Nº'), 1, 0, 'C',true);
            $pdf->Cell(40, 10, 'Nombre Mascota', 1, 0, 'C',true);
            $pdf->Cell(45, 10, 'Nombre Propietario', 1, 0, 'C',true);
            $pdf->Cell(45, 10, 'Veterinario', 1, 0, 'C',true);
            $pdf->Cell(30, 10, 'Fecha Cirugia', 1, 0, 'C',true);
            $pdf->Cell(30, 10, 'Proxima cita', 1, 0, 'C',true);
            $pdf->Cell(66, 10, 'Tipo de cirugia', 1, 1, 'C',true);

           $contador=1;
            $pdf->SetFont('Arial', '', 10);
            foreach ($cirugias as $cirugia) {
                $fechaCirugia = date('d-m-Y', strtotime($cirugia->fecha_cirugia));
                $fechaCita = date('d-m-Y', strtotime($cirugia->fecha_cita));

                $pdf->Cell(10, 10, $contador, 1, 0, 'C');
                $pdf->Cell(40, 10, utf8_decode($cirugia->mascota->nombre_mascota), 1, 0, '');
                $pdf->Cell(45, 10, utf8_decode($cirugia->propietario->nombre_completo), 1, 0, '');
                $pdf->Cell(45, 10, utf8_decode($cirugia->veterinario->nombre_completo), 1, 0, '');
                $pdf->Cell(30, 10, $fechaCirugia, 1, 0, 'C');
                $pdf->Cell(30, 10, $fechaCita, 1, 0, 'C');
                $pdf->Cell(66, 10, utf8_decode($cirugia->tipo_cirugia), 1, 1, '');
                $contador++;
            }
            $pdf->Ln(1);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, utf8_decode('Cantidad de Cirugías: ') . count($cirugias), 0, 1, '');
            $output = $pdf->Output('', 'S');
            return new Response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename=reporte_cirugia.pdf',
            ]);

        }
    
    //vacunas
    public function vacuna(Request $request)
{
    $buscar = $request->get('buscar');
    $veterinarios = User::all();
    $mascotas = Mascota::all();
    $productos = Producto::all();
    $propietarios = Propietario::all();
    $perPage = $request->get('perPage', 5);
    $user = Auth::user();
    $vacunacionesQuery = Vacunacion::query();
    
    if ($user->hasRole('Administrador')) {
        $vacunacionesQuery->with(['mascota', 'mascota.propietario', 'veterinario', 'producto']);
    } else {
        $vacunacionesQuery->where('id_veterinario', $user->id)
                          ->with(['mascota', 'mascota.propietario', 'veterinario', 'producto']);
    }

    if ($buscar) {
        $vacunacionesQuery->where(function($query) use ($buscar) {
            $query->whereHas('mascota', function($subQuery) use ($buscar) {
                $subQuery->where('nombre_mascota', 'like', '%' . $buscar . '%');
            })
            ->orWhereHas('mascota.propietario', function($subQuery) use ($buscar) {
                $subQuery->where('nombre_completo', 'like', '%' . $buscar . '%');
            })
            ->orWhereHas('veterinario', function($subQuery) use ($buscar) {
                $subQuery->where('nombre_completo', 'like', '%' . $buscar . '%');
            })
            ->orWhereHas('producto', function($subQuery) use ($buscar) {
                $subQuery->where('nombre_producto', 'like', '%' . $buscar . '%');
            });
        });
    }
    
    $vacunaciones = $vacunacionesQuery->paginate($perPage);
    
    return view('admin.registros.vacunacion.vacuna', [
        'veterinarios' => $veterinarios, 
        'mascotas' => $mascotas,
        'propietarios' => $propietarios,
        'productos' => $productos,
        'vacunacion' => $vacunaciones,
        'vacunaciones' => $vacunaciones,
        'buscar' => $buscar,
    ]);
}
    

public function add_vacuna(){
    $user = Auth::user();
    $veterinarios = User::all();
    $productos = Producto::all();
    
    if ($user->hasRole('Administrador')) {
        // Si el usuario es administrador, mostramos todos los propietarios y mascotas
        $propietarios = Propietario::all();
        $mascotas = Mascota::all();
    } else {
        // Si el usuario no es administrador, mostramos solo los propietarios relacionados con él
        $propietarios = Propietario::where('id_veterinario', $user->id)->get();
        $mascotas = Mascota::whereIn('id_propietario', $propietarios->pluck('id'))->get();
    }

    $vacunacion = Vacunacion::all();
    
    return view('admin.registros.vacunacion.add_vacuna', compact('veterinarios', 'propietarios', 'mascotas', 'vacunacion', 'productos'));
}

   

    public function store_vacuna(Request $request){
        // Validar los datos del formulario
        $request->validate([
            'id_mascota' => 'required|exists:mascotas,id',
            'id_veterinario' => 'required|exists:veterinarios,id',
            'id_propietario' => 'required|exists:propietarios,id',
            'id_producto' => 'required|exists:productos,id',
            'fecha' => 'required|date',
            'fecha_cita' => 'required|date|after_or_equal:fecha',
            'costo' => 'required|numeric|between:10,999',
        ],  [
            'fecha_consulta.required' => 'El campo Fecha es obligatorio',
            'fecha_cita.required' => 'El campo Fecha de Cita es obligatorio.',
            'fecha_cita.date' => 'El campo Fecha de Cita debe ser una fecha válida.',
            'fecha_cita.after_or_equal' => 'La Fecha de Cita debe ser igual o posterior a la Fecha de Consulta.',
            'costo.between' => 'El campo costo debe tener 2 o 3 dígitos.',
            'costo.numeric' => 'El campo costo  debe contener solo números.',
            'costo.required' => 'El campo costo es obligatorio.',
        ]);
       
        $vacunacion = new Vacunacion();
        $vacunacion->id_mascota = $request->input('id_mascota');
        $vacunacion->id_veterinario = $request->input('id_veterinario');
        $vacunacion->id_propietario = $request->input('id_propietario');
        $vacunacion->id_producto = $request->input('id_producto');
        $vacunacion->fecha = $request->date('fecha');
        $vacunacion->fecha_cita = $request->date('fecha_cita');
        $vacunacion->costo = $request->input('costo');
    
        // Guardar la vacunacion en la base de datos
        $vacunacion->save();

        // Redirigir a la lista de citas o a donde desees
        return redirect()->route('admin.registros.vacunacion.vacuna');
    }

    public function edit_vacuna($id){
        $user = Auth::user();
        $vacunacion = Vacunacion::find($id);
        $productos = Producto::all();
        // Si el usuario es un veterinario, filtramos los propietarios y mascotas
        if ($user->hasRole('Veterinario')) {
            $propietarios = Propietario::where('id_veterinario', $user->id)->get();
            $mascotas = Mascota::whereIn('id_propietario', $propietarios->pluck('id'))->get();
        } else {
            // Si el usuario es un administrador, mostramos todos los propietarios y mascotas
            $propietarios = Propietario::all();
            $mascotas = Mascota::all();
        }
    
        $veterinarios = User::all();
        
        // Pasar los datos a la vista de edición
        return view('admin.registros.vacunacion.edit_vacuna', compact('veterinarios', 'mascotas', 'propietarios', 'vacunacion','productos'));
    }



    public function update_vacuna(Request $request, $id) {
        // Validar los datos del formulario
        $request->validate([
            'id_mascota' => 'required|exists:mascotas,id',
            'id_veterinario' => 'required|exists:veterinarios,id',
            'id_propietario' => 'required|exists:propietarios,id',
            'id_producto' => 'required|exists:productos,id',
            'fecha' => 'required|date',
            'fecha_cita' => 'required|date|after_or_equal:fecha',
            'costo' => 'required|numeric|between:10,999',
        ],  [
            'fecha_consulta.required' => 'El campo Fecha es obligatorio',
            'fecha_cita.required' => 'El campo Fecha de Cita es obligatorio.',
            'fecha_cita.date' => 'El campo Fecha de Cita debe ser una fecha válida.',
            'fecha_cita.after_or_equal' => 'La Fecha de Cita debe ser igual o posterior a la Fecha de Consulta.',
            'costo.between' => 'El campo costo debe tener 2 o 3 dígitos.',
            'costo.numeric' => 'El campo costo  debe contener solo números.',
            'costo.required' => 'El campo costo es obligatorio.',
        ]);
    
        // Buscar la consulta por ID
        $vacunacion = Vacunacion::find($id);
        
        // Actualizar los datos de la mascota con los datos del formulario
        $vacunacion->id_mascota = $request->input('id_mascota');
        $vacunacion->id_veterinario = $request->input('id_veterinario');
        $vacunacion->id_propietario = $request->input('id_propietario');
        $vacunacion->id_producto = $request->input('id_producto');
        $vacunacion->fecha = $request->date('fecha');
        $vacunacion->fecha_cita = $request->date('fecha_cita');
        $vacunacion->costo = $request->input('costo');
        // Actualiza otros campos según tus necesidades
    
        // Guardar los cambios
        $vacunacion->save();
    
        // Redirigir a la página de listado de mascotas con un mensaje de éxito
        return redirect()->route('admin.registros.vacunacion.vacuna', ['id' => $vacunacion->id])->with('actualizado', 'ok');
    }

    public function vacuna_pdf($id)
{
    date_default_timezone_set('America/La_Paz');
    $vacunacion = Vacunacion::with('mascota', 'veterinario')->find($id);

    // Inicializar el PDF
    $pdf = new FPDF('P', 'mm', 'letter');
    $pdf->SetMargins(20, 20, 20);
    $pdf->AddPage();
    $pdf->SetTitle('Reporte de Vacunacion');
    $pdf->Image('assetsadmin/img/marca.jpg', 0, 0, 215.9, 279.4);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Image('assetsadmin/img/logo.jpg',22, 15, 40);
    $pdf->Ln(3);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetY($pdf->GetY() - 10); // Ajusta esta línea para mover hacia arriba el texto
    $pdf->Cell(130,0); // Mueve el cursor de la celda hacia la derecha
    $pdf->Cell(25, 0, utf8_decode('Dirección:'), 0, 0, 'R'); // Texto "Dirección:" alineado a la derecha
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(23, 0, utf8_decode('Calle Tumusla'), 0, 1, 'R'); // Texto "Calle Tumusla" alineado a la derecha
    $pdf->Ln(2);
    function fecha3() {
        $meses = array(
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre'
        );
    
        $dia = date('d'); // Día actual
        $mes = $meses[intval(date('m'))]; // Mes actual en literal
        $anio = date('Y'); // Año actual
    
        return $dia . ' de ' . $mes . ' de ' . $anio;
    }
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(136);
    $pdf->Cell(47, 3, 'Fecha: ', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(7, 3, fecha3(), 0, 1, 'R'); // Fecha actual
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(136);
    $pdf->Cell(19, 2, 'Hora: ',0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(2,2, date('H:i'), 0, 1, 'R'); // Hora actua
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(136);
    $pdf->Cell(30, 3, utf8_decode('Celular:'),0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(2,3, ('74530647'), 0, 1, 'R');
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(157);
    $pdf->Cell(2, 3, 'TUPIZA', 0, 1, 'R');
    // Información de la consulta
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'VACUNACION', 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Numero de cirugia:',0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(72,10, utf8_decode($vacunacion->id),0);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(39,10, utf8_decode('Fecha vacunacion:'),0);
    $pdf->SetFont('Arial', '', 12);
    $fecha = date('d-m-Y', strtotime($vacunacion->fecha));
    $pdf->Cell(0, 10, $fecha, 0);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(42, 25, 'Nombre propietario:',0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(70,25,utf8_decode($vacunacion->propietario->nombre_completo), 0);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(27,25, utf8_decode('Proxima cita:'),0);
    $pdf->SetFont('Arial', '', 12);
    $fecha_cita = date('d-m-Y', strtotime($vacunacion->fecha_cita));
    $pdf->Cell(0, 25, $fecha_cita, 0);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(38, 40, 'Nombre mascota:',0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0,40, utf8_decode($vacunacion->mascota->nombre_mascota), 0);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(35, 55, 'Nombre medico:',0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0,55, utf8_decode($vacunacion->veterinario->nombre_completo), 0);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(22, 70, utf8_decode ('Producto:'),0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(92,70, utf8_decode ($vacunacion->producto->nombre_producto),0);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(25,70, utf8_decode('Costo (Bs):'),0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0,70, utf8_decode($vacunacion->costo), 0);
    // Generar salida del PDF
    $output = $pdf->Output('', 'S');
    
    return new Response($output, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename=vacuna.pdf',
    ]);
}

        public function eliminarVacuna(Vacunacion $vacunacion)
        {
            $vacunacion->delete();
            return redirect()->route('admin.registros.vacunacion.vacuna', ['id' => $vacunacion->id])->with('eliminar', 'ok');
        }

        public function ReporteFechasVacuna(Request $request)
        {
            date_default_timezone_set('America/La_Paz');
            $request->validate([
                'desde' => 'required|date',
                'hasta' => 'required|date|after_or_equal:desde',
            ]);
            $startDate = $request->input('desde');
            $endDate = $request->input('hasta');
            
            $vacunacion = [];
            $user = Auth::user();
                    if ($user->hasRole('Administrador')) {
                        $vacunacion = Vacunacion::whereBetween('fecha', [$startDate, $endDate])->get();
                    } elseif ($user->hasRole('Veterinario')) {
                        $vacunacion = Vacunacion::where('id_veterinario', $user->id)
                                    ->whereBetween('fecha', [$startDate, $endDate])
                                    ->get();
                    }
            $pdf = new FPDF('L', 'mm', 'letter');
            $pdf->SetMargins(10, 15, 10);
            $pdf->AddPage();
            $pdf->SetTitle('Reporte de Vacunas');
           
            $pdf->Image('assetsadmin/img/logo.jpg', 10, 10, 60);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetY($pdf->GetY() - 5); // Ajusta esta línea para mover hacia arriba el texto
            $pdf->Cell(205,0); // Mueve el cursor de la celda hacia la derecha
            $pdf->Cell(25, 0, utf8_decode('Dirección:'), 0, 0, 'R'); // Texto "Dirección:" alineado a la derecha
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(23, 0, utf8_decode('Calle Tumusla'), 0, 1, 'R'); // Texto "Calle Tumusla" alineado a la derecha
            $pdf->Ln(2);
            function fecha4() {
                $meses = array(
                    1 => 'enero',
                    2 => 'febrero',
                    3 => 'marzo',
                    4 => 'abril',
                    5 => 'mayo',
                    6 => 'junio',
                    7 => 'julio',
                    8 => 'agosto',
                    9 => 'septiembre',
                    10 => 'octubre',
                    11 => 'noviembre',
                    12 => 'diciembre'
                );
            
                $dia = date('d'); // Día actual
                $mes = $meses[intval(date('m'))]; // Mes actual en literal
                $anio = date('Y'); // Año actual
            
                return $dia . ' de ' . $mes . ' de ' . $anio;
            }
            
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(211);
            $pdf->Cell(47, 3, 'Fecha: ', 0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(7, 3, fecha4(), 0, 1, 'R'); // Fecha actual con mes literal
            $pdf->Ln(1);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(211);
            $pdf->Cell(19, 2, 'Hora: ',0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(2,2, date('H:i'), 0, 1, 'R'); // Hora actua
            $pdf->Ln(1);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(211);
            $pdf->Cell(30, 3, utf8_decode('Celular:'),0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(2,3, ('74530647'), 0, 1, 'R');
            $pdf->Ln(1);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(233);
            $pdf->Cell(2, 3, 'TUPIZA', 0, 1, 'R');
        
            $pdf->Ln(15);
        
            // Encabezados de la tabla
            function fechaLiteral2($fecha) {
                $meses = array(
                    1 => 'enero',
                    2 => 'febrero',
                    3 => 'marzo',
                    4 => 'abril',
                    5 => 'mayo',
                    6 => 'junio',
                    7 => 'julio',
                    8 => 'agosto',
                    9 => 'septiembre',
                    10 => 'octubre',
                    11 => 'noviembre',
                    12 => 'diciembre'
                );
                
                $dia = date('d', strtotime($fecha));
                $mes = $meses[intval(date('m', strtotime($fecha)))];
                $anio = date('Y', strtotime($fecha));
                
                return $dia . ' de ' . $mes . ' de ' . $anio;
            }
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Reporte de Vacunaciones desde '.fechaLiteral2($startDate).' hasta '.fechaLiteral2($endDate), 0, 1, 'C'); // Modificar el título
            $pdf->Ln(5);
            $pdf->SetFillColor(200, 220, 255);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(8, 10, utf8_decode('Nº'), 1, 0, 'C',true);
            $pdf->Cell(30, 10, 'Mascota', 1, 0, 'C',true);
            $pdf->Cell(45, 10, 'Nombre Propietario', 1, 0, 'C',true);
            $pdf->Cell(45, 10, 'Veterinario', 1, 0, 'C',true);
            $pdf->Cell(30, 10, 'Fecha Vacuna', 1, 0, 'C',true);
            $pdf->Cell(30, 10, 'Proxima cita', 1, 0, 'C',true);
            $pdf->Cell(50, 10, 'Nombre Producto', 1, 0, 'C',true);
            $pdf->Cell(25, 10, 'Costo (Bs)', 1, 1, 'C',true);

           $contador=1;
            $pdf->SetFont('Arial', '', 10);
            foreach ($vacunacion as $vacuna) {
                $fechaVacuna = date('d-m-Y', strtotime($vacuna->fecha));
                $fechaCita = date('d-m-Y', strtotime($vacuna->fecha_cita));

                $pdf->Cell(8, 10, $contador, 1, 0, 'C');
                $pdf->Cell(30, 10, utf8_decode($vacuna->mascota->nombre_mascota), 1, 0, '');
                $pdf->Cell(45, 10, utf8_decode($vacuna->propietario->nombre_completo), 1, 0, '');
                $pdf->Cell(45, 10, utf8_decode($vacuna->veterinario->nombre_completo), 1, 0, '');
                $pdf->Cell(30, 10, $fechaVacuna, 1, 0, 'C');
                $pdf->Cell(30, 10, $fechaCita, 1, 0, 'C');
                $pdf->Cell(50, 10, utf8_decode($vacuna->producto->nombre_producto), 1, 0, '');
                $pdf->Cell(25, 10, utf8_decode($vacuna->costo), 1, 1, 'C');
                $contador++;
            }
            $pdf->Ln(1); // Salto de línea antes del conteo
            $pdf->SetFont('Arial', '', 12);
            $totalVacunas = count($vacunacion); // Corrección de la variable
            $pdf->Cell(0, 10, 'Cantidad de Vacunas: ' . $totalVacunas, 0, 1, '');

            // Capturar la salida del PDF en una variable
            $output = $pdf->Output('', 'S');

            // Retornar una respuesta HTTP con el PDF incrustado en el navegador
            return new Response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename=reporte_vacunacion.pdf',
            ]);

        }
    //servicios
    
    public function servicio(Request $request)
{
    $buscar = $request->get('buscar');
    $veterinarios = User::all();
    $mascotas = Mascota::all();
    $propietarios = Propietario::all();
    $perPage = $request->get('perPage', 5);
    $user = Auth::user();
    
    // Definir la consulta predeterminada
    $serviciosQuery = Servicio::query();
    
    // Ajustar la consulta según el rol del usuario
    if (!$user->hasRole('Administrador')) {
        $serviciosQuery = $serviciosQuery->where('id_veterinario', $user->id);
    }
    
    // Aplicar filtro de búsqueda si está presente
    if (!empty($buscar)) {
        $serviciosQuery->where(function ($query) use ($buscar) {
            $query->whereHas('propietario', function ($q) use ($buscar) {
                $q->where('nombre_completo', 'like', "%$buscar%");
            })
            ->orWhereHas('veterinario', function ($q) use ($buscar) {
                $q->where('nombre_completo', 'like', "%$buscar%");
            })
            ->orWhereHas('mascota', function ($q) use ($buscar) {
                $q->where('nombre_mascota', 'like', "%$buscar%");
            })
            ->orWhere('tipo_servicio', 'like', "%$buscar%");
        });
    }
    
    // Paginar los resultados
    $servicios = $serviciosQuery->paginate($perPage);

    return view('admin.servicios.servicio', [
        'veterinarios' => $veterinarios, 
        'mascotas' => $mascotas,
        'propietarios' => $propietarios,
        'servicios' => $servicios,
        'buscar' => $buscar,
    ]);
}

public function add_servicio(){
    $user = Auth::user();
    $veterinarios = User::all();
    
    if ($user->hasRole('Administrador')) {
        // Si el usuario es administrador, mostramos todos los propietarios y mascotas
        $propietarios = Propietario::all();
        $mascotas = Mascota::all();
    } else {
        // Si el usuario no es administrador, mostramos solo los propietarios relacionados con él
        $propietarios = Propietario::where('id_veterinario', $user->id)->get();
        $mascotas = Mascota::whereIn('id_propietario', $propietarios->pluck('id'))->get();
    }

    $servicios = Servicio::all();
    
    return view('admin.servicios.add_servicio', [
        'veterinarios' => $veterinarios,
        'propietarios' => $propietarios,
        'mascotas' => $mascotas,
        'servicios' => $servicios
    ]);
}


  

    public function store_servicio(Request $request){
        // Validar los datos del formulario
        $request->validate([
            'id_mascota' => 'required|exists:mascotas,id',
            'id_propietario' => 'required|exists:propietarios,id',
            'id_veterinario' => 'required|exists:veterinarios,id',
            'fecha_servicio' => 'required|date',
            'tipo_servicio' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$/',
            'costo' => 'required|numeric|between:10,999',
        
        ], [


            'costo.between' => 'El campo costo debe tener 2 o 3 dígitos.',
            'costo.numeric' => 'El campo costo  debe contener solo números.',
            'costo.required' => 'El campo costo es obligatorio.',
            'tipo_servicio.regex' => 'El campo tipo servicio solo permite letras y espacios.',
            'tipo_servicio.required' => 'El campo tipo servicio es obligatorio.',
        ]);
       
        $servicios = new Servicio();
        $servicios->id_mascota = $request->input('id_mascota');
        $servicios->id_propietario = $request->input('id_propietario');
        $servicios->id_veterinario = $request->input('id_veterinario');
        $servicios->fecha_servicio = $request->date('fecha_servicio');
        $servicios->tipo_servicio = $request->input('tipo_servicio');
        $servicios->costo = $request->input('costo');
    
        // Guardar la servicios en la base de datos
        $servicios->save();

        // Redirigir a la lista de citas o a donde desees
        return redirect()->route('admin.servicios.servicio');
    }

    public function edit_servicio($id){
        $user = Auth::user();
        $servicio = Servicio::find($id);
        
        // Si el usuario es un veterinario, filtramos los propietarios y mascotas
        if ($user->hasRole('Veterinario')) {
            $propietarios = Propietario::where('id_veterinario', $user->id)->get();
            $mascotas = Mascota::whereIn('id_propietario', $propietarios->pluck('id'))->get();
        } else {
            // Si el usuario es un administrador, mostramos todos los propietarios y mascotas
            $propietarios = Propietario::all();
            $mascotas = Mascota::all();
        }
    
        $veterinarios = User::all();
        
        // Pasar los datos a la vista de edición
        return view('admin.servicios.edit_servicio', [
            'veterinarios' => $veterinarios,
            'mascotas' => $mascotas,
            'propietarios' => $propietarios,
            'servicio' => $servicio
        ]);
    }

    
    public function update_servicio(Request $request, $id) {
        // Validar los datos del formulario
        $request->validate([
            'id_mascota' => 'required|exists:mascotas,id',
            'id_propietario' => 'required|exists:propietarios,id',
            'id_veterinario' => 'required|exists:veterinarios,id',
            'fecha_servicio' => 'required|date',
            'tipo_servicio' => 'required|regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ ]+$/',
            'costo' => 'required|numeric|between:10,999',
        
        ], [


            'costo.between' => 'El campo costo debe tener 2 o 3 dígitos.',
            'costo.numeric' => 'El campo costo  debe contener solo números.',
            'costo.required' => 'El campo costo es obligatorio.',
            'tipo_servicio.regex' => 'El campo tipo servicio solo permite letras y espacios.',
            'tipo_servicio.required' => 'El campo tipo servicio es obligatorio.',
        ]);
    
        // Buscar la consulta por ID
        $servicio = Servicio::find($id);
        
        // Actualizar los datos de la mascota con los datos del formulario
        $servicio->id_mascota = $request->input('id_mascota');
        $servicio->id_propietario = $request->input('id_propietario');
        $servicio->id_veterinario = $request->input('id_veterinario');
        $servicio->fecha_servicio = $request->input('fecha_servicio');
        $servicio->tipo_servicio = $request->input('tipo_servicio');
        $servicio->costo = $request->input('costo');
        // Actualiza otros campos según tus necesidades
    
        // Guardar los cambios
        $servicio->save();
    
        // Redirigir a la página de listado de mascotas con un mensaje de éxito
        return redirect()->route('admin.servicios.servicio', ['id' => $servicio->id])->with('actualizado', 'ok');
    }

    public function servicio_pdf($id)
{
    date_default_timezone_set('America/La_Paz');
    $servicio = Servicio::with('mascota', 'veterinario')->find($id);

    // Inicializar el PDF
    $pdf = new FPDF('P', 'mm', 'letter');
    $pdf->SetMargins(20, 20, 20);
    $pdf->AddPage();
    $pdf->SetTitle(utf8_decode('Reporte de Baño y Peluqueria'));
    $pdf->Image('assetsadmin/img/marca.jpg', 0, 0, 215.9, 279.4);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Image('assetsadmin/img/logo.jpg',22, 15, 40);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetY($pdf->GetY() - 10); // Ajusta esta línea para mover hacia arriba el texto
    $pdf->Cell(130,0); // Mueve el cursor de la celda hacia la derecha
    $pdf->Cell(25, 0, utf8_decode('Dirección:'), 0, 0, 'R'); // Texto "Dirección:" alineado a la derecha
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(23, 0, utf8_decode('Calle Tumusla'), 0, 1, 'R'); // Texto "Calle Tumusla" alineado a la derecha
    $pdf->Ln(2);
    function fecha6() {
        $meses = array(
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre'
        );
    
        $dia = date('d'); // Día actual
        $mes = $meses[intval(date('m'))]; // Mes actual en literal
        $anio = date('Y'); // Año actual
    
        return $dia . ' de ' . $mes . ' de ' . $anio;
    }
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(136);
    $pdf->Cell(47, 3, 'Fecha: ', 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(7, 3, fecha6(), 0, 1, 'R'); // Fecha actual
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(136);
    $pdf->Cell(19, 2, 'Hora: ',0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(2,2, date('H:i'), 0, 1, 'R'); // Hora actua
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(136);
    $pdf->Cell(30, 3, utf8_decode('Celular:'),0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(2,3, ('74530647'), 0, 1, 'R');
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(157);
    $pdf->Cell(2, 3, 'TUPIZA', 0, 1, 'R');
    // Información de la consulta
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode('BAÑO Y PELUQUERIA'), 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(42, 10, 'Numero de servicio:',0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(75,10, utf8_decode($servicio->id),0);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(15,10, utf8_decode('Fecha:'),0);
    $pdf->SetFont('Arial', '', 12);
    $fecha_servicio = date('d-m-Y', strtotime($servicio->fecha_servicio));
    $pdf->Cell(0, 10, $fecha_servicio, 0);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(42, 25, 'Nombre propietario:',0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0,25,utf8_decode($servicio->propietario->nombre_completo), 0);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(38, 40, 'Nombre mascota:',0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0,40, utf8_decode($servicio->mascota->nombre_mascota), 0);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(35, 55, 'Nombre medico:',0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0,55, utf8_decode($servicio->veterinario->nombre_completo), 0);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(30, 70, utf8_decode ('Tipo Servicio:'),0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(88,70, utf8_decode ($servicio->tipo_servicio),0);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(25,70, utf8_decode('Costo (Bs):'),0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0,70, utf8_decode($servicio->costo), 0);
    // Generar salida del PDF
    $output = $pdf->Output('', 'S');
    
    return new Response($output, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename=vacuna.pdf',
    ]);
}

        public function eliminarServicio(Servicio $servicio)
        {
            $servicio->delete();
            return redirect()->route('admin.servicios.servicio', ['id' => $servicio->id])->with('eliminar', 'ok');
        }


        public function ReporteFechasServicio(Request $request)
        {
            date_default_timezone_set('America/La_Paz');
            // Validar el rango de fechas enviado desde el formulario
            $request->validate([
                'desde' => 'required|date',
                'hasta' => 'required|date|after_or_equal:desde',
            ]);
            
            // Obtener fechas del request
            $startDate = $request->input('desde');
            $endDate = $request->input('hasta');
            
            $servicio = [];
            $user = Auth::user();
                    if ($user->hasRole('Administrador')) {
                        $servicio = Servicio::whereBetween('fecha_servicio', [$startDate, $endDate])->get();
                    } elseif ($user->hasRole('Veterinario')) {
                        $servicio = Servicio::where('id_veterinario', $user->id)
                                    ->whereBetween('fecha_servicio', [$startDate, $endDate])
                                    ->get();
                    }
            $pdf = new FPDF('L', 'mm', 'letter');
            $pdf->SetMargins(10, 15, 10);
            $pdf->AddPage();
            $pdf->SetTitle(utf8_decode('Reporte de Baño y Peluqueria'));
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Image('assetsadmin/img/logo.jpg', 10, 10, 60);
        
            $pdf->SetY($pdf->GetY() - 5); // Ajusta esta línea para mover hacia arriba el texto
            $pdf->Cell(205,0); // Mueve el cursor de la celda hacia la derecha
            $pdf->Cell(25, 0, utf8_decode('Dirección:'), 0, 0, 'R'); // Texto "Dirección:" alineado a la derecha
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(23, 0, utf8_decode('Calle Tumusla'), 0, 1, 'R'); // Texto "Calle Tumusla" alineado a la derecha
            $pdf->Ln(2);
            function fecha5() {
                $meses = array(
                    1 => 'enero',
                    2 => 'febrero',
                    3 => 'marzo',
                    4 => 'abril',
                    5 => 'mayo',
                    6 => 'junio',
                    7 => 'julio',
                    8 => 'agosto',
                    9 => 'septiembre',
                    10 => 'octubre',
                    11 => 'noviembre',
                    12 => 'diciembre'
                );
            
                $dia = date('d'); // Día actual
                $mes = $meses[intval(date('m'))]; // Mes actual en literal
                $anio = date('Y'); // Año actual
            
                return $dia . ' de ' . $mes . ' de ' . $anio;
            }
            
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(211);
            $pdf->Cell(47, 3, 'Fecha: ', 0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(7, 3, fecha5(), 0, 1, 'R'); // Fecha actual con mes literal
            $pdf->Ln(1);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(211);
            $pdf->Cell(19, 2, 'Hora: ',0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(2,2, date('H:i'), 0, 1, 'R'); // Hora actua
            $pdf->Ln(1);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(211);
            $pdf->Cell(30, 3, utf8_decode('Celular:'),0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(2,3, ('74530647'), 0, 1, 'R');
            $pdf->Ln(1);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(233);
            $pdf->Cell(2, 3, 'TUPIZA', 0, 1, 'R');
        
            $pdf->Ln(15);
        
            // Encabezados de la tabla
            function fechaLiteral3($fecha) {
                $meses = array(
                    1 => 'enero',
                    2 => 'febrero',
                    3 => 'marzo',
                    4 => 'abril',
                    5 => 'mayo',
                    6 => 'junio',
                    7 => 'julio',
                    8 => 'agosto',
                    9 => 'septiembre',
                    10 => 'octubre',
                    11 => 'noviembre',
                    12 => 'diciembre'
                );
                
                $dia = date('d', strtotime($fecha));
                $mes = $meses[intval(date('m', strtotime($fecha)))];
                $anio = date('Y', strtotime($fecha));
                
                return $dia . ' de ' . $mes . ' de ' . $anio;
            }
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10,utf8_decode ('Reporte de Baño y peluqueria desde ').fechaLiteral3($startDate).' hasta '.fechaLiteral3($endDate), 0, 1, 'C'); // Modificar el título
            $pdf->Ln(5);
            $pdf->SetFillColor(200, 220, 255);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(10, 10, utf8_decode('Nº'), 1, 0, 'C',true);
            $pdf->Cell(40, 10, 'Nombre Mascota', 1, 0, 'C',true);
            $pdf->Cell(45, 10, 'Nombre Propietario', 1, 0, 'C',true);
            $pdf->Cell(45, 10, 'Veterinario', 1, 0, 'C',true);
            $pdf->Cell(33, 10, 'Fecha', 1, 0, 'C',true);
            $pdf->Cell(50, 10, 'Tipo de Servicio', 1, 0, 'C',true);
            $pdf->Cell(30, 10, 'Costo (Bs)', 1, 1, 'C',true);

            $contador=1;
            $pdf->SetFont('Arial', '', 10);
            $totalServicio = 0; // Inicializar la variable para contar los servicios
            foreach ($servicio as $servicios) {
                $fechaServicio = date('d-m-Y', strtotime($servicios->fecha_servicio));

                $pdf->Cell(10, 10, $contador, 1, 0, 'C');
                $pdf->Cell(40, 10, utf8_decode($servicios->mascota->nombre_mascota), 1, 0, 'C');
                $pdf->Cell(45, 10, utf8_decode($servicios->propietario->nombre_completo), 1, 0, '');
                $pdf->Cell(45, 10, utf8_decode($servicios->veterinario->nombre_completo), 1, 0, '');
                $pdf->Cell(33, 10, $fechaServicio, 1, 0, 'C');
                $pdf->Cell(50, 10, utf8_decode($servicios->tipo_servicio), 1, 0, '');
                $pdf->Cell(30, 10, utf8_decode($servicios->costo), 1, 1, 'C');
                $contador++;
                $totalServicio++;
            }
            $pdf->Ln(1); 
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'Cantidad de Servicios: ' . $totalServicio, 0, 1, '');

            // Capturar la salida del PDF en una variable
            $output = $pdf->Output('', 'S');

            // Retornar una respuesta HTTP con el PDF incrustado en el navegador
            return new Response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename=reporte_servicio_bano_peluqueria.pdf',
            ]);

        }
    //productos
    public function producto(Request $request)
    {
        $buscar = trim($request->get('buscar'));
        $perPage = $request->input('perPage', 5);
        $productos = Producto::where('nombre_producto', 'LIKE', '%' . $buscar . '%')
                             ->paginate($perPage);
    
        return view('admin.productos.producto', [
            'productos' => $productos,
            'buscar' => $buscar        
        ]);
    }

    public function add_producto(){
        return view('admin.productos.add_producto');
    }

    public function store_producto(Request $request){
        // Validar los datos del formulario
        $request->validate([

            'nombre_producto' => 'required|unique:productos,nombre_producto',
            'fecha_exp' => 'required|date',
            'descripcion' => 'required',
        ], [
            'nombre_producto.required' => 'El campo nombre producto es obligatorio.',
            'nombre_producto.unique' => 'Este nombre de producto ya está registrado.',
            'descripcion.required' => 'El campo decripcion es obligatorio.',
            'fecha_exp.required' => 'El campo Fecha de vencimiento es obligatorio.',

        ]);
        
        $productos = new Producto();
        $productos->nombre_producto = $request->input('nombre_producto');
        $productos->fecha_exp = $request->input('fecha_exp');
        $productos->descripcion = $request->input('descripcion');
    

        $productos->save();

        // Redirigir a la lista de citas o a donde desees
        return redirect()->route('admin.productos.producto');
    }

    public function edit_producto($id){
        $producto = Producto::find($id);
        $productos = Producto::all();

        return view('admin.productos.edit_producto', [
            'productos' => $productos,
            'producto' => $producto,
            
        ]);
    }

    public function update_producto(Request $request, $id)
    {
    // Validación de datos
    $request->validate([
        'nombre_producto' => 'required',
        'fecha_exp' => 'required|date',
        'descripcion' => 'required',
    ], [
        
        'nombre_producto.required' => 'El campo nombre producto es obligatorio.',
        'descripcion.required' => 'El campo decripcion es obligatorio.',
        'fecha_exp.required' => 'El campo Fecha de vencimiento es obligatorio.',

    ]);

    // Obtener el propietario por ID
    $productos = Producto::find($id);

    

    // Actualizar los datos del propietario con los datos del formulario
    $productos->nombre_producto = $request->input('nombre_producto');
    $productos->fecha_exp = $request->input('fecha_exp');
    $productos->descripcion = $request->input('descripcion');
    
    // Guardar los cambios
    $productos->save();

    // Redireccionar a la página de edición o a donde desees
    return redirect()->route('admin.productos.producto', ['id' => $productos->id])->with('actualizado', 'ok');
}

public function eliminarProducto(Producto $producto)
    {
        try {
            $producto->delete();
            return redirect()->route('admin.productos.producto', ['id' => $producto->id])->with('eliminar', 'ok');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) { // Código de error para violación de clave externa
                return redirect()->route('admin.productos.producto', ['id' => $producto->id])->with('error', 'ok');
            }  
                
            
        }
    }

    public function ReporteFechasProducto(Request $request)
{
    // Validar el rango de fechas enviado desde el formulario
    $request->validate([
        'desde' => 'required|date',
        'hasta' => 'required|date|after_or_equal:desde',
    ]);

    // Obtener fechas del request
    $startDate = $request->input('desde');
    $endDate = $request->input('hasta');

    // Consultar los productos dentro del rango de fechas
    $productos = Producto::whereBetween('fecha_exp', [$startDate, $endDate])->get();

    // Iniciar el PDF usando FPDF
    $pdf = new FPDF('P', 'mm', 'letter');
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();
    $pdf->SetTitle('Reporte de Productos');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Image('assetsadmin/img/logo.jpg', 10, 10, 50);
    
    $pdf->Ln(25);

    // Título del reporte
    $pdf->Cell(0, 10, 'Reporte de Productos desde ' . $startDate . ' hasta ' . $endDate, 0, 1, 'C');

    $pdf->Ln(5);

    // Encabezados de la tabla
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(15, 10, 'ID', 1, 0, 'C');
    $pdf->Cell(60, 10, 'Nombre del Producto', 1, 0, 'C');
    $pdf->Cell(35, 10, 'Fecha Expiracion', 1, 0, 'C');
    $pdf->Cell(75, 10, 'Descripcion', 1, 1, 'C');

    // Establecer interlineado personalizado para la descripción
    $interlineado = 0.5;

    // Datos de las consultas en la tabla
    $pdf->SetFont('Arial', '', 10);
    foreach ($productos as $producto) {
        $pdf->Cell(15, 10, $producto->id, 0, 0, 'C');
        $pdf->Cell(60, 10, utf8_decode($producto->nombre_producto), 0, 0, '');
        $pdf->Cell(35, 10, $producto->fecha_exp, 0, 0, 'C');

        // Descripción con interlineado personalizado
        $descripcion = utf8_decode($producto->descripcion);
        $descripcion = wordwrap($descripcion, 100000); // Ajustar la descripción a 70 caracteres por línea
        $descripcion_lines = explode("\n", $descripcion);

        foreach ($descripcion_lines as $line) {
            $pdf->MultiCell(75, 10 * $interlineado, $line, 1, 'L'); // Ajustar el alto de la celda según el interlineado
        }
    }
    
    // Conteo de productos
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Cantidad de Productos: ' . count($productos), 0, 1, '');

    // Capturar la salida del PDF en una variable
    $output = $pdf->Output('S');

    // Retornar una respuesta HTTP con el PDF incrustado en el navegador
    return response($output, 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename=reporte_productos.pdf');
}

}
