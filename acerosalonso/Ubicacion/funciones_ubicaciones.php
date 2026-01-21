<?php
include '../conexion.php';

// Obtener todas las ubi
function obtenerUbicaciones($conn) {
    $sql = "SELECT * FROM ubicaciones ORDER BY id DESC";
    return $conn->query($sql);
}

// Obtener una sola ubi
function obtenerUbicacionPorId($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM ubicaciones WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Insertar ubi
function insertarUbicacion($conn, $descripcion, $imagen, $url) {
    $stmt = $conn->prepare("INSERT INTO ubicaciones (descripcion, imagen_nombre, url) VALUES (?,?,?)");
    $stmt->bind_param("sss", $descripcion, $imagen, $url);
    return $stmt->execute();
}

// Editar ubi
function actualizarUbicacion($conn, $id, $descripcion, $imagen, $url) {
    if ($imagen == null) {
        $stmt = $conn->prepare("UPDATE ubicaciones SET descripcion=?, url=? WHERE id=?");
        $stmt->bind_param("ssi", $descripcion, $url, $id);
    } else {
        $stmt = $conn->prepare("UPDATE ubicaciones SET descripcion=?, imagen_nombre=?, url=? WHERE id=?");
        $stmt->bind_param("sssi", $descripcion, $imagen, $url, $id);
    }
    return $stmt->execute();
}

// Eliminar ubi
function eliminarUbicacion($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM ubicaciones WHERE id=?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>
