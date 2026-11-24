
<?php

require_once '../config.php';

header("Content-Type: application/json; charset=utf-8");

$respuesta = array(
    "success" => false,
    "mensaje" => "",
    "id_empleado" => "",
    "nombre" => "",
    "apellidos" => "",
    "puesto" => "",
    "telefono" => "",
    "correo" => ""
);

if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
    isset($_POST['id_empleado'])) {

    $id = intval($_POST['id_empleado']);

    $sql = "SELECT
                id_empleado,
                nombre,
                apellidos,
                puesto,
                telefono,
                correo
            FROM empleados
            WHERE id_empleado = ?";

    $stmt = $cx->prepare($sql);

    if ($stmt) {

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($row = $resultado->fetch_assoc()) {

            $respuesta['success'] = true;
            $respuesta['mensaje'] = "SE ENCONTRO REGISTRO";

            $respuesta['id_empleado'] = $row['id_empleado'];
            $respuesta['nombre'] = $row['nombre'];
            $respuesta['apellidos'] = $row['apellidos'];
            $respuesta['puesto'] = $row['puesto'];
            $respuesta['telefono'] = $row['telefono'];
            $respuesta['correo'] = $row['correo'];

        } else {

            $respuesta['mensaje'] = "NO SE ENCONTRO EL EMPLEADO";

        }

        $stmt->close();

    } else {

        $respuesta['mensaje'] = "ERROR EN LA CONSULTA";

    }

} else {

    $respuesta['mensaje'] = "NO SE RECIBIO EL ID DEL EMPLEADO";

}

$cx->close();

echo json_encode($respuesta);

?>
