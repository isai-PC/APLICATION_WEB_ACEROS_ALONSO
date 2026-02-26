<?php
// --- Guardar en: Privado/PrincipalCategorias/guardardetalle.php ---
require_once __DIR__ . '/../../conexion.php';
include 'funcionesdetalle.php'; 

// Esta función solo se usa para ÉXITO o ELIMINACIÓN
function volver_listado($mensaje) {
    $to = 'listadodetalle.php?mensaje=' . $mensaje;
    header('Location: ' . $to);
    exit;
}

// Validar imagen
function validar_imagen_segura($archivo) {
    if (empty($archivo['name']) || $archivo['error'] !== UPLOAD_ERR_OK) return true;

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $archivo['tmp_name']);
    finfo_close($finfo);

    $permitidos = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mime, $permitidos)) return false;

    if (getimagesize($archivo['tmp_name']) === false) return false;

    return true;
}

$accion = $_POST['accion'] ?? '';

$datos = [
    'id_categoria' => (int)($_POST['id_categoria'] ?? 0),
    'nombre_producto' => trim($_POST['nombre_producto'] ?? ''),
    'unidad_medida' => empty($_POST['unidad_medida']) ? null : trim($_POST['unidad_medida']),
    'calibre' => empty($_POST['calibre']) ? null : trim($_POST['calibre']),
    'metros' => empty($_POST['metros']) ? null : (float)$_POST['metros'],
    'kg' => empty($_POST['kg']) ? null : (float)$_POST['kg'],
    'color' => empty($_POST['color']) ? null : trim($_POST['color']),
    'ced' => empty($_POST['ced']) ? null : trim($_POST['ced']),
    'ton' => empty($_POST['ton']) ? null : (float)$_POST['ton'],
    'cm' => empty($_POST['cm']) ? null : (float)$_POST['cm'],
    'ImagenesProducto' => null 
];

// --- INTERCEPTOR DE SEGURIDAD ---
if (isset($_FILES['ImagenesProducto']) && !empty($_FILES['ImagenesProducto']['name'])) {
    if (!validar_imagen_segura($_FILES['ImagenesProducto'])) {
        // ERROR: Volvemos al FORMULARIO DE EDICIÓN
        $id = (int)($_POST['id_producto'] ?? 0);
        $url = 'editardetalle.php?error=formato';
        if ($id > 0) $url .= '&id=' . $id;
        
        header('Location: ' . $url);
        exit;
    }
}

if ($accion === 'crear') {
    if ($datos['nombre_producto'] === '' || $datos['id_categoria'] === 0) {
        header('Location: editardetalle.php?error=1'); 
        exit;
    }
    
    $img = subir_imagen_producto($_FILES['ImagenesProducto'] ?? []);
    $datos['ImagenesProducto'] = $img;
    
    $ok = crear_producto($conn, $datos);
    volver_listado($ok ? 'guardado' : 'error');

} elseif ($accion === 'actualizar') {
    $id = (int)($_POST['id_producto'] ?? 0);
    $imagen_actual = trim($_POST['imagen_actual'] ?? '');
    
    if (!$id || $datos['nombre_producto'] === '' || $datos['id_categoria'] === 0) {
        header('Location: editardetalle.php?id='.$id.'&error=1'); 
        exit;
    }
    
    $imgNueva = subir_imagen_producto($_FILES['ImagenesProducto'] ?? []);
    $conImagenNueva = false;
    
    if ($imgNueva) {
        $datos['ImagenesProducto'] = $imgNueva;
        $conImagenNueva = true;
    } else {
        $datos['ImagenesProducto'] = $imagen_actual;
    }
    
    $ok = actualizar_producto($conn, $id, $datos, $conImagenNueva);
    volver_listado($ok ? 'actualizado' : 'error');

} elseif ($accion === 'eliminar') {
    $id = (int)($_POST['id_producto'] ?? 0);
    if (!$id) {
        header('Location: listadodetalle.php?error=1'); exit;
    }
    $ok = eliminar_producto($conn, $id);
    volver_listado($ok ? 'eliminado' : 'error');

} else {
    header('Location: listadodetalle.php');
    exit;
}
?>