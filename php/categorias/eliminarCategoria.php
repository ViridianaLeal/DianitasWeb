
<?php

require_once '../config.php';

header("Content-Type: application/json; charset=utf-8");

// Verificar que se recibió el ID
if (!isset($_POST['id_categoria']) || !is_numeric($_POST['id_categoria'])) {

    echo json_encode([
        "success" => false,
        "mensaje" => "ID de categoría no válido"
    ]);

    exit;
}

$id = (int) $_POST['id_categoria'];

// Preparar consulta SQL
$sql = "DELETE FROM categorias WHERE id_categoria = ?";

$stmt = $cx->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "mensaje" => "Error al preparar la consulta: " . $cx->error
    ]);

    exit;
}

// Asociar ID
$stmt->bind_param("i", $id);

// Ejecutar eliminación
if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        echo json_encode([
            "success" => true,
            "mensaje" => "Categoría eliminada correctamente"
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "mensaje" => "No se encontró la categoría"
        ]);

    }

} else {

    echo json_encode([
        "success" => false,
        "mensaje" => "Error al eliminar: " . $stmt->error
    ]);

}

// Cerrar conexiones
$stmt->close();
$cx->close();

?>
