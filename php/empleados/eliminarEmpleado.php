
<?php

require_once '../config.php';

header("Content-Type: application/json; charset=utf-8");

$respuesta = array(
    "success" => false,
    "mensaje" => ""
);

if (
    $_SERVER['REQUEST_METHOD'] == 'POST' &&
    isset($_POST['id_empleado'])
) {

    $id = intval($_POST['id_empleado']);

    if ($id <= 0) {

        $respuesta['mensaje'] = "ID DE EMPLEADO NO VALIDO";

    } else {

        try {

            $cx->begin_transaction();

            // Eliminar cuenta de usuario
            $sqlUsuario = "DELETE FROM usuarios
                WHERE id_empleado = ?";

            $stmtUsuario = $cx->prepare($sqlUsuario);

            $stmtUsuario->bind_param("i", $id);
            $stmtUsuario->execute();

            // Eliminar empleado
            $sqlEmpleado = "DELETE FROM empleados
                WHERE id_empleado = ?";

            $stmtEmpleado = $cx->prepare($sqlEmpleado);

            $stmtEmpleado->bind_param("i", $id);
            $stmtEmpleado->execute();

            if ($stmtEmpleado->affected_rows > 0) {

                $cx->commit();

                $respuesta['success'] = true;
                $respuesta['mensaje'] = "EMPLEADA ELIMINADA CORRECTAMENTE";

            } else {

                $cx->rollback();

                $respuesta['mensaje'] = "NO SE ENCONTRO EL EMPLEADO";

            }

            $stmtUsuario->close();
            $stmtEmpleado->close();

        } catch (mysqli_sql_exception $e) {

            $cx->rollback();

            $respuesta['mensaje'] =
                "ERROR AL ELIMINAR EL EMPLEADO";

        }

    }

} else {

    $respuesta['mensaje'] = "NO SE RECIBIO EL ID DEL EMPLEADO";

}

$cx->close();

echo json_encode($respuesta);

?>
