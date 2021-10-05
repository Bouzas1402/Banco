<?php

require_once "config/configuracion.php";



$sql = "SELECT * FROM cliente WHERE DNI = ?";

$nombre = "";
$saldo = 0;
$DNI = "3456543D";
$id_cuenta = "";

$mysqli = conexionbd();

$stmt = $mysqli->prepare($sql);

$stmt->bind_param("s", $DNI);


$stmt->execute();

$resultado = $stmt->get_result();
$filacliente = $resultado->fetch_assoc();

$stmt->close();

$sql = "SELECT * FROM cuenta WHERE DNI = ?";

    $stmt = $mysqli->prepare($sql);

$stmt->bind_param("s", $DNI);


$stmt->execute();

$resultado = $stmt->get_result();
$filacuenta = $resultado->fetch_assoc();

$id_cuenta = $filacuenta["id_cuenta"];

$stmt->close();

$mysqli->close();


if(isset($_POST["cuenta"]) && $_SERVER["REQUEST_METHOD"] == "POST"){
    $sql = "SELECT * FROM cuenta WHERE IBAN = ?";

    $mysqli = new mysqli(DB_SERVER, DB_USUARIO, DB_PASSWORD, DB_NOMBRE);

// Check connection
    if($mysqli === false) {
        die("ERROR: Could not connect. " . $mysqli->connect_error);
    }

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $_POST["cuenta"]);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows == 1){

        $sql = "INSERT INTO movimientos_cuenta (cantidad) VALUES (?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("id", $id_cuenta, $_POST["cantidad"]);
        $stmt->execute();
        $stmt->store_result();
        $stmt->close();


    }
        $mysqli->close();
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
    <script src="js/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <style>


    </style>

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


</nav>

<div class="container-fluid">
    <div class="row">


        <div class="col-sm-4 col-12">

            <h3 class="my-5 text-center"><?php echo $filacliente["nombre"] . " " . $filacliente["contrasena"] ?></h3>
            <div class="row align-content-center  h-75 mb-5">
                <ol class=" me-2 list-group align-bottom w-100">
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
        </div>

        <div class="col-sm-8 col-12">



            <div class="w-100 my-md-3 ps-md-3">

                <div class="bg-dark me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center text-white overflow-hidden">
                    <div class="row justify-content-center my-3 py-3">
                        <div class="col-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-currency-exchange" viewBox="0 0 16 16">
                                <path d="M0 5a5.002 5.002 0 0 0 4.027 4.905 6.46 6.46 0 0 1 .544-2.073C3.695 7.536 3.132 6.864 3 5.91h-.5v-.426h.466V5.05c0-.046 0-.093.004-.135H2.5v-.427h.511C3.236 3.24 4.213 2.5 5.681 2.5c.316 0 .59.031.819.085v.733a3.46 3.46 0 0 0-.815-.082c-.919 0-1.538.466-1.734 1.252h1.917v.427h-1.98c-.003.046-.003.097-.003.147v.422h1.983v.427H3.93c.118.602.468 1.03 1.005 1.229a6.5 6.5 0 0 1 4.97-3.113A5.002 5.002 0 0 0 0 5zm16 5.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0zm-7.75 1.322c.069.835.746 1.485 1.964 1.562V14h.54v-.62c1.259-.086 1.996-.74 1.996-1.69 0-.865-.563-1.31-1.57-1.54l-.426-.1V8.374c.54.06.884.347.966.745h.948c-.07-.804-.779-1.433-1.914-1.502V7h-.54v.629c-1.076.103-1.808.732-1.808 1.622 0 .787.544 1.288 1.45 1.493l.358.085v1.78c-.554-.08-.92-.376-1.003-.787H8.25zm1.96-1.895c-.532-.12-.82-.364-.82-.732 0-.41.311-.719.824-.809v1.54h-.005zm.622 1.044c.645.145.943.38.943.796 0 .474-.37.8-1.02.86v-1.674l.077.018z"/>
                            </svg>
                        </div>
                        <div class="col-5 lead"><?php echo $filacuenta["saldo"] . " €" ?></div>
                    </div>
                    <p class="lead"><?php echo $filacuenta["IBAN"] ?></p>

                    <div class="text-dark bg-light shadow-sm mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">

                        <form class="col-8" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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




            <div class="w-100 my-md-3 ps-md-3">
                <div class="bg-dark me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center text-white overflow-hidden">
                    <div class="my-3 py-3">
                        <h2 class="display-5">Another headline</h2>
                        <p class="lead">And an even wittier subheading.</p>
                    </div>
                    <div class="bg-light shadow-sm mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">

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
                    .
                </p>
            </div>
        </div>
    </div>
</footer>
<!-- Sección de Copyright -->
<div class="copyright py-4 text-center bg-warning">
    <div class="container"><small>Copyright &copy; Your Website 2021</small></div>
</div>




<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group">
        <label>Cuenta</label>
        <input type="text" name="cuenta" class="form-control " value="<?php  ?>">

    </div>
    <div class="form-group">
        <label>cantidad</label>
        <input type="number" name="cantidad" class="form-control">

    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Login">
    </div>
</form>


<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>

