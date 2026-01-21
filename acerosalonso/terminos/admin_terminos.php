<?php
require_once '../conexion.php';

session_start();
$usuarioHeader = '';
if (isset($_SESSION['id_empleado'])) {
    $nombre = $_SESSION['nombre'] ?? 'Usuario';
    $usuarioHeader = "Usuario: " . htmlspecialchars($nombre);
}


if (!isset($conn)) {
    if (isset($con)) {
        $conn = $con;
    } elseif (isset($conexion)) {
        $conn = $conexion;
    }
}

if (!isset($conn)) {
    die("Error: no se pudo establecer la conexión a la base de datos.");
}

$mensaje = "";
$tipo_mensaje = "";

// =========================
//   MENSAJES POR GET
// =========================
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === "creado") {
        $mensaje = "Término agregado correctamente.";
        $tipo_mensaje = "ok";
    } elseif ($_GET['msg'] === "editado") {
        $mensaje = "Término actualizado correctamente.";
        $tipo_mensaje = "ok";
    } elseif ($_GET['msg'] === "eliminado") {
        $mensaje = "Término eliminado correctamente.";
        $tipo_mensaje = "ok";
    }
}

// =========================
//   ELIMINAR (POST)
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'eliminar') {
    $id = intval($_POST['id'] ?? 0);

    if ($id > 0) {
        $sql  = "DELETE FROM terminos_condiciones WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: admin_terminos.php?msg=eliminado");
            exit();
        } else {
            $mensaje = "Error al eliminar el término.";
            $tipo_mensaje = "error";
        }
        $stmt->close();
    } else {
        $mensaje = "ID de término no válido para eliminar.";
        $tipo_mensaje = "error";
    }
}

// =========================
//   LISTA DE TÉRMINOS
// =========================
$sqlLista = "SELECT * FROM terminos_condiciones ORDER BY id ASC";
$lista = $conn->query($sqlLista);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Administrar Términos y Condiciones</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Privado/StylesGenerales.css?v=<?php echo time() ?>">
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
                <li><a href="../Registro/empleados.php">Usuarios</a></li>
                <li><a href="../Ubicacion/listar_ubicaciones.php">Ubicaciones</a></li>
                <li><a href="../Preguntasfrecuentes/index.php">Preguntas Frecuentes</a></li>
                <li><a href="../contacto/admin_contacto.php">Contacto</a></li>
                <li><a href="../terminos/admin_terminos.php">Terminos y Condiciones</a></li>
                <li><a href="../../paginaPrincipal.php">Inicio</a></li>
            </ul>
        </nav>
    </aside>

    <!--=========================== ACCESIBILIDAD ================================== -->
    <script src="../Accesibilidad/accesi.js?v=<?php echo time(); ?>"></script>
    <div id="btnAccesibilidad" onclick="event.stopPropagation(); toggleMenuAccesibilidad()">
        <img src="../Accesibilidad/accesibilidad.png" style="width: 100%; height:100%; object-fit:cover;">
    </div>
    <iframe
        id="menuAccesibilidad"
        src="../Accesibilidad/MenuAccesibilidad.html"
        class="accesibilidad-frame">
    </iframe>
    <!-- ========================================================================== -->

    <main class="admin-detalle">

        <div class="container">

            <h1>Lista de Términos y Condiciones</h1>
            <p>Aquí puedes ver todos los términos registrados, editarlos o eliminarlos.</p>

            <!-- MENSAJES -->
            <?php if ($mensaje !== ""): ?>
                <?php if ($tipo_mensaje === 'ok'): ?>
                    <div class="success-message">
                        <?= htmlspecialchars($mensaje); ?>
                    </div>
                <?php else: ?>
                    <div class="notice notice--err">
                        <?= htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- BOTÓN AGREGAR TÉRMINO -->
            <div style="margin:20px 0; text-align:left;">
                <a href="agregar_terminos.php"><button class="btnAgregar">Agregar nuevo</button>
                </a>
            </div>

            <!-- TABLA -->
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Título</th>
                            <th style="width:140px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($lista && $lista->num_rows > 0): ?>
                            <?php $i = 1; ?>
                            <?php while ($row = $lista->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= htmlspecialchars($row['titulo']); ?></td>
                                    <td>
                                        <div class="row-actions">
                                            <!-- EDITAR: va al formulario en agregar_terminos.php -->
                                            <a class="btn btn-edit"
                                                href="agregar_terminos.php?id=<?= $row['id']; ?>">
                                                Editar
                                            </a>

                                            <!-- ELIMINAR -->
                                            <form action="admin_terminos.php" method="post"
                                                onsubmit="return confirm('¿Seguro que deseas eliminar este término?');">
                                                <input type="hidden" name="accion" value="eliminar">
                                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                <button type="submit" class="btn btn-danger">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No hay términos registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
    <footer>
        <div class="footer-sections">
        </div>
        <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
    </footer>

</body>

</html>