<?php
include 'funciones.php';
$datos = obtenerMisionVision($conn);

session_start();
$usuarioHeader = '';
if (isset($_SESSION['id_empleado'])) {
    $nombre = $_SESSION['nombre'] ?? 'Usuario';
    $usuarioHeader = "Usuario: " . htmlspecialchars($nombre);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Por qué Elegirnos</title>
    <link rel="stylesheet" href="../Privado/StylesGenerales.css?v=<?php echo time(); ?>">
</head>
<body>
<header>
    <section class="logo">
        <img src="../ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
        <h2>Aceros Alonso</h2>
    </section>
    <nav>
        <ul>
            <?php echo htmlspecialchars($usuarioHeader) ?>
            <li><a href="../Login/CerrarSesion.php">Cerrar sesión</a></li>
        </ul>
    </nav>
</header>
<aside>
    <nav>
        <ul>
            <li><a href="../Consultas/consultas.php">Consultas</a></li>
            <li><a href="../Privado/PrincipalCategorias/listadodetalle.php">Productos</a></li>
            <li><a href="../Privado/PrincipalCategorias/Categorias/listado_Categoria.php">Categorias</a></li>
            <li class="menu">
                <a href="#">Mision Vision</a>
                <ul class="ContenidoMenu">
                    <li><a href="../MisionVision/editar_mision.php">Mision</a></li>
                    <li><a href="../MisionVision/editar_vision.php">Vision</a></li>
                    <li><a href="../MisionVision/editar_info.php">Por que elegirnos</a></li>
                </ul>
            <li><a href="../Registro/empleados.php">Empleados</a></li>
            <li><a href="../Ubicacion/listar_ubicaciones.php">Ubicaciones</a></li>
            <li><a href="../Preguntasfrecuentes/index.php">Preguntas Frecuentes</a></li>
            <li><a href="../contacto/admin_contacto.php">Contacto</a></li>
            <li><a href="../terminos/admin_terminos.php">Terminos y Condiciones</a></li>
            <li><a href="../../paginaPrincipal.php">Inicio</a></li>
        </ul>
    </nav>
</aside>

<script src="../Accesibilidad/accesi.js?v=<?php echo time(); ?>"></script>
<div id="btnAccesibilidad" onclick="event.stopPropagation(); toggleMenuAccesibilidad()">
    <img src="../Accesibilidad/accesibilidad.png" style="width: 100%; height:100%; object-fit:cover;">
</div>
<iframe id="menuAccesibilidad" src="../Accesibilidad/MenuAccesibilidad.html" class="accesibilidad-frame"></iframe>

<main class="admin-detalle">
    <h1>Editar Por qué Elegirnos</h1>
    <div class="container2">
        <?php if (isset($_GET['exito'])): ?>
            <p class="success-message">Actualización exitosa.</p>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <p class="notice notice--err"><?= htmlspecialchars($_GET['error']) ?></p>
        <?php endif; ?>

        <form action="guardar_info.php" method="post" enctype="multipart/form-data">
            <div class="form-section">
                <label for="info">¿Por qué elegir los productos de ACASA?</label>
                <textarea id="info" name="info" rows="8" cols="50" required><?= htmlspecialchars($datos['info']) ?></textarea>
                <label for="iminfo">Imagen “Por qué elegirnos”:</label>
                <input id="iminfo" type="file" name="iminfo">
                <?php if ($datos['iminfo']): ?>
                    <img src="images/<?= htmlspecialchars($datos['iminfo']) ?>" alt="Imagen Por Qué Elegirnos Actual">
                <?php endif; ?>
            </div>
            <div class="button-container">
                <button type="submit" class="btnGuardar">Guardar</button>
            </div>
        </form>
    </div>
</main>

<footer>
    <div class="footer-sections">
        <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
    </div>
</footer>
</body>
</html>
