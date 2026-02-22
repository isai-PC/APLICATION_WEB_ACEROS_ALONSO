<?php
session_start();
require_once('funciones.php');

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['id_empleado'])) {
    header("Location: ../Login/Login.php");
    exit;
}

$departamentos = obtenerDepartamentos();
$usuarioHeader = "Usuario: " . ($_SESSION['nombre'] ?? '');
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
            <li><a href="../ProcedimietosC/consultas.php">Consultas 2</a></li>
        </ul>
    </nav>
</aside>

<script src="../Accesibilidad/accesi.js?v=<?php echo time(); ?>"></script>

<main>
<section class="contenedor-reporte">
<h1>REPORTES</h1>

<div style="background:#dfe6ed;padding:20px;border-radius:8px;border:1px solid #ccc;text-align:center;margin-bottom:20px;">
    <h3 style="margin-bottom:10px;color:#333;">Consultas Rapidas</h3>
    <select style="width:95%;padding:12px;border-radius:5px;border:1px solid #ccc;font-size:16px;cursor:pointer;background:white;"
        onchange="if(this.value) window.location.href=this.value;">
        <option value="">-- Seleccione una consulta --</option>
        <option value="reporte_criticos.php">Empleados cuyas faltas superan el promedio general</option>
        <option value="reporte_resumen.php">Total de personal por departamento</option>
        <option value="reporte_asistencias_mes.php">Total de asistencias por departamento en cada mes</option>
        <option value="reporte_ausentes.php">Días sin registro de asistencia por empleado</option>
        <option value="Consultas_3/Reporte_Asistencias_FechaEspécifica.php">Asistencias por departamento por mes especifico</option>
        <option value="reporte_De_incidencias.php">Detalle completo de incidencias</option>
        <option value="reporte_dese_asistencia.php">Empleados con mas asistencias que el promedio</option>
    </select>
</div>

  
<br>

<form id="formEmpleado" class="formulario-reporte" onsubmit="return false;">

<fieldset class="caja-filtros">
<legend>Filtros de búsqueda</legend>

<div class="rango-fecha-cont">
<label>Rango de fecha:</label>
<input type="date" id="fecha_inicio">
<span>a</span>
<input type="date" id="fecha_fin">
</div>

<section class="campos-empleado">
<label>ID Empleado:
<input type="text" id="id_empleado">
</label>

<label>Nombre:
<input type="text" id="nombre_empleado" readonly>
</label>

<label>Puesto:
<input type="text" id="puesto_empleado" readonly>
</label>

<label>Departamento:
<input type="text" id="departamento_empleado" readonly>
</label>
</section>

<button type="button" class="btn-generar" onclick="cargarReporteEmpleado()">Generar</button>
</fieldset>

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
<tbody id="tabla-empleado">
<tr>
<td colspan="6" style="text-align:center;">No hay datos para mostrar</td>
</tr>
</tbody>
</table>
</section>

</form>
</section>
</main>

<footer>
<p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
</footer>

<script src="reporteEmpleado.js?v=<?php echo time(); ?>"></script>

</body>
</html>