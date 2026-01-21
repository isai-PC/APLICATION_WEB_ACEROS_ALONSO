<?php
include '../conexion.php';

function obtenerPreguntas($conn) {
    $sql = "SELECT * FROM preguntas_frecuentes ORDER BY id DESC";
    return $conn->query($sql);
}

function obtenerPreguntaPorId($conn, $id) {
    $sql = "SELECT * FROM preguntas_frecuentes WHERE id = $id";
    return $conn->query($sql)->fetch_assoc();
}
?>
