<?php
include 'funciones_ubicaciones.php';

$id = $_POST['id'];
$descripcion = trim($_POST['descripcion']);
$url = trim($_POST['url']);

// VALIDACIONES
if (strlen($descripcion) < 3) {
    header("Location: editar_ubicacion.php?id=$id&error=La descripción debe tener al menos 3 caracteres.");
    exit;
}

if (is_numeric($descripcion)) {
    header("Location: editar_ubicacion.php?id=$id&error=La descripción no puede ser solo números.");
    exit;
}

$imagen = null;

// VALIDAR IMAGEN si se subió una
if (!empty($_FILES['imagen']['name'])) {

    $allowed = ['png', 'jpg', 'jpeg', 'gif'];
    $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        header("Location: editar_ubicacion.php?id=$id&error=El archivo debe ser PNG, JPG, JPEG o GIF.");
        exit;
    }

    if ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
        header("Location: editar_ubicacion.php?id=$id&error=La imagen no puede superar los 5MB.");
        exit;
    }

    // Si pasa las validaciones, guardar
    $imagen = $_FILES['imagen']['name'];
    move_uploaded_file($_FILES['imagen']['tmp_name'], "images/" . $imagen);
}

// Actualizar
actualizarUbicacion($conn, $id, $descripcion, $imagen, $url);

// Redirigir si todo OK
header("Location: listar_ubicaciones.php");
exit;
?>

