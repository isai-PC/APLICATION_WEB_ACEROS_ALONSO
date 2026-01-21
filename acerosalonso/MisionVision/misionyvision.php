<?php
include '../conexion.php';

// Consultar misión y visión
$sql = "SELECT * FROM mision_vision LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $datos = $result->fetch_assoc();
} else {
    // Valores por defecto si no hay registros
    $datos = [
        'mision' => 'Nuestra misión aún no ha sido registrada.',
        'vision' => 'Nuestra visión aún no ha sido registrada.',
        'img_mision' => 'default_mision.jpg',
        'img_vision' => 'default_vision.jpg'
    ];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aceros Alonso | Misión y Visión</title>
    <link rel="icon" type="image/png" href="../ACASALogoAcerosA.png">
    <!--    <link rel="stylesheet" href="misionvision.css"> -->
    <link rel="stylesheet" href="../StylesGeneralesPublic.css?v=<?php echo time(); ?>">
</head>

<body>
    <header>
        <section class="logo">
            <img src="../ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
            <h2>Aceros Alonso</h2>
        </section>
        <nav aria-label="Navegación principal">
            <ul>
                <li class="menu">
                    <a href="#productos" aria-haspopup="true" aria-expanded="false">Categorías</a>
                    <ul class="ContenidoMenu" role="menu" aria-label="Categorías">
                        <?php
                        // Consulta para obtener las categorías
                        $sqlMenu = "SELECT id_categoria, nombre_categoria FROM categoria ORDER BY nombre_categoria ASC";
                        $resMenu = $conn->query($sqlMenu);

                        if ($resMenu && $resMenu->num_rows > 0) {
                            // Si hay categorías, crea un enlace para cada una
                            while ($cat = $resMenu->fetch_assoc()) {
                                $id = (int)$cat['id_categoria'];
                                $nombre = htmlspecialchars($cat['nombre_categoria']);

                                echo '<li role="none"><a role="menuitem" href="../Catalogo/CatalogoCategorias.php?id_categoria=' . $id . '">' . $nombre . '</a></li>';
                            }
                        } else {
                            echo '<li role="none"><a role="menuitem" href="#">Sin categorías</a></li>';
                        }
                        ?>
                    </ul>
                </li>

                <li class="menu">
                    <a href="#" aria-haspopup="true" aria-expanded="false">Acerca de nosotros</a>
                    <ul class="ContenidoMenu" role="menu" aria-label="Acerca de nosotros">
                        <li role="none"><a role="menuitem" href="../contacto/contacto.php">Contacto</a></li>
                        <li role="none"><a role="menuitem" href="../terminos/terminos.php">Términos y Condiciones</a></li>
                        <li role="none"><a role="menuitem" href="../Preguntasfrecuentes/preguntas.php">Preguntas frecuentes</a></li>
                    </ul>
                </li>

                <li><a href="../paginaPrincipal.php">Inicio</a></li>
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


    <main>
        <!-- Sección Misión -->
        <section class="info-section" id="mision">
            <div class="text-content">
                <h1>Misión</h1>
                <p><?php echo $datos['mision']; ?></p>
            </div>
            <?php if (!empty($datos['img_mision'])): ?>
                <img src="images/<?php echo $datos['img_mision']; ?>" alt="Imagen Misión" class="info-image">
            <?php endif; ?>
        </section>

        <!-- Sección Visión -->
        <section class="info-section" id="vision">
            <div class="text-content">
                <h1>Visión</h1>
                <p><?php echo $datos['vision']; ?></p>
            </div>
            <?php if (!empty($datos['img_vision'])): ?>
                <img src="images/<?php echo $datos['img_vision']; ?>" alt="Imagen Visión" class="info-image">
            <?php endif; ?>
        </section>

        <!-- Por qué elegir ACASA -->
        <section class="info-section reverse" id="porqueelejir">
            <div class="text-content">
                <h1>¿Por qué elegir los productos de Aceros Alonso?</h1>
                <p><?php echo nl2br(htmlspecialchars($datos['info'])); ?></p>
            </div>
            <?php if (!empty($datos['iminfo'])): ?>
                <img src="images/<?php echo htmlspecialchars($datos['iminfo']); ?>" alt="Beneficios de elegir ACASA" class="info-image">
            <?php endif; ?>
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