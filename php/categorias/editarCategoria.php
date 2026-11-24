
<?php

require_once '../config.php';

header("Content-Type: application/json; charset=utf-8");

// Verificar que se recibieron los datos
if (
    !isset($_POST['id_categoria']) ||
    !isset($_POST['nombre'])
) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Faltan datos para actualizar la categoría"
    ]);
    exit;
}

$id = (int) $_POST['id_categoria'];
$nombre = trim($_POST['nombre']);
$activo = isset($_POST['activo']) ? (int) $_POST['activo'] : 1;

// Validar nombre
if ($nombre === '') {
    echo json_encode([
        "success" => false,
        "mensaje" => "El nombre de la categoría es obligatorio"
    ]);
    exit;
}

// Preparar consulta SQL
$sql = "UPDATE categorias
        SET nombre = ?, activo = ?
        WHERE id_categoria = ?";

$stmt = $cx->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Error al preparar la consulta: " . $cx->error
    ]);
    exit;
}

// Asociar parámetros
$stmt->bind_param("sii", $nombre, $activo, $id);

// Ejecutar actualización
if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "mensaje" => "Categoría actualizada correctamente"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "mensaje" => "Error al actualizar: " . $stmt->error
    ]);

}

// Cerrar conexiones
$stmt->close();
$cx->close();

?>
