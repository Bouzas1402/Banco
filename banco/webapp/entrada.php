<?php
    session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}

        require_once "config/configuracion.php";


        $DNI = "";
        $DNI_err = $login_err = "";





?>

<!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Login</title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
            <style>
                body{ font: 14px sans-serif; }
                .wrapper{ width: 360px; padding: 20px; }
            </style>
        </head>
        <body>
            <div class="wrapper">
            <h2>Login</h2>
            <p>Please fill in your credentials to login.</p>

            <?php
            if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>DNI</label>
                    <input type="text" name="DNI" class="form-control <?php echo (!empty($DNI_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $DNI; ?>">
                    <span class="invalid-feedback"><?php echo $DNI_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>

            </form>

            </div>
<?php
    foreach ($_SESSION as $codigo => $valor){
    echo " " . $codigo . " : " . $valor . "<br>";
}
?>
        </body>
    </html>