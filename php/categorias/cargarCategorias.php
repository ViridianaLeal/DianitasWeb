
<?php

require_once '../config.php';

header("Content-Type: application/json; charset=utf-8");

// Consultar todas las categorías
$sql = "SELECT * FROM categorias";

$resultado = $cx->query($sql);

// Preparar respuesta
$salida = array(
    "data" => array(),
    "success" => true,
    "mensaje" => ""
);

// Verificar si la consulta funcionó
if (!$resultado) {

    echo json_encode([
        "success" => false,
        "mensaje" => "Error al consultar categorías: " . $cx->error,
        "data" => []
    ]);

    $cx->close();
    exit;
}

// Recorrer los registros
if ($resultado->num_rows > 0) {

    while ($row = $resultado->fetch_assoc()) {

        $salida['data'][] = array(
            $row['id_categoria'],
            $row['nombre'],
            $row['activo']
        );

    }

    $salida['mensaje'] = "Categorías encontradas";

} else {

    $salida['mensaje'] = "No hay categorías registradas";

}

// Cerrar conexión
$cx->close();

// Devolver JSON
echo json_encode($salida);

?>
