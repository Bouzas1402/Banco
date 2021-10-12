<?php
require_once "config/configuracion.php";





$DNI = "15940455S";
$contrasena = "";
$contrasena_err = $crear_cuenta_err = "";


if(isset($_POST["contrasena_crear_cuenta"]) && isset($_POST["confirmar"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

    if(empty(trim($_POST["contrasena_crear_cuenta"]))){
        $contrasena_err = "Por favor ingrese una contraseña.";
    } else{
        $contrasena = trim($_POST["contrasena_crear_cuenta"]);
    }


    if(empty($contrasena_err)){

        $mysqli = conexionbd();




        $sql = "SELECT IBAN FROM cuenta WHERE DNI = ?";

        if ($stmt = $mysqli->prepare($sql)) {


            $stmt->bind_param("s", $DNI);
            if ($stmt->execute()) {

                $resultado = $stmt->num_rows();

                if($resultado >= 3){
                    $crear_cuenta_err = "Ya tienes tres cuentas, para abrir otra dirijase a una de nuestras oficinas.";
                } else {
                    $ibans = $stmt->get_result();
                    $ibanCorrecto = true;
                    do {
                        $ibanCorrecto = true;
                        $nuevo_iban = crearIban();
                        foreach ($ibans as $valor){
                            if ($valor == $nuevo_iban){
                                $ibanCorrecto = false;
                            }
                        }
                    } while($ibanCorrecto);


                    $sql = "INSERT INTO cuenta (IBAN, saldo, DNI) VALUES (?, 3500, ?)";

                    if ($stmt = $mysqli->prepare($sql)){



                        $stmt->bind_param("ss", $nuevo_iban,$dni);
                        // Redirect to login page
                        if($stmt->execute()){

                            header("location: login.php");
                        } else {
                            echo "Oops! Something went wrong. Please try again later.";
                        }

                    }


                }




            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        $mysqli->close();
    }
}










function crearIban (){
    $nuevo_iban = "IBAN";
    for ($i = 0; $i < 14; $i++){
        $nuevo_iban = $nuevo_iban . rand(0,9);
    }
    return $nuevo_iban;
}


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>


    <script src="js/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">



</head>
<body>


<div class="w-100 my-lg-3 ps-lg-3">
    <div class="bg-dark me-lg-3 pt-3 px-3 pt-lg-5 px-lg-5 text-center text-white overflow-hidden">
        <div class="my-3 py-3">
            <h2 class="display-5">Another headline</h2>
            <p class="lead">And an even wittier subheading.</p>
        </div>



        <div class="bg-light shadow-sm mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
            <form class="col" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="input-group mb-3">
                    <div class="input-group-prepend h-100">
                        <span class="input-group-text">Contraseña</span>
                    </div>
                    <input type="password" class="form-control <?php echo (!empty($contrasena_err)) ? 'is-invalid' : '' ?>" name="contrasena_crear_cuenta">
                    <span class="invalid-feedback"><?php echo $contrasena_err; ?></span>
                    <div class="input-group-prepend">
                        <div class="input-group-text h-100">
                            <input type="checkbox" name="confirmar" value="confirmar" required>
                            <input type="submit" class="btn btn-primary" value="confirmar">
                        </div>
                    </div>
                </div>
            </form>
        </div>




    </div>
</div>




<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>

