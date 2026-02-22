<?php
header('Content-Type: application/json');
require_once '../conexion.php'; // ← sube un nivel

$empleado = $_GET['empleado'] ?? 1;
$inicio   = $_GET['inicio'] ?? date('Y-m-01');
$fin      = $_GET['fin'] ?? date('Y-m-d');

$response = [
    'empleado' => $empleado,
    'inicio'   => $inicio,
    'fin'      => $fin
];

$stmt = $conn->prepare("CALL sp_reporte_empleado_completo(?, ?, ?)");

if (!$stmt) {
    echo json_encode(["error" => $conn->error]);
    exit;
}

$stmt->bind_param("iss", $empleado, $inicio, $fin);
$stmt->execute();

$resultado = $stmt->get_result();
$response['reporte'] = $resultado->fetch_all(MYSQLI_ASSOC);

$stmt->close();

echo json_encode($response);
?>