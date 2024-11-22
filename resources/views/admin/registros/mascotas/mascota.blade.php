<!DOCTYPE html>
<html lang="en">


<!-- patients23:17-->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('assetsadmin/img/favicon.ico')}}">
    <title>Administrador</title>
    <link rel="stylesheet" type="text/css" href="{{asset('assetsadmin/css/bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assetsadmin/css/font-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assetassetsadmin/css/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assetsadmin/css/style.css')}}">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    
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
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title">Mascotas</h4>
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    </div>
                    <div class=" col-sm-8 col-9 text-right m-b-3">
                        @can('Crear mascota')
                        <a href="{{route('admin.registros.mascotas.add_mascota')}}" class="btn btn btn-outline-primary btn-rounded float-right my-1"><i class="fa fa-plus "></i> Agregar mascota</a>
                        @endcan
                        <form method="GET" action="{{ route('admin.registros.mascotas.mascota') }}" class="d-flex flex-wrap col-12 col-md-9 col-lg-6 justify-content-end m-b-3 ml-auto">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button type="submit" class="btn btn-outline-primary mx-2" data-mdb-ripple-init>
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <input id="search-focus" type="text" name="buscar" class="form-control form-control-sm" placeholder="Buscar por propietario, veterinario y mascota" value="{{ request('buscar') }}" />
                            </div>
                        </form>
                        
                    </div>    
                </div>
                
				<div class="row my-2">
                    <div class="form-group row col-12 col-md-8">
                        <label for="perPageSelect" class="col-auto col-form-label text-center text-sm-left pr-2">Mostrar:</label>
                        <div class="col-auto d-flex align-items-center pl-0">
                            <select id="perPageSelect" class="form-control w-auto">
                                <option value="5" {{ $mascotas->perPage() == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $mascotas->perPage() == 10 ? 'selected' : '' }}>10</option>
                                <option value="15" {{ $mascotas->perPage() == 15 ? 'selected' : '' }}>15</option>
                            </select>
                        </div>
                    </div>
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-bordered table-striped custom-table">
								<thead>
									<tr class="table-primary">
                                        <th>Nº</th>
										<th>Propietario</th>
                                        <th>Veterinario</th>
										<th>Mascota</th>
										<th>Raza</th>
										<th>Sexo</th>
										<th>Fecha de Nacimiento</th>
										<th>Peso (Kg)</th>
										<th>Color</th>
                                        <th>Imagen</th>
										<th class="text-right">Accion</th>
                                        
									</tr>
								</thead>
                                
								<tbody>
                                    @php
                                $contador = ($mascotas->currentPage() - 1) * $mascotas->perPage() + 1;
                                    @endphp
                                     @if($mascotas->isEmpty())
                                     <tr>
                                         <td colspan="11" class="text-center">No se encontraron registros.</td>
                                     </tr>
                                     @else
                                    @foreach($mascotas as $mascota)
									<tr class="table-success">
                                        <td>{{ $contador }}</td>
                                        <td>{{$mascota->propietario->nombre_completo}}</td>
                                        <td>{{$mascota->veterinario->nombre_completo}}</td>
                                        <td>{{$mascota->nombre_mascota}}</td>
                                        <td>{{$mascota->raza}}</td>
                                        <td>{{$mascota->sexo}}</td>
                                        <td>{{ \Carbon\Carbon::parse($mascota->fecha_nacimiento)->format('d-m-Y') }}</td>
                                        <td>{{$mascota->peso}}</td>
                                        <td>{{$mascota->color}}</td>
                                        <td>{{$mascota->imagen}}</td>
										
										<td class="col-md-1" style="width: 120px;">
                                            @can('Editar mascota')
                                            <a class="btn btn-sm btn btn btn-outline-primary" href="{{ route('admin.registros.mascotas.edit_mascotas', ['id' => $mascota->id]) }}">
                                                <i class="fa-solid fa-pencil"></i>
                                            </a>
                                            @endcan
                                            @can('Eliminar mascota')
                                            <form action="{{route('eliminar-mascota', $mascota->id)}}" class="formulario-eliminar" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn btn btn-outline-danger" type="submit">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                               </form>
                                               @endcan
                                               @can('Generar Kardex mascota')
                                               <a class="btn btn-sm btn btn btn-outline-primary" href="{{route('cardex_pdf', ['id' => $mascota->id])}}" target="_blank">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                            @endcan
                                        </td>
									</tr>
                                    @php
                                    $contador++;
                                    @endphp
									@endforeach
                                    @endif
								</tbody>
                                
							</table>
                            <div class="pagination-container d-flex justify-content-end">
                                @if ($mascotas->currentPage() > 1)
                                    <a href="{{ $mascotas->previousPageUrl() }}" class="btn btn-outline-primary">&laquo; Anterior</a>
                                @endif
        
                                @for ($i = 1; $i <= $mascotas->lastPage(); $i++)
                                    <a href="{{ $mascotas->url($i) }}" class="btn btn-outline-primary">{{ $i }}</a>
                                @endfor
        
                                @if ($mascotas->hasMorePages())
                                    <a href="{{ $mascotas->nextPageUrl() }}" class="btn btn-outline-primary">Siguiente &raquo;</a>
                                @endif
                            </div>
						</div>
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

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    <script>
            let dataTableOptions = {
                // Aquí coloca las opciones de tu DataTable
                lengthMenu: [5, 10, 15, 20, 50,100],
                columnDefs: [
                    { className: "centered", targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9,10] },
                    { orderable: false, targets: [10] },
                    
                ],
                pageLength: 5,
                destroy: true,
                language: {
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    zeroRecords: "Ningún usuario encontrado",
                    info: "Mostrando de _START_ a _END_ de un total de _TOTAL_ registros",
                    infoEmpty: "Ningún usuario encontrado",
                    infoFiltered: "(filtrados desde _MAX_ registros totales)",
                    search: "Buscar:",
                    loadingRecords: "Cargando...",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                }
            };

            $(document).ready(function() {
                // Inicializar el DataTable
                let dataTableIsInitialized = false;
                if (!dataTableIsInitialized) {
                    let dataTable = $('#mascotas-table').DataTable(dataTableOptions);
                    dataTableIsInitialized = true;
                }
            });
    </script>

<!--<script>
    $(document).ready(function() {
        var placeholderText = " Buscar por propietario, veterinario, mascota,raza, sexo y color";
        var placeholderIndex = 0;
        setInterval(function() {
            var placeholder = $("#search-focus").attr("placeholder");
            var nextChar = placeholderText.charAt(placeholderIndex);
            placeholder = placeholder.substring(1) + nextChar;
            $("#search-focus").attr("placeholder", placeholder);
            placeholderIndex = (placeholderIndex + 1) % placeholderText.length;
        }, 90);
    });
    </script>-->
    <script>
        document.getElementById('perPageSelect').addEventListener('change', function() {
            var perPage = this.value;
            var url = "{{ route('admin.registros.mascotas.mascota') }}?perPage=" + perPage;
            window.location.href = url;
        });
    </script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('eliminar') == 'ok')
    <script>
        Swal.fire({
            title: "<span class='playfair-display-font'>Eliminado!</span>",
            text: "La mascota se eliminó correctamente.",
            icon: "success"
        });
    </script>
@endif

<script>
    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
            title: "<span class='playfair-display-font'>¿Estás seguro?</span>",
            text: "La mascota se eliminará!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "¡Sí, Eliminar!",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>

@if (session('error') == 'ok')
    <script>
        Swal.fire({
            title: "<span class='playfair-display-font'>No se puede eliminar la mascota porque hay registros asociados en otra tabla.</span>",
            showClass: {
                popup: `
                    animate__animated
                    animate__fadeInUp
                    animate__faster
                `
            },
            hideClass: {
                popup: `
                    animate__animated
                    animate__fadeOutDown
                    animate__faster
                `
            }
        });
    </script>
@endif
@if (session('actualizado') == 'ok')
    <script>
        Swal.fire({
            position: "center",
            icon: "success",
            title: "Mascota actualizado correctamente",
            showConfirmButton: false,
            timer: 1500,
            customClass: {
                title: 'playfair-display-font'
            }
        });
    </script>
@endif
    <style>
        .playfair-display-font {
            font-family: "Playfair Display", serif;
            font-optical-sizing: auto;
            font-weight: <weight>;
            font-style: normal;
        }
    </style>
</body>
<!-- patients23:19-->
</html>