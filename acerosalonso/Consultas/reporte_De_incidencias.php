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
$reporte = obtenerDetalleIncidencias();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Completo de Incidencias | Aceros Alonso</title>
    <link rel="stylesheet" href="consulta.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../Privado/StylesGenerales.css?v=<?php echo time(); ?>">
     <style>
        /* Encabezados en naranja para consistencia visual */
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
            <h1>DETALLE COMPLETO DE INCIDENCIAS</h1>
            <div style="background: #dfe6ed; padding: 20px; border-radius: 8px; border: 1px solid #ccc; text-align: center; margin-bottom: 30px;">
                <h3 style="margin-bottom: 10px; color: #333;">Consultas Rapidas</h3>
                <select onchange="if(this.value) window.location.href=this.value;" style="width: 95%; padding: 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 16px; cursor: pointer; background: white;">
                    <option value="">-- Seleccione una consulta --</option>
                    <option value="reporte_criticos.php">Empleados cuyas faltas superan el promedio general</option>
                    <option value="reporte_resumen.php">Total de personal por departamento</option>
                    <option value="reporte_asistencias_mes.php">Total de asistencias por departamento en cada mes</option>
                    <option value="reporte_ausentes.php" >Días sin registro de asistencia por empleado</option>
                </select>
            </div>
            <p style="text-align: center; margin-bottom: 20px;">
                Listado completo de todas las incidencias registradas.
            </p>

            <section class="caja-resultados">
                <table>
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Departamento</th>
                            <th>Tipo Incidencia</th>
                            <th>Fecha</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($reporte)): ?>
                            <?php foreach ($reporte as $fila): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars(
                                            $fila['Nombre'] . ' ' .
                                            $fila['Apellido_Paterno'] . ' ' .
                                            $fila['Apellido_Materno']
                                        ) ?>
                                    </td>
                                    <td><?= htmlspecialchars($fila['Departamento']) ?></td>
                                    <td><?= htmlspecialchars($fila['Tipo_Incidencia']) ?></td>
                                    <td><?= htmlspecialchars($fila['Fecha']) ?></td>
                                    <td><?= htmlspecialchars($fila['Motivo']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">
                                    No hay incidencias registradas.
                                </td>
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
