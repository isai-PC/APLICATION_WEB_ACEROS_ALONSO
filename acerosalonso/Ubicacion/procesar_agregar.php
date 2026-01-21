<?php
include 'funciones_ubicaciones.php';

$descripcion = trim($_POST['descripcion']);
$url = trim($_POST['url']);
$imagen = $_FILES['imagen'];

/* las validaciones*/

// descripción 
if (preg_match('/\d/', $descripcion)) {
    echo "<script>alert('La descripción no puede contener números.'); window.history.back();</script>";
    exit;
}

// descriocion para que solo pueda agregar letras
if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s,.\-]+$/', $descripcion)) {
    echo "<script>alert('La descripción solo debe contener letras, espacios y signos permitidos.'); window.history.back();</script>";
    exit;
}

// validacion de URL
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    echo "<script>alert('Debes ingresar una URL válida.'); window.history.back();</script>";
    exit;
}

// Validación de imagen por extensión permitida
$extensiones = ['jpg', 'jpeg', 'png', 'webp'];
$extension = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));

if (!in_array($extension, $extensiones)) {
    echo "<script>alert('La imagen debe ser JPG, JPEG, PNG o WEBP.'); window.history.back();</script>";
    exit;
}

// Validación MIME como refuerzo (pero ya no bloquea PNG por error del servidor)
$permitidos = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/x-png'];
$tipo = strtolower($imagen['type']);

if (!in_array($tipo, $permitidos)) {
    // Si el MIME es raro pero la extensión es válida → permitir
    // Solo se bloquea si el MIME es claramente inseguro
    if ($tipo !== "application/octet-stream") {
        echo "<script>alert('El formato de imagen no es válido.'); window.history.back();</script>";
        exit;
    }
}

// tamaño de la imagen 2MB
if ($imagen['size'] > 2 * 1024 * 1024) {
    echo "<script>alert('La imagen debe pesar menos de 2MB.'); window.history.back();</script>";
    exit;
}

/* guardar imagen */

$dir = "images/";
$nombreImagen = time() . "_" . basename($imagen['name']); // Evita nombres repetidos
$rutaFinal = $dir . $nombreImagen;

if (!move_uploaded_file($imagen['tmp_name'], $rutaFinal)) {
    echo "<script>alert('Error al guardar la imagen.'); window.history.back();</script>";
    exit;
}

/* para qure se aguarde en la base de datos */

insertarUbicacion($conn, $descripcion, $nombreImagen, $url);

header("Location: listar_ubicaciones.php");
exit;
?>

