
<?php

require_once '../config.php';

header("Content-Type: application/json; charset=utf-8");

$respuesta = array(
    "data" => array()
);

$sql = "SELECT
            id_empleado,
            nombre,
            apellidos,
            puesto,
            telefono,
            correo
        FROM empleados";

$resultado = $cx->query($sql);

if ($resultado) {

    while ($row = $resultado->fetch_assoc()) {

        $respuesta['data'][] = array(
            $row['id_empleado'],
            $row['nombre'],
            $row['apellidos'],
            $row['puesto'],
            $row['telefono'],
            $row['correo']
        );

    }

} else {

    $respuesta['error'] = "ERROR AL CONSULTAR EMPLEADOS";

}

$cx->close();

echo json_encode($respuesta);

?>
