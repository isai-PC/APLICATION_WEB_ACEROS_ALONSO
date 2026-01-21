<?php
include '../conexion.php';

$pregunta = $_POST['pregunta'];
$respuesta = $_POST['respuesta'];

$sql = "INSERT INTO preguntas_frecuentes (pregunta, respuesta)
        VALUES ('$pregunta', '$respuesta')";

$conn->query($sql);

header("Location: index.php");
exit;
?>
