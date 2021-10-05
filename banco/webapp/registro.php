<?php
// Include config file
require_once "config/configuracion.php";

// Define variables and initialize with empty values
$DNI = $nombre = $apellido1 = $apellido2 = $nacionalidad = $telefono = $fecha_nacimiento = "";
$DNI_err = $nombre_err = $apellido1_err = $nacionalidad_err = $telefono_err = $fecha_nacimiento_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validacion del DNI
    if(empty(trim($_POST["DNI"]))){
        $DNI_err = "Por favor introduzca un DNI.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["DNI"]))){
        $DNI_err = "El DNI debe ser 8 digitos y 1 letra.";
    } else{
        // Prepare a select statement
        $sql = "SELECT nombre FROM cliente WHERE DNI = ?";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_DNI);

            // Set parameters
            $param_DNI = trim($_POST["DNI"]);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // store result
                $stmt->store_result();

                if($stmt->num_rows == 1){
                    $DNI_err = "This username is already taken.";
                } else{
                    $DNI = trim($_POST["DNI"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Validacion del nombre
    if(empty(trim($_POST["nombre"]))){
        $nombre_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["nombre"]))){
        $nombre_err = "Username can only contain letters, numbers, and underscores.";
    }

    // Validación del primer apellido
    if(empty(trim($_POST["apellido1"]))){
        $apellido1_err = "Por favor introduzca un apellido.";
    } else{
        $apellido1 = trim($_POST["apellido1"]);
    }

    // Validacion del segundo apellido
    $apellido2 = trim($_POST["apellido2"]);


    // Validacion nacionalidad:

    if(empty((trim($_POST["nacionalidad"])))){
        $nacionalidad_err = "Introduzca alguna nacionalidad";
    } else {
        $nacionalidad = trim($_POST["nacionalidad"]);
    }

    // Validacion telefono:



    // Validacion nacimiento:



    // Check input errors before inserting in database
    if(empty($DNI_err) && empty($nombre_err) && empty($apellido1_err) && empty($nacionalidad_err) && empty($telefono_err) && empty($fecha_nacimiento_err)){

        // Prepare an insert statement
        $sql = "INSERT INTO cliente VALUES (?, ?, ?, ?, ?, ?, ?)";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sssssss", $param_DNI, $param_nombre, $param_apellido1, $param_apellido2, $param_nacionalidad, $param_telefono, $param_fecha_nacimiento);

            // Set parameters
            $param_DNI = $DNI;
            $param_nombre = $nombre;
            $param_apellido1 = $apellido1;
            $param_apellido2 = $apellido2;
            $param_nacionalidad = $nacionalidad;
            $param_telefono = $telefono;
            $param_fecha_nacimiento = $fecha_nacimiento;

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Oops! Tenmemos problemas en estos momentos, intentelo mas tarde.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
<div class="wrapper">
    <h2>Sign Up</h2>
    <p>Please fill this form to create an account.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>DNI</label>
            <input type="text" name="DNI" class="form-control <?php echo (!empty($DNI_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $DNI; ?>">
            <span class="invalid-feedback"><?php echo $DNI_err; ?></span>
        </div>
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
            <label>Nacionalidad</label>
            <input type="text" name="nacionalidad" class="form-control <?php echo (!empty($nacionalidad_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nacionalidad; ?>">
            <span class="invalid-feedback"><?php echo $nacionalidad_err; ?></span>
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <input type="reset" class="btn btn-secondary ml-2" value="Reset">
        </div>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </form>
</div>
</body>
</html>