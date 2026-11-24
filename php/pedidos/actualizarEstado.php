
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../config.php";

$datos = json_decode(file_get_contents("php://input"), true);

$id = $datos["id_pedido"] ?? 0;
$estado = $datos["estado"] ?? "";

$estadosPermitidos = [
    "Pendiente",
    "Preparando",
    "Terminado"
];

if (!$id || !in_array($estado, $estadosPermitidos, true)) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Datos incorrectos"
    ]);
    exit;
}

$sql = "UPDATE pedidos_cocina
        SET estado = ?
        WHERE id_pedido = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("si", $estado, $id);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "mensaje" => "Estado actualizado"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "mensaje" => "No se pudo actualizar"
    ]);
}

$stmt->close();
?>
