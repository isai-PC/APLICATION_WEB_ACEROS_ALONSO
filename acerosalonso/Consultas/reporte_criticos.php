<?php
session_start();
require_once('funciones.php');

// Seguridad y caché institucional de Aceros Alonso
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['id_empleado'])) {
    header("Location: ../Login/Login.php");
    exit;
}

// Lógica de filtrado por mes (Año actual 2026)
$mesSeleccionado = $_POST['mes_filtro'] ?? date('n');
$reporte = obtenerPersonalCriticoPorMes($mesSeleccionado, 2026);

$usuarioHeader = "Usuario: " . ($_SESSION['nombre'] ?? '');

function getNombreMes($n) {
    $meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    return $meses[$n] ?? "Mes no válido";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Crítico | Aceros Alonso</title>
    <link rel="stylesheet" href="consulta.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../Privado/StylesGenerales.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="estilos_especiales.css?v=<?php echo time(); ?>">
</head>

<body>
    <header>
        <section class="logo">
            <img src="../ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
            <h2>Aceros Alonso</h2>
        </section>
        <nav>
            <ul>
                <li><?= htmlspecialchars($usuarioHeader) ?></li>
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
        <img src="../Accesibilidad/accesibilidad.png" alt="Accesibilidad">
    </div>
    <iframe id="menuAccesibilidad" src="../Accesibilidad/MenuAccesibilidad.html" class="accesibilidad-frame"></iframe>

    <main>
        <section class="contenedor-reporte">
            <h1>RESUMEN DE PERSONAL CRÍTICO (<?= strtoupper(getNombreMes($mesSeleccionado)) ?>)</h1>

            <div class="contenedor-consultas-rapidas">
                <h3>Consultas Rapidas</h3>
                <select name="opcion_especial" class="select-navegacion" onchange="if(this.value) window.location.href=this.value;">
                    <option value="">-- Seleccione una consulta --</option>
                    <option value="reporte_criticos.php" selected>Empleados cuyas faltas superan el promedio mensual</option>
                    <option value="reporte_resumen.php">Total de personal por departamento</option>
                    <option value="reporte_asistencias_mes.php">Total de asistencias por departamento en cada mes</option>
                    <option value="reporte_ausentes.php">Días sin registro de asistencia por empleado</option>
                </select>
            </div>

            <form method="POST" class="filtro-mes-container">
                <label>Analizar Mes:</label>
                <select name="mes_filtro" class="select-mes-filtro">
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>" <?= $i == $mesSeleccionado ? 'selected' : '' ?>>
                            <?= getNombreMes($i) ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="btn-filtro-naranja">Actualizar</button>
            </form>

            <section class="caja-resultados">
                <?php if (!empty($reporte)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th>Departamento</th>
                                <th>Faltas en <?= getNombreMes($mesSeleccionado) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reporte as $fila): ?>
                                <tr>
                                    <td><?= htmlspecialchars($fila['Empleado']) ?></td>
                                    <td><?= htmlspecialchars($fila['Departamento']) ?></td>
                                    <td class="resaltado-critico"><?= htmlspecialchars($fila['Total_Faltas']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="mensaje-vacio">
                        <p>No se detectó personal crítico en <?= getNombreMes($mesSeleccionado) ?>.</p>
                    </div>
                <?php endif; ?>
            </section>
        </section>
    </main>

    <footer>
        <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
    </footer>
</body>
</html>