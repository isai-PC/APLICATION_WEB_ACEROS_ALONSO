<?php
require_once __DIR__ . '/../../conexion.php';
require_once __DIR__ . '/funcionesdetalle.php'; 

/**
 * Helper para redireccionar al listado con un mensaje
 */
function volver_listado($mensaje) {
    header('Location: listadodetalle.php?mensaje=' . $mensaje);
    exit;
}

/**
 * Valida que el archivo subido sea una imagen permitida
 */
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

// Obtenemos la acción del formulario
$accion = $_POST['accion'] ?? '';

// 1. LÓGICA PARA CREAR PRODUCTO (CON TRANSACCIÓN E HISTORIAL)
if ($accion === 'crear') {
    
    // Validación de campos obligatorios
    if (trim($_POST['nombre_producto'] ?? '') === '' || (int)$_POST['id_categoria'] === 0) {
        header('Location: editardetalle.php?error=1'); 
        exit;
    }

    // Interceptor de seguridad para la imagen
    if (isset($_FILES['ImagenesProducto']) && !empty($_FILES['ImagenesProducto']['name'])) {
        if (!validar_imagen_segura($_FILES['ImagenesProducto'])) {
            header('Location: editardetalle.php?error=formato');
            exit;
        }
    }

    // Procesar la subida física de la imagen
    $img = subir_imagen_producto($_FILES['ImagenesProducto'] ?? []);

    // Armamos el paquete de 11 datos exactos para el Procedimiento Almacenado
    $datos = [
        'id_categoria'    => (int)$_POST['id_categoria'],
        'nombre_producto' => trim($_POST['nombre_producto']),
        'unidad_medida'   => trim($_POST['unidad_medida'] ?? ''),
        'calibre'         => trim($_POST['calibre'] ?? ''),
        'metros'          => (float)($_POST['metros'] ?? 0),
        'kg'              => (float)($_POST['kg'] ?? 0),
        'color'           => trim($_POST['color'] ?? ''),
        'ced'             => trim($_POST['ced'] ?? ''),
        'ton'             => (float)($_POST['ton'] ?? 0),
        'cm'              => (float)($_POST['cm'] ?? 0),
        'ImagenesProducto'=> $img ?? ''
    ];

    // Ejecutamos la función que llama a la TRANSACCIÓN en MySQL
    $ok = crear_producto($conn, $datos);
    
    volver_listado($ok ? 'guardado' : 'error');

// LÓGICA PARA ACTUALIZAR
} elseif ($accion === 'actualizar') {
    $id = (int)($_POST['id_producto'] ?? 0);
    $imagen_actual = trim($_POST['imagen_actual'] ?? '');
    
    if (!$id || trim($_POST['nombre_producto']) === '' || (int)$_POST['id_categoria'] === 0) {
        header('Location: editardetalle.php?id='.$id.'&error=1'); 
        exit;
    }

    // Procesar imagen nueva si existe
    $imgNueva = subir_imagen_producto($_FILES['ImagenesProducto'] ?? []);
    $conImagenNueva = false;
    
    $datos_update = [
        'id_categoria'    => (int)$_POST['id_categoria'],
        'nombre_producto' => trim($_POST['nombre_producto']),
        'unidad_medida'   => trim($_POST['unidad_medida'] ?? ''),
        'calibre'         => trim($_POST['calibre'] ?? ''),
        'metros'          => (float)$_POST['metros'],
        'kg'              => (float)$_POST['kg'],
        'color'           => trim($_POST['color'] ?? ''),
        'ced'             => trim($_POST['ced'] ?? ''),
        'ton'             => (float)$_POST['ton'],
        'cm'              => (float)$_POST['cm']
    ];

    if ($imgNueva) {
        $datos_update['ImagenesProducto'] = $imgNueva;
        $conImagenNueva = true;
    } else {
        $datos_update['ImagenesProducto'] = $imagen_actual;
    }
    
    $ok = actualizar_producto($conn, $id, $datos_update, $conImagenNueva);
    volver_listado($ok ? 'actualizado' : 'error');

// LÓGICA PARA ELIMINAR
} elseif ($accion === 'eliminar') {
    $id = (int)($_POST['id_producto'] ?? 0);
    if (!$id) {
        header('Location: listadodetalle.php?error=1'); 
        exit;
    }
    $ok = eliminar_producto($conn, $id);
    volver_listado($ok ? 'eliminado' : 'error');

} else {
    header('Location: listadodetalle.php');
    exit;
}