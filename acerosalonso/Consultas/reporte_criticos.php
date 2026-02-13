<?php
session_start();
require_once('funciones.php');

// Seguridad y caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['id_empleado'])) {
    header("Location: ../Login/Login.php");
    exit;
}

$usuarioHeader = "Usuario: " . ($_SESSION['nombre'] ?? '');
$reporte = obtenerPersonalCritico();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Crítico | Aceros Alonso</title>
    <link rel="stylesheet" href="consulta.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../Privado/StylesGenerales.css?v=<?php echo time();?>">
    <style>
        .caja-resultados table thead th {
            background-color: #d9702e !important; 
            color: white !important;             
            padding: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <section class="logo">
            <img src="../ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
            <h2>Aceros Alonso</h2>
        </section>
        <nav>
            <ul>
                <?= htmlspecialchars($usuarioHeader) ?>
                <li><a href="../Login/CerrarSesion.php">Cerrar sesion</a></li>
            </ul>
        </nav>
    </header> 
   
    <aside>
        <nav>
            <ul>
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
                <li><a href="../Ubicacion/listar_ubicaciones.php">Ubicaciones</a></li>
                <li><a href="../Preguntasfrecuentes/index.php">Preguntas Frecuentes</a></li>
                <li><a href="../contacto/admin_contacto.php">Contacto</a></li>
                <li><a href="../terminos/admin_terminos.php">Terminos y Condiciones</a></li>
                <li><a href="consultas.php">Consultas</a></li>
                <li><a href="../../paginaPrincipal.php">Inicio</a></li>
            </ul>
        </nav>
    </aside>

    <script src="../Accesibilidad/accesi.js?v=<?php echo time(); ?>"></script>
    <div id="btnAccesibilidad" onclick="event.stopPropagation(); toggleMenuAccesibilidad()">
        <img src="../Accesibilidad/accesibilidad.png" style="width: 100%; height:100%; object-fit:cover;">
    </div>
    <iframe id="menuAccesibilidad" src="../Accesibilidad/MenuAccesibilidad.html" class="accesibilidad-frame"></iframe>

    <main>
        <section class="contenedor-reporte">
            <h1>RESUMEN DE PERSONAL CRÍTICO</h1>
            <p style="text-align: center; margin-bottom: 20px;">Personal con faltas superiores al promedio general.</p>

            <section class="caja-resultados">
                <table>
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Departamento</th>
                            <th>Total Faltas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($reporte)): ?>
                            <?php foreach ($reporte as $fila): ?>
                                <tr>
                                    <td><?= htmlspecialchars($fila['Empleado']) ?></td>
                                    <td><?= htmlspecialchars($fila['Departamento']) ?></td>
                                    <td style="color: red; font-weight: bold;"><?= htmlspecialchars($fila['Total_Faltas']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center;">No hay personal crítico detectado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </section>
    </main>

    <footer>
        <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
    </footer>
</body>
</html>