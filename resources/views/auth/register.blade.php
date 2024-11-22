
<!doctype html>
<html lang="en">
  <head>
  
  	<title>Registrarse</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link href="assets/https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<link rel="stylesheet" href="asset/css/style.css">
	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

	</head>
	<body class=" img js-fullheight" style="background-image: url(asset/images/bg1.jpg);">
		
	<section class="ftco-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-6 text-center mb-5">
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-md-6 col-lg-4">
					<div class="login-wrap p-0">
		      	<h3 class="mb-4 text-center">Registrarse</h3>
		      	<form  class="signin-form" method="POST">
                    @csrf
		      		<div class="form-group">
		      			<input type="text" class="form-control" placeholder="Nombre Completo" id="nombre_completo" name="nombre_completo" >
                          @error('nombre_completo')
                          <p class="border border-red-500 rounded-md bg-red-100 w-full text-red-600 p-2 my-2">El campo de nombre completo es obligatorio.</p>
                           @enderror
		      		</div>

                      <div class="form-group">
                        <input type="email" class="form-control" placeholder="Correo electronico" id="email" name="email" >
                        @error('email')
                        <p class="border border-red-500 rounded-md bg-red-100 w-full text-red-600 p-2 my-2">El campo de correo electrónico es obligatorio.</p>
                         @enderror
                    </div>

						<div class="form-group">
							<input placeholder="Contraseña" id="password" name="password" type="password" class="form-control">
							<span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password" onclick="togglePasswordVisibility('#password')"></span>
							@error('password')
							<p class="border border-red-500 rounded-md bg-red-100 w-full text-red-600 p-2 my-2">El campo de contraseña es obligatorio.</p>
							@enderror
						</div>
						
						<script>
							function togglePasswordVisibility(passwordFieldId) {
								var passwordField = $(passwordFieldId);
						
								// Cambia el tipo de entrada entre "password" y "text"
								var fieldType = passwordField.attr("type") === "password" ? "text" : "password";
								passwordField.attr("type", fieldType);
						
								// Cambia el icono del ojo entre abierto y cerrado
								$(passwordFieldId + "+ .field-icon").toggleClass("fa-eye fa-eye-slash");
						
								// Enfócate en el campo de contraseña después de cambiar el tipo de entrada
								passwordField.focus();
							}
						</script>

								<div class="form-group">
									<input placeholder="Repita la contraseña" id="password_confirmation" name="password_confirmation" type="password" class="form-control">
									<span toggle="#password_confirmation" class="fa fa-fw fa-eye field-icon toggle-password_confirmation" onclick="togglePasswordVisibility('#password_confirmation')"></span>
								</div>
								
								<script>
									function togglePasswordVisibility(passwordFieldId) {
										var passwordField = $(passwordFieldId);
								
										// Cambia el tipo de entrada entre "password" y "text"
										var fieldType = passwordField.attr("type");
										passwordField.attr("type", fieldType === "password" ? "text" : "password");
								
										// Cambia el icono del ojo entre abierto y cerrado
										$(passwordFieldId + "+ .field-icon").toggleClass("fa-eye fa-eye-slash");
									}
								</script>
				 
	            <div class="form-group">
	            	<button type="submit" class="form-control btn btn-primary submit px-3">Registrarse</button>
	            </div>

	          </form>
	          
		      </div>
				</div>
			</div>
		</div>
	</section>

	<script src="asset/js/jquery.min.js"></script>
  <script src="asset/js/popper.js"></script>
  <script src="asset/js/bootstrap.min.js"></script>
  <script src="asset/js/main.js"></script>
  <script>
	$(document).ready(function() {
	  $(".toggle-password").click(function() {
		// Obtén el campo de contraseña
		var passwordField = $("#password");
  
		// Cambia el tipo de entrada entre "password" y "text"
		var fieldType = passwordField.attr("type");
		passwordField.attr("type", fieldType === "password" ? "text" : "password");
  
		// Cambia el icono del ojo entre abierto y cerrado
		$(this).toggleClass("fa-eye fa-eye-slash");
	  });
	});
  </script>
  

	</body>
</html>