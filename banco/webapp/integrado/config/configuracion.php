<?php
// Fichero de configuracion:
define("DB_SERVER", "db");
define("DB_USERNAME", "banco");
define("DB_PASSWORD", "banco");
define("DB_NAME", "bancodb");


function conexionbd(){
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($mysqli === false) {
    die("ERROR: Could not connect. " . $mysqli->connect_error);
}
return $mysqli;
}