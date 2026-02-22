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

// Lógica para el filtrado dinamico
$mesSeleccionado = $_POST['mes_filtro'] ?? date('n');
$reporte = obtenerPersonalCriticoPorMes($mesSeleccionado);

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
    <link rel="stylesheet" href="consultas.css?v=<?php echo time(); ?>">
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
            <h1>RESUMEN DE PERSONAL CRÍTICO (<?= strtoupper(getNombreMes($mesSeleccionado)) ?>)</h1>

            <div style="background: #dfe6