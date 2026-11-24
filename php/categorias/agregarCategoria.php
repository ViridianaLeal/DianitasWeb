
<?php

header('Content-Type: application/json; charset=utf-8');

include_once("../config.php");

// Recibir el nombre
$nombre = trim($_POST['nombre'] ?? '');

// Validar el nombre
if ($nombre === '') {

    echo json_encode([
        "success" => false,
        "mensaje" => "El nombre es obligatorio"
    ]);

    exit;
}

// Preparar consulta SQL
$sql = "INSERT INTO categorias (nombre, activo) VALUES (?, 1)";

$stmt = $cx->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "mensaje" => "Error al preparar la consulta: " . $cx->error
    ]);

    exit;
}

// Asociar el nombre
$stmt->bind_param("s", $nombre);

// Ejecutar registro
if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "mensaje" => "Categoría agregada correctamente",
        "id_categoria" => $stmt->insert_id,
        "nombre" => $nombre,
        "activo" => 1
    ]);

} else {

    echo json_encode([
        "success" => false,
        "mensaje" => "Error al registrar la categoría: " . $stmt->error
    ]);

}

// Cerrar conexiones
$stmt->close();
$cx->close();

?>
