<?php
include 'funciones.php';

$vision = $_POST['vision'];
$img_vision = null;
$dir = 'images/';

// Validar si subieron una imagen
if (!empty($_FILES['img_vision']['name'])) {

    // VALIDAR IMAGEN
    if (!validarImagenSubida($_FILES['img_vision'])) {
        header("Location: editar_vision.php?error=La imagen no es válida.");
        exit;
    }

    // Si pasa validación, guardar archivo
    $img_vision = $_FILES['img_vision']['name'];
    move_uploaded_file($_FILES['img_vision']['tmp_name'], $dir . $img_vision);
}

$ok = actualizarSoloVision($conn, $vision, $img_vision);

header("Location: editar_vision.php?" . ($ok ? "exito=1" : "error=1"));
exit;
