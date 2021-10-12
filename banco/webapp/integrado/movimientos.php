<?php
require_once "config/configuracion.php";
$cuenta=$_GET["cuenta"];
$mysqli = conexionbd();

$sql = "SELECT * FROM movimientos_cuenta WHERE IBAN = ?";

if ($stmt = $mysqli->prepare($sql)) {

    $stmt->bind_param("s", $cuenta);

    if ($stmt->execute()) {
        $filas = $stmt->get_result();
        $lista = "";
        foreach ($filas as $valor) {
            $iban = $valor["cuenta_recepcion"];
            $cantidad = $valor["cantidad"];
            $fecha_mov = $valor["fecha_movimiento"];

            $lista = $lista . "<p>Tranferencia a " . $iban . " de " . $cantidad . " € <br> " . $fecha_mov . "</p>";
        }

        echo $lista;

    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    $stmt->close();

}
$mysqli->close();