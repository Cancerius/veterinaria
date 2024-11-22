<!DOCTYPE html>
<html lang="en">


<!-- add-patient24:06-->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('assetsadmin/img/favicon.ico')}}">
    <title>Administrador</title>
    <link rel="stylesheet" type="text/css" href="{{asset('assetsadmin/css/bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assetsadmin/css/font-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assetsadmin/css/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assetsadmin/css/style.css')}}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Henny+Penny&family=Lato:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Sedan:ital@0;1&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Henny+Penny&family=Jaini&family=Jaini+Purva&family=Kalam:wght@300;400;700&family=Lato:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Montserrat+Alternates:wght@100;200;300;400;500;600;700;800;900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poetsen+One&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Sedan:ital@0;1&family=The+Girl+Next+Door&family=Vollkorn:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Henny+Penny&family=Jaini&family=Jaini+Purva&family=Kalam:wght@300;400;700&family=Lato:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Montserrat+Alternates:wght@100;200;300;400;500;600;700;800;900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poetsen+One&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Sedan:ital@0;1&family=The+Girl+Next+Door&family=Vollkorn:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
    integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer">
    <!--[if lt IE 9]>
		<script src="assetsadmin/js/html5shiv.min.js"></script>
		<script src="assetsadmin/js/respond.min.js"></script>
	<![endif]-->
</head>

<body>
    <div class="main-wrapper">
        <div class="header">
			<div class="header-left">
				<a href="index-2.html" class="logo">
					<img src="{{asset('assetsadmin/img/logo.jpg')}}" width="170" height="48" alt="">
				</a>
			</div>
			<a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
            <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>
            <ul class="nav user-menu float-right">
            
                <li class="nav-item dropdown has-arrow">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                        
						<span>{{auth()->user()->nombre_completo}}</span>
                    </a>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="{{ route('login.destroy')}}">Salir</a>
					</div>
                </li>
            </ul>
            
        </div>
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">Menu</li>
                        <li class="active">
                            <a href="{{ route('admin.index')}}"><i class="fa fa-dashboard"></i> <span>Inicio</span></a>
                        </li>
                        @can('Tabla veterinario')
						<li>
                            <a href="{{ route('admin.doctores.doctor')}}"><i class="fa fa-user-md"></i> <span>Veterinarios</span></a>
                        </li>
                        @endcan
                        @can('Tabla roles')
                        <li>
                            <a href="{{route('admin.roles.index')}}" ><i class="fa fa-users-cog "></i> <span>Lista de Roles</span></a>
                        </li>
                        @endcan
                        <li class="submenu">
                            
							<a href=""><i class="fa fa-book"></i> <span>Registros</span> <span class="menu-arrow"></span></a>
                            
							<ul style="display: none;">
                                @can('Tabla propietario')
								<li><a href="{{route('admin.registros.propietarios.propietario')}}" ><i class="fa fa-book"></i>Propietarios</a></li>
								@endcan
                                @can('Tabla mascota')
                                <li><a href="{{route('admin.registros.mascotas.mascota')}}"><i class="fa fa-book"></i>Mascotas</a></li>
                                @endcan
                                @can('Tabla consulta')
                                <li><a href="{{route('admin.registros.consultas.consulta')}}"><i class="fa fa-book"></i>Consultas</a></li>
                                @endcan
                                @can('Tabla cirugia')
                                <li><a href="{{route('admin.registros.cirugias.cirugia')}}"><i class="fa fa-book"></i>Cirugias</a></li>
                                @endcan
                                @can('Tabla vacunacion')
                                <li><a href="{{route('admin.registros.vacunacion.vacuna')}}"><i class="fa fa-book"></i>Vacunacion</a></li>
								@endcan
							</ul>
						</li>
    
                        <li>
                            @can('Tabla peluqueria y baño')
                            <a href="{{route('admin.servicios.servicio')}}"><i class="fa fa-book"></i> <span>Baño y Peluqueria</span></a>
                        </li>
                        @endcan
                        @can('Tabla productos')
                        <li>
                            <a href="{{route('admin.productos.producto')}}"><i class="fa fa-hospital-o"></i> <span>Productos</span></a>
                        </li> 
                        @endcan   
                    </ul>
                </div>
            </div>
        </div>
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <h4 class="page-title">Editar Propietario</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        @if ($errors->any())
                        <div class="alert alert-danger" style="font-size: 14px; padding: 5px;">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                        <form method="POST" action="{{ route('admin.registros.propietarios.update', ['id' => $propietario->id]) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for='nombre_completo'>Nombre Propietario <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" id="nombre_completo" name="nombre_completo" value="{{ $propietario->nombre_completo }}">
                                    </div>
                                </div> 
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for='id_veterinario'>Veterinario <span class="text-danger">*</span></label>
                                        <select class="form-control" type="text" id="id_veterinario" name="id_veterinario" >
                                            @foreach ($veterinarios as $user)
                                                @if ($user->id === Auth::id()) <!-- Filtrar el usuario autenticado -->
                                                    <option value="{{ $user->id }}" {{ $user->id === $propietario->id_veterinario ? 'selected' : '' }}>
                                                        {{ $user->nombre_completo }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div> 
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for='celular'>Celular <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" id="celular" name="celular" value="{{ $propietario->celular }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for='direccion'>Direccion <span class="text-danger">*</span></label>
                                        <input class="form-control center" type="text" id="direccion" name="direccion" value="{{ $propietario->direccion }}">
                                    </div>
                                </div>
                                
                                
                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn center">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
			
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <script src="{{asset('assetsadmin/js/jquery-3.2.1.min.js')}}"></script>
	<script src="{{asset('assetsadmin/js/popper.min.js')}}"></script>
    <script src="{{asset('assetsadmin/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('assetsadmin/js/jquery.slimscroll.js')}}"></script>
    <script src="{{asset('assetsadmin/js/select2.min.js')}}"></script>
	<script src="{{asset('assetsadmin/js/moment.min.js')}}"></script>
    <script src="{{asset('assetsadmin/js/app.js')}}"></script>
</body>


<!-- add-patient24:07-->
</html>