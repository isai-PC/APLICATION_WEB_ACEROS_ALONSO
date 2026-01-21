<?php
session_start(); 
include __DIR__ . '/../conexion.php'; 

// Obtener ID del producto actual
$id_producto = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_producto <= 0) {
    header('Location: ../paginaPrincipal.php');
    exit;
}

//  Buscar el producto principal
$stmt = $conn->prepare("SELECT * FROM productos WHERE id_producto = ?");
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$resultado = $stmt->get_result();
$producto = $resultado->fetch_assoc();
$stmt->close();

if (!$producto) {
    echo "<h1>Producto no encontrado</h1>";
    echo "<a href='../paginaPrincipal.php'>Volver al inicio</a>";
    exit; // Salimos si no hay producto
}

// Variables del Producto
$nombre_principal = htmlspecialchars($producto['nombre_producto']);
$img_principal = !empty($producto['ImagenesProducto']) ? htmlspecialchars($producto['ImagenesProducto']) : 'placeholder.jpg';
$id_cat_actual = (int)$producto['id_categoria']; // ID de la categoría de este producto
$descripcion_corta = !empty($producto['unidad_medida']) ? htmlspecialchars($producto['unidad_medida']) : htmlspecialchars($producto['calibre'] ?? '');

// Obtener el NOMBRE de la categoría actual 
$nombre_cat_actual = "Esta Categoría"; 
$stmtCat = $conn->prepare("SELECT nombre_categoria FROM categoria WHERE id_categoria = ?");
$stmtCat->bind_param("i", $id_cat_actual);
$stmtCat->execute();
$resCat = $stmtCat->get_result()->fetch_assoc();
if ($resCat) {
    $nombre_cat_actual = $resCat['nombre_categoria'];
}
$stmtCat->close();

//  Especificaciones
$specs = [
    'Unidad de Medida' => $producto['unidad_medida'],
    'Calibre' => $producto['calibre'],
    'Metros'  => $producto['metros'],
    'Kg'      => $producto['kg'],
    'Color'   => $producto['color'],
    'Ced'     => $producto['ced'],
    'Ton'     => $producto['ton'],
    'Cm'      => $producto['cm']
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $nombre_principal; ?> | Aceros Alonso</title>
    
    <link rel="stylesheet" href="../StylesGeneralesPublic.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="detallevista.css?v=<?php echo time(); ?>">
    
    <link rel="icon" href="../ACASALogoAcerosA.png" type="image/png" sizes="16px">
</head>
<body>
<header>
    <section class="logo">
        <img src="../ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
        <h2>Aceros Alonso</h2>
    </section>
    
    <script src="../Accesibilidad/accesi.js?v=<?php echo time(); ?>"></script>

    <nav>
        <ul>
            <li><a href="../paginaPrincipal.php">Inicio</a></li>

           
            <li class="menu">
                <a href="#">Categorías</a>
                <ul class="ContenidoMenu">
                    <?php
                    $sqlMenu = "SELECT id_categoria, nombre_categoria FROM categoria ORDER BY nombre_categoria ASC";
                    $resMenu = $conn->query($sqlMenu);
                    if ($resMenu && $resMenu->num_rows > 0) {
                        while ($cat = $resMenu->fetch_assoc()) {
                            $id = $cat['id_categoria'];
                            $nombre = htmlspecialchars($cat['nombre_categoria']);
                            // Link al catálogo
                            echo "<li><a href='../Catalogo/CatalogoCategorias.php?id_categoria=$id'>$nombre</a></li>";
                        }
                    } else {
                        echo "<li><a href='#'>Sin categorías</a></li>";
                    }
                    ?>
                </ul>
            </li>

            <li class="menu">
                <a href="#">Productos en <?php echo htmlspecialchars($nombre_cat_actual); ?></a>
                <ul class="ContenidoMenu">
                    <?php
                    $sqlProdsMenu = "SELECT id_producto, nombre_producto FROM productos WHERE id_categoria = ? ORDER BY nombre_producto ASC";
                    $stmtMenuP = $conn->prepare($sqlProdsMenu);
                    $stmtMenuP->bind_param("i", $id_cat_actual);
                    $stmtMenuP->execute();
                    $resMenuP = $stmtMenuP->get_result();

                    if ($resMenuP && $resMenuP->num_rows > 0) {
                        while ($prodItem = $resMenuP->fetch_assoc()) {
                            $pid = $prodItem['id_producto'];
                            $pnom = htmlspecialchars($prodItem['nombre_producto']);
                            
                            echo "<li><a href='../vistadetalle/vistadetalle.php?id=$pid'>$pnom</a></li>";
                        }
                    } else {
                        echo "<li><a href='#'>Sin más productos</a></li>";
                    }
                    $stmtMenuP->close();
                    ?>
                </ul>
            </li>
    

                 <li class="menu">
                <a href="#">Acerca de nosotros</a>
                <ul class="ContenidoMenu">
                    <li><a href="../MisionVision/misionyvision.php">Nosotros</a></li>
                <!--     <li><a href="../paginaPrincipal.php#contacto">Contacto</a></li>-->
       <!--             <li><a href="../Preguntasfrecuentes/preguntas.php">Preguntas frecuentes</a></li> -->
                </ul>
            </li>

            <?php if (!isset($_SESSION['id_empleado'])): ?>
                <li><a href="../Login/Login.php" class="btnLogin" style="cursor:pointer;">Login</a></li>
            <?php else: ?>
                <?php if ($_SESSION['tipo_usuario'] == 2): ?>
                    <li><a href="../Consultas/consultas.php">Administración</a></li>
                <?php elseif ($_SESSION['tipo_usuario'] == 1): ?>
                    <li><a href="../Consultas/consultas_empleado.php?id=<?= $_SESSION['id_empleado'] ?>">Empleado</a></li>
                <?php endif; ?>
                <li><a href="../Login/CerrarSesion.php">Cerrar Sesión</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<div id="btnAccesibilidad" onclick="toggleMenuAccesibilidad()">
    <img src="../Accesibilidad/accesibilidad.png" style="width: 100%; height:100%; object-fit:cover;" alt="Accesibilidad">
</div>
<iframe
    id="menuAccesibilidad"
    src="../Accesibilidad/MenuAccesibilidad.html"
    class="accesibilidad-frame">
</iframe>

<main class="detalle-main">

    <div class="breadcrumb">
        <a href="../Catalogo/CatalogoCategorias.php?id_categoria=<?php echo $id_cat_actual; ?>">Volver al Catálogo</a> 
        / <span><?php echo $nombre_principal; ?></span>
    </div>

    <div class="detalle-container">
        <div class="detalle-imagen">
            <img src="../imagenes/<?php echo $img_principal; ?>" alt="<?php echo $nombre_principal; ?>">
        </div>

        <div class="detalle-info">
            <h1><?php echo $nombre_principal; ?></h1>
            
            <?php if($descripcion_corta): ?>
                <p class="descripcion-corta"><?php echo $descripcion_corta; ?></p>
            <?php endif; ?>
            
            <h2>Especificaciones Técnicas</h2>
            <table class="specs-table">
                <tbody>
                    <?php foreach ($specs as $label => $val): ?>
                        <?php if (!empty($val) && $val !== 'N/A' && $val != 0): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($label); ?>:</strong></td>
                            <td><?php echo htmlspecialchars($val); ?></td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

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
<?php
// Cerrar conexión al final
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
</html>