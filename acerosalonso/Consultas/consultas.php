<?php
session_start(); // por si luego se quiere usar sesiones

// consultas.php
require_once('funciones.php');

// Evitar que se vea con la flecha "atrás"
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// Evitar caché en páginas privadas
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Si NO hay sesión iniciada, redirige al login
if (!isset($_SESSION['id_empleado'])) {
    header("Location: ../Login/Login.php");
    exit;
}

// Cargar lista de departamentos para el combo box
$departamentos = obtenerDepartamentos();
// Detectar modo: empleado o departamento
$modo = $_POST['modo'] ?? 'empleado';

// Variables comunes
$idEmpleado = $_POST['id_empleado'] ?? '';
$fechaInicio = $_POST['fecha_inicio'] ?? '';
$fechaFin = $_POST['fecha_fin'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$puesto = $_POST['puesto'] ?? '';
$departamento = $_POST['departamento'] ?? '';
$selectDepto = $_POST['select_depto'] ?? '';

//$usuarioHeader = "Admin - " . $nombreUsuario = "Administrador";
$usuarioHeader = "Usuario: " . ($_SESSION['nombre'] ?? '');


// Resultados
$reporteEmpleado = [];
$reporteDepartamento = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar'])) {

    if ($modo === 'empleado') {
        // AUTOLLENADO DE CAMPOS
        if (!empty($idEmpleado)) {
            $datosEmpleado = obtenerDatosEmpleado($idEmpleado);
            $nombre = $datosEmpleado['nombre'];
            $puesto = $datosEmpleado['puesto'];
            $departamento = $datosEmpleado['departamento'];
        }

        if (!empty($idEmpleado) && !empty($fechaInicio) && !empty($fechaFin)) {
            $reporteEmpleado = obtenerReporteEmpleado($idEmpleado, $fechaInicio, $fechaFin);
        }
    } elseif ($modo === 'departamento') {
        if (!empty($selectDepto) && !empty($fechaInicio) && !empty($fechaFin)) {
            // selectDepto contiene el Id_Departamento
            $reporteDepartamento = ReporteDepartamentos($selectDepto, $fechaInicio, $fechaFin);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aceros Alonso | Reportes</title>
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
                <!-- <li><a href="../MisionVision/editar_misionvision.php">MisionVision</a></li>-->
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
        <section class="contenedor-reporte">
            <h1>REPORTES</h1>


            <div style="background: #dfe6ed; padding: 20px; border-radius: 8px; border: 1px solid #ccc; text-align: center; margin-bottom: 20px;">
                <h3 style="margin-bottom: 10px; color: #333;">Consultas Rapidas</h3>

                <select name="opcion_especial"
                    style="width: 95%; padding: 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 16px; cursor: pointer; background: white;"
                    onchange="if(this.value) window.location.href=this.value;">

                    <option value="">-- Seleccione una consulta --</option>
                    <option value="reporte_criticos.php">Empleados cuyas faltas superan el promedio general</option>
                    <option value="reporte_resumen.php">Total de personal por departamento</option>
                    <option value="reporte_asistencias_mes.php">Total de asistencias por departamento en cada mes</option>
                    <option value="reporte_ausentes.php">Días sin registro de asistencia por empleado</option>
                    <option value="Consultas_3/Reporte_Asistencias_FechaEspécifica.php">Asistencias por departamento por mes especifico</option>
                </select>
            </div>
            <!-- BOTONES DE MODO  -->
            <form method="POST" class="form-opciones">
                <button type="submit" name="modo" value="empleado" class="<?= $modo == 'empleado' ? 'activo' : 'desactivo' ?>">Por Empleado</button>
                <button type="submit" name="modo" value="departamento" class="<?= $modo == 'departamento' ? 'activo' : 'desactivo' ?>">Por Departamento</button>
            </form>
            <br>

            <!-- djgifjiogoifmhoimf lolis gordas-->
            <!-- djgifjiogoifmhoimf -->
            <!-- HSKFKKFNDKSN jsfjs-->

            <!-- FORMULARIO DE EMPLEADO -->
            <?php if ($modo === 'empleado'): ?>
                <form method="POST" class="formulario-reporte">
                    <input type="hidden" name="modo" value="empleado">
                    <fieldset class="caja-filtros">
                        <legend>Filtros de búsqueda</legend>
                        <div class="rango-fecha-cont">
                            <label for="fecha_inicio">Rango de fecha:</label>g
                            <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio) ?>" required>
                            <span>a</span>
                            <input type="date" id="fecha_fin" name="fecha_fin" value="<?= htmlspecialchars($fechaFin) ?>" required>
                        </div>

                        <section class="campos-empleado">
                            <label>ID Empleado: <input type="text" name="id_empleado" value="<?= htmlspecialchars($idEmpleado) ?>"></label>
                            <label>Nombre: <input type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>" readonly></label>
                            <label>Puesto: <input type="text" name="puesto" value="<?= htmlspecialchars($puesto) ?>" readonly></label>
                            <label>Departamento: <input type="text" name="departamento" value="<?= htmlspecialchars($departamento) ?>" readonly></label>
                        </section>

                        <button type="submit" name="generar" class="btn-generar">Generar</button>
                    </fieldset>

                    <!-- TABLA DE RESULTADOS (EMPLEADO) -->
                    <section class="caja-resultados">
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora Entrada</th>
                                    <th>Hora Salida</th>
                                    <th>Horas Trabajadas</th>
                                    <th>Horas Extra</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($reporteEmpleado)): ?>
                                    <?php foreach ($reporteEmpleado as $fila): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($fila['Fecha']) ?></td>
                                            <td><?= htmlspecialchars($fila['Hora Entrada']) ?></td>
                                            <td><?= htmlspecialchars($fila['Hora Salida']) ?></td>
                                            <td><?= htmlspecialchars($fila['Horas Trabajadas']) ?></td>
                                            <td><?= htmlspecialchars($fila['Horas Extra']) ?></td>
                                            <td><?= htmlspecialchars($fila['Tipo']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align:center;">No hay datos para mostrar</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </section>
                </form>

                <!-- FORMULARIO DE DEPARTAMENTO -->
            <?php elseif ($modo === 'departamento'): ?>
                <form method="POST" class="formulario-reporte">
                    <input type="hidden" name="modo" value="departamento">
                    <fieldset class="caja-filtros">
                        <legend>Seleccionar Departamento</legend>
                        <div class="rango-fecha-cont">
                            <label for="fecha_inicio">Rango de fecha:</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio) ?>">
                            <span>a</span>
                            <input type="date" id="fecha_fin" name="fecha_fin" value="<?= htmlspecialchars($fechaFin) ?>">
                        </div>

                        <label>Departamento:
                            <select name="select_depto">
                                <option value="">Seleccione un departamento...</option>
                                <?php foreach ($departamentos as $d): ?>
                                    <option value="<?= htmlspecialchars($d['Id_Departamento']) ?>" <?= $selectDepto == $d['Id_Departamento'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($d['Departamento']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <button type="submit" name="generar" class="btn-generar">Generar</button>
                    </fieldset>
                    <!-- TABLA DE RESULTADOS (DEPARTAMENTO) -->
                    <section class="caja-resultados">
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Asistencias</th>
                                    <th>Faltas</th>
                                    <th>Retardos</th>
                                    <th>Permisos</th>
                                    <th>Horas Trabajadas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($reporteDepartamento)): ?>
                                    <?php foreach ($reporteDepartamento as $fila): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($fila['Fecha']) ?></td>
                                            <td><?= htmlspecialchars($fila['Asistencias']) ?></td>
                                            <td><?= htmlspecialchars($fila['Faltas']) ?></td>
                                            <td><?= htmlspecialchars($fila['Retardos']) ?></td>
                                            <td><?= htmlspecialchars($fila['Permisos']) ?></td>
                                            <td><?= htmlspecialchars($fila['HorasTrabajadas']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align:center;">No hay datos para mostrar</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </section>
                </form>
            <?php endif; ?>

        </section>
    </main>

    <footer>
        <div class="footer-sections">

        </div>
        <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
    </footer>

</body>

</html>