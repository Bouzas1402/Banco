<?php
require_once "config/configuracion.php";

$q=$_GET["q"];

$mysqli = conexionbd();

$sql = "SELECT * FROM cuenta WHERE id = ?";

if ($stmt = $mysqli->prepare($sql)) {

    $stmt->bind_param("s", $q);

    if ($stmt->execute()) {
        $filas = $stmt->fetch_fields();

        foreach ($filas as $valor) {
            $x = $valor->DNI;
            echo $x;

        }

    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    $stmt->close();

}
