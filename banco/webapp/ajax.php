<?php

require_once "config/configuracion.php";

$mysqli = conexionbd();

if(isset(($_REQUEST["term"]))){

    echo "<p>HOLAAAAAAAAAAAA<p>";

}

?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>

        <script type="text/javascript">




        </script>

    </head>
    <body>

    <div class="row align-content-center  h-75 mb-5">
        <ol id="xhr" class="me-2 list-group align-bottom w-100">
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto fw-bold">cuenta 1</div>
                <span class="badge bg-primary text-white rounded-pill">14</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto fw-bold">cuenta 2</div>
                <span class="badge bg-primary text-white rounded-pill">14</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto fw-bold">cuenta 3</div>
                <span class="badge bg-primary text-white rounded-pill">14</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto fw-bold">cuenta 3</div>
                <span class="badge bg-primary text-white rounded-pill">14</span>
            </li>
        </ol>
    </div>


    <script src="ajax.js"/>
    </body>
</html>
