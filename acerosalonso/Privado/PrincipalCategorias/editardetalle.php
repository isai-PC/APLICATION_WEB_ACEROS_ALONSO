<?php
require_once __DIR__ . '/../../conexion.php';
require_once __DIR__ . '/funcionesdetalle.php';

// Inicializar sesión y usuario
session_start();
$usuarioHeader = '';
if (isset($_SESSION['id_empleado'])) {
    $nombre = $_SESSION['nombre'] ?? 'Usuario';
    $usuarioHeader = "Usuario: " . htmlspecialchars($nombre);
}

// Helpers
function h($v)
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}
function param_get_int(string $key): int
{
    $v = filter_input(INPUT_GET, $key, FILTER_VALIDATE_INT);
    return is_int($v) ? $v : 0;
}

//  OBTENER EL ID DEL PRODUCTO
$id = param_get_int('id');

// Si hay ID, cargamos el producto
$producto = [
    'id_producto' => 0,
    'id_categoria' => '',
    'nombre_producto' => '',
    'unidad_medida' => '',
    'calibre' => '',
    'metros' => '',
    'kg' => '',
    'color' => '',
    'ced' => '',
    'ton' => '',
    'cm' => '',
    'precio' => '',
    'ImagenesProducto' => ''
];

if ($id > 0) {
    $encontrado = obtener_producto($conn, $id);
    if ($encontrado) {
        $producto = $encontrado;
    }
}

// OBTENER LA LISTA DE CATEGORÍAS
$categorias_lista = listar_categorias_dropdown($conn);

// LISTAS PARA LOS SELECTS
$lista_colores = ['N/A', 'Rojo', 'Negro', 'Verde', 'Azul', 'Blanco', 'Naranja', 'Amarillo', 'Gris', 'Galvanizado', 'Pintro', 'Zintro'];
$lista_unidades = ['Pieza', 'Kg', 'Metro', 'Tramo', 'Rollo', 'Lámina', 'Tonelada', 'Caja', 'Paquete', 'Juego'];
$lista_calibres = ['N/A', '10', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30', '32', '40', '80'];

// Ruta publica de imágenes
$IMG_DIR_PUBLIC = '../../imagenes/';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Admin | Editar Producto</title>
    <link rel="icon" type="image/png" href="../../ACASALogoAcerosA.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../StylesGenerales.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="styledetalle.css?v=<?php echo time(); ?>">
</head>

<body>
    <header>
        <section class="logo">
            <img src="../../ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
            <h2>Aceros Alonso</h2>
        </section>
        <nav>
            <ul>
                <?php echo htmlspecialchars($usuarioHeader) ?>
                <li><a href="../../Login/CerrarSesion.php">Cerrar sesion</a></li>
            </ul>
        </nav>
    </header>

    <aside>
        <nav>
            <ul>
                <li><a href="../../../Consultas/consultas.php">Consultas</a></li>
                <li><a href="../../Privado/PrincipalCategorias/listadodetalle.php">Productos</a></li>
                <li><a href="../../Privado/PrincipalCategorias/Categorias/listado_Categoria.php">Categorias</a></li>
                <li class="menu">
                    <a href="#">Mision Vision</a>
                    <ul class="ContenidoMenu">
                        <li><a href="../MisionVision/editar_mision.php">Mision</a></li>
                        <li><a href="../MisionVision/editar_vision.php">Vision</a></li>
                        <li><a href="../MisionVision/editar_info.php">Por que elegirnos</a></li>
                    </ul>
                </li>
                <li><a href="../../Registro/empleados.php">Usuarios</a></li>
                <li><a href="../../Ubicacion/listar_ubicaciones.php">Ubicaciones</a></li>
                <li><a href="../../Preguntasfrecuentes/index.php">Preguntas Frecuentes</a></li>
                <li><a href="../../contacto/admin_contacto.php">Contacto</a></li>
                <li><a href="../..//terminos/admin_terminos.php">Terminos y Condiciones</a></li>
                <li><a href="../../paginaPrincipal.php">Inicio</a></li>
            </ul>
        </nav>
    </aside>

    <main class="admin-detalle">

        <h1>Administrar Productos del Catálogo</h1>
        <?php if (isset($_GET['error'])): ?>

            <?php if ($_GET['error'] == 'formato'): ?>
                <div class="alerta-form alerta-error">
                    ¡Formato de imagen no válido! Solo se aceptan JPG, PNG o WEBP.
                </div>

            <?php elseif ($_GET['error'] == 1): ?>
                <div class="alerta-form alerta-error">
                    Error: Faltan datos obligatorios (Nombre o Categoría).
                </div>
            <?php endif; ?>

        <?php endif; ?>

        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'exito'): ?>
            <div class="alerta-form alerta-exito">
                ¡Operación realizada correctamente!
            </div>
        <?php endif; ?>

        <form action="guardardetalle.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="id_producto" value="<?php echo (int)$producto['id_producto']; ?>">
            <input type="hidden" name="accion" value="<?php echo ($producto['id_producto'] > 0) ? 'actualizar' : 'crear'; ?>">

            <h2>Información Principal</h2>
            <section class="container2">

                <div class="row">

                    <label>
                        Nombre del Producto *
                        <input type="text" name="nombre_producto" value="<?php echo h($producto['nombre_producto']); ?>" required>
                    </label>

                    <label>
                        Categoría *
                        <select name="id_categoria" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($categorias_lista as $cat): ?>
                                <option
                                    value="<?php echo (int)$cat['id_categoria']; ?>"
                                    <?php if ($cat['id_categoria'] == $producto['id_categoria']) echo 'selected'; ?>>
                                    <?php echo h($cat['nombre_categoria']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <label>Imagen (jpg, png, webp) — si no eliges, se conserva la actual</label>

                <input type="file" id="inputImagen" name="ImagenesProducto" accept=".jpg, .jpeg, .png, .webp">

                <input type="hidden" name="imagen_actual" value="<?php echo h($producto['ImagenesProducto'] ?? ''); ?>">

                <?php if (!empty($producto['ImagenesProducto'])): ?>
                    <p>Actual: <img class="thumb" src="<?php echo $IMG_DIR_PUBLIC . h($producto['ImagenesProducto']); ?>" alt="" style="max-height:100px; display:block; margin-top:5px;"></p>
                <?php endif; ?>

                <h2>Especificaciones Técnicas (Vista Detalle)</h2>

                <div class="row">

                    <label>
                        Unidad de Medida
                        <select name="unidad_medida">
                            <option value="">-- Seleccione --</option>
                            <?php
                            $unidadActual = h($producto['unidad_medida']);
                            foreach ($lista_unidades as $opcion):
                            ?>
                                <option value="<?php echo $opcion; ?>" <?php if ($unidadActual == $opcion) echo 'selected'; ?>>
                                    <?php echo $opcion; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>
                        Calibre
                        <select name="calibre">
                            <option value="">-- N/A --</option>
                            <?php
                            $calibreActual = h($producto['calibre']);
                            foreach ($lista_calibres as $opcion):
                            ?>
                                <option value="<?php echo $opcion; ?>" <?php if ($calibreActual == $opcion) echo 'selected'; ?>>
                                    <?php echo $opcion; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>
                        Metros (ej: 12.50)
                        <input type="number" step="0.01" min="0" name="metros" value="<?php echo h($producto['metros']); ?>">
                    </label>

                    <label>
                        Kg (ej: 158.75)
                        <input type="number" step="0.01" min="0" name="kg" value="<?php echo h($producto['kg']); ?>">
                    </label>

                    <label>
                        Color
                        <select name="color">
                            <option value="">-- N/A --</option>
                            <?php
                            $colorActual = h($producto['color']);
                            foreach ($lista_colores as $opcion):
                            ?>
                                <option value="<?php echo $opcion; ?>" <?php if ($colorActual == $opcion) echo 'selected'; ?>>
                                    <?php echo $opcion; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>
                        Ced
                        <input type="text" name="ced" value="<?php echo h($producto['ced']); ?>">
                    </label>

                    <label>
                        Ton (ej: 1.5)
                        <input type="number" step="0.01" min="0" name="ton" value="<?php echo h($producto['ton']); ?>">
                    </label>

                    <label>
                        Cm (ej: 38.00)
                        <input type="number" step="0.01" min="0" name="cm" value="<?php echo h($producto['cm']); ?>">
                    </label>

                    <label>
                        Precio (ej: 100.00)
                        <input type="number" step="0.01" min="0" name="precio" value="<?php echo h($producto['precio']); ?>">
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="listadodetalle.php" class="btn btn-neutral">Cancelar</a>
                </div>
            </section>

        </form>

    </main>

    <footer>
        <div class="footer-sections">
            <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
        </div>
    </footer>

    <script>
        document.getElementById('inputImagen').addEventListener('change', function() {
            var archivo = this.files[0];
            if (archivo) {
                var tipo = archivo.type;
                var validos = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];

                if (!validos.includes(tipo)) {
                    // Alerta del navegador
                    alert('FORMATO NO VÁLIDO\nSelecciona solo imágenes (JPG, PNG, WEBP).');
                    this.value = ''; // Borra la selección
                }
            }
        });
    </script>

</body>

</html>