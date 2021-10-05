<?php
// Fichero de configuracion:
define("DB_USUARIO", "quevedo");
define("DB_PASSWORD", "quevedo");
define("DB_SERVER", "db");
define("DB_NOMBRE", "banco");


function conexionbd(){
$mysqli = new mysqli(DB_SERVER, DB_USUARIO, DB_PASSWORD, DB_NOMBRE);

// Check connection
if($mysqli === false) {
    die("ERROR: Could not connect. " . $mysqli->connect_error);
}
return $mysqli;
}