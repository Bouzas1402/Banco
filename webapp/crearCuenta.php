<?php
require_once "config/configuracion.php";

function crearCuenta ($dni, $contrasena_crear_cuenta){

    $cuenta_creada = "";
    $contrasena_err = $cantidad_err = $crear_cuenta_err = "";

    if(empty(trim($contrasena_crear_cuenta))){
        $contrasena_err = "Por favor ingrese una contraseña.";
    } else{
        $contrasena = $contrasena_crear_cuenta;
    }
    if(empty($contrasena_err)) {
        $mysqli = conexionbd();
        $sql = "SELECT contrasena FROM cliente WHERE dni = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $dni);
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();
                // Bind result variables
                $stmt->bind_result($hashed_password);
                if ($stmt->fetch()) {
                    if (password_verify($contrasena, $hashed_password)) {
                        $sql = "SELECT iban FROM cuenta WHERE dni = ?";
                        if ($stmt = $mysqli->prepare($sql)) {
                            $stmt->bind_param("s", $dni);
                            if ($stmt->execute()) {
                                $stmt->store_result();
                                if ($stmt->num_rows >= 3) {
                                    $crear_cuenta_err = "Ya tienes tres cuentas, para abrir otra dirijase a una de nuestras oficinas.";
                                } else {
                                    $ibanCorrecto = false;
                                    do {
                                        $ibanCorrecto = false;
                                        $nuevo_iban = crearIban();
                                        $sql = "SELECT * FROM cuenta WHERE iban = ?";
                                        if ($stmt = $mysqli->prepare($sql)){
                                            $stmt->bind_param("s", $nuevo_iban);
                                            if ($stmt->execute()){
                                                $stmt->store_result();
                                                if ($stmt->num_rows == 1){
                                                    $ibanCorrecto = true;
                                                }
                                            } else {
                                                echo "Oops! Something went wrong. Please try again later.";
                                            }
                                        }
                                    } while ($ibanCorrecto);
                                    $sql = "INSERT INTO cuenta (iban, saldo, dni) VALUES (?, 3500, ?)";
                                    if ($stmt = $mysqli->prepare($sql)) {
                                        $stmt->bind_param("ss", $nuevo_iban, $dni);
                                        // Redirect to login page
                                        if ($stmt->execute()) {

                                            $cuenta_creada = "Cuenta creada. Felicidades";
                                        } else {
                                            echo "Oops! Something went wrong. Please try again later.";
                                        }
                                    }
                                }
                            }else {
                                echo "Oops! Something went wrong. Please try again later.";
                            }
                        }
                    } else {
                        // Password is not valid, display a generic error message
                        $contrasena_err = "contraseña incorrecta.";
                    }
                }
            } else {
                echo "Ha habido un error. Por favor inténtelo de nuevo más tarde.";
            }
        }
        // Close statement
        $stmt->close();
        $mysqli->close();
    }
    return array($contrasena_err, $cantidad_err, $crear_cuenta_err, $cuenta_creada);
}
function crearIban (){
    $nuevo_iban = "ES";
    for ($i = 0; $i < 22; $i++){
        $nuevo_iban = $nuevo_iban . rand(0,9);
    }
    return $nuevo_iban;
}
