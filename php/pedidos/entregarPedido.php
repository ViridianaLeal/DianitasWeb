<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../config.php";

$datos = json_decode(
    file_get_contents("php://input"),
    true
);

$id = (int) ($datos["id_pedido"] ?? 0);

$sql = "UPDATE pedidos_cocina
        SET estado = 'Entregado'
        WHERE id_pedido = ?
        AND estado = 'Terminado'";

$stmt = $conexion->prepare($sql);

$stmt->bind_param("i", $id);

$success = $stmt->execute()
           && $stmt->affected_rows > 0;

echo json_encode([
    "success" => $success
]);

$stmt->close();