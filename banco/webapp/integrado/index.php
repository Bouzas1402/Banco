<?php

require_once "config/configuracion.php";

// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$dni = $_SESSION["dni"];
$saldo = $tranferencia_hecha = $contrasena = $cuenta_creada = "";
$iban_err = $cantidad_err = $contrasena_err = $crear_cuenta_err = "";


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
    $sql = "SELECT iban, saldo FROM cuenta WHERE dni LIKE ? LIMIT 1";
    if($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $dni,);
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
    list($_SESSION["saldo"], $tranferencia_hecha, $iban_err, $cantidad_err) = hacerTransferencia($_POST["cantidad"], $_POST["cuenta"], $_SESSION["iban"], $_POST["ingreso_gasto"]);
}

if(isset($_POST["contrasena_crear_cuenta"]) && isset($_POST["confirmar"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
   include "crearCuenta.php";
   list($contrasena_err, $cantidad_err, $crear_cuenta_err, $cuenta_creada) = crearCuenta($dni, $_POST["contrasena_crear_cuenta"]);
}

if (isset($_POST["cambiar_cuenta"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $iban_y_saldo = explode(",", $_POST["cambiar_cuenta"]);
    $_SESSION["iban"] = $iban_y_saldo[0];
    $_SESSION["saldo"] = $iban_y_saldo[1];
}

if (isset($_POST["cerrarSesion"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
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
    <link rel="stylesheet" href="assets/css/font.css">
    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <style type="text/css">

        .colorCorporativo {
            background-color:rgba(251,102,16,0.88);
        }
    </style>

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
            xmlhttp.open("GET", "cuentas.php/?dni=<?php echo $dni?>", true);
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

<nav class="colorCorporativo navbar navbar-expand-sm d-flex justify-content-between align-items-center">
    <div class="navbar-brand">
        <img class="align-top mx-3" src="assets/img/confundido.png" width="45px" height="45px" alt="">
    </div>
    <div class="fontPanda">BANCA PANDA</div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <button class="btn me-2 bg-dark " aria-label="Close" type="submit" name="cerrarSesion">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-power text-white" viewBox="0 0 16 16">
                <path d="M7.5 1v7h1V1h-1z"/>
                <path d="M3 8.812a4.999 4.999 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812z"/>
            </svg>
        </button>
    </form>
</nav>



<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4 col-12">
            <h3 class="my-5 text-center">HOLA</h3>
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

        <div class="col-lg-8 px-0">
            <div class="w-100">
                <div class="bg-dark w-100 me-lg-3 pt-3 px-3 pt-lg-5 px-lg-5 text-center text-white overflow-hidden">
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
                                <input type="text" name="cuenta" step="any" class="form-control  <?php echo (!empty($iban_err)) ? 'is-invalid' : ''; ?>" >
                                <span class="invalid-feedback"><?php echo $iban_err; ?></span>
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
            <div class="w-100">
                <div class="bg-dark w-100 me-lg-3 pt-3 px-3 pt-lg-5 px-lg-5 text-center text-white overflow-hidden">
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
                            <h1>Condiciones para la creación de una nueva cuenta:</h1>
                            <p>Mediante el contrato de depósito a la vista, se permite al cliente ingresar dinero en una
                                entidad de crédito, y esta queda obligada a su devolución en el momento en que el titular
                                lo solicite, así como a prestar el denominado «servicio de caja», en virtud del cual la
                                entidad se obliga a realizar pagos y cobros a terceros en ejecución de órdenes que recibe
                                del cliente, de forma que se convierte en mandataria singular del cliente y administra,
                                como si fueran propios, los fondos disponibles a favor de este. Por lo general, la entidad
                                suele ofrecer al cliente una remuneración o tipo de interés por el dinero depositado y
                                puede cobrar comisión por las operaciones incluidas en el servicio de caja. Es habitual
                                que el contrato de apertura de cuenta se concierte con duración indefinida y con facultad
                                de las partes de darlo por terminado en cualquier momento (al estar basado, como el de
                                comisión, en la mutua confianza); para su cancelación, deberá seguirse el procedimiento
                                establecido en el propio contrato y/o en la normativa de aplicación.
                                En el expediente R-201810900, la entidad en la que el reclamante había abierto una
                                cuenta corriente indicó que no se podía solicitar la cancelación a distancia, sino que debía
                                solicitarla físicamente en la oficina bancaria. El DCMR consideró que la entidad se había
                                apartado de las buenas prácticas bancarias, al no acreditar que dicha exigencia estuviera
                                prevista en el correspondiente contrato en el que, además, estaba pactada la operativa en
                                banca a distancia mediante usuario y claves sustitutivos de la firma manuscrita.
                                La Ley 16/2009, de 13 de noviembre, de Servicios de Pago (LSP), define la cuenta de
                                pago como «una cuenta a nombre de uno o varios usuarios de servicios de pago que sea
                                utilizada para la ejecución de operaciones de pago». La cuenta de pago es el equivalente
                                al depósito o cuenta a la vista bancaria (bajo la forma tanto de cuenta corriente como de
                                libreta de ahorros), en la que se practican diversos adeudos (ordenante) y abonos
                                (beneficiario). La ley fue desarrollada por la Orden EHA/1608/2010, de 14 de junio, sobre
                                transparencia de las condiciones y requisitos de información aplicables a los servicios de
                                pago (BOE del 18). Ambas normas son aplicables también a no consumidores, salvo que
                                se pacte lo contrario.
                                La apertura de una cuenta requiere el consentimiento expreso y la aceptación de sus
                                condiciones por todas las partes que la formalizan, siempre dentro del principio
                                fundamental de la libertad de contratación que inspira nuestro ordenamiento jurídico. Sin
                                perjuicio de dicho principio, la normativa actual establece una serie de requerimientos que
                                las entidades deben observar, en relación tanto con la información y con las explicaciones
                                previas al contrato como con el contenido del contrato. La letra que se ha de utilizar en
                                cualquier documento de información al cliente, en fase tanto precontractual como
                                contractual, debe tener un tamaño apropiado para facilitar su lectura, y, en todo caso, la
                                letra minúscula que se utilice no podrá tener una altura inferior a 1,5 mm.
                                Por otra parte, la normativa de servicios de pago atribuye la carga de la prueba, del
                                cumplimiento de los requisitos de información en ella establecidos, a las entidades
                                proveedoras de servicios de pago (art. 20 de la LSP).
                                El 5 de febrero de 2016 entró en vigor la Orden ECC/2316/2015, de 4 de noviembre,
                                relativa a las obligaciones de información y clasificación de productos financieros (BOE
                                del 5 de noviembre), ya citada, conocida como «Orden de los semáforos» u «Orden de los
                                números», por cuanto establece una clasificación de los productos financieros mediante
                                una escala de seis colores y seis números (del 1 al 6), que puede ser sustituida por una
                                escala solo numérica. Por ejemplo, el número 1 sería de aplicación a los depósitos
                                bancarios a la vista o a plazo en euros, mientras que las cuentas denominadas en moneda
                                distinta del euro entrarían en la clase 6. De ella interesa destacar que:</p>
                            <ul>
                            <li>Obliga a las entidades de crédito comercializadoras de depósitos a incluir, tanto
                                en sus comunicaciones publicitarias como en la información previa que ha de
                                facilitar al cliente según el artículo 6 de la Orden EHA 2899/2011, un indicador del
                                riesgo y, en su caso, unas alertas por liquidez y complejidad.</li>
                            <li>Resulta de aplicación a depósitos a la vista, de ahorro y a plazo, si bien se
                                excluyen de su ámbito los depósitos estructurados1
                                . En el expediente R201813020, la reclamación tenía como objeto una imposición a plazo en libras
                                esterlinas. El DCMR, una vez resaltado el hecho de que los depósitos bancarios en
                                moneda distinta del euro están clasificados en la clase 6 o de máximo riesgo de
                                acuerdo con la citada «Orden de los semáforos», estimó que se había producido
                                un quebrantamiento de las buenas prácticas bancarias, puesto que la entidad no
                                acreditó que hubiese facilitado a su cliente información sobre todas las
                                condiciones del depósito en libras.</li>

                            </ul>
                            <p>A mayor abundamiento, y sin perjuicio de su no aplicabilidad durante 2018, resulta de
                                interés señalar, como cierre de este epígrafe, que, en el trámite de apertura de cuentas de
                                pago, el Real Decreto-ley 19/2017, desarrollado mediante Circular del Banco de España
                                2/2019, introduce la obligación de entrega de un documento informativo sobre
                                comisiones estandarizado. Sobre el contenido y el formato específicos de este
                                documento, puede consultarse el recuadro 2.2, sobre el Real Decreto-ley 19/2017, de 24
                                de noviembre, de cuentas de pago básicas, traslado de cuentas de pago y
                                comparabilidad de comisiones, y su normativa de desarrollo. </p>
                            <h4>Explicaciones adecuadas</h4>
                            <p>Las explicaciones adecuadas y la información precontractual son dos cosas distintas, si
                                bien se complementan mutuamente. Ni el artículo 9 de la Orden EHA/2899/2011, ya
                                citada, ni la norma quinta de la también citada Circular del Banco de España 5/2012, que
                                la desarrolla, exigen la forma escrita para las explicaciones adecuadas, si bien tampoco la
                                excluyen. Así, las explicaciones adecuadas, según están concebidas en la orden y en la
                                circular, se facilitarán normalmente de forma verbal durante el proceso de
                                comercialización, aclarando y complementando la información precontractual, que, por su
                                naturaleza, será en muchos casos de carácter más técnico.
                                Según el artículo 9 de la Orden EHA/2899/2011, las explicaciones deberán incluir, entre
                                otras aclaraciones, las consecuencias que la celebración de un contrato de servicios
                                bancarios puede tener para el cliente. Además, en caso de que la relación contractual
                                vaya a girar sobre operaciones que, como el depósito a la vista, se hallan incluidas en el
                                anejo 1 de la Circular del Banco de España 5/2012, las explicaciones deberán hacer
                                mención de la existencia del anejo 1, de su contenido —«Información trimestral sobre
                                comisiones y tipos practicados u ofertados de manera más habitual en las operaciones
                                más frecuentes con los perfiles de clientes más comunes que sean personas físicas»— y
                                del lugar en que el cliente puede consultarlo2
                                .
                                En el supuesto de que la comercialización de un producto —en este caso, el depósito a la
                                vista— se acompañe de una recomendación personalizada, especialmente en el caso de
                                campañas de distribución masiva, las entidades deberán extremar la diligencia en las
                                explicaciones que han de facilitar al cliente. A tal fin, recabarán de este la información
                                adecuada sobre sus necesidades y su situación financiera, y ajustarán la información que
                                le suministren a los datos así recabados.
                                En general, este DCMR ha manifestado que es obligación de las entidades, desde el
                                punto de vista de las buenas prácticas, explicar a sus clientes de manera veraz las
                                características de las promociones que ofrecen y la forma de verificar los requisitos para
                                su obtención, de modo que tengan un conocimiento claro y preciso de las prestaciones
                                que pueden recibir, para evitar que alberguen falsas expectativas al respecto.
                                Consideramos que las entidades deben estar en condiciones de acreditar que facilitaron a
                                sus clientes información precontractual y explicaciones adecuadas (personalizadas, para
                                su concreta situación particular), de modo que se justifique que estos, antes de contratar,
                                estuvieron en condiciones de conocer con exactitud las condiciones o los requisitos que
                                debían cumplir para la obtención de determinada promoción, vinculada a la apertura o
                                mantenimiento de una cuenta y/o la domiciliación de ciertos ingresos y pagos.
                                En la práctica bancaria reciente, numerosas entidades han desarrollado campañas o
                                programas de captación o fidelización de clientes, consistentes en ofrecer a los titulares
                                de cuentas a la vista determinadas ventajas (véanse exención de comisiones,
                                bonificaciones, especial remuneración, etc.), siempre que cumplieran ciertos requisitos
                                (los más frecuentes: domiciliación de nómina, pensión o ingresos recurrentes,
                                domiciliación de recibos, cierto número de pagos con tarjeta, etc.), y, así, durante 2018 se
                                nos han presentado numerosas reclamaciones en las que los clientes, de algún modo,
                                cuestionaban la exactitud o corrección de las explicaciones facilitadas en la oficina
                                bancaria: en unos casos, mostraban su sorpresa porque no se les habían aplicado las
                                ventajas que esperaban obtener, a pesar de que, entendían, habían cumplido los
                                requisitos acordados con los empleados de la entidad; en otros casos, manifestaban
                                expresamente que en la oficina se les habían facilitado información o explicaciones poco
                                claras, o engañosas, sobre los requisitos necesarios para disfrutar de especiales
                                condiciones</p>
                            <h3>Información precontractual</h3>
                            <p>Tanto la normativa de servicios de pago como la normativa general de transparencia
                                regulan la exigencia de que sea entregada al cliente de forma gratuita la oportuna
                                información precontractual, con carácter previo al momento en que quede vinculado por
                                el contrato u oferta, de manera que pueda comparar ofertas similares y adoptar una
                                decisión informada sobre cualquier producto bancario. La información precontractual
                                deberá ser clara, oportuna y suficiente, objetiva y no engañosa; para los depósitos o
                                cuentas a la vista, la obligación de información previa podrá ser cumplida proporcionando
                                al cliente una copia del borrador del contrato.
                                En la mayoría de los expedientes de reclamación presentados en el ejercicio 2018 en los
                                que, en cierto modo —en alguno de los escritos incorporados al expediente que fueron
                                remitidos a la entidad para que formulase alegaciones—, el reclamante puso de
                                manifiesto la deficiencia en la información o en las explicaciones que la entidad le facilitó,
                                con anterioridad a la contratación, en relación con las condiciones de la cuenta que se
                                proponía contratar (o sobre los requisitos necesarios para la aplicación de tales
                                condiciones) —bien porque acusó expresamente a la entidad de no informarle
                                correctamente, bien porque puso de manifiesto su interpretación o su conocimiento
                                discrepantes respecto a los de la entidad sobre la aplicación a su cuenta de las
                                condiciones o ventajas, o sobre el cumplimiento de los requisitos—, este DCMR
                                consideró imprescindible que la entidad financiera hubiera aportado al expediente de
                                reclamación una copia de la información precontractual que facilitó al cliente en su día,
                                fechada y con la firma del cliente en señal de su recepción</p>
                            <h3>Formalización del contrato</h3>
                            <p>La entrega y el contenido mínimo obligatorio de los contratos de depósitos a la vista se
                                recogen en el artículo 12 de la Orden EHA/1608/2010, a la cual nos remitimos, que resulta
                                aplicable cuando el cliente sea un consumidor o cuando, no siéndolo, no se haya pactado
                                otro régimen. Las entidades deberán entregar al cliente un ejemplar del documento
                                contractual con suficiente antelación a la fecha en que quede vinculado por el contrato
                                marco u oferta o, si esto no es posible porque el contrato se haya celebrado a instancias
                                del usuario a través de un medio de comunicación a distancia, inmediatamente después
                                de su celebración. El titular del contrato tendrá derecho a recibir en papel o en otro
                                soporte duradero las condiciones contractuales en cualquier momento de la relación
                                contractual.</p>
                            <h3>Cuentas en divisas</h3>
                            <p>TEste DCMR estima que, en este tipo de cuentas, las entidades deben velar por que el
                                contenido de los contratos formalizados se adapte a lo establecido en la normativa de
                                servicios de pago en relación con su utilización y, sobre todo, recoja expresamente las
                                condiciones particulares de estas cuentas en cuanto a la forma de disposición de la
                                divisa, su conversión y los costes que ello podría suponer, según la modalidad de
                                reembolso elegida (transferencia, obtención de billetes de esa moneda o conversión a
                                otras divisas). Véase más adelante, a este respecto, la comisión de manipulación.
                                En el expediente se pudo comprobar que la documentación que recogía las condiciones
                                de un contrato de cuenta en divisas no incluía ninguna previsión con respecto a la forma
                                de disposición de la divisa, su conversión y los costes que ello pudiera suponer, según la
                                modalidad de reembolso elegida (transferencia, obtención de billetes o conversión a otras
                                divisas), por lo que el DCMR concluyó que podría haberse producido un quebrantamiento
                                de la normativa de transparencia</p>
                            <h3>Integridad de los contratos</h3>
                            <p>Las entidades deben asegurarse de la integridad de los contratos que suscriben con sus
                                clientes, de modo que contemplen y regulen todas las posibles vicisitudes que puedan
                                suceder a lo largo de cada relación. De no ser así, las buenas prácticas exigen que, en el
                                momento de constatarse la ausencia de un pacto contractual preciso, las entidades traten
                                de llegar a un acuerdo con sus clientes para que estos presten su consentimiento a
                                alguna de las posibles formas de actuación en esos casos.
                            </p>
                            <h3>Oscuridad de las cláusulas</h3>
                            <p>El principio de transparencia que inspira las buenas prácticas bancarias exige que los
                                documentos contractuales huyan de cualquier tipo de estipulación susceptible de admitir
                                interpretaciones opuestas, de modo que sus textos sean fácilmente comprensibles y
                                directamente aplicables.
                                A la vista de lo anterior (y al margen de que la interpretación definitiva de una cláusula, así
                                como la determinación de las consecuencias que de aquella pudieran derivarse,
                                correspondería realizarla, en exclusividad, a los jueces o tribunales de justicia), el DCMR
                                considera —en línea con lo previsto en el artículo 1288 del Código Civil— que, en caso de
                                cláusulas oscuras susceptibles de diversas interpretaciones, debe considerarse contraria
                                a las buenas prácticas bancarias la actuación de las entidades consistente en efectuar
                                interpretaciones de los contratos que les resulten más favorables, sin haber tratado de
                                llegar a un acuerdo previo con sus clientes titulares de cuenta sobre el alcance de lo
                                pactado.</p>
                            <h3>Entrega de documento contractual y conservación de documentos</h3>
                            <p>Como hemos dicho, en cualquier momento de la relación contractual el cliente tiene
                                derecho a solicitar y a recibir una copia del documento contractual en el que se formaliza
                                el depósito a la vista, en papel o en soporte duradero. De acuerdo con ello, las entidades
                                están obligadas a conservar copia del contrato firmada por aquel (así como el recibí, que
                                podrá constar en el propio documento), debiendo estar en condiciones de ponerla a
                                disposición del cliente siempre que este lo solicite, bien en papel, bien en soporte
                                duradero si la contratación se efectuó por medios electrónicos. Cuando los contratos
                                sean intervenidos por fedatario público, la entidad podrá enviar por correo el contrato
                                intervenido; en estos casos, el recibí del cliente lo constituirá el correspondiente acuse de
                                recibo del envío.
                                Este DCMR entiende, haciendo suya la doctrina del Tribunal Supremo en este asunto, que
                                las entidades han de conservar toda aquella documentación relativa al nacimiento,
                                modificación y extinción de los derechos y de las obligaciones que les incumben, no solo
                                durante el plazo de seis años, sino al menos durante el período en que, a tenor de las
                                normas sobre prescripción, pueda resultarles conveniente promover el ejercicio de los
                                primeros, o sea posible que les llegue a ser exigido el cumplimiento de las segundas3
                                .
                                Este criterio ha adquirido rango legal en virtud del Real Decreto-ley 19/2018, en relación
                                con los documentos y registros que permitan acreditar el cumplimiento de las
                                obligaciones de las entidades relativas a la prestación y utilización de servicios de pago.
                                Cuando los reclamantes denuncian no haber sido informados de las condiciones que se
                                aplican en sus contratos (tipo de interés, comisiones, posibilidad de descubierto...), son
                                las entidades las que deben acreditar que sus clientes las conocían y las aceptaron. En
                                los casos en los que no se ha acreditado adecuadamente ante este DCMR la debida
                                formalización de los contratos, se ha apreciado posible quebrantamiento de la normativa y
                                de las buenas prácticas bancarias, por entender que estos pudieron no haberse
                                formalizado, o pudo existir falta de la debida diligencia en la custodia de los documentos
                                justificativos de las relaciones jurídicas mantenidas con sus clientes, o, en su caso, falta
                                de colaboración con el propio DCMR en la resolución de la reclamación.
                                Además, entendemos que el contrato y las condiciones deben facilitarse a los clientes tan
                                pronto como lo soliciten, pues no se considera una buena práctica bancaria demorar la
                                respuesta hasta que se plantee reclamación ante este DCMR, ya que en tales casos la
                                actuación de la entidad evidencia un desinterés que contradice los principios de lealtad
                                hacia su cliente, así como de claridad y transparencia, que deben regir las relaciones
                                entre las partes.
                                Durante 2018, el DCMR ha tramitado expedientes de reclamación cuyo objeto fue que la
                                oficina bancaria no había atendido la solicitud de su cliente de entrega de copia de su
                                contrato de cuenta. En muchos casos, las entidades han alegado la antigüedad de los
                                contratos o su extravío a consecuencia del cierre de sucursales o, en general, como
                                resultado de procesos de fusión de entidades. Este DCMR ha insistido en la importancia
                                de custodiar adecuadamente los documentos que recojan las condiciones contractuales
                                según prevé la normativa de aplicación. Hay que recordar, además, que las entidades
                                deben estar en condiciones de facilitar a sus clientes, en el momento en que lo soliciten,
                                copia de los contratos formalizados, según se prevé en la norma novena de la Circular del
                                Banco de España 5/2012. Por este motivo, el DCMR concluyó apreciando posible
                                quebrantamiento de la normativa cuando la entidad reconocía que no había podido
                                localizar el contrato suscrito con el cliente.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pie de pagina -->
<footer class="colorCorporativo footer text-center">
    <div class="container">
        <div class="row py-4">
            <!-- Localizacion del pie de pagina-->
            <div class="col-lg-4 mb-5 mb-lg-0">
                <h4 class="text-uppercase mb-4">Localización</h4>
                <p class="lead mb-0">
                    2215 John Daniel Drive
                    <br />
                    Clark, MO 65243
                </p>
            </div>
            <!-- Iconos de la redes sociales del pie de pagina-->
            <div class="col-lg-4 mb-5 mb-lg-0">
                <h4 class="text-uppercase mb-4">Sitios Web</h4>
                <a class="btn btn-outline-light btn-social rounded-circle mx-1" href="#!"><i class="fab fa-fw fa-facebook-f"></i></a>
                <a class="btn btn-outline-light btn-social rounded-circle mx-1" href="#!"><i class="fab fa-fw fa-twitter"></i></a>
                <a class="btn btn-outline-light btn-social rounded-circle mx-1" href="#!"><i class="fab fa-fw fa-linkedin-in"></i></a>
                <a class="btn btn-outline-light btn-social rounded-circle mx-1" href="#!"><i class="fab fa-fw fa-dribbble"></i></a>
            </div>
            <!-- Texto del pie de pagina -->
            <div class="col-lg-4">
                <h4 class="text-uppercase mb-4">Sobre nosotros</h4>
                <p class="lead mb-0">
                    Nueva banca agil moderna podras hacer casi todo por internet.
                    <a href="http://startbootstrap.com"><p>Banca PANDA</p></a>
                </p>
            </div>
        </div>
    </div>
</footer>
<!-- Sección de Copyright -->
<div class="copyright py-4 text-center colorCorporativo">
    <div class="container"><small>Copyright &copy; Your Website 2021</small></div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

