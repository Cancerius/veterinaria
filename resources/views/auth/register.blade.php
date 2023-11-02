
<!doctype html>
<html lang="en">
  <head>
  
  	<title>Registrarse</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link href="assets/https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<link rel="stylesheet" href="asset/css/style.css">

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
		      			<input type="text" class="form-control" placeholder="Nombre" id="name" name="name" >
                          @error('name')
                          <p class="border border-red-500 rounded-md bg-red-100 w-full text-red-600 p-2 my-2">{{$message}}</p>
                           @enderror
		      		</div>

                      <div class="form-group">
                        <input type="email" class="form-control" placeholder="Email" id="email" name="email" >
                        @error('email')
                        <p class="border border-red-500 rounded-md bg-red-100 w-full text-red-600 p-2 my-2">{{$message}}</p>
                         @enderror
                    </div>

					<div class="form-group">
                        <input type="text" class="form-control" placeholder="Rol" id="role" name="role" >
                        @error('email')
                        <p class="border border-red-500 rounded-md bg-red-100 w-full text-red-600 p-2 my-2">{{$message}}</p>
                         @enderror
                    </div>
					

	            <div class="form-group">
	              <input placeholder="Password" id="password" name="password" type="password" class="form-control" >
	              <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                  @error('password')
                  <p class="border border-red-500 rounded-md bg-red-100 w-full text-red-600 p-2 my-2">{{$message}}</p>
                   @enderror
                </div>

                <div class="form-group">
                    <input placeholder="Password confirmacion" id="password_confirmation" name="password_confirmation"type="password" class="form-control" >
                    <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                  </div>

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

	</body>
</html>