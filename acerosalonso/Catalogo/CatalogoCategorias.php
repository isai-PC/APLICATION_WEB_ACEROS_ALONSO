<?php

session_start(); // Iniciamos sesión para el menú de usuario
include '../conexion.php';

// ID de Categoría
$cat_id = 0;
if (isset($_GET['id_categoria'])) $cat_id = (int) $_GET['id_categoria'];
elseif (isset($_GET['cat_id']))   $cat_id = (int) $_GET['cat_id'];

// Nombre de Categoría
$nombreCat = 'Todos los productos';
if ($cat_id > 0) {
    $stmt = $conn->prepare("SELECT nombre_categoria FROM categoria WHERE id_categoria = ?");
    $stmt->bind_param("i", $cat_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res) $nombreCat = $res['nombre_categoria'];
    $stmt->close();
}

// Consulta de Productos
$cols = "id_producto, id_categoria, nombre_producto, unidad_medida, calibre, metros, kg, color, ced, ton, cm, ImagenesProducto";

if ($cat_id > 0) {
    $stmtP = $conn->prepare("SELECT $cols FROM productos WHERE id_categoria = ? ORDER BY id_producto DESC");
    $stmtP->bind_param("i", $cat_id);
    $stmtP->execute();
    $productos = $stmtP->get_result();
} else {
    $productos = $conn->query("SELECT $cols FROM productos ORDER BY id_producto DESC");
}

// Consulta para el Menú para que salga igual al principal
$sqlMenu = "SELECT id_categoria, nombre_categoria FROM categoria ORDER BY nombre_categoria ASC";
$resMenu = $conn->query($sqlMenu);

$usuarioHeader = '';
if (isset($_SESSION['id_empleado'])) {
    $nombre = $_SESSION['nombre'] ?? 'Usuario'; // Lo que se guarda en el login
    $usuarioHeader = "Usuario: $nombre";
}

//<a href="#">Productos en <?php echo htmlspecialchars($nombreCat);</a>
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo | <?php echo htmlspecialchars($nombreCat); ?></title>


    <!-- <link rel="stylesheet" href="StyleCatalogo.css?v=<?php echo time(); ?>"> -->
    <link rel="stylesheet" href="../StylesGeneralesPublic.css?v=<?php echo time(); ?>">


    <link rel="icon" href="../ACASALogoAcerosA.png" type="image/png" sizes="16px">
</head>

<body>

    <header>
        <section class="logo">
            <img src="../ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
            <h2>Aceros Alonso</h2>
        </section>

        <nav>
            <ul>
                <li><a href="../paginaPrincipal.php">Inicio</a></li>

                <li class="menu">
                    <a href="#">Categorías</a>
                    <ul class="ContenidoMenu">
                        <?php
                        if ($resMenu && $resMenu->num_rows > 0) {
                            $resMenu->data_seek(0);
                            while ($cat = $resMenu->fetch_assoc()) {
                                $id = $cat['id_categoria'];
                                $nombre = htmlspecialchars($cat['nombre_categoria']);
                                echo "<li><a href='CatalogoCategorias.php?id_categoria=$id'>$nombre</a></li>";
                            }
                        }
                        ?>
                    </ul>
                </li>
                <?php if ($cat_id > 0): ?>
                    <li class="menu">
                        <a href="#">Productos</a>
                        <ul class="ContenidoMenu">
                            <?php
                            // Consultamos solo ID y Nombre de los productos de ESTA categoría
                            $sqlProdsMenu = "SELECT id_producto, nombre_producto FROM productos WHERE id_categoria = ? ORDER BY nombre_producto ASC";
                            $stmtMenuP = $conn->prepare($sqlProdsMenu);
                            $stmtMenuP->bind_param("i", $cat_id);
                            $stmtMenuP->execute();
                            $resMenuP = $stmtMenuP->get_result();
                            if ($resMenuP && $resMenuP->num_rows > 0) {
                                while ($prod = $resMenuP->fetch_assoc()) {

                                    $pid = $prod['id_producto'];
                                    $pnom = htmlspecialchars($prod['nombre_producto']);
                                    // Este link lleva directo a la VISTA DETALLE del producto
                                    echo "<li><a href='../vistadetalle/vistadetalle.php?id=$pid'>$pnom</a></li>";
                                }
                            } else {
                                echo "<li><a href='#'>Sin productos</a></li>";
                            }
                            $stmtMenuP->close();
                            ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <li class="menu">
                    <a href="#">Acerca de nosotros</a>
                    <ul class="ContenidoMenu">
                        <li><a href="../MisionVision/misionyvision.php">Nosotros</a></li>
                   <!--      <li><a href="../paginaPrincipal.php#contacto">Contacto</a></li> -->
                        <!-- <li><a href="../Preguntasfrecuentes/preguntas.php">Preguntas frecuentes</a></li> -->
                    </ul>
                </li>

                <?php if (!isset($_SESSION['id_empleado'])): ?>
                    <li><a href="../Login/Login.php" class="btnLogin">Login</a></li>
                <?php else: ?>
                    <?php if ($_SESSION['tipo_usuario'] == 2): ?>
                        <li><a href="../Consultas/consultas.php">Administración</a></li>
                    <?php elseif ($_SESSION['tipo_usuario'] == 1): ?>
                        <li><a href="../Consultas/consultas_empleado.php?id=<?= $_SESSION['id_empleado'] ?>">Empleado</a></li>
                    <?php endif; ?>
                    <li><a href="../Login/CerrarSesion.php">Cerrar Sesión</a></li>
                <?php endif; ?>
                    <?php if (!empty($usuarioHeader)): ?>
                    <li><?= htmlspecialchars($usuarioHeader) ?></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
 <!--===========================COPIAR ESTO Y PEGAR EN LAS DEMAS PAGINAS================================== -->
    <script src="../Accesibilidad/accesi.js?v=<?php echo time(); ?>"></script>
    <!-- Botón de accesibilidad -->
    <div id="btnAccesibilidad" onclick="event.stopPropagation(); toggleMenuAccesibilidad()">
        <img src="../Accesibilidad/accesibilidad.png" style="width: 100%; height:100%; object-fit:cover;">
    </div>
    <!-- Iframe del menú -->
    <iframe
        id="menuAccesibilidad"
        src="../Accesibilidad/MenuAccesibilidad.html"
        class="accesibilidad-frame">
    </iframe>
    <!-- ===================================================================================================== -->


    <main id="catalogo">
        <h1><?php echo htmlspecialchars($nombreCat); ?></h1>

        <?php if ($productos && $productos->num_rows > 0): ?>
            <section class="grid-catalogo">
                <?php while ($row = $productos->fetch_assoc()): ?>
                    <?php
                    $id_prod = (int)$row['id_producto'];
                    $img = !empty($row['ImagenesProducto']) ? $row['ImagenesProducto'] : 'placeholder.jpg';
                    $titulo = $row['nombre_producto'];

                    // Resumen visible
                    $resumen = [];
                    if (!empty($row['unidad_medida'])) $resumen[] = $row['unidad_medida'];
                    if (!empty($row['calibre']))       $resumen[] = "Cal: " . $row['calibre'];
                    $textoResumen = implode(' · ', $resumen);

                    // Detalles para el Overlay
                    $detalles = [
                        'Metros' => $row['metros'],
                        'Kilos'  => $row['kg'],
                        'Color'  => $row['color'],
                        'Cédula' => $row['ced'],
                        'Ton'    => $row['ton']
                    ];
                    ?>

                    <a class="card-link" href="../vistadetalle/vistadetalle.php?id=<?= $id_prod ?>">



                        <article class="card">


                            <figure>
                                <img src="../imagenes/<?= htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($titulo); ?>">
                            </figure>

                            <div class="card-content">
                                <h3><?= htmlspecialchars($titulo); ?></h3>
                                <p><?= htmlspecialchars($textoResumen); ?></p>
                            </div>

                            <div class="overlay">
                                <h4>Detalles Técnicos</h4>
                                <ul>
                                    <?php foreach ($detalles as $lbl => $val): ?>
                                        <?php if (!empty($val) && $val != 0): ?>
                                            <li>
                                                <span class="lbl"><?= $lbl ?>:</span>
                                                <span class="val"><?= htmlspecialchars($val); ?></span>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                                <span class="btn-ver">Ver Detalle</span>
                            </div>
                        </article>
                    </a>
                <?php endwhile; ?>
            </section>
        <?php else: ?>
            <div class="no-products">
                <p>No se encontraron productos en esta categoría.</p>
                <a href="../paginaPrincipal.php" class="btn-volver">Volver al inicio</a>
            </div>
        <?php endif; ?>
    </main>

 <footer>
        <div class="footer-sections" id="contacto">
            <div class="barra">
                <?php
                $sql = "SELECT pregunta, respuesta FROM preguntas_frecuentes LIMIT 1";
                $answer = $conn->query($sql);
                if ($answer && $answer->num_rows > 0):
                    while ($row = $answer->fetch_assoc()):
                        $pregunta = $row['pregunta'] ?? '';
                        $respuesta = $row['respuesta'] ?? '';
                ?>
                        <h3><strong><?php echo htmlspecialchars($pregunta) ?></strong></h3>
                        <p><?php echo htmlspecialchars($respuesta) ?></p>
                <?php
                    endwhile;
                    // Liberar resultados cuando ya no se necesiten
                    if (method_exists($answer, 'free')): $answer->free();
                    endif;
                else:
                    echo '<p style="padding:1rem;">Aún no hay preguntas frecuentes para mostrar.</p>';
                endif;
                ?>
            </div>
            <div class="barra">

                <?php
                // Obtener URLs de redes sociales 
                $sql = "SELECT red_social_1, red_social_2, red_social_3 FROM contacto_info LIMIT 1";
                $result = $conn->query($sql);
                $red1 = $red2 = $red3 = '#';

                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $red1 = $row['red_social_1'] ?? '#';
                    $red2 = $row['red_social_2'] ?? '#';
                    $red3 = $row['red_social_3'] ?? '#';
                    if (method_exists($result, 'free')) { // Liberar resultados cuando ya no se necesiten
                        $result->free();
                    }
                }
                ?>

                <h3>Nuestras Redes Sociales</h3>
                <div class="redes">
                    <a href="<?php echo htmlspecialchars($red1); ?>" target="_blank" rel="noopener noreferrer"><img
                            src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Facebook_Logo_%282019%29.png/500px-Facebook_Logo_%282019%29.png"
                            alt="Facebook"></a>
                    <a href="<?php echo htmlspecialchars($red2); ?>" target="_blank" rel="noopener noreferrer"><img
                            src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Instagram_icon.png/1200px-Instagram_icon.png"
                            alt="Instagram"></a>
                    <a href="<?php echo htmlspecialchars($red3); ?>" target="_blank" rel="noopener noreferrer"><img
                            src="https://upload.wikimedia.org/wikipedia/commons/0/01/X-Logo-Round-Color.png"
                            alt="X"></a>
                </div>
            </div>
            <div class="barra">
                <ul class="barra-links">
                    <li><a href="../contacto/contacto.php">Contacto</a></li>
                    <li><a href="../Preguntasfrecuentes/preguntas.php">Preguntas frecuentes</a></li>
                    <li><a href="../terminos/terminos.php">Términos y condiciones</a></li>
                </ul>
            </div>

        </div>
        <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
    </footer>
</body>

</html>