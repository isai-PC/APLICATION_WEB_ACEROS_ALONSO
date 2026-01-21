<?php
require_once __DIR__ . '/../../conexion.php';
require_once __DIR__ . '/funcionesdetalle.php';

// Helpers
function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
function param_get_int(string $key): int {
$v = filter_input(INPUT_GET, $key, FILTER_VALIDATE_INT);
return is_int($v) ? $v : 0;
}

// Cargar datos
$id  = param_get_int('id');
$ok  = (string)($_GET['ok'] ?? '');
$err = (string)($_GET['err'] ?? '');

$prod = $id ? obtener_producto($conn, $id) : null;
$is_edit = (bool)$prod;

$lista            = listar_productos($conn);          // para la tabla
$categorias_lista = listar_categorias_dropdown($conn);  // para el select

// Rutas
$IMG_DIR_PUBLIC = '../../imagenes/'; // carpeta pública de imágenes
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Admin | Productos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../StylesGenerales.css?v=<?php echo time(); ?>">
</head>

<body>
    <header>
        <section class="logo">
            <img src="../../ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
            <h2>Aceros Alonso</h2>
        </section>
        <nav>
            <ul>
                <li><a href="../../paginaPrincipal.php">Inicio</a></li><li><a href="../../Consultas/consultas.php">Consultas</a></li>
            </nav>
    </header>
    <main class="admin-detalle">
        <h1>Administrar Productos del Catálogo</h1>

        <?php if ($ok !== ''): ?>
        <div class="notice">Guardado correctamente.</div>
        <?php endif; ?>
        <?php if ($err !== ''): ?>
        <div class="notice notice--err">Hubo un error al guardar. (Faltan datos obligatorios)</div>
        <?php endif; ?>

        <form action="guardardetalle.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_producto" value="<?php echo (int)($prod['id_producto'] ?? 0); ?>">

            <h2>Información Principal</h2>
            <div class="row">
                <div>
                    <label for="nombre_producto">Nombre del Producto *</label>
                    <input id="nombre_producto" name="nombre_producto" type="text" required
                        value="<?php echo h($prod['nombre_producto'] ?? ''); ?>">
                </div>
                <div>
                    <label for="id_categoria">Categoría *</label>
                    <select id="id_categoria" name="id_categoria" required>
                        <option value="">-- Seleccione --</option>
                        <?php foreach ($categorias_lista as $cat): 
                $sel = ($is_edit && (int)$prod['id_categoria'] === (int)$cat['id_categoria']) ? 'selected' : ''; ?>
                        <option value="<?php echo (int)$cat['id_categoria']; ?>" <?php echo $sel; ?>>
                            <?php echo h($cat['nombre_categoria']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <label for="ImagenesProducto">Imagen (jpg, png, webp) — si no eliges, se conserva la actual</label>
            <input id="ImagenesProducto" name="ImagenesProducto" type="file" accept=".jpg,.jpeg,.png,.webp">
            <input type="hidden" name="imagen_actual" value="<?php echo h($prod['ImagenesProducto'] ?? ''); ?>">
            <?php if (!empty($prod['ImagenesProducto'])): ?>
            <p>Actual:
                <img class="thumb" src="<?php echo $IMG_DIR_PUBLIC . h($prod['ImagenesProducto']); ?>"
                    alt="Imagen actual">
            </p>
            <?php endif; ?>

            <h2>Especificaciones Técnicas (Vista Detalle)</h2>

            <div class="row">
                <div>
                    <label for="unidad_medida">Unidad de Medida</label>
                    <input id="unidad_medida" name="unidad_medida" type="text"
                        value="<?php echo h($prod['unidad_medida'] ?? ''); ?>">
                </div>
                <div>
                    <label for="calibre">Calibre</label>
                    <input id="calibre" name="calibre" type="text" value="<?php echo h($prod['calibre'] ?? ''); ?>">
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="metros">Metros (ej: 12.50)</label>
                    <input id="metros" name="metros" type="number" step="0.01"
                        value="<?php echo h($prod['metros'] ?? ''); ?>">
                </div>
                <div>
                    <label for="kg">Kg (ej: 150.75)</label>
                    <input id="kg" name="kg" type="number" step="0.01" value="<?php echo h($prod['kg'] ?? ''); ?>">
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="color">Color</label>
                    <input id="color" name="color" type="text" value="<?php echo h($prod['color'] ?? ''); ?>">
                </div>
                <div>
                    <label for="ced">Ced</label>
                    <input id="ced" name="ced" type="text" value="<?php echo h($prod['ced'] ?? ''); ?>">
                </div>
            </div>

            <div class="row">
                <div>
                    <label for="ton">Ton (ej: 1.5)</label>
                    <input id="ton" name="ton" type="number" step="0.01" value="<?php echo h($prod['ton'] ?? ''); ?>">
                </div>
                <div>
                    <label for="cm">Cm (ej: 30.00)</label>
                    <input id="cm" name="cm" type="number" step="0.01" value="<?php echo h($prod['cm'] ?? ''); ?>">
                </div>
            </div>

            <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap">
                <?php if ($is_edit): ?>
                <button class="btn btn-primary" type="submit" name="accion" value="actualizar">Actualizar</button>
                <button class="btn btn-danger" type="submit" name="accion" value="eliminar"
                    onclick="return confirm('¿Eliminar este producto?');">Eliminar</button>
                <a class="btn btn-neutral" href="editardetalle.php">Nuevo Producto</a>
                <?php else: ?>
                <button class="btn btn-primary" type="submit" name="accion" value="crear">Guardar Producto</button>
                <?php endif; ?>
            </div>
        </form>
        
    </main> <footer>
    <div class="footer-sections">
    
    <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
    </div>
</footer>

</body>
</html>