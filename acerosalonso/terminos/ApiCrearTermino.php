<?php
header('Content-Type: application/json');
require_once '../conexion.php';

// Solo permitir método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// Obtener datos enviados por POST
$titulo = $_POST['titulo'] ?? null;
$contenido = $_POST['contenido'] ?? null;

// Validar datos
if (!$titulo || !$contenido) {
    echo json_encode(["error" => "Faltan datos obligatorios"]);
    exit;
}

// Preparar llamada al procedimiento
$stmt = $conn->prepare("CALL CrearNuevoTermino(?, ?)");

if (!$stmt) {
    echo json_encode(["error" => $conn->error]);
    exit;
}

// Vincular parámetros
$stmt->bind_param("ss", $titulo, $contenido);

// Ejecutar
if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Término creado correctamente"
    ]);
} else {
    echo json_encode([
        "error" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>