<?php

require_once "config/configuracion.php";

// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$DNI = $_SESSION["dni"];
$saldo = $tranferencia_hecha = $contrasena = $cuenta_creada = "";
$IBAN_err = $cantidad_err = $contrasena_err = $crear_cuenta_err = "";


if(isset($_COOKIE['contador'])){
    // Caduca en un año
    setcookie('contador', $_COOKIE['contador'] + 1, time() + 365 * 24 * 60 * 60);
    $mensaje = 'Número de visitas: ' . $_COOKIE['contador'];
}
else {
    // Caduca en un año
    setcookie('contador', 1, time() + 365 * 24 * 60 * 60);
    $mensaje = 'Bienvenido a nuestra página web';
}


if (empty(trim($_SESSION["iban"]))) {
    $mysqli = conexionbd();
    $sql = "SELECT IBAN, saldo FROM cuenta WHERE DNI LIKE ? LIMIT 1";
    if($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $DNI,);
        if ($stmt->execute()) {
            $stmt->store_result();
            $stmt->bind_result($_SESSION["iban"], $_SESSION["saldo"]);
            $stmt->fetch();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    $mysqli->close();
}


// Si se envia el formulario con cuenta y cantidad se mete en el if:
if(isset($_POST["cuenta"]) && isset($_POST["cantidad"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    include 'tranferencias.php';
    list($_SESSION["saldo"], $tranferencia_hecha, $IBAN_err, $cantidad_err) = hacerTransferencia($_POST["cantidad"], $_POST["cuenta"], $_SESSION["iban"], $_POST["ingreso_gasto"]);
}

if(isset($_POST["contrasena_crear_cuenta"]) && isset($_POST["confirmar"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
   include "crearCuenta.php";

   list($contrasena_err, $cantidad_err, $crear_cuenta_err, $cuenta_creada) = crearCuenta($DNI, $_POST["contrasena_crear_cuenta"]);
}

if (isset($_POST["cambiar_cuenta"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $iban_y_saldo = explode(",", $_POST["cambiar_cuenta"]);
    $_SESSION["iban"] = $iban_y_saldo[0];
    $_SESSION["saldo"] = $iban_y_saldo[1];
}

if (isset($_POST["cerrarSesion"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();
    session_destroy();
    header("location: login.php");
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
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <script type="text/javascript">

        function cuentasCliente() {
            let xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4
                    if (xmlhttp.status == 200) {
                        document.getElementById("cuentas").innerHTML = xmlhttp.responseText;
                    }
                    else if (xmlhttp.status == 400) {
                        alert('There was an error 400');
                    }
                    else {
                        alert('something else other than 200 was returned');
                    }
                }
            };
            xmlhttp.open("GET", "cuentas.php/?dni=<?php echo $DNI?>", true);
            xmlhttp.send();
        };

        function movimientosCuenta() {
            let xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4
                    if (xmlhttp.status == 200) {
                        $movimientos = xmlhttp.responseText;
                        if ($movimientos == ""){
                            document.getElementById("movimientos").innerHTML = "No hay movimientos todavia";
                        } else {
                            document.getElementById("movimientos").innerHTML = $movimientos;
                        }
                    }
                    else if (xmlhttp.status == 400) {
                        alert('There was an error 400');
                    }
                    else {
                        alert('something else other than 200 was returned');
                    }
                }
            };
            xmlhttp.open("GET", "movimientos.php/?cuenta=<?php echo $_SESSION["iban"]?>", true);
            xmlhttp.send();
        };
        document.addEventListener("DOMContentLoaded", function (){
            cuentasCliente();
            movimientosCuenta();
        });
    </script>

</head>
<body>

<nav class="navbar navbar-expand-sm navbar-dark bg-dark" aria-label="Third navbar example">
    <a class="navbar-brand" href="#">
        <img class="align-top" src="#" width="30" height="20" alt="">
    </a>
    <button class="align-content-between navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample03" aria-controls="navbarsExample03" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarsExample03">
        <ul class="navbar-nav me-auto mb-2 mb-sm-0">
            <li class="nav-item">
                <a class="nav-link" aria-current="page" href="#">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
            </li>
        </ul>
    </div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <button class="align-content-between btn  me-2 bg-white " aria-label="Close" type="submit" name="cerrarSesion">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-power" viewBox="0 0 16 16">
                <path d="M7.5 1v7h1V1h-1z"/>
                <path d="M3 8.812a4.999 4.999 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812z"/>


            </svg>
        </button>
    </form>
</nav>


<div class="cookies">
    <h2>Cookies</h2>
    <p>¿Aceptas nuestras cookies?</p>
    <a href="?politica-cookies=1">Aceptar</a>
</div>



<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4 col-12">
            <h3 class="my-5 text-center">Carlos Bouzas</h3>
            <div class="row align-content-center  h-75 mb-5">
                <ol class="me-2 list-group  w-100">
                    <form id="cuentas" class="col-12" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                    </form>
                </ol>
                <div id="movimientos" class="d-none d-lg-block col overflow-scroll text-dark h-50 w-100 mb-5">
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-12">
            <div class="w-100 my-lg-3 ps-lg-3">
                <div class="bg-dark me-lg-3 pt-3 px-3 pt-lg-5 px-lg-5 text-center text-white overflow-hidden">
                    <div class="row justify-content-center my-3 py-3">
                        <div class="col-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-currency-exchange" viewBox="0 0 16 16">
                                <path d="M0 5a5.002 5.002 0 0 0 4.027 4.905 6.46 6.46 0 0 1 .544-2.073C3.695 7.536 3.132 6.864 3 5.91h-.5v-.426h.466V5.05c0-.046 0-.093.004-.135H2.5v-.427h.511C3.236 3.24 4.213 2.5 5.681 2.5c.316 0 .59.031.819.085v.733a3.46 3.46 0 0 0-.815-.082c-.919 0-1.538.466-1.734 1.252h1.917v.427h-1.98c-.003.046-.003.097-.003.147v.422h1.983v.427H3.93c.118.602.468 1.03 1.005 1.229a6.5 6.5 0 0 1 4.97-3.113A5.002 5.002 0 0 0 0 5zm16 5.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0zm-7.75 1.322c.069.835.746 1.485 1.964 1.562V14h.54v-.62c1.259-.086 1.996-.74 1.996-1.69 0-.865-.563-1.31-1.57-1.54l-.426-.1V8.374c.54.06.884.347.966.745h.948c-.07-.804-.779-1.433-1.914-1.502V7h-.54v.629c-1.076.103-1.808.732-1.808 1.622 0 .787.544 1.288 1.45 1.493l.358.085v1.78c-.554-.08-.92-.376-1.003-.787H8.25zm1.96-1.895c-.532-.12-.82-.364-.82-.732 0-.41.311-.719.824-.809v1.54h-.005zm.622 1.044c.645.145.943.38.943.796 0 .474-.37.8-1.02.86v-1.674l.077.018z"/>
                            </svg>
                        </div>
                        <div class="col-5 lead"><?php echo ($_SESSION["saldo"] . " €"); ?></div>
                    </div>
                    <p class="lead"><?php echo $_SESSION["iban"] ?></p>
                    <div class="align-items-center row text-dark bg-light shadow-sm mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
                        <form class="col-12" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
                    </div>
                </div>
            </div>
            <div class="w-100 my-lg-3 ps-lg-3">
                <div class="bg-dark me-lg-3 pt-3 px-3 pt-lg-5 px-lg-5 text-center text-white overflow-hidden">
                    <div class="my-3 py-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" fill="currentColor" class="me-5 bi bi-piggy-bank" viewBox="0 0 16 16">
                            <path d="M5 6.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0zm1.138-1.496A6.613 6.613 0 0 1 7.964 4.5c.666 0 1.303.097 1.893.273a.5.5 0 0 0 .286-.958A7.602 7.602 0 0 0 7.964 3.5c-.734 0-1.441.103-2.102.292a.5.5 0 1 0 .276.962z"/>
                            <path fill-rule="evenodd" d="M7.964 1.527c-2.977 0-5.571 1.704-6.32 4.125h-.55A1 1 0 0 0 .11 6.824l.254 1.46a1.5 1.5 0 0 0 1.478 1.243h.263c.3.513.688.978 1.145 1.382l-.729 2.477a.5.5 0 0 0 .48.641h2a.5.5 0 0 0 .471-.332l.482-1.351c.635.173 1.31.267 2.011.267.707 0 1.388-.095 2.028-.272l.543 1.372a.5.5 0 0 0 .465.316h2a.5.5 0 0 0 .478-.645l-.761-2.506C13.81 9.895 14.5 8.559 14.5 7.069c0-.145-.007-.29-.02-.431.261-.11.508-.266.705-.444.315.306.815.306.815-.417 0 .223-.5.223-.461-.026a.95.95 0 0 0 .09-.255.7.7 0 0 0-.202-.645.58.58 0 0 0-.707-.098.735.735 0 0 0-.375.562c-.024.243.082.48.32.654a2.112 2.112 0 0 1-.259.153c-.534-2.664-3.284-4.595-6.442-4.595zM2.516 6.26c.455-2.066 2.667-3.733 5.448-3.733 3.146 0 5.536 2.114 5.536 4.542 0 1.254-.624 2.41-1.67 3.248a.5.5 0 0 0-.165.535l.66 2.175h-.985l-.59-1.487a.5.5 0 0 0-.629-.288c-.661.23-1.39.359-2.157.359a6.558 6.558 0 0 1-2.157-.359.5.5 0 0 0-.635.304l-.525 1.471h-.979l.633-2.15a.5.5 0 0 0-.17-.534 4.649 4.649 0 0 1-1.284-1.541.5.5 0 0 0-.446-.275h-.56a.5.5 0 0 1-.492-.414l-.254-1.46h.933a.5.5 0 0 0 .488-.393zm12.621-.857a.565.565 0 0 1-.098.21.704.704 0 0 1-.044-.025c-.146-.09-.157-.175-.152-.223a.236.236 0 0 1 .117-.173c.049-.027.08-.021.113.012a.202.202 0 0 1 .064.199z"/>
                        </svg>
                        <span class="display-5">Crear una nueva cuenta</span>
                        <p class="lead">Cuenta credito con 3500 € iniciales</p>
                    </div>



                    <div class="bg-light shadow-sm mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
                        <form class="col" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text h-100">Contraseña</span>
                                </div>
                                <input type="password" class="form-control <?php echo (!empty($contrasena_err)) ? 'is-invalid' : ''; echo (!empty($crear_cuenta_err)) ? 'is-invalid' : ''; echo (!empty($cuenta_creada)) ? 'is-valid' : ''; ?>" name="contrasena_crear_cuenta">
                                <div class="input-group-prepend">
                                    <div class="input-group-text h-100">
                                        <div class="input-group-text h-100">
                                            <input type="checkbox" name="confirmar" value="confirmar" required>

                                        </div>
                                        <input type="submit" class="btn btn-primary" value="confirmar">
                                    </div>

                                </div>
                                <span class="invalid-feedback" style="z-index: 1"><?php echo $crear_cuenta_err; echo $contrasena_err ?></span>
                                <span class="valid-feedback"><?php echo $cuenta_creada; ?></span>
                            </div>
                        </form>

                        <div class="col overflow-scroll text-dark h-75">
                            <h1>Section 1</h1>
                            <p>Try to scroll this page and look at the navigation bar while scrolling!</p>
                            <p>Try to scroll this page and look at the navigation bar while scrolling!</p>
                            <p>Try to scroll this page and look at the navigation bar while scrolling!</p>
                            <p>Try to scroll this page and look at the navigation bar while scrolling!</p>
                            <p>Try to scroll this page and look at the navigation bar while scrolling!</p>
                            <p>Try to scroll this page and look at the navigation bar while scrolling!</p>
                            <p>Try to scroll this page and look at the navigation bar while scrolling!</p>
                            <p>Try to scroll this page and look at the navigation bar while scrolling!</p>
                            <p>Try to scroll this page and look at the navigation bar while scrolling!</p>
                            <p>Try to scroll this page and look at the navigation bar while scrolling!</p>
                            <p>Try to scroll this page and look at the navigation bar while scrolling!</p>
                            <p>Try to scroll this page and look at the navigation bar while scrolling!</p>
                            <p>Try to scroll this page and look at the navigation bar while scrolling!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pie de pagina -->
<footer class="footer text-center bg-danger">
    <div class="container">
        <div class="row py-4">
            <!-- Localizacion del pie de pagina-->
            <div class="col-lg-4 mb-5 mb-lg-0">
                <h4 class="text-uppercase mb-4">Location</h4>
                <p class="lead mb-0">
                    2215 John Daniel Drive
                    <br />
                    Clark, MO 65243
                </p>
            </div>
            <!-- Iconos de la redes sociales del pie de pagina-->
            <div class="col-lg-4 mb-5 mb-lg-0">
                <h4 class="text-uppercase mb-4">Around the Web</h4>
                <a class="btn btn-outline-light btn-social rounded-circle mx-1" href="#!"><i class="fab fa-fw fa-facebook-f"></i></a>
                <a class="btn btn-outline-light btn-social rounded-circle mx-1" href="#!"><i class="fab fa-fw fa-twitter"></i></a>
                <a class="btn btn-outline-light btn-social rounded-circle mx-1" href="#!"><i class="fab fa-fw fa-linkedin-in"></i></a>
                <a class="btn btn-outline-light btn-social rounded-circle mx-1" href="#!"><i class="fab fa-fw fa-dribbble"></i></a>
            </div>
            <!-- Texto del pie de pagina -->
            <div class="col-lg-4">
                <h4 class="text-uppercase mb-4">About Freelancer</h4>
                <p class="lead mb-0">
                    Freelance is a free to use, MIT licensed Bootstrap theme created by
                    <a href="http://startbootstrap.com">Start Bootstrap</a>
                </p>
            </div>
        </div>
    </div>
</footer>
<!-- Sección de Copyright -->
<div class="copyright py-4 text-center bg-warning">
    <div class="container"><small>Copyright &copy; Your Website 2021</small></div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

