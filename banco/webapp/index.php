<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
<h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["DNI"]); ?></b>. Welcome to our site.</h1>
<p>
    <a href="resetearPassword.php" class="btn btn-warning">Reset Your Password</a>
    <a href="cierreDeSesion.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
</p>
<?php

foreach ($_SESSION as $codigo => $valor){
    echo " " . $codigo . " : " . $valor . "<br>";
}
/*
while ($prueba = each($_SESSION)) {
    print " ".$prueba[1]." es ".$prueba[0]."<br />";
}
foreach ($_SESSION as $key => $val) {
    echo " " . $key . " " . $val;
}
*/
?>
</body>
</html>