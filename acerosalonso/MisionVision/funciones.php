<?php
include '../conexion.php';

// Obtener datos completos
function obtenerMisionVision($conn) {
    $sql = "SELECT * FROM mision_vision WHERE id = 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return [
            'mision' => '',
            'vision' => '',
            'info' => '',
            'img_mision' => '',
            'img_vision' => '',
            'iminfo' => ''
        ];
    }
}

// Funciones de actualización por sección
function actualizarSoloMision($conn, $mision, $img_mision = null) {
    if ($img_mision) {
        $sql = "UPDATE mision_vision SET mision=?, img_mision=? WHERE id=1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $mision, $img_mision);
    } else {
        $sql = "UPDATE mision_vision SET mision=? WHERE id=1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $mision);
    }
    return $stmt->execute();
}

function actualizarSoloVision($conn, $vision, $img_vision = null) {
    if ($img_vision) {
        $sql = "UPDATE mision_vision SET vision=?, img_vision=? WHERE id=1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $vision, $img_vision);
    } else {
        $sql = "UPDATE mision_vision SET vision=? WHERE id=1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $vision);
    }
    return $stmt->execute();
}

function actualizarSoloInfo($conn, $info, $iminfo = null) {
    if ($iminfo) {
        $sql = "UPDATE mision_vision SET info=?, iminfo=? WHERE id=1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $info, $iminfo);
    } else {
        $sql = "UPDATE mision_vision SET info=? WHERE id=1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $info);
    }
    return $stmt->execute();
}


function validarImagen1($nombre) {
    $valid_ext = ['jpg','jpeg','png','webp'];
    $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
    return in_array($ext, $valid_ext);
}

function validarImagenSubida($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if ($file['size'] > 4 * 1024 * 1024) { 
        return false;
    }

    $info = getimagesize($file['tmp_name']);
    if ($info === false) {
        return false;
    }

    $valid_ext = ['jpg','jpeg','png','webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    return in_array($ext, $valid_ext);
}

?>
