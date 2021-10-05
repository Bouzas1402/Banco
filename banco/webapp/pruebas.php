<?php

require_once "config/configuracion.php";

$IBAN = "IBAN13423590086532";

$IBAN_err = $cantidad_err = "";

// Si se envia el formulario con cuenta y cantidad se mete en el if:
if(isset($_POST["cuenta"]) && isset($_POST["cantidad"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

    // Comprobamos que el IBAN es un IBAN valido
    if(empty(trim($_POST["cuenta"]))){
        $IBAN_err = "Introduzca un IBAN.";
    } elseif (!preg_match("/^IBAN[0-9]{14}/", trim($_POST["cuenta"]))){
        $IBAN_err = "No es un IBAN valido.";
    }

    // se realiza la conexión:
    $mysqli = conexionbd();

    // Sacamos el saldo de la cuenta de la sesion;
    $sql = "SELECT saldo FROM cuenta WHERE IBAN = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $IBAN);
        if ($stmt->execute()) {

            $busqueda = $stmt->get_result();
            $saldo = $busqueda->fetch_assoc();
            $cantidad = (double)$saldo;
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt->close();
    }

    // Comprobamos que el campo cantidad no esta vacio o es mayor que el saldo de la cuenta:
    if(empty(trim($_POST["cantidad"]))){
        $cantidad_err = "Introduzca una cantidad.";
    } elseif ((double)trim($_POST["cantidad"]) > $cantidad) {
        $cantidad_err = "Es mas dinero del que tienes en la cuenta.";
    }



    if (empty($IBAN_err) && empty($cantidad_err)) {
        //Se busca el IBAN al que se quiere hacer la transferencia:
        $sql = "SELECT * FROM cuenta WHERE IBAN = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $_POST["cuenta"]);
            if ($stmt->execute()) {
                $stmt->store_result();
                // Si el IBAN esta en nuestra base de datos se sumara la cantidad a su saldo y se le restara al saldo de la cuenta de la sesión y se creara un registro en moviminetos_cuenta:
                if ($stmt->num_rows == 1) {
                    // Se crea el registro en movimientos cuenta:
                    $sql = "INSERT INTO movimientos_cuenta (IBAN, cantidad) VALUES (?, ?)";
                    if ($stmt = $mysqli->prepare($sql)) {
                        $stmt->bind_param("sd", $IBAN, $_POST["cantidad"]);
                        if ($stmt->execute()) {
                            $stmt->store_result();
                        } else {
                            echo "Oops! Something went wrong. Please try again later.";
                        }
                        // Se suma la cantidad a la cuenta de nuestra base de datos:
                        $sql = "UPDATE cuenta SET saldo = saldo + ? WHERE IBAN = ?";
                        if ($stmt = $mysqli->prepare($sql)) {
                            $stmt->bind_param("ds", $_POST["cantidad"], $_POST["cuenta"]);
                            if ($stmt->execute()) {
                                $stmt->store_result();
                            } else {
                                echo "Oops! Something went wrong. Please try again later.";
                            }
                        }
                        // Se resta la cantidad a la cuenta de la sesión:
                        $sql = "UPDATE cuenta SET saldo = (saldo - ?) WHERE IBAN = ?";
                        if ($stmt = $mysqli->prepare($sql)) {
                            $stmt->bind_param("ds", $_POST["cantidad"], $IBAN);
                            if ($stmt->execute()) {
                                $stmt->store_result();
                            } else {
                                echo "Oops! Something went wrong. Please try again later.";
                            }
                        }
                    }
                    // Si no esta en nuestra base de datos se creara el registro en movimientos_cuenta y se restara el saldo en la cuenta de la sesion:
                } else {
                    // Se crea el registro en movimientos_cuenta
                    $sql = "INSERT INTO movimientos_cuenta (IBAN, cantidad) VALUES (?, ?)";
                    if ($stmt = $mysqli->prepare($sql)) {
                        $stmt->bind_param("sd", $IBAN, $_POST["cantidad"]);
                        if ($stmt->execute()) {
                            $stmt->store_result();
                        } else {
                            echo "Oops! Something went wrong. Please try again later.";
                        }
                    }
                    // Se resta la cantidad a la cuenta de la sesión:
                    $sql = "UPDATE cuenta SET saldo = (saldo - ?) WHERE IBAN = ?";
                    if ($stmt = $mysqli->prepare($sql)) {
                        $stmt->bind_param("ds", $_POST["cantidad"], $IBAN);
                        if ($stmt->execute()) {
                            $stmt->store_result();
                        } else {
                            echo "Oops! Something went wrong. Please try again later.";
                        }
                    }
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Cierre del statement:
            $stmt->close();
        }
    }
        // Se cierra la conexión:
        $mysqli->close();
}


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>

    <!-- Iconos (version gratuita)-->
    <script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" crossorigin="anonymous"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>





<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group">
        <label>Cuenta</label>
        <input type="text" name="cuenta" step="any" class="form-control  <?php echo (!empty($IBAN_err)) ? 'is-invalid' : ''; ?>" >
        <span class="invalid-feedback"><?php echo $IBAN_err; ?></span>
    </div>
    <div class="form-group">
        <label>cantidad</label>
        <input type="number" name="cantidad" class="form-control"  <?php echo (!empty($cantidad_err)) ? 'is-invalid' : ''; ?>">
        <span class="invalid-feedback"><?php echo $cantidad_err; ?></span>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Login">
    </div>
</form>


<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
