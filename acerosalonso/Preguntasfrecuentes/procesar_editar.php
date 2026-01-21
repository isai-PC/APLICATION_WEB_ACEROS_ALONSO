<?php
include '../conexion.php';

$id = $_POST['id'];
$pregunta = $_POST['pregunta'];
$respuesta = $_POST['respuesta'];

$sql = "UPDATE preguntas_frecuentes 
        SET pregunta='$pregunta', respuesta='$respuesta'
        WHERE id=$id";

$conn->query($sql);

header("Location: index.php");
exit;
?>
