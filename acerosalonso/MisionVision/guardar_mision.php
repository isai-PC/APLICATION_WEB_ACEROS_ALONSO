<?php
include 'funciones.php';

$mision = $_POST['mision'];
$img_mision = null;
$dir = 'images/';

// Validar si subieron imagen
if (!empty($_FILES['img_mision']['name'])) {

    // VALIDACIÓN REAL DE IMAGEN
    if (!validarImagenSubida($_FILES['img_mision'])) {
        header("Location: editar_mision.php?error=La imagen no es válida.");
        exit;
    }

    // Si pasa la validación, guardar
    $img_mision = $_FILES['img_mision']['name'];
    move_uploaded_file($_FILES['img_mision']['tmp_name'], $dir.$img_mision);
}

$ok = actualizarSoloMision($conn, $mision, $img_mision);

header("Location: editar_mision.php?" . ($ok ? "exito=1" : "error=1"));
exit;
