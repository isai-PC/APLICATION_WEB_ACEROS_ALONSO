<?php
session_start();
require_once('funciones.php');

// Evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Validar sesión
if (!isset($_SESSION['id_empleado'])) {
    header("Location: ../Login/Login.php");
    exit;
}

// Cargar departamentos
$departamentos = obtenerDepartamentos();
$usuarioHeader = "Usuario: " . ($_SESSION['nombre'] ?? '');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aceros Alonso | Reporte por Departamento</title>
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
            <li><a href="../Login/CerrarSesion.php">Cerrar sesión</a></li>
        </ul>
    </nav>
</header>

<aside>
    <nav>
        <ul>
            <li><a href="../Privado/PrincipalCategorias/listadodetalle.php">Productos</a></li>
            <li><a href="../Privado/PrincipalCategorias/Categorias/listado_Categoria.php">Categorias</a></li>
            <li><a href="../Registro/empleados.php">Empleados</a></li>
            <li><a href="../Ubicacion/listar_ubicaciones.php">Ubicaciones</a></li>
            <li><a href="consultas.php">Consultas</a></li>
            <li><a href="../../paginaPrincipal.php">Inicio</a></li>
        </ul>
    </nav>
</aside>

<!-- Accesibilidad -->
<script src="../Accesibilidad/accesi.js?v=<?php echo time(); ?>"></script>
<div id="btnAccesibilidad" onclick="event.stopPropagation(); toggleMenuAccesibilidad()">
    <img src="../Accesibilidad/accesibilidad.png" style="width: 100%; height:100%; object-fit:cover;">
</div>
<iframe
    id="menuAccesibilidad"
    src="../Accesibilidad/MenuAccesibilidad.html"
    class="accesibilidad-frame">
</iframe>

<main>
    <section class="contenedor-reporte">
        <h1>REPORTE POR DEPARTAMENTO</h1>

        <div class="formulario-reporte">
            <fieldset class="caja-filtros">
                <legend>Filtros</legend>

                <div class="rango-fecha-cont">
                    <label>Rango de fecha:</label>
                    <input type="date" id="fecha_inicio">
                    <span>a</span>
                    <input type="date" id="fecha_fin">
                </div>

                <label>Departamento:
                    <select id="select_depto">
                        <option value="">Seleccione un departamento...</option>
                        <?php foreach ($departamentos as $d): ?>
                            <option value="<?= htmlspecialchars($d['Id_Departamento']) ?>">
                                <?= htmlspecialchars($d['Departamento']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <button type="button" class="btn-generar" onclick="generarReporte()">
                    Generar
                </button>

            </fieldset>
        </div>

        <!-- TABLA (estructura fija, tbody dinámico) -->
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
                <tbody id="tablaReporteBody">
                    <tr>
                        <td colspan="6" style="text-align:center;">
                            No hay datos para mostrar
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

    </section>
</main>

<footer>
    <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
</footer>

<!-- JS externo -->
<script src="reporteDepartamentos.js?v=<?php echo time(); ?>"></script>

</body>
</html>