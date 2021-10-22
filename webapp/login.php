<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}
 
// Include config file
require_once "config/configuracion.php";


// Define variables and initialize with empty values
$dni = $contrasena = $nombre = $apellido1 = "";
$dni_err = $contrasena_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $mysqli = conexionbd();

    // Check if username is empty
    if(empty(trim($_POST["dni"]))){
        $dni_err = "Por favor, ingrese un DNI válido.";
    } else{
        $dni = trim($_POST["dni"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["contrasena"]))){
        $contrasena_err = "Por favor ingrese una contraseña.";
    } else{
        $contrasena = trim($_POST["contrasena"]);
    }
    
    // Validate credentials
    if(empty($dni_err) && empty($contrasena_err)){
        // Prepare a select statement
        $sql = "SELECT dni, contrasena FROM cliente WHERE dni = ?";
        
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $param_dni = $dni;
            $stmt->bind_param("s", $param_dni);
            
            // Set parameters

            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Store result
                $stmt->store_result();
                
                // Check if username exists, if yes then verify password
                if($stmt->num_rows == 1){
                    // Bind result variables
                    $stmt->bind_result($dni, $hashed_password);
                    if($stmt->fetch()){
                        if(password_verify($contrasena, $hashed_password)){
                            // Password is correct, so start a new session

                            $sql = "SELECT nombre, apellido1 FROM cliente WHERE dni = ?";
                            if($stmt = $mysqli->prepare($sql)) {
                                $stmt->bind_param("s", $dni);
                                if ($stmt->execute()) {
                                    $stmt->store_result();
                                    $stmt->bind_result($nombre, $apellido1);
                                    $stmt->fetch();
                                } else {
                                    echo "Oops! Something went wrong. Please try again later.";
                                }
                            }

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["dni"] = $dni;
                            $_SESSION["nombre"] = $nombre;
                            $_SESSION["apellido1"] = $apellido1;
                            $_SESSION["iban"] = "";
                            $_SESSION["saldo"] = 0;

                            
                            // Redirect user to welcome page
                            header("location: index.php");

                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "DNI o contraseña incorrecta.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "DNI o contraseña incorrecta.";
                }
            } else{
                echo "Ha habido un error. Por favor inténtelo de nuevo más tarde.";
            }

            // Close statement

        }
        $stmt->close();
    }
    // Close connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/sign-in/">

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>
    <!-- Custom styles for this template -->
    <link href="assets/css/signin.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="text-center">
<!--<div class="wrapper">-->

<?php
if(!empty($login_err)){
    echo '<div class="alert alert-danger">' . $login_err . '</div>';
}
?>

<form class="form-signin" action="<?php echo htmlspecialchars($_SERVER["SCRIPT_NAME"]); ?>" method="post">
    <!--<div class="form-group">-->
    <img class="mb-4" src="assets/img/confundido.png" alt="" width="72" height="72">
    <h2>Acceso</h2>
    <p>Por favor introduce tus credenciales para acceder.</p>
    <label for="inD" class="sr-only">DNI</label>
    <input type="text" id="inD" name="dni"  autofocus class="form-control <?php echo (!empty($dni_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $dni; ?>">
    <span class="invalid-feedback"><?php echo $dni_err; ?></span>
    <!-- </div>
     <div class="form-group"> -->
    <label for="inCont" class="sr-only">Contraseña</label>
    <input type="password" id="inCont" name="contrasena" class="form-control <?php echo (!empty($contrasena_err)) ? 'is-invalid' : ''; ?>">
    <span class="invalid-feedback"><?php echo $contrasena_err; ?></span>
    <!-- </div>
     <div class="form-group"> -->
    <input type="submit" class="btn btn-lg btn-block col" value="Acceder">
    <!-- </div>-->
    <p class="my-1">¿No tienes una cuenta? <a href="registro.php">¡Regístrate!</a>.</p>
</form>
<!-- </div>-->
</body>
</html>