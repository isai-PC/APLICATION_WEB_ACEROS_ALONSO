<?php
header('Content-Type: application/json');
require_once '../conexion.php';

// Consulta solo el término activo
$sql = "SELECT titulo, contenido, fecha 
        FROM terminos_condiciones 
        WHERE activo = 1 
        LIMIT 1";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["error" => $conn->error]);
    exit;
}

if ($result->num_rows > 0) {
    $termino = $result->fetch_assoc();

    echo json_encode([
        "success" => true,
        "data" => $termino
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "No hay término activo"
    ]);
}

$conn->close();
?>