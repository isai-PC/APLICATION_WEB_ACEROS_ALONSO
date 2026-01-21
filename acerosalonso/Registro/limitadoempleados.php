<?php
include 'funciones_empleados.php';

// Si hay un ID por GET, cargamos los datos de ese empleado (viene desde consultasEmpleado.php)
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Si no hay ID  mostrar error
if (!$id) {
  die("<h2>Acceso no autorizado</h2>");
}

$emp = obtener_empleado($conn, $id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../../ACASALogoAcerosA.png">
  <title>Perfil del Empleado</title>
  <link rel="stylesheet" href="../Privado/StylesGenerales.css?v=<?php echo time() ?>">
</head>
<body>

<header>
  <section class="logo">
    <img src="ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
    <h2>Aceros Alonso</h2>
  </section>
  <nav>
    <ul>
      <li><a href="../Consultas/consultas_empleado.php?= $id ?>">Consultas</a></li>
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
<main class="admin-detalle">
  <h1>Mi Perfil</h1>
  
  <section class="container2">

  <?php if (!empty($_GET['ok'])): ?>
    <div class="notice">Datos actualizados correctamente.</div>
  <?php endif; ?>
  <?php if (!empty($_GET['err'])): ?>
    <div class="notice notice--err">Error al actualizar los datos.</div>
  <?php endif; ?>

  <?php if ($emp): ?>
  <!-- Formulario del perfil -->
  <form action="guardar_empleado.php" method="post">
    <input type="hidden" name="id_empleado" value="<?= $emp['Id_Empleado'] ?>">
    <input type="hidden" name="accion" value="actualizar_perfil">

    <div class="form-grid">
      <label for="nombre">Nombre completo
        <input id="nombre" type="text" readonly
               value="<?= htmlspecialchars($emp['Nombre'].' '.$emp['Apellido_Paterno'].' '.$emp['Apellido_Materno']) ?>">
      </label>

      <label for="correo">Correo
        <input id="correo" type="email" name="correo" required
               value="<?= htmlspecialchars($emp['Correo']) ?>">
      </label>

      <label for="telefono">Teléfono
        <input id="telefono" type="text" name="telefono" required
               value="<?= htmlspecialchars($emp['Telefono']) ?>">
      </label>

      <label for="contrasena">Contraseña
        <input id="contrasena" type="password" name="contrasena">
      </label>
    </div>

    <div class="actions-bar">
      <button class="btn btn-primary" type="submit">Actualizar Perfil</button>
      <a href="../Consultas/consultas_empleado.php" class="btn btn-neutral">Cancelar</a>
    </div>
  </form>
  <?php else: ?>
    <p>No se encontró la información del empleado.</p>
  <?php endif; ?>
 </section>
</main>

<footer>
  <div class="footer-sections"></div>
  <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
</footer>

</body>
</html>
