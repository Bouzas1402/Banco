<?php
require_once "config/configuracion.php";

$dni=$_GET["dni"];

$mysqli = conexionbd();

$sql = "SELECT * FROM cuenta WHERE DNI = ?";

if ($stmt = $mysqli->prepare($sql)) {

    $stmt->bind_param("s", $dni);

    if ($stmt->execute()) {
        $filas = $stmt->get_result();
        $lista = "";
        foreach ($filas as $valor) {
            $iban = $valor["IBAN"];
            $saldo = $valor["saldo"];

            $lista = $lista . "<li class='list-group-item d-flex justify-content-between align-items-start'>
                <div class='ms-2 me-auto fw-bold'>" . $iban . "</div>
                <button type='submit' name='cambiar_cuenta' value='" . $iban . "," . $saldo . "' class='badge bg-primary text-white rounded-pill'>" . $saldo . "</button>
            </li>";
        }

        echo $lista;

    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    $stmt->close();

}
$mysqli->close();
