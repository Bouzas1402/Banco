<?php

require_once "config/configuracion.php";

$IBAN = "IBAN13423590086532";
$DNI = "9876443S";
$IBAN_err = $cantidad_err = "";
$tranferencia_hecha = "";


// Si se envia el formulario con cuenta y cantidad se mete en el if:
if(isset($_POST["cuenta"]) && isset($_POST["cantidad"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

    // se realiza la conexión:
    $mysqli = conexionbd();

    if ($_POST["ingreso_gasto"] == "transferencia") {

        // Comprobamos que el IBAN es un IBAN valido
        if (empty(trim($_POST["cuenta"]))) {
            $IBAN_err = "Introduzca un IBAN.";
        } elseif (!preg_match("/^IBAN[0-9]{14}/", trim($_POST["cuenta"]))) {
            $IBAN_err = "No es un IBAN valido.";
        }

        // Sacamos el saldo de la cuenta de la sesion;
        $sql = "SELECT saldo FROM cuenta WHERE IBAN = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $IBAN);
            if ($stmt->execute()) {

                $busqueda = $stmt->get_result();
                $saldo = $busqueda->fetch_assoc();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Comprobamos que el campo cantidad no esta vacio o es mayor que el saldo de la cuenta:
        if (empty(trim($_POST["cantidad"]))) {
            $cantidad_err = "Introduzca una cantidad.";
        } elseif (trim($_POST["cantidad"]) > $saldo["saldo"]) {
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
                        $sql = "INSERT INTO movimientos_cuenta (IBAN, cantidad, cuenta_recepcion) VALUES (?, ?, ?)";
                        if ($stmt = $mysqli->prepare($sql)) {
                            $stmt->bind_param("sds", $IBAN, $_POST["cantidad"], $_POST["cuenta"]);
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
                                $stmt->close();
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
                                $stmt->close();
                            }
                            $stmt->close();
                        }
                        // Si no esta en nuestra base de datos se creara el registro en movimientos_cuenta y se restara el saldo en la cuenta de la sesion:
                    } else {
                        // Se crea el registro en movimientos_cuenta
                        $sql = "INSERT INTO movimientos_cuenta (IBAN, cantidad) VALUES (?, ?, ?)";
                        if ($stmt = $mysqli->prepare($sql)) {
                            $stmt->bind_param("sds", $IBAN, $_POST["cantidad"], $_POST["cuenta"]);
                            if ($stmt->execute()) {
                                $stmt->store_result();
                            } else {
                                echo "Oops! Something went wrong. Please try again later.";
                            }
                            $stmt->close();
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
                            $stmt->close();
                        }
                    }

                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }


            }

            $tranferencia_hecha = "Dinero transferido";
        }
    } elseif ($_POST["ingreso_gasto"] == "ingreso") {

        if ($_POST["cantidad"] > 1000) {
            $cantidad_err = "Para ingresar mas dinero debe acudir a una de nuestras oficinas.";
        }
        if(empty($cantidad_err)){
        // Se resta la cantidad a la cuenta de la sesión:
        $sql = "UPDATE cuenta SET saldo = (saldo + ?) WHERE IBAN = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ds", $_POST["cantidad"], $IBAN);
            if ($stmt->execute()) {
                $stmt->store_result();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }

            $sql = "INSERT INTO movimientos_cuenta (IBAN, cantidad, cuenta_recepcion) VALUES (?, ?, ?)";
            if ($stmt = $mysqli->prepare($sql)) {
                $ingreso_web = "ingreso desde web";
                $stmt->bind_param("sds", $IBAN, $_POST["cantidad"], $ingreso_web);
                if ($stmt->execute()) {
                    $stmt->store_result();
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
                $stmt->close();
            }

            $tranferencia_hecha = "Dinero transferido";
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

    <script type="text/javascript">

        document.addEventListener("DOMContentLoaded", function (event){



        });

    </script>

    <!-- Iconos (version gratuita)-->
    <script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" crossorigin="anonymous"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>





<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group">
        <label>
            <input type="radio" name="ingreso_gasto" value="transferencia" required> Transferencia
        </label>
        <label>
            <input type="radio" name="ingreso_gasto" value="ingreso"> Ingreso
        </label>
    </div>

    <div class="form-group">
        <label>Cuenta</label>
        <input type="text" name="cuenta" step="any" class="form-control  <?php echo (!empty($IBAN_err)) ? 'is-invalid' : ''; ?>" >
        <span class="invalid-feedback"><?php echo $IBAN_err; ?></span>
    </div>
    <div class="form-group">
        <label>cantidad</label>
        <input type="number" name="cantidad" class="form-control  <?php echo (!empty($cantidad_err)) ? 'is-invalid' : ''; echo (!empty($tranferencia_hecha)) ? 'is-valid' : ''; ?>">
        <span class="invalid-feedback"><?php echo $cantidad_err; ?></span>
        <span class="valid-feedback"><?php echo $tranferencia_hecha; ?></span>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Login">
    </div>
</form>



<div class="row align-content-center  h-75 mb-5">
    <div class=" me-2 btn-group-vertical align-bottom w-100">
        <div class="btn btn-default d-flex justify-content-between align-items-start">
            <div class="ms-2 me-auto fw-bold">cuenta 1</div>
            <span class="badge bg-primary text-white rounded-pill">14</span>
        </div>
        <div class="btn btn-default d-flex justify-content-between align-items-start">
            <div class="ms-2 me-auto fw-bold">cuenta 2</div>
            <span class="badge bg-primary text-white rounded-pill">14</span>
        </div>
        <div class="btn btn-default d-flex justify-content-between align-items-start">
            <div class="ms-2 me-auto fw-bold">cuenta 3</div>
            <span class="badge bg-primary text-white rounded-pill">14</span>
        </div>
        <div class="btn btn-default d-flex justify-content-between align-items-start" onclick="">
            <div class="ms-2 me-auto fw-bold">cuenta 3</div>
            <span class="badge bg-primary text-white rounded-pill">14</span>
        </div>
    </div>
</div>


<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
