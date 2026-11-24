
<?php

header("Content-Type: application/json; charset=utf-8");

require_once '../config.php';

$respuesta = [
    "success" => false,
    "mensaje" => ""
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = trim($_POST["correo"] ?? "");
    $contrasena = $_POST["contrasena"] ?? "";

    if ($correo === "" || $contrasena === "") {
        $respuesta["mensaje"] = "Completa todos los campos.";
    } else {

        $sql = "SELECT e.nombre, e.puesto, u.contrasena
                FROM usuarios u
                INNER JOIN empleados e
                ON u.id_empleado = e.id_empleado
                WHERE u.correo = ?";

        $stmt = $cx->prepare($sql);

        if ($stmt) {

            $stmt->bind_param("s", $correo);
            $stmt->execute();

            $resultado = $stmt->get_result();

            if ($fila = $resultado->fetch_assoc()) {

                if (password_verify($contrasena, $fila["contrasena"])) {

                    $respuesta["success"] = true;
                    $respuesta["mensaje"] = "Bienvenida a Dianita's";
                    $respuesta["nombre"] = $fila["nombre"];
                    $respuesta["puesto"] = $fila["puesto"];

                } else {
                    $respuesta["mensaje"] = "Contraseña incorrecta.";
                }

            } else {
                $respuesta["mensaje"] = "El correo no está registrado.";
            }

            $stmt->close();

        } else {
            $respuesta["mensaje"] = "No se pudo preparar la consulta.";
        }
    }

} else {
    $respuesta["mensaje"] = "Solicitud no válida.";
}

echo json_encode($respuesta);

?>
