<?php
require_once __DIR__ . '/../../conexion.php';

function subir_imagen_producto(array $file): ?string
{
    if (empty($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) return null;

    // Solo permitimos JPG, PNG, WEBP
    $permitidasExt = ['jpg', 'jpeg', 'png', 'webp'];
    $permitidasMime = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5 MB

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $permitidasExt)) return null;

    if (isset($file['size']) && $file['size'] > $maxSize) return null;

    $info = @getimagesize($file['tmp_name']);
    if ($info === false) return null;

    $mime = $info['mime'] ?? '';
    if (!in_array($mime, $permitidasMime)) return null;

    if (!is_uploaded_file($file['tmp_name'])) return null;

    $nombre = 'prod_' . uniqid() . '.' . $ext;
    $destinoDir = __DIR__ . '/../../../imagenes/';

    if (!is_dir($destinoDir)) @mkdir($destinoDir, 0755, true);

    $destino = $destinoDir . $nombre;

    if (!move_uploaded_file($file['tmp_name'], $destino)) return null;

    return $nombre;
}

function listar_categorias_dropdown(mysqli $conn): array
{
    $sql = "SELECT id_categoria, nombre_categoria FROM categoria ORDER BY nombre_categoria ASC";
    $res = $conn->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function contar_productos(mysqli $conn, $id_categoria = 0): int
{
    $sql = "SELECT COUNT(*) as total FROM productos";
    if ($id_categoria > 0) $sql .= " WHERE id_categoria = " . (int)$id_categoria;
    $res = $conn->query($sql);
    $fila = $res->fetch_assoc();
    return $fila ? (int)$fila['total'] : 0;
}

function listar_productos(mysqli $conn, $id_categoria = 0, $limit = 10, $offset = 0): array
{
    $sql = "SELECT p.id_producto, p.nombre_producto, p.ImagenesProducto, c.nombre_categoria
            FROM productos p
            LEFT JOIN categoria c ON p.id_categoria = c.id_categoria";

    if ($id_categoria > 0) $sql .= " WHERE p.id_categoria = " . (int)$id_categoria;

    $sql .= " ORDER BY p.id_producto DESC LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $res = $stmt->get_result();

    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function obtener_producto(mysqli $conn, int $id): ?array
{
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $prod = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $prod ?: null;
}

function crear_producto(mysqli $conn, array $datos): bool
{
    mysqli_begin_transaction($conn);
    try {
        // Normalizar valores numéricos: null → 0
        $metros = $datos['metros'] ?? 0;
        $kg = $datos['kg'] ?? 0;
        $ton = $datos['ton'] ?? 0;
        $cm = $datos['cm'] ?? 0;
        
        $sql1 = "INSERT INTO productos (id_categoria, nombre_producto, unidad_medida, calibre, metros, kg, color, ced, ton, cm, ImagenesProducto)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param(
            "isssddssddds",
            $datos['id_categoria'],
            $datos['nombre_producto'],
            $datos['unidad_medida'],
            $datos['calibre'],
            $metros,
            $kg,
            $datos['color'],
            $datos['ced'],
            $ton,
            $cm,
            $datos['ImagenesProducto']
        );
        $stmt1->execute();

        $idProducto = $stmt1->insert_id;
        //Insercion de datos a la tabla de historial
        $sql2 = "INSERT INTO historial_productos (id_producto_ref, id_categoria, nombre_producto, unidad_medida, calibre, metros, kg, color, ced, ton, cm, ImagenesProducto)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param(
            "iissddssddds",
            $idProducto,
            $datos['id_categoria'],
            $datos['nombre_producto'],
            $datos['unidad_medida'],
            $datos['calibre'],
            $metros,
            $kg,
            $datos['color'],
            $datos['ced'],
            $ton,
            $cm,
            $datos['ImagenesProducto']
        );
        $stmt2->execute();

        $stmt1->close();
        $stmt2->close();

        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return false;
    }
}

function actualizar_producto(mysqli $conn, int $id, array $datos, bool $conImagen): bool
{
    mysqli_begin_transaction($conn);
    try {
        // Normalizar valores numéricos: null → 0
        $metros = $datos['metros'] ?? 0;
        $kg = $datos['kg'] ?? 0;
        $ton = $datos['ton'] ?? 0;
        $cm = $datos['cm'] ?? 0;
        $imgProducto = $datos['ImagenesProducto'] ?? null;
        
        if ($conImagen) {
            $sql1 = "UPDATE productos SET 
                    id_categoria=?, nombre_producto=?, unidad_medida=?, calibre=?, 
                    metros=?, kg=?, color=?, ced=?, ton=?, cm=?, ImagenesProducto=?
                    WHERE id_producto=?";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param(
                "isssddssddsi",
                $datos['id_categoria'],
                $datos['nombre_producto'],
                $datos['unidad_medida'],
                $datos['calibre'],
                $metros,
                $kg,
                $datos['color'],
                $datos['ced'],
                $ton,
                $cm,
                $imgProducto,
                $id
            );
        } else {
            $sql1 = "UPDATE productos SET 
                    id_categoria=?, nombre_producto=?, unidad_medida=?, calibre=?, 
                    metros=?, kg=?, color=?, ced=?, ton=?, cm=?
                    WHERE id_producto=?";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param(
                "isssddssddi",
                $datos['id_categoria'],
                $datos['nombre_producto'],
                $datos['unidad_medida'],
                $datos['calibre'],
                $metros,
                $kg,
                $datos['color'],
                $datos['ced'],
                $ton,
                $cm,
                $id
            );
        }
        $stmt1->execute();
        $stmt1->close();


        $sql2 = "INSERT INTO historial_productos
                (id_producto_ref, id_categoria, nombre_producto, unidad_medida, calibre,
                 metros, kg, color, ced, ton, cm, ImagenesProducto)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);

        $stmt2->bind_param(
            "iissddssddds",
            $id,
            $datos['id_categoria'],
            $datos['nombre_producto'],
            $datos['unidad_medida'],
            $datos['calibre'],
            $metros,
            $kg,
            $datos['color'],
            $datos['ced'],
            $ton,
            $cm,
            $imgProducto
        );

        $stmt2->execute();
        $stmt2->close();

        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return false;
    }
}


function eliminar_producto(mysqli $conn, int $id): bool
{
    $stmt = $conn->prepare("DELETE FROM productos WHERE id_producto=?");
    $stmt->bind_param("i", $id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}
