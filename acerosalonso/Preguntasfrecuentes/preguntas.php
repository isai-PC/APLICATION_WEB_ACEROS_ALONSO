<?php
include '../conexion.php';
$query = "SELECT pregunta, respuesta FROM preguntas_frecuentes ORDER BY id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../ACASALogoAcerosA.png">
    <title>Preguntas Frecuentes | Aceros Alonso</title>

    <link rel="stylesheet" href="../StylesGeneralesPublic.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../Privado/StylesGenerales.css?v=<?php echo time(); ?>">
    <!-- <link rel="stylesheet" href="preguntas.css?v=<?php echo time(); ?>"> -->

    <link rel="icon" href="../ACASALogoAcerosA.png" type="image/png">

</head>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aceros Alonso | Página Oficial</title>
    <link rel="icon" href="ACASALogoAcerosA.png" type="image/png" sizes="16px">
    <link rel="stylesheet" href="StylesGeneralesPublic.css?v=<?php echo time(); ?>">
</head>

<body>
    <header>
        <section class="logo">
            <img src="../ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
            <h2>Aceros Alonso</h2>
        </section>

        <nav>
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
                    <a href="#">Acerca de nosotros</a>
                    <ul class="ContenidoMenu">
                        <li><a href="../MisionVision/misionyvision.php">Nosotros</a></li>
                    </ul>
                </li>
                <li><a href="../paginaPrincipal.php">Inicio</a></li>
            </ul>
        </nav>
    </header>
    <div id="btnAccesibilidad" onclick="event.stopPropagation(); toggleMenuAccesibilidad()"
        aria-label="Abrir menú de accesibilidad" role="button">
        <img src="../Accesibilidad/accesibilidad.png" alt="Accesibilidad" style="width:100%;height:100%;object-fit:cover;">
    </div>
    <iframe
        id="menuAccesibilidad"
        class="accesibilidad-frame"
        src="../Accesibilidad/MenuAccesibilidad.html"
        title="Menú de accesibilidad">
    </iframe>
    <main>
        <section class="contenedor-reporte">
            <h1>Preguntas Frecuentes</h1>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <article class="faq-card">
                        <button type="button" class="faq-question">
                            <span>
                                <?= htmlspecialchars($row['pregunta'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <span class="arrow" aria-hidden="true">➤</span>
                        </button>
                        <div class="faq-answer">
                            <?= nl2br(htmlspecialchars($row['respuesta'], ENT_QUOTES, 'UTF-8')) ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center; margin-top:20px;">
                    No hay preguntas registradas aún.
                </p>
            <?php endif; ?>
        </section>
    </main>

    <script src="pregunta.js?v=<?php echo time(); ?>"></script>
    <script src="../Accesibilidad/accesi.js?v=<?php echo time(); ?>"></script>


</body>
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
                <li><a href="../terminos/terminos.php">Términos y condiciones</a></li>
            </ul>
        </div>

    </div>
    <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
</footer>

</html>