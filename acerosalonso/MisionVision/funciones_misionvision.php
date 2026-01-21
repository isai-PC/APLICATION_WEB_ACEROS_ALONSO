<?php
include '../conexion.php';

function obtenerMisionVision($conn) {
    if (!$conn) return false;

    $sql = "SELECT * FROM mision_vision LIMIT 1";
    $result = $conn->query($sql);

    if ($result === false || $result->num_rows === 0) {
        return [
            'mision' => 'Nuestra misión aún no ha sido registrada.',
            'vision' => 'Nuestra visión aún no ha sido registrada.',
            'info'   => 'Aún no se ha registrado información de por qué elegirnos.',
            'img_mision' => 'default_mision.jpg',
            'img_vision' => 'default_vision.jpg',
            'iminfo' => 'default_info.jpg'
        ];
    }

    return $result->fetch_assoc();
}

function validarImagen1($nombre) {
    $valid_ext = ['jpg','jpeg','png','webp'];
    $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
    return in_array($ext, $valid_ext);
}

function validarImagenSubida($file) {
    // Validar errores de subida
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Validar tamaño (máximo 4MB)
    if ($file['size'] > 4 * 1024 * 1024) { 
        return false;
    }

    // Validar que realmente sea una imagen
    $info = getimagesize($file['tmp_name']);
    if ($info === false) {
        return false; // No es imagen real
    }

    // Validar extensión permitida
    $valid_ext = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    return in_array($ext, $valid_ext);
}


function actualizarMisionVision($conn, $mision, $vision, $info, $img_mision = null, $img_vision = null, $iminfo = null) {

    if (!$conn) return false;
    if (empty($mision) || empty($vision) || empty($info)) return false;

    if ($img_mision && !validarImagen1($img_mision)) return false;
    if ($img_vision && !validarImagen1($img_vision)) return false;
    if ($iminfo && !validarImagen1($iminfo)) return false;

    $sql = "UPDATE mision_vision SET mision=?, vision=?, info=?";
    $params = [$mision, $vision, $info];

    if ($img_mision) {
        $sql .= ", img_mision=?";
        $params[] = $img_mision;
    }
    if ($img_vision) {
        $sql .= ", img_vision=?";
        $params[] = $img_vision;
    }
    if ($iminfo) {
        $sql .= ", iminfo=?";
        $params[] = $iminfo;
    }

    $sql .= " WHERE id=1";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;

    $tipos = str_repeat('s', count($params));
    $stmt->bind_param($tipos, ...$params);

    return $stmt->execute();
}

?>

