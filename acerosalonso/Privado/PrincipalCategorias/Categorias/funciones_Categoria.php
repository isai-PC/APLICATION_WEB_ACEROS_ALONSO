<?php
// --- Guardar en: Privado/PrincipalCategorias/funciones_Categoria.php ---
require_once __DIR__ . '/../../../conexion.php';

// 1. FUNCIÓN PARA LLENAR EL DROPDOWN (SELECT)
function listar_todas_las_categorias_simple(mysqli $conn): array {
    $sql = "SELECT id_categoria, nombre_categoria FROM categoria ORDER BY nombre_categoria ASC";
    $res = $conn->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

// 2. FUNCIÓN PRINCIPAL DE LA TABLA (CON FILTRO)
function listar_categorias_filtradas(mysqli $conn, $id_filtro = 0): array {
    // Consulta base
    $sql = "SELECT * FROM categoria";
    
    // Si el usuario seleccionó una opción en el select (ID > 0), filtramos
    if ($id_filtro > 0) {
        $sql .= " WHERE id_categoria = " . (int)$id_filtro;
    }

    $sql .= " ORDER BY id_categoria DESC";
    
    $res = $conn->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}


function listar_categorias_paginated(mysqli $conn, int $id_filtro = 0, int $inicio = 0, int $por_pagina = 10): array {
    $where = '';
    if ($id_filtro > 0) {
        $where = ' WHERE id_categoria = ' . (int)$id_filtro;
    }

    // Obtener total
    $sqlCount = "SELECT COUNT(*) AS cnt FROM categoria" . $where;
    $resCount = $conn->query($sqlCount);
    $total = 0;
    if ($resCount) {
        $row = $resCount->fetch_assoc();
        $total = isset($row['cnt']) ? (int)$row['cnt'] : 0;
        if (method_exists($resCount, 'free')) $resCount->free();
    }

    // Obtener filas paginadas
    $sql = "SELECT * FROM categoria" . $where . " ORDER BY id_categoria DESC LIMIT " . (int)$inicio . "," . (int)$por_pagina;
    $res = $conn->query($sql);
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    if ($res && method_exists($res, 'free')) $res->free();

    return ['rows' => $rows, 'total' => $total];
}

// ...S 
function obtener_categoria(mysqli $conn, int $id): ?array {
    $stmt = $conn->prepare("SELECT * FROM categoria WHERE id_categoria = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $cat = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $cat ?: null;
}

function subir_imagen(array $file): ?string {
    if (empty($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    // Extensiones y tipos MIME permitidos
    $permitidasExt = ['jpg','jpeg','png','webp','gif'];
    $permitidasMime = ['image/jpeg','image/png','image/webp','image/gif'];

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $permitidasExt)) return null;

    // Tamaño máximo: 3 MB
    $maxSize = 3 * 1024 * 1024;
    if (isset($file['size']) && $file['size'] > $maxSize) return null;

    // Comprobar que el archivo es una imagen válida y obtener su MIME
    $info = @getimagesize($file['tmp_name']);
    if ($info === false) return null;
    $mime = $info['mime'] ?? '';
    if (!in_array($mime, $permitidasMime)) return null;

    // Asegurarse de que sea una subida válida
    if (!is_uploaded_file($file['tmp_name'])) return null;

    $nombre = 'cat_' . uniqid() . '.' . $ext;
    // Guardar en la carpeta pública `acerosalonso/imagenes`
    $dirDestino = __DIR__ . '/../../../imagenes/';
    if (!is_dir($dirDestino)) {
        @mkdir($dirDestino, 0755, true);
    }
    $destino = $dirDestino . $nombre;
    if (!move_uploaded_file($file['tmp_name'], $destino)) return null;
    return $nombre;
}
    
function crear_categoria(mysqli $conn, string $nombre, ?string $texto, ?string $img): bool {
    $stmt = $conn->prepare("INSERT INTO categoria (nombre_categoria, texto_secundario, imagen_categoria) VALUES (?,?,?)");
    $stmt->bind_param("sss", $nombre, $texto, $img);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function actualizar_categoria(mysqli $conn, int $id, string $nombre, ?string $texto, ?string $imgNuevo): bool {
    if ($imgNuevo) {
        $stmt = $conn->prepare("UPDATE categoria SET nombre_categoria=?, texto_secundario=?, imagen_categoria=? WHERE id_categoria=?");
        $stmt->bind_param("sssi", $nombre, $texto, $imgNuevo, $id);
    } else {
        $stmt = $conn->prepare("UPDATE categoria SET nombre_categoria=?, texto_secundario=? WHERE id_categoria=?");
        $stmt->bind_param("ssi", $nombre, $texto, $id);
    }
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function eliminar_categoria(mysqli $conn, int $id): bool {
    $stmt = $conn->prepare("DELETE FROM categoria WHERE id_categoria=?");
    $stmt->bind_param("i", $id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}
?>