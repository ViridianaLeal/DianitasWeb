
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

// Preparar consulta
$sql = "SELECT id_categoria, nombre, activo
        FROM categorias
        WHERE id_categoria = ?";

$stmt = $cx->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Error al preparar la consulta: " . $cx->error
    ]);
    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {

    $row = $resultado->fetch_assoc();

    echo json_encode([
        "success" => true,
        "mensaje" => "Se encontró el registro",
        "id_categoria" => $row['id_categoria'],
        "nombre" => $row['nombre'],
        "activo" => $row['activo']
    ]);

} else {

    echo json_encode([
        "success" => false,
        "mensaje" => "No se encontró la categoría"
    ]);

}

$stmt->close();
$cx->close();

?>
