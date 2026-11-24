<?php

$server = "localhost";
$user = "root";
$pass = "";
$bd = "dianitasdb";

// Conexión a la base de datos
$cx = mysqli_connect($server, $user, $pass, $bd);

// Verificar conexión
if (!$cx) {
    die("Error de conexión a la base de datos");
}

// Configurar caracteres
mysqli_set_charset($cx, "utf8mb4");

// Compatibilidad con los archivos de pedidos
$conexion = $cx;