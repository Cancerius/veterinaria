
<!DOCTYPE html>
<html lang="en">
<head>
	<title>iniciar Sesion</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="assetslogin/images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assetslogin/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assetslogin/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assetslogin/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assetslogin/vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="assetslogin/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assetslogin/vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assetslogin/vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="assetslogin/vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assetslogin/css/util.css">
	<link rel="stylesheet" type="text/css" href="assetslogin/css/main.css">
<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100" style="background-image: url('assetslogin/images/bg-01.jpg');">
			<div class="wrap-login100 p-t-30 p-b-50">
				<span class="login100-form-title p-b-41">
					iniciar Sesion
				</span>
				<form class="login100-form validate-form p-b-33 p-t-5" method="POST" action="">
                    @csrf

					<div class="wrap-input100 validate-input" data-validate = "Enter username">
						<input class="input100" type="email" id ='email' name="email" placeholder="Correo electronico">
						<span class="focus-input100" data-placeholder="&#xe82a;"></span>
					</div>


					<div class="wrap-input100 validate-input" data-validate="Enter password">
						<input class="input100" type="password" id='password' name="password" placeholder="Contraseña">
						<span class="focus-input100" data-placeholder="&#xe80f;"></span>
					</div>

                    @error('message')

                         <p class="border border-red-500 rounded-md bg-red-100 w-full
                                text-red-600 p-2 my-2">* {{$message}}</p>

                                    @enderror

					<div class="container-login100-form-btn m-t-32">
						<button class="login100-form-btn">
							Iniciar Sesion
						</button>
					</div>

				</form>
			</div>
		</div>
	</div>
	

	<div id="dropDownSelect1"></div>
	
<!--===============================================================================================-->
	<script src="assetslogin/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="assetslogin/vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="assetslogin/vendor/bootstrap/js/popper.js"></script>
	<script src="assetslogin/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="assetslogin/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="assetslogin/vendor/daterangepicker/moment.min.js"></script>
	<script src="assetslogin/vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="assetslogin/vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="assetslogin/js/main.js"></script>

</body>
</html>