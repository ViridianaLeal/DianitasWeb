
<?php

require_once '../config.php';

header("Content-Type: application/json; charset=utf-8");

$respuesta = array(
    "success" => false,
    "mensaje" => ""
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Obtener datos del formulario
    $id = intval($_POST['id_empleado'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $puesto = strtolower(trim($_POST['puesto'] ?? ''));
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');

    // Validar campos
    if (
        $id <= 0 ||
        $nombre == '' ||
        $apellidos == '' ||
        $puesto == '' ||
        $telefono == '' ||
        $correo == ''
    ) {

        $respuesta['mensaje'] = "TODOS LOS CAMPOS SON OBLIGATORIOS";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $respuesta['mensaje'] = "CORREO NO VALIDO";

    } elseif (!in_array($puesto, array(
        'mesera',
        'cocinera',
        'administrador'
    ), true)) {

        $respuesta['mensaje'] = "EL PUESTO NO ES VALIDO";

    } else {

        try {

            // Iniciar transacción
            $cx->begin_transaction();

            // ==================================
            // 1. ACTUALIZAR EMPLEADO
            // ==================================

            $sqlEmpleado = "UPDATE empleados SET
                nombre = ?,
                apellidos = ?,
                puesto = ?,
                telefono = ?,
                correo = ?
                WHERE id_empleado = ?";

            $stmtEmpleado = $cx->prepare($sqlEmpleado);

            $stmtEmpleado->bind_param(
                "sssssi",
                $nombre,
                $apellidos,
                $puesto,
                $telefono,
                $correo,
                $id
            );

            $stmtEmpleado->execute();

            // ==================================
            // 2. ACTUALIZAR CORREO DEL USUARIO
            // ==================================

            $sqlUsuario = "UPDATE usuarios
                SET correo = ?
                WHERE id_empleado = ?";

            $stmtUsuario = $cx->prepare($sqlUsuario);

            $stmtUsuario->bind_param(
                "si",
                $correo,
                $id
            );

            $stmtUsuario->execute();

            // ==================================
            // 3. CONFIRMAR CAMBIOS
            // ==================================

            $cx->commit();

            $respuesta['success'] = true;
            $respuesta['mensaje'] =
                "EMPLEADA ACTUALIZADA CORRECTAMENTE";

            // Cerrar consultas
            $stmtEmpleado->close();
            $stmtUsuario->close();

        } catch (mysqli_sql_exception $e) {

            // Deshacer cambios si ocurre un error
            $cx->rollback();

            error_log(
                "ERROR AL EDITAR EMPLEADO: " . $e->getMessage()
            );

            $respuesta['mensaje'] =
                "ERROR AL ACTUALIZAR. VERIFICA SI EL CORREO YA EXISTE O SI LOS DATOS SON CORRECTOS.";

        }

    }

} else {

    $respuesta['mensaje'] = "SOLICITUD NO VALIDA";

}

// Cerrar conexión
$cx->close();

// Enviar respuesta JSON
echo json_encode($respuesta);

?>
