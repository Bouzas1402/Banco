<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.html");
    exit;
}

// Include config file
require_once "config/configuracion.php";

// Define variables and initialize with empty values
$DNI = $contrasena = "";
$DNI_err = $contrasena_err = $login_err = "";


// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if username is empty
    if(empty(trim($_POST["DNI"]))){
        $DNI_err = "Introduzca un DNI";
    } else{
        $DNI = trim($_POST["DNI"]);
    }

    // Check if password is empty
    if(empty(trim($_POST["contrasena"]))){
        $contrasena_err = "Introduzca su contraseña.";
    } else{
        $contrasena = trim($_POST["contrasena"]);
    }

    // Validate credentials
    if(empty($DNI_err) && empty($contrasena_err)){
        // Prepare a select statement
        $sql = "SELECT DNI, contrasena FROM cliente WHERE DNI = ?";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_DNI);

            // Set parameters
            $param_DNI = $DNI;

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Store result
                $stmt->store_result();

                // Check if username exists, if yes then verify password
                if($stmt->num_rows == 1){

                    // Bind result variables
                    $stmt->bind_result($DNI, $contrasena);
                    if($stmt->fetch()){
                        if(password_verify($contrasena, $hashed_contrasena)){
                            // Password is correct, so start a new session
                            // session_start();

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["DNI"] = $DNI;


                            // Redirect user to welcome page
                            header("location: index.php");
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
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
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="DNI" class="form-control <?php echo (!empty($DNI_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $DNI; ?>">
                <span class="invalid-feedback"><?php echo $DNI_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="contrasena" class="form-control <?php echo (!empty($contrasena_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $contrasena_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="registro.php">Sign up now</a>.</p>
        </form>

    </div>
    <?php
    foreach ($_SESSION as $codigo => $valor){
        echo " " . $codigo . " : " . $valor . "<br>";
    }
    ?>
</body>
</html>