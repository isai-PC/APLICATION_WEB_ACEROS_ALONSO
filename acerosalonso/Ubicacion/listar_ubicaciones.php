<?php
include 'funciones_ubicaciones.php';
require_once("../conexion.php");//NO LO QUITEN 

// Inicio de sesión y usuario para header
session_start();
$usuarioHeader = '';
if (isset($_SESSION['id_empleado'])) {
    $nombre = $_SESSION['nombre'] ?? 'Usuario';
    $usuarioHeader = "Usuario: " . htmlspecialchars($nombre);
}

// Paginación: mostrar 5 por página
$pagina_actual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
$por_pagina = 5;
$inicio = ($pagina_actual - 1) * $por_pagina;

// Total de registros
$resCount = $conn->query("SELECT COUNT(*) AS cnt FROM ubicaciones");
$total_registros = 0;
if ($resCount) {
    $r = $resCount->fetch_assoc();
    $total_registros = isset($r['cnt']) ? (int)$r['cnt'] : 0;
    if (method_exists($resCount, 'free')) $resCount->free();
}

// Obtener filas paginadas
$ubicaciones = $conn->query("SELECT * FROM ubicaciones ORDER BY id DESC LIMIT " . (int)$inicio . "," . (int)$por_pagina);

// Calcular total de páginas
$total_paginas = $total_registros > 0 ? (int)ceil($total_registros / $por_pagina) : 1;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubicaciones | Admin</title>
    <!-- <link rel="stylesheet" href="ubicaciones.css?v=<?php echo time(); ?>"> -->
    <link rel="stylesheet" href="../Privado/StylesGenerales.css?v=<?php echo time(); ?>">
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
                <li><a href="../Login/CerrarSesion.php">Cerrar sesion</a></li>
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
                </li>
                <li><a href="../Registro/empleados.php">Empleados</a></li>
                <li><a href="#">Ubicaciones</a></li>
                <li><a href="../Preguntasfrecuentes/index.php">Preguntas Frecuentes</a></li>
                <li><a href="../contacto/admin_contacto.php">Contacto</a></li>
                <li><a href="../terminos/admin_terminos.php">Terminos y Condiciones</a></li>
                <li><a href="../../paginaPrincipal.php">Inicio</a></li>

            </ul>
        </nav>

    </aside>
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
    <main class="admin-detalle">

        <h1>Ubicaciones</h1>

        <h2>Nuestras sucursales</h2>

        <div>
            <a href="agregar_ubicacion.php">
                <button class="btnAgregar" type="button">
                    Agregar ubicacion
                </button>
            </a>
        </div>

        <div class="container2">
            <div class="cards">
                <?php while ($fila = $ubicaciones->fetch_assoc()): ?>
                    <div class="card">
                        <img src="images/<?php echo $fila['imagen_nombre']; ?>" alt="">
                        <h3><?php echo $fila['descripcion']; ?></h3>
                        <p><a href="<?php echo $fila['url']; ?>" target="_blank">Ver en Google Maps</a></p>

                        <div class="actions">
                            <a href="editar_ubicacion.php?id=<?php echo $fila['id']; ?>"><button class="btn btn-edit" type="submit" name="accion" value="eliminar">Editar</button></a>
                            <a href="eliminar_ubicacion.php?id=<?php echo $fila['id']; ?>" class="delete"><button class="btn btn-danger" type="submit" name="accion" value="eliminar">Eliminar</button></a>

                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

        </div>

        <?php if (!empty($total_paginas) && $total_paginas > 1): ?>
            <div class="paginacion" style="margin-top:16px; text-align:center;">
                <?php if ($pagina_actual > 1): ?>
                    <a href="?pagina=<?php echo $pagina_actual - 1; ?>">« Anterior</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <?php if ($i == $pagina_actual): ?>
                        <span class="activo" style="margin:0 6px; font-weight:bold;"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?pagina=<?php echo $i; ?>" style="margin:0 6px;"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($pagina_actual < $total_paginas): ?>
                    <a href="?pagina=<?php echo $pagina_actual + 1; ?>">Siguiente »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </main>

    <footer>
        <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
    </footer>

</body>

</html>