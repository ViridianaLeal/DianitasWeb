
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../config.php";

$datos = json_decode(file_get_contents("php://input"), true);

if (!$datos || empty($datos["productos"]) || empty($datos["mesa"])) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Datos del pedido incompletos"
    ]);
    exit;
}

$mesa = $datos["mesa"];
$productos = json_encode(
    $datos["productos"],
    JSON_UNESCAPED_UNICODE
);
$total = $datos["total"];

$sql = "INSERT INTO pedidos_cocina
        (mesa, productos, total)
        VALUES (?, ?, ?)";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Error al preparar el pedido"
    ]);
    exit;
}

$stmt->bind_param("ssd", $mesa, $productos, $total);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "mensaje" => "Pedido enviado a cocina",
        "id_pedido" => $conexion->insert_id
    ]);
} else {
    echo json_encode([
        "success" => false,
        "mensaje" => "No se pudo guardar el pedido"
    ]);
}

$stmt->close();
?>
