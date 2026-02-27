<?php
session_start();
include 'conexion.php';

$usuarioHeader = '';
if (isset($_SESSION['id_empleado'])) {
    $nombre = $_SESSION['nombre'] ?? 'Usuario'; // Lo que se guarda en el login
    $usuarioHeader = "Usuario: $nombre";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aceros Alonso | Página Oficial</title>
    <link rel="icon" href="ACASALogoAcerosA.png" type="image/png" sizes="16px">
    <link rel="stylesheet" href="StylesGeneralesPublic.css?v=<?php echo time(); ?>">
</head>

<body>
    <script src="js/Slider.js"></script>

    <header>
        <section class="logo">
            <img src="ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
            <h2>Aceros Alonso</h2>
        </section>


        <nav>
            <ul>

             
                <li><a href="paginaPrincipal.php">Inicio</a></li>

                <li class="menu">
                    <a href="#productos">Categorías</a>
                    <ul class="ContenidoMenu">
                        <?php
                        // Consulta para obtener las categorías
                        $sqlMenu = "SELECT id_categoria, nombre_categoria FROM categoria ORDER BY nombre_categoria ASC";
                        $resMenu = $conn->query($sqlMenu);

                        if ($resMenu && $resMenu->num_rows > 0) {
                            // Si hay categorías, crea un enlace para cada una
                            while ($cat = $resMenu->fetch_assoc()) {
                                $id = $cat['id_categoria'];
                                $nombre = htmlspecialchars($cat['nombre_categoria']);


                                echo "<li><a href='Catalogo/CatalogoCategorias.php?id_categoria=$id'>$nombre</a></li>";
                            }
                        } else {
                            echo "<li><a href='#'>Sin categorías</a></li>";
                        }
                        ?>
                    </ul>
                </li>
                
                
                
                <li class="menu">
    <a href="#">Productos</a>
    <ul class="ContenidoMenu">

        <?php
        $sqlProdsMenu = "SELECT id_producto, nombre_producto 
                         FROM productos 
                         ORDER BY nombre_producto ASC";
        $resMenuP = $conn->query($sqlProdsMenu);

        if ($resMenuP && $resMenuP->num_rows > 0) {
            while ($prod = $resMenuP->fetch_assoc()) {
                    $pid = $prod['id_producto'];
                    $pnom = htmlspecialchars($prod['nombre_producto']);

                    echo "<li><a href='vistadetalle/vistadetalle.php?id=$pid'>$pnom</a></li>";
            }
        } else {
            echo "<li><a href='#'>Sin productos</a></li>";
        }
        ?>
    </ul>
</li>


                <li class="menu">
                    <a href="#">Acerca de nosotros</a>
                    <ul class="ContenidoMenu">
                        <li><a href="MisionVision/misionyvision.php">Nosotros</a></li>
                    </ul>
                </li>


                <?php if (!isset($_SESSION['id_empleado'])): ?>
                    <li><a href="Login/Login.php" class="btnLogin" style="cursor:pointer;">Login</a></li>
                <?php else: ?>
                    <?php if ($_SESSION['tipo_usuario'] == 2): ?>
                        <li><a href="Consultas/consultas.php">Administración</a></li>
                    <?php elseif ($_SESSION['tipo_usuario'] == 1): ?>
                        <li><a href="Consultas/consultas_empleado.php?id=<?= $_SESSION['id_empleado'] ?>">Empleado</a></li>
                    <?php endif; ?>
                    <li><a href="Login/CerrarSesion.php">Cerrar Sesión</a></li>
                <?php endif; ?>




                <?php if (!empty($usuarioHeader)): ?>
                    <li><?= htmlspecialchars($usuarioHeader) ?></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!--===========================COPIAR ESTO Y PEGAR EN LAS DEMAS PAGINAS================================== -->
    <script src="Accesibilidad/accesi.js?v=<?php echo time(); ?>"></script>
    <!-- Botón de accesibilidad -->
    <div id="btnAccesibilidad" onclick="event.stopPropagation(); toggleMenuAccesibilidad()">
        <img src="Accesibilidad/accesibilidad.png" style="width: 100%; height:100%; object-fit:cover;">
    </div>
    <!-- Iframe del menú -->
    <iframe
        id="menuAccesibilidad"
        src="Accesibilidad/MenuAccesibilidad.html"
        class="accesibilidad-frame">
    </iframe>
    <!-- ===================================================================================================== -->

    <main>
        <section id="productos">
            <h1>Nuestras Categorías</h1>
            <div class="EnvolturaSlider">
                <div class="ContenidoSlider">
                    <div class="slider" id="slider">
                        <?php
                        //  consulta para el slider 
                        $sql = "SELECT id_categoria, nombre_categoria, texto_secundario, imagen_categoria FROM categoria ORDER BY id_categoria ASC";
                        $res = $conn->query($sql);

                        if ($res && $res->num_rows > 0):
                            while ($row = $res->fetch_assoc()):
                                $id   = (int)$row['id_categoria'];
                                $tit  = $row['nombre_categoria'] ?? '';
                                $sub  = $row['texto_secundario'] ?? '';
                                $img  = $row['imagen_categoria'];
                                $rutaImg = 'imagenes/' . ($img ?: 'placeholder.jpg');
                                $href = "Catalogo/CatalogoCategorias.php?id_categoria=" . $id;
                        ?>
                                <div class="slider-item">
                                    <div class="card">
                                        <a href="<?php echo htmlspecialchars($href); ?>">
                                            <img src="<?php echo htmlspecialchars($rutaImg); ?>"
                                                alt="<?php echo htmlspecialchars($tit); ?>">
                                            <div class="card-content">
                                                <h3><?php echo htmlspecialchars($tit); ?></h3>
                                                <?php if (!empty($sub)): ?>
                                                    <p><?php echo htmlspecialchars($sub); ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="overlay">
                                                <p>Más información acerca de los productos o más categorías del mismo producto o variantes:</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                        <?php
                            endwhile;
                        else:
                            echo '<p style="padding:1rem;">Aún no hay categorías para mostrar.</p>';
                        endif;
                        ?>
                    </div>
                </div>
                <button class="btn btnAnterior" onclick="moveSlide(-1)">⟨</button>
                <button class="btn btn-right" onclick="moveSlide(1)">⟩</button>
            </div>
        </section>

        <section id="sucursales">
            <h1>Nuestras Sucursales</h1>
            <div class="sucursal-container">
                <?php
                $sql = "SELECT id, descripcion, imagen_nombre, url FROM ubicaciones ORDER BY id ASC";
                $a = $conn->query($sql);

                if ($a && $a->num_rows > 0):
                    while ($row = $a->fetch_assoc()):
                        $id = (int)$row['id'];
                        $descripcion = $row['descripcion'] ?? '';
                        $img = $row['imagen_nombre'] ?? '';
                        $url = $row['url'] ?? '#';
                        $rutaImg = 'Ubicacion/images/' . ($img ?: 'placeholder.jpg');
                ?>
                        <div class="sucursal-card">
                            <a href="<?php echo htmlspecialchars($url); ?>">
                                <img src="<?php echo htmlspecialchars($rutaImg); ?>" alt="<?php echo htmlspecialchars($descripcion); ?>">
                                <div class="sucursal-info">
                                    <h4><?php echo htmlspecialchars($descripcion); ?></h4>
                                </div>
                            </a>
                        </div>
                <?php
                    endwhile;
                else:
                    echo '<p style="padding:1rem;">Aún no hay sucursales para mostrar.</p>';
                endif;
                ?>
            </div>
        </section>
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
                    <li><a href="contacto/contacto.php">Contacto</a></li>
                    <li><a href="Preguntasfrecuentes/preguntas.php">Preguntas frecuentes</a></li>
                    <li><a href="terminos/NewTerminos.php">Términos y condiciones</a></li>
                </ul>
            </div>

        </div>
        <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
    </footer>
</body>
<?php
// Cerrar conexión
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>

</html>