<?php
session_start();
require_once('funciones.php');

// Seguridad y gestión de caché institucional
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['id_empleado'])) {
    header("Location: ../Login/Login.php");
    exit;
}

$usuarioHeader = "Usuario: " . ($_SESSION['nombre'] ?? '');
$reporte = obtenerResumenPorDepartamento();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen Plantilla | Aceros Alonso</title>
    <link rel="stylesheet" href="consulta.css?v=<?php echo time(); ?>">
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
        <img src="../Accesibilidad/accesibilidad.png">
    </div>
    <iframe id="menuAccesibilidad" src="../Accesibilidad/MenuAccesibilidad.html" class="accesibilidad-frame"></iframe>

    <main>
        <section class="contenedor-reporte">
            <h1>RESUMEN DE DEPARTAMENTOS</h1>
            <div class="contenedor-consultas-rapidas">
                <h3>Consultas Rapidas</h3>

                <select name="opcion_especial" onchange="if(this.value) window.location.href=this.value;">
                    <option value="">-- Seleccione una consulta --</option>
                    <option value="reporte_criticos.php">Empleados cuyas faltas superan el promedio general</option>
                    <option value="reporte_resumen.php" selected>Total de personal por departamento</option>
                    <option value="reporte_asistencias_mes.php">Total de asistencias por departamento en cada mes</option>
                    <option value="reporte_ausentes.php">Días sin registro de asistencia por empleado</option>
                    <option value="Consultas_3/Reporte_Asistencias_FechaEspécifica.php">Asistencias por departamento por mes especifico</option>
                </select>
            </div>

            <section class="caja-resultados">
                <table>
                    <thead>
                        <tr>
                            <th>Departamento</th>
                            <th>Total Empleados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($reporte)): ?>
                            <?php foreach ($reporte as $fila): ?>
                                <tr>
                                    <td><?= htmlspecialchars($fila['Departamento']) ?></td>
                                    <td style="font-weight: bold;"> <?= htmlspecialchars($fila['total_empleados']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" style="text-align:center;">No hay departamentos registrados.</td>
                            </tr>
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