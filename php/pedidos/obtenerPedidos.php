
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../config.php";

$sql = "SELECT id_pedido, mesa, productos, total,
               estado, fecha
        FROM pedidos_cocina
        WHERE estado IN ('Pendiente', 'Preparando')
        ORDER BY fecha ASC";

$resultado = $conexion->query($sql);

$pedidos = [];

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $fila["productos"] = json_decode(
            $fila["productos"],
            true
        );

        $pedidos[] = $fila;
    }
}

echo json_encode(
    $pedidos,
    JSON_UNESCAPED_UNICODE
);
?>
