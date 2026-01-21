<?php
include 'funciones.php';

$info = $_POST['info'];
$iminfo = null;
$dir = 'images/';

// Validar si subieron una imagen
if (!empty($_FILES['iminfo']['name'])) {

    // VALIDACIÓN REAL DE IMAGEN
    if (!validarImagenSubida($_FILES['iminfo'])) {
        header("Location: editar_info.php?error=La imagen no es válida.");
        exit;
    }

    // Si pasa validación, guardar archivo
    $iminfo = $_FILES['iminfo']['name'];
    move_uploaded_file($_FILES['iminfo']['tmp_name'], $dir . $iminfo);
}

$ok = actualizarSoloInfo($conn, $info, $iminfo);

header("Location: editar_info.php?" . ($ok ? "exito=1" : "error=1"));
exit;
