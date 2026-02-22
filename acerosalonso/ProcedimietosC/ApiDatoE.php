<?php
header('Content-Type: application/json');
require_once('funciones.php');

if (isset($_GET['id'])) {
    $idEmpleado = $_GET['id'];
    $datos = obtenerDatosEmpleado($idEmpleado);
    echo json_encode($datos);
} else {
    echo json_encode(['error' => 'ID no proporcionado']);
}
?>