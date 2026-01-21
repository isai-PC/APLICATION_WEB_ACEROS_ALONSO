<?php
require_once '../conexion.php';
session_start();
$usuarioHeader = '';
if (isset($_SESSION['id_empleado'])) {
  $nombre = $_SESSION['nombre'] ?? 'Usuario';
  $usuarioHeader = "Usuario: " . htmlspecialchars($nombre);
}
// Adaptar conexión si usa $con
if (!isset($conn) && isset($con)) {
    $conn = $con;
}

$mensaje = "";
$tipo_mensaje = "";

$id       = $_GET['id'] ?? '';
$titulo   = "";
$contenido = "";

// =========================
//   CARGAR DATOS SI EDITA
// =========================
if ($id !== "") {
    $sql = "SELECT * FROM terminos_condiciones WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $titulo   = $row['titulo'];
        $contenido = $row['contenido'];
    }
    $stmt->close();
}

// =========================
//   GUARDAR (INSERT / UPDATE)
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idPost       = $_POST['id'] ?? '';
    $tituloPost   = trim($_POST['titulo'] ?? '');
    $contPost     = trim($_POST['contenido'] ?? '');

    if ($tituloPost === "" || $contPost === "") {
        $mensaje = "El título y el contenido son obligatorios.";
        $tipo_mensaje = "error";

    } else {

        if ($idPost === "") {
            // INSERT
            $sql = "INSERT INTO terminos_condiciones (titulo, contenido) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $tituloPost, $contPost);
            $stmt->execute();
            $stmt->close();

            header("Location: admin_terminos.php?msg=creado");
            exit();

        } else {
            // UPDATE
            $sql = "UPDATE terminos_condiciones SET titulo = ?, contenido = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $tituloPost, $contPost, $idPost);
            $stmt->execute();
            $stmt->close();

            header("Location: admin_terminos.php?msg=editado");
            exit();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= $id === "" ? "Agregar Término" : "Editar Término" ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Privado/StylesGenerales.css?v=<?php echo time(); ?>">
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
            <li><a href="../Privado/PrincipalCategorias/listadodetalle.php">Productos</a></li>
            <li><a href="../Privado/PrincipalCategorias/Categorias/listado_Categoria.php">Categorias</a></li>
            <li><a href="../MisionVision/editar_misionvision.php">MisionVision</a></li>
            <li><a href="../Registro/empleados.php">Usuarios</a></li>
            <li><a href="../Ubicacion/listar_ubicaciones.php">Ubicaciones</a></li>
            <li><a href="../Preguntasfrecuentes/index.php">Preguntas Frecuentes</a></li>
            <li><a href="../contacto/admin_contacto.php">Contacto</a></li>
            <li><a href="../terminos/admin_terminos.php">Términos y Condiciones</a></li>
            <li><a href="../../paginaPrincipal.php">Inicio</a></li>
        </ul>
    </nav>
</aside>

<!-- ACCESIBILIDAD -->
<script src="../Accesibilidad/accesi.js?v=<?php echo time(); ?>"></script>
<div id="btnAccesibilidad" onclick="event.stopPropagation(); toggleMenuAccesibilidad()">
    <img src="../Accesibilidad/accesibilidad.png" style="width: 100%; height:100%; object-fit:cover;">
</div>
<iframe id="menuAccesibilidad" src="../Accesibilidad/MenuAccesibilidad.html" class="accesibilidad-frame"></iframe>

<main class="admin-detalle">

    <div class="container">

        <h1><?= $id === "" ? "Agregar Nuevo Término" : "Editar Término" ?></h1>
        <p>Llena el siguiente formulario.</p>

        <?php if ($mensaje !== ""): ?>
            <?php if ($tipo_mensaje === "error"): ?>
                <div class="notice notice--err"><?= htmlspecialchars($mensaje); ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <form action="agregar_terminos.php" method="post">

            <!-- ID (solo si edita) -->
            <input type="hidden" name="id" value="<?= htmlspecialchars($id); ?>">

            <label for="titulo">
                Título:
                <input type="text" id="titulo" name="titulo"
                       value="<?= htmlspecialchars($titulo); ?>" required>
            </label>

            <label for="contenido">
                Contenido:
                <textarea id="contenido" name="contenido" rows="7" required><?= htmlspecialchars($contenido); ?></textarea>
            </label>

            <div class="button-container" style="text-align:left;">
                <button type="submit" class="btn btn-primary">
                    <?= $id === "" ? "Guardar" : "Actualizar" ?>
                </button>

                <a href="admin_terminos.php" class="btn btn-neutral">Cancelar</a>
            </div>

        </form>

    </div>

</main>

<footer>
    <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
</footer>

</body>
</html>
