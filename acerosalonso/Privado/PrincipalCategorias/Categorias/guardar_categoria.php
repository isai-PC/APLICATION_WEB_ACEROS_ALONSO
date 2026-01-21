<?php
// --- Guardar en: Privado/PrincipalCategorias/guardar_categoria.php ---
require_once __DIR__ . '/../../../conexion.php';
include 'funciones_Categoria.php';

// Redirige al LISTADO con mensaje de éxito/error
function volver($mensaje) {
    header('Location: listado_Categoria.php?mensaje=' . $mensaje);
    exit;
}

// Redirige al FORMULARIO si faltan datos
function volver_form($id, $error) {
    header('Location: listado_Categoria.php?id='.$id.'&error=1');
    exit;
}

$accion = $_POST['accion'] ?? '';

// --- LÓGICA DE CREAR ---
if ($accion === 'crear') {
    $nombre = trim($_POST['nombre_categoria'] ?? '');
    $texto  = trim($_POST['texto_secundario'] ?? '');
    
    if ($nombre === '') volver_form(0, 'vacio');

    $img = subir_imagen($_FILES['imagen_categoria'] ?? []);
    
    // Si se subió archivo pero la validación falló, redirige con error
    if (!empty($_FILES['imagen_categoria']['name']) && $img === null) {
        header('Location: agregar_Categoria.php?error=imagen_invalida');
        exit;
    }
    
    $ok = crear_categoria($conn, $nombre, $texto, $img);
    
    volver($ok ? 'guardado' : 'error');

// --- LÓGICA DE ACTUALIZAR ---
} elseif ($accion === 'actualizar') {
    $id     = (int)($_POST['id_categoria'] ?? 0);
    $nombre = trim($_POST['nombre_categoria'] ?? '');
    $texto  = trim($_POST['texto_secundario'] ?? '');
    $actual = trim($_POST['imagen_actual'] ?? '');

    if (!$id || $nombre === '') volver_form($id, 'vacio');

    $imgNueva = subir_imagen($_FILES['imagen_categoria'] ?? []);
    
    // Si se subió archivo pero la validación falló, redirige con error
    if (!empty($_FILES['imagen_categoria']['name']) && $imgNueva === null) {
        header('Location: agregar_Categoria.php?id='.$id.'&error=imagen_invalida');
        exit;
    }
    
    // Si no sube imagen nueva, usamos la actual (hidden)
    $imgFinal = $imgNueva ?: $actual;

    $ok = actualizar_categoria($conn, $id, $nombre, $texto, $imgFinal);
    
    volver($ok ? 'actualizado' : 'error');

// --- LÓGICA DE ELIMINAR ---
} elseif ($accion === 'eliminar') {
    $id = (int)($_POST['id_categoria'] ?? 0);
    if (!$id) volver('error');

    $ok = eliminar_categoria($conn, $id);
    
    volver($ok ? 'eliminado' : 'error');

} else {
    volver('error');
}
?>