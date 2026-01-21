<?php
include 'funciones_ubicaciones.php';

$id = $_GET['id'];

eliminarUbicacion($conn, $id);

header("Location: listar_ubicaciones.php");
exit;
?>
