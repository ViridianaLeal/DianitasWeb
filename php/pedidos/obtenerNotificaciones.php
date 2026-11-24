<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../config.php";

$sql = "SELECT id_pedido, mesa, total, estado, fecha
        FROM pedidos_cocina
        WHERE estado = 'Terminado'
        ORDER BY fecha ASC";

$resultado = $conexion->query($sql);

$notificaciones = [];

while ($fila = $resultado->fetch_assoc()) {
    $notificaciones[] = $fila;
}

echo json_encode(
    $notificaciones,
    JSON_UNESCAPED_UNICODE
);