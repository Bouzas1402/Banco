<?php
// Include config file
require_once "config/configuracion.php";
    $mysqli = conexionbd();

if(isset($_REQUEST["term"])) {
    // Prepare a select statement
    $sql = "SELECT * FROM paises WHERE nombre LIKE ?";

    if ($stmt = $mysqli->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("s", $param_term);

        // Set parameters
        $param_term = $_REQUEST["term"] . '%';

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            $result = $stmt->get_result();

            // Check number of rows in the result set
            if ($result->num_rows > 0) {
                // Fetch result rows as an associative array
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    echo "<p>" . $row["nombre"] . "</p>";
                }
            } else {
                echo "<p>No se encontraron resultados</p>";
            }
        } else {
            echo "ERROR: No se pudo ejecutar $sql. ";
        }
        $stmt->close();
    }

    // Close statement

    $mysqli->close();
}
// Close connection




