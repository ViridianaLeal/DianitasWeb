
<?php

require_once '../config.php';

header("Content-Type: application/json; charset=utf-8");

// Respuesta inicial
$respuesta = array(
    "success" => false,
    "mensaje" => ""
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Obtener datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $puesto = strtolower(trim($_POST['puesto'] ?? ''));
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    // Validar campos obligatorios
    if (
        $nombre == '' ||
        $apellidos == '' ||
        $puesto == '' ||
        $telefono == '' ||
        $correo == '' ||
        $contrasena == ''
    ) {

        $respuesta['mensaje'] = "TODOS LOS CAMPOS SON OBLIGATORIOS";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $respuesta['mensaje'] = "CORREO NO VALIDO";

    } elseif (strlen($contrasena) < 8) {

        $respuesta['mensaje'] = "LA CONTRASEÑA DEBE TENER MINIMO 8 CARACTERES";

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
            // 1. REGISTRAR EMPLEADO
            // ==================================

            $sqlEmpleado = "INSERT INTO empleados
                (nombre, apellidos, puesto, telefono, correo)
                VALUES (?, ?, ?, ?, ?)";

            $stmtEmpleado = $cx->prepare($sqlEmpleado);

            $stmtEmpleado->bind_param(
                "sssss",
                $nombre,
                $apellidos,
                $puesto,
                $telefono,
                $correo
            );

            $stmtEmpleado->execute();

            // Obtener ID generado automáticamente
            $idEmpleado = $cx->insert_id;

            // ==================================
            // 2. CIFRAR CONTRASEÑA
            // ==================================

            $passwordHash = password_hash(
                $contrasena,
                PASSWORD_DEFAULT
            );

            // ==================================
            // 3. REGISTRAR USUARIO
            // ==================================

            $sqlUsuario = "INSERT INTO usuarios
                (id_empleado, correo, contrasena)
                VALUES (?, ?, ?)";

            $stmtUsuario = $cx->prepare($sqlUsuario);

            $stmtUsuario->bind_param(
                "iss",
                $idEmpleado,
                $correo,
                $passwordHash
            );

            $stmtUsuario->execute();

            // ==================================
            // 4. CONFIRMAR REGISTROS
            // ==================================

            $cx->commit();

            $respuesta['success'] = true;

            $respuesta['mensaje'] =
                "EMPLEADO Y USUARIO REGISTRADOS CORRECTAMENTE";

            // Cerrar consultas
            $stmtEmpleado->close();
            $stmtUsuario->close();

        } catch (mysqli_sql_exception $e) {

            // Deshacer cambios si ocurre un error
            $cx->rollback();

            error_log(
                "ERROR AL REGISTRAR EMPLEADO: " . $e->getMessage()
            );

            $respuesta['mensaje'] =
                "ERROR AL REGISTRAR. VERIFICA SI EL CORREO YA EXISTE O SI LOS DATOS SON CORRECTOS.";

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
