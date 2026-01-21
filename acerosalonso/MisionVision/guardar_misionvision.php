<?php
include 'funciones_misionvision.php';
include '../conexion.php';

$mision = $_POST['mision'];
$vision = $_POST['vision'];
$info   = $_POST['info'];

$dir = 'images/';

$img_mision = null;
$img_vision = null;
$iminfo = null;


// VALIDAR IMAGEN MISIÓN
if (!empty($_FILES['img_mision']['name'])) {

    if (!validarImagenSubida($_FILES['img_mision'])) {
        header("Location: editar_misionvision.php?error=La imagen de Misión no es válida");
        exit;
    }

    $img_mision = $_FILES['img_mision']['name'];
    move_uploaded_file($_FILES['img_mision']['tmp_name'], $dir.$img_mision);
}


// VALIDAR IMAGEN VISIÓN
if (!empty($_FILES['img_vision']['name'])) {

    if (!validarImagenSubida($_FILES['img_vision'])) {
        header("Location: editar_misionvision.php?error=La imagen de Visión no es válida");
        exit;
    }

    $img_vision = $_FILES['img_vision']['name'];
    move_uploaded_file($_FILES['img_vision']['tmp_name'], $dir.$img_vision);
}


// VALIDAR IMAGEN INFO
if (!empty($_FILES['iminfo']['name'])) {

    if (!validarImagenSubida($_FILES['iminfo'])) {
        header("Location: editar_misionvision.php?error=La imagen de Información no es válida");
        exit;
    }

    $iminfo = $_FILES['iminfo']['name'];
    move_uploaded_file($_FILES['iminfo']['tmp_name'], $dir.$iminfo);
}



// GUARDAR EN BD
$actualizado = actualizarMisionVision($conn, $mision, $vision, $info, $img_mision, $img_vision, $iminfo);

if ($actualizado) {
    header("Location: editar_misionvision.php?exito=1");
    exit;
} else {
    header("Location: editar_misionvision.php?error=No se pudo actualizar la información");
    exit;
}
?>

