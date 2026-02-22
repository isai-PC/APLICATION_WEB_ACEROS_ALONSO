<?php
header('Content-Type: application/json');
require_once '../conexion.php'; // ← sube un nivel

$departamento = $_GET['departamento'] ?? 1;
$inicio       = $_GET['inicio'] ?? date('Y-m-01');
$fin          = $_GET['fin'] ?? date('Y-m-d');

$response = [
    'departamento' => $departamento,
    'inicio'       => $inicio,
    'fin'          => $fin
];

$stmt = $conn->prepare("CALL sp_reporte_departamento_completo(?, ?, ?)");

if (!$stmt) {
    echo json_encode(["error" => $conn->error]);
    exit;
}

$stmt->bind_param("iss", $departamento, $inicio, $fin);
$stmt->execute();

$resultado = $stmt->get_result();
$response['reporte'] = $resultado->fetch_all(MYSQLI_ASSOC);

$stmt->close();

echo json_encode($response);
?>