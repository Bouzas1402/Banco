<?php
require_once "config/configuracion.php";



function hacerTransferencia ($cantidad, $cuenta, $iban, $ingreso_gasto){
    $saldo = $tranferencia_hecha = "";
    $iban_err = $cantidad_err = "";

    // se realiza la conexión:
    $mysqli = conexionbd();
    if ($ingreso_gasto == "transferencia") {
        // Comprobamos que el IBAN es un IBAN valido
        if (empty(trim($cuenta))) {
            $iban_err = "Introduzca un IBAN.";
        } elseif (!preg_match("/^ES[0-9]{22}/", $cuenta)) {
            $iban_err = "No es un IBAN valido.";
        }
        // Sacamos el saldo de la cuenta de la sesion;
        $sql = "SELECT saldo FROM cuenta WHERE iban = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $iban);
            if ($stmt->execute()) {
                $busqueda = $stmt->get_result();
                $saldo = $busqueda->fetch_assoc();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        // Comprobamos que el campo cantidad no esta vacio o es mayor que el saldo de la cuenta:
        if (empty(trim($cantidad))) {
            $cantidad_err = "Introduzca una cantidad.";
        } elseif (trim($cantidad) > $saldo["saldo"]) {
            $cantidad_err = "Es mas dinero del que tienes en la cuenta.";
        } elseif (trim($cantidad) <= 0 || trim($cantidad) === null){
            $cantidad_err = "Cantidad introducida incorrecta.";
        }

        if (empty($iban_err) && empty($cantidad_err)) {
            //Se busca el IBAN al que se quiere hacer la transferencia:
            $sql = "SELECT * FROM cuenta WHERE iban = ?";
            if ($stmt = $mysqli->prepare($sql)) {
                $stmt->bind_param("s", $cuenta);
                if ($stmt->execute()) {
                    $stmt->store_result();
                    // Si el IBAN esta en nuestra base de datos se sumara la cantidad a su saldo y se le restara al saldo de la cuenta de la sesión y se creara un registro en moviminetos_cuenta:
                    if ($stmt->num_rows == 1) {
                        // Se crea el registro en movimientos cuenta:
                        $sql = "INSERT INTO movimientos_cuenta (iban, cantidad, cuenta_recepcion) VALUES (?, ?, ?)";
                        if ($stmt = $mysqli->prepare($sql)) {
                            $stmt->bind_param("sds", $iban, $cantidad, $cuenta);
                            if ($stmt->execute()) {
                                $stmt->store_result();
                            } else {
                                echo "Oops! Something went wrong. Please try again later.";
                            }
                            // Se suma la cantidad a la cuenta de nuestra base de datos:
                            $sql = "UPDATE cuenta SET saldo = saldo + ? WHERE iban = ?";
                            if ($stmt = $mysqli->prepare($sql)) {
                                $stmt->bind_param("ds", $cantidad, $cuenta);
                                if ($stmt->execute()) {
                                    $stmt->store_result();
                                } else {
                                    echo "Oops! Something went wrong. Please try again later.";
                                }
                            }
                            // Se resta la cantidad a la cuenta de la sesión:
                            $sql = "UPDATE cuenta SET saldo = (saldo - ?) WHERE iban = ?";
                            if ($stmt = $mysqli->prepare($sql)) {
                                $stmt->bind_param("ds", $cantidad, $iban);
                                if ($stmt->execute()) {
                                    $stmt->store_result();
                                    $saldo["saldo"] = doubleval($saldo["saldo"]) - doubleval($cantidad);
                                } else {
                                    echo "Oops! Something went wrong. Please try again later.";
                                }
                            }
                        }
                        // Si no esta en nuestra base de datos se creara el registro en movimientos_cuenta y se restara el saldo en la cuenta de la sesion:
                    } else {
                        // Se crea el registro en movimientos_cuenta
                        $sql = "INSERT INTO movimientos_cuenta (iban, cantidad) VALUES (?, ?, ?)";
                        if ($stmt = $mysqli->prepare($sql)) {
                            $stmt->bind_param("sds", $iban, $cantidad, $cuenta);
                            if ($stmt->execute()) {
                                $stmt->store_result();
                            } else {
                                echo "Oops! Something went wrong. Please try again later.";
                            }
                        }
                        // Se resta la cantidad a la cuenta de la sesión:
                        $sql = "UPDATE cuenta SET saldo = (saldo - ?) WHERE iban = ?";
                        if ($stmt = $mysqli->prepare($sql)) {
                            $stmt->bind_param("ds", $cantidad, $iban);
                            if ($stmt->execute()) {
                                $stmt->store_result();
                                $saldo["saldo"] = doubleval($saldo["saldo"]) - doubleval($cantidad);
                            } else {
                                echo "Oops! Something went wrong. Please try again later.";
                            }
                        }
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
            $tranferencia_hecha = "Dinero transferido";
        }
        $stmt->close();
    } elseif ($ingreso_gasto == "ingreso") {

        $sql = "SELECT saldo FROM cuenta WHERE iban = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $iban);
            if ($stmt->execute()) {
                $busqueda = $stmt->get_result();
                $saldo = $busqueda->fetch_assoc();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        if ($cantidad > 1000) {
            $cantidad_err = "Para ingresar mas dinero debe acudir a una de nuestras oficinas.";
        } elseif (trim($cantidad) <= 0 || trim($cantidad) === null){
            $cantidad_err = "Cantidad introducida incorrecta.";
        }
        if(empty($cantidad_err)){
            $sql = "UPDATE cuenta SET saldo = (saldo + ?) WHERE iban = ?";
            if ($stmt = $mysqli->prepare($sql)) {
                $stmt->bind_param("ds", $cantidad, $iban);
                if ($stmt->execute()) {
                    $stmt->store_result();
                    $saldo["saldo"] = doubleval($saldo["saldo"]) + doubleval($cantidad);
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
            $sql = "INSERT INTO movimientos_cuenta (iban, cantidad, cuenta_recepcion) VALUES (?, ?, ?)";
            if ($stmt = $mysqli->prepare($sql)) {
                $ingreso_web = "ingreso desde web";
                $stmt->bind_param("sds", $iban, $cantidad, $ingreso_web);
                if ($stmt->execute()) {
                    $stmt->store_result();
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
            $tranferencia_hecha = "Dinero transferido";
            $stmt->close();
        }
    }
    // Se cierra la conexión:
    $mysqli->close();
    return array($saldo["saldo"], $tranferencia_hecha, $iban_err, $cantidad_err);
}
