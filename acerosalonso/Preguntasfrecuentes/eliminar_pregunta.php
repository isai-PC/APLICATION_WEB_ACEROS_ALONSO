<?php
include '../conexion.php';

$id = $_GET['id'];

$sql = "DELETE FROM preguntas_frecuentes WHERE id = $id";
$conn->query($sql);

header("Location: index.php");
exit;
?>
