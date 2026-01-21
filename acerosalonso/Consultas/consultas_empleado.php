<?php
// consultasEmpleado.php
session_start();
require_once('funciones.php'); // Funciones para obtener datos y reportes

// Evitar cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// 1) Validar que haya sesión iniciada
if (!isset($_SESSION['id_empleado'])) {
    $_SESSION['error'] = "Debes iniciar sesión.";
    header("Location: ../Login/Login.php");
    exit;
}

// 2) Validar que realmente sea EMPLEADO
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 1) {
    $_SESSION['error'] = "Acceso no autorizado (solo empleados).";
    header("Location: ../Login/Login.php");
    exit;
}

// 3) El id del empleado viene de la sesión
$idEmpleado = (int)$_SESSION['id_empleado'];

// Obtener datos del empleado
$datosEmpleado = obtenerDatosEmpleado($idEmpleado);
if (!$datosEmpleado) {
    $_SESSION['error'] = "Empleado no encontrado.";
    header("Location: ../Login/Login.php");
    exit;
}

$nombre       = $datosEmpleado['nombre'];
$puesto       = $datosEmpleado['puesto'];
$departamento = $datosEmpleado['departamento'];

// Variables de filtro
$fechaInicio = $_POST['fecha_inicio'] ?? '';
$fechaFin    = $_POST['fecha_fin'] ?? '';
$reporteEmpleado = [];

// Generar reporte si hay fechas y se envió el form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($fechaInicio) && !empty($fechaFin)) {
    $reporteEmpleado = obtenerReporteEmpleado($idEmpleado, $fechaInicio, $fechaFin);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="../../ACASALogoAcerosA.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Empleado</title>
    <!-- <link rel="stylesheet" href="consulta.css"> -->
    <link rel="stylesheet" href="../Privado/StylesGenerales.css?v=<?php echo time() ?>">
</head>

<body>
    <header>
        <section class="logo">
            <img src="../ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
            <h2>Aceros Alonso</h2>
        </section>
        <nav>
            <ul>
                <!-- Aquí SIGUE usando el id del empleado, pero ahora viene de la sesión -->
                <li><a href="../Registro/limitadoempleados.php?id=<?= $idEmpleado ?>">Perfil</a></li>
                <li><a href="../Login/CerrarSesion.php">Cerrar sision</a></li>
            </ul>
        </nav>
    </header>
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
            <h1>Reporte de: <?= htmlspecialchars($nombre) ?></h1>

            <!-- FORMULARIO SOLO RANGO DE FECHAS -->
            <form method="POST" class="formulario-reporte">
                <fieldset class="caja-filtros">
                    <legend>Filtros de búsqueda</legend>
                    <div class="rango-fecha-cont">
                        <label for="fecha_inicio">Rango de fecha:</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio"
                            value="<?= htmlspecialchars($fechaInicio) ?>" required>
                        <span>a</span>
                        <input type="date" id="fecha_fin" name="fecha_fin"
                            value="<?= htmlspecialchars($fechaFin) ?>" required>
                    </div>

                    <section class="campos-empleado">
                        <label>ID Empleado: <input type="text" value="<?= htmlspecialchars($idEmpleado) ?>" readonly></label>
                        <label>Nombre: <input type="text" value="<?= htmlspecialchars($nombre) ?>" readonly></label>
                        <label>Puesto: <input type="text" value="<?= htmlspecialchars($puesto) ?>" readonly></label>
                        <label>Departamento: <input type="text" value="<?= htmlspecialchars($departamento) ?>" readonly></label>
                    </section>

                    <button type="submit" name="generar" class="btn-generar">Generar</button>
                </fieldset>
            </form>

            <!-- TABLA DE RESULTADOS -->
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
        </section>
    </main>

    <footer>
        <div class="footer-secciones">
            <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
    </footer>

</body>

</html>