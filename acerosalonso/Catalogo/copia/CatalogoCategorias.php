<?php
// /Catalogo/CatalogoCategorias.php
include '../conexion.php';

// Acepta id_categoria (y opcionalmente cat_id por si alguna liga vieja lo usa) [cite: 102-105]
$cat_id = 0;
if (isset($_GET['id_categoria'])) $cat_id = (int) $_GET['id_categoria'];
elseif (isset($_GET['cat_id']))   $cat_id = (int) $_GET['cat_id'];

// Si no viene id => mostramos todo, pero la idea es que SI venga desde la portada
$nombreCat = 'Todos los productos';
if ($cat_id > 0) {
    // Obtiene el nombre de la categoría [cite: 108-115]
    $stmt = $conn->prepare("SELECT nombre_categoria FROM categoria WHERE id_categoria = ?");
    $stmt->bind_param("i", $cat_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res) $nombreCat = $res['nombre_categoria'];
    $stmt->close();
}

// === Consulta de productos (de la tabla 'catalogo') ===
$cols = "id_producto, id_categoria, nombre_producto, texto_secundario, imagen_producto";
if ($cat_id > 0) {
    // Obtiene los productos de esa categoría
    $stmtP = $conn->prepare("SELECT $cols FROM catalogo WHERE id_categoria = ? ORDER BY nombre_producto ASC");
    $stmtP->bind_param("i", $cat_id);
    $stmtP->execute();
    $productos = $stmtP->get_result();
} else {
    // Obtiene todos los productos si no hay ID
    $productos = $conn->query("SELECT $cols FROM catalogo ORDER BY nombre_producto ASC");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo | <?php echo htmlspecialchars($nombreCat); ?></title>
    
    <link rel="stylesheet" href="../style.css?v=4">
    
    <link rel="stylesheet" href="StyleCatalogo.css?v=<?php echo time(); ?>">    
    
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
            <li><a href="../paginaPrincipal.php#contacto">Contacto</a></li>
            <li><a href="../Login/Login.php" class="btnLogin" style="cursor:pointer;">Login</a></li>
        </ul>
    </nav>
</header>

<main id="catalogo">
    
    <h1><?php echo htmlspecialchars($nombreCat); ?></h1>
    <p class="subtitulo">
        <?php echo $cat_id > 0 ? 'Productos de esta categoría.' : 'Todos los productos de nuestro catálogo.'; ?>
    </p>

    <div class="lista-productos-container">

        <?php if ($productos && $productos->num_rows > 0): ?>
            <?php while ($row = $productos->fetch_assoc()): ?>
                
                <div class="producto-item">
                    
                    <div class="producto-imagen">
                        <img src="../imagenes/<?php echo htmlspecialchars($row['imagen_producto'] ?? 'placeholder.jpg'); ?>"
                             alt="<?php echo htmlspecialchars($row['nombre_producto']); ?>">
                    </div>

                    <div class="producto-info">
                        
                        <div class="producto-info-header">
                            <h3><?php echo htmlspecialchars($row['nombre_producto']); ?></h3>
                            </div>

                        <p class="descripcion-producto">
                            <?php echo nl2br(htmlspecialchars($row['texto_secundario'])); ?>
                        </p>
                    </div>

                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; padding: 30px 0;">No se encontraron productos en esta categoría.</p>
        <?php endif; ?>

    </div>
</main>

<footer>
    <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
</footer>

</body>
</html>