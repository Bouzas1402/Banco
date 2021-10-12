<?php
// Include config file
require_once "config/configuracion.php";

// Define variables and initialize with empty values
$nombre = $apellido1 = $apellido2 = $nacionalidad = $telefono = $dni = $contrasena = $confirm_contrasena = "";
$nombre_err = $apellido1_err = $apellido2_err = $nacionalidad_err = $telefono_err =$dni_err = $contrasena_err = $confirm_contrasena_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $mysqli = conexionbd();
    // Validate username
    if(empty(trim($_POST["dni"]))){
        $dni_err = "Por favor introduce un DNI válido.";
    } elseif(!preg_match('/[0-9]{7,8}[A-Z]/', trim($_POST["dni"]))){
        $dni_err = "El DNI sólo puede contener letras, numeros, y guión bajo.";
    } else{
        // Prepare a select statement
        $sql = "SELECT * FROM cliente WHERE DNI = ?";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $dni = trim($_POST["dni"]);
            $stmt->bind_param("s", $dni);

            // Set parameters


            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // store result
                $result = $stmt->get_result();

                if($result->num_rows == 1){
                    $dni_err = "El DNI ya está en uso.";
                } else{
                    $dni = trim($_POST["dni"]);
                }
            } else{
                echo "Ha habido un error. Por favor inténtelo de nuevo más tarde.";
            }

            // Close statement
            $stmt->close();
        }
    }

    if(empty(trim($_POST["nombre"]))){
        $nombre_err = "Por favor, introduce un nombre.";
    }else{
        $nombre = trim($_POST["nombre"]);
    }

    if(empty(trim($_POST["apellido1"]))){
        $apellido1_err = "Por favor, introduce tu primer apellido.";
    }else{
        $apellido1 = trim($_POST["apellido1"]);
    }

    if(empty(trim($_POST["apellido2"]))){
        $apellido2_err = "Por favor, introduce tu segundo apellido.";
    }else{
        $apellido2 = trim($_POST["apellido2"]);
    }

    if(empty(trim($_POST["nacionalidad"]))){
        $nacionalidad_err = "Por favor, introduce tu nacionalidad.";
    }else{
        $nacionalidad = trim($_POST["nacionalidad"]);
    }

    if(empty(trim($_POST["telefono"]))){
        $telefono_err = "Por favor, introduce tu número de teléfono.";
    }else{
        $telefono = trim($_POST["telefono"]);
    }

    // Validate password
    if(empty(trim($_POST["contrasena"]))){
        $contrasena_err = "Por favor, introduce una contraseña.";
    } elseif(strlen(trim($_POST["contrasena"])) <= 6){
        $contrasena_err = "La contraseña debe tener al menos 6 caracteres.";
    } else{
        $contrasena = trim($_POST["contrasena"]);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirm_contrasena"]))){
        $confirm_contrasena_err = "Confirma la contraseña.";
    } elseif($_POST["contrasena"] != $_POST["confirm_contrasena"]){
        $confirm_contrasena_err = "La contraseña no coincide.";
    } else {
        $confirm_contrasena = trim($_POST["confirm_contrasena"]);
    }

    // Check input errors before inserting in database
    if(empty($nombre_err) && empty($apellido1_err) && empty($apellido2_err) &&
        empty($nacionalidad_err) && empty($telefono_err) && empty($dni_err) && empty($contrasena_err) &&
        empty($confirm_contrasena_err)){

        // Prepare an insert statement
        $sql = "INSERT INTO cliente VALUES (?, ?, ?, ?, ?, ?, ?)";

        if($stmt = $mysqli->prepare($sql)){

            // Set parameters
            $param_dni = $dni;
            $param_nombre = $nombre;
            $param_apellido1 = $apellido1;
            $param_apellido2 = $apellido2;
            $param_nacionalidad = $nacionalidad;
            $param_telefono = $telefono;
            $param_contrasena = password_hash($contrasena, PASSWORD_DEFAULT);
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sssssss", $param_dni, $param_nombre, $param_apellido1,
                $param_apellido2, $param_nacionalidad, $param_telefono, $param_contrasena);

            // Creates a password hash

            // Attempt to execute the prepared statement
            if($stmt->execute()){


            } else{
                echo "Ha habido un error. Por favor inténtelo de nuevo más tarde.";
            }

            // Close statement

        }

        $nuevo_iban = "IBAN";
        for ($i = 0; $i < 14; $i++){
            $nuevo_iban = $nuevo_iban . rand(0,9);
        }

        $sql = "INSERT INTO cuenta (IBAN, saldo, DNI) VALUES (?, 3500, ?)";

        if ($stmt = $mysqli->prepare($sql)){

            $stmt->bind_param("ss", $nuevo_iban,$dni);
            // Redirect to login page
            if($stmt->execute()){

                header("location: login.php");
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

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
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
<div class="wrapper">
    <h2>Registro</h2>
    <p>Por favor rellena el formulario para crear una cuenta.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["SCRIPT_NAME"]); ?>" method="post">
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control <?php echo (!empty($nombre_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nombre; ?>">
            <span class="invalid-feedback"><?php echo $nombre_err; ?></span>
        </div>
        <div class="form-group">
            <label>Primer Apellido</label>
            <input type="text" name="apellido1" class="form-control <?php echo (!empty($apellido1_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $apellido1; ?>">
            <span class="invalid-feedback"><?php echo $apellido1_err; ?></span>
        </div>
        <div class="form-group">
            <label>Segundo Apellido</label>
            <input type="text" name="apellido2" class="form-control <?php echo (!empty($apellido2_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $apellido2; ?>">
            <span class="invalid-feedback"><?php echo $apellido2_err; ?></span>
        </div>
        <div class="form-group">
            <label>DNI</label>
            <input type="text" name="dni" class="form-control <?php echo (!empty($dni_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $dni; ?>">
            <span class="invalid-feedback"><?php echo $dni_err; ?></span>
        </div>
        <div class="form-group">
            <label>Nacionalidad</label>
            <input type="text" name="nacionalidad" class="form-control <?php echo (!empty($nacionalidad_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nacionalidad; ?>">
            <span class="invalid-feedback"><?php echo $nacionalidad_err; ?></span>
        </div>
        <div class="form-group">
            <label>Teléfono</label>
            <input type="text" name="telefono" class="form-control <?php echo (!empty($telefono_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $telefono; ?>">
            <span class="invalid-feedback"><?php echo $telefono_err; ?></span>
        </div>
        <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="contrasena" class="form-control <?php echo (!empty($contrasena_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $contrasena; ?>">
            <span class="invalid-feedback"><?php echo $contrasena_err; ?></span>
        </div>
        <div class="form-group">
            <label>Confirma la contraseña</label>
            <input type="password" name="confirm_contrasena" class="form-control <?php echo (!empty($confirm_contrasena_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_contrasena; ?>">
            <span class="invalid-feedback"><?php echo $confirm_contrasena_err; ?></span>
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Registrarse">
            <input type="reset" class="btn btn-secondary ml-2" value="Borrar">
        </div>
        <p>¿Ya tienes una cuenta? <a href="login.php">Accede</a>.</p>
    </form>
</div>
</body>
</html>