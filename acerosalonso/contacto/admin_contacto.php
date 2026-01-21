<?php
require_once '../conexion.php';

session_start();
$usuarioHeader = '';
if (isset($_SESSION['id_empleado'])) {
    $nombre = $_SESSION['nombre'] ?? 'Usuario';
    $usuarioHeader = "Usuario: " . htmlspecialchars($nombre);
}

$mensaje = "";
$tipo_mensaje = "";

// Obtener el registro (solo 1)
$sql = "SELECT * FROM contacto_info LIMIT 1";
$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    $info = $res->fetch_assoc();
} else {
    $info = [
        'id'           => '',
        'dias'         => '',
        'horario'      => '',
        'telefono'     => '',
        'whatsapp'     => '',
        'correo'       => '',
        'direccion'    => '',
        'red_social_1' => '',
        'red_social_2' => '',
        'red_social_3' => '',
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id        = $_POST['id'] ?? '';
    $dias      = $_POST['dias'] ?? '';
    $horario   = $_POST['horario'] ?? '';
    $telefono  = $_POST['telefono'] ?? '';
    $whatsapp  = $_POST['whatsapp'] ?? '';
    $correo    = $_POST['correo'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $r1        = $_POST['red_social_1'] ?? '';
    $r2        = $_POST['red_social_2'] ?? '';
    $r3        = $_POST['red_social_3'] ?? '';

    if ($id === '' || $id == 0) {
        $sqlSave = "INSERT INTO contacto_info 
                (dias, horario, telefono, whatsapp, correo, direccion, red_social_1, red_social_2, red_social_3)
                VALUES (?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sqlSave);
        $stmt->bind_param(
            "sssssssss",
            $dias,
            $horario,
            $telefono,
            $whatsapp,
            $correo,
            $direccion,
            $r1,
            $r2,
            $r3
        );
    } else {
        $sqlSave = "UPDATE contacto_info SET 
                    dias=?, horario=?, telefono=?, whatsapp=?, correo=?, direccion=?, 
                    red_social_1=?, red_social_2=?, red_social_3=? 
                WHERE id=?";
        $stmt = $conn->prepare($sqlSave);
        $stmt->bind_param(
            "sssssssssi",
            $dias,
            $horario,
            $telefono,
            $whatsapp,
            $correo,
            $direccion,
            $r1,
            $r2,
            $r3,
            $id
        );
    }

    if ($stmt->execute()) {
        $mensaje = "Información guardada correctamente.";
        $tipo_mensaje = "ok";

        $res2 = $conn->query("SELECT * FROM contacto_info LIMIT 1");
        if ($res2 && $res2->num_rows > 0) {
            $info = $res2->fetch_assoc();
        }
    } else {
        $mensaje = "Error al guardar: " . $conn->error;
        $tipo_mensaje = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Aceros Alonso | Editar información de contacto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Usa la hoja de estilos general -->
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
    <!--===========================S================================== -->
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
    <main class="admin-detalle">
        <h1>Editar información de contacto</h1>

        <div class="container">

            <?php if ($mensaje !== ""): ?>
                <?php if ($tipo_mensaje === "ok"): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php else: ?>
                    <div class="notice notice--err">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($info['id']); ?>">


                <div class="row">

                    <div>
                        <label for="dias">
                            Días
                            <small style="font-weight: normal; color:#555; margin-top:4px;">
                                Ejemplo: Lunes a Viernes
                            </small>
                        </label>
                        <input type="text" id="dias" name="dias"
                            value="<?php echo htmlspecialchars($info['dias']); ?>">
                    </div>

                    <div>
                        <label for="horario">
                            Horario
                            <small style="font-weight: normal; color:#555; margin-top:4px;">
                                Ejemplo: 8:00 a.m. - 6:00 p.m.
                            </small>
                        </label>
                        <input type="text" id="horario" name="horario"
                            value="<?php echo htmlspecialchars($info['horario']); ?>">
                    </div>

                    <div>
                        <label for="telefono">Teléfono</label>
                        <input type="text" id="telefono" name="telefono"
                            inputmode="numeric" maxlength="10"
                            oninput="this.value = this.value.replace(/[^0-9]/g,'');"
                            value="<?php echo htmlspecialchars($info['telefono']); ?>">
                    </div>

                    <div>
                        <label for="whatsapp">WhatsApp</label>
                        <input type="text" id="whatsapp" name="whatsapp"
                            inputmode="numeric" maxlength="10"
                            oninput="this.value = this.value.replace(/[^0-9]/g,'');"
                            value="<?php echo htmlspecialchars($info['whatsapp']); ?>">
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label for="correo">Correo electrónico</label>
                        <input type="email" id="correo" name="correo"
                            value="<?php echo htmlspecialchars($info['correo']); ?>">
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label for="direccion">Dirección</label>
                        <textarea id="direccion" name="direccion"><?php echo htmlspecialchars($info['direccion']); ?></textarea>
                    </div>

                    <div>
                        <label for="red_social_1">
                            Red social 1
                            <small style="font-weight: normal; color:#555; margin-top:4px;">
                                Ejemplo: Facebook / @miempresa
                            </small>
                        </label>
                        <input type="text" id="red_social_1" name="red_social_1"
                            value="<?php echo htmlspecialchars($info['red_social_1']); ?>">
                    </div>

                    <div>
                        <label for="red_social_2">
                            Red social 2
                            <small style="font-weight: normal; color:#555; margin-top:4px;">
                                Ejemplo: Instagram / @miempresa
                            </small>
                        </label>
                        <input type="text" id="red_social_2" name="red_social_2"
                            value="<?php echo htmlspecialchars($info['red_social_2']); ?>">
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label for="red_social_3">
                            Red social 3
                            <small style="font-weight: normal; color:#555; margin-top:4px;">
                                Ejemplo: TikTok, X, etc.
                            </small>
                        </label>
                        <input type="text" id="red_social_3" name="red_social_3"
                            value="<?php echo htmlspecialchars($info['red_social_3']); ?>">
                    </div>

                </div>

                <div class="button-container" style="margin-top: 20px; text-align:left">
                    <button type="submit" class="btnGuardar">Guardar cambios</button>
                    <a href="../Consultas/consultas.php" class="btn btn-neutral"> Cancelar</a>
                </div>

            </form>
        </div>
    </main>

    <footer>
        <div class="footer-sections">
        </div>
        <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
    </footer>

</body>

</html>