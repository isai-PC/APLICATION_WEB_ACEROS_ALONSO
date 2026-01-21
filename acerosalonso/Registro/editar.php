<?php
include 'funciones_empleados.php';
session_start();
$usuarioHeader = '';
if (isset($_SESSION['id_empleado'])) {
  $nombre = $_SESSION['nombre'] ?? 'Usuario';
  $usuarioHeader = "Usuario: " . htmlspecialchars($nombre);
}
// Si hay un ID, cargamos los datos de ese empleado
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$emp = $id ? obtener_empleado($conn, $id) : null;

// Cargamos listas para los select
$departamentos = obtener_departamentos($conn);
$puestos = obtener_puestos($conn);
$usuarios = obtener_tipos_usuario($conn);
$lista = listar_empleados($conn);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Empleados</title>
  <link rel="stylesheet" href="empleados.css?=<?= time() ?>">
  <link rel="stylesheet" href="../Privado/StylesGenerales.css?v=<?php echo time(); ?>">
</head>

<body>

  <header>
    <section class="logo">
      <img src="ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
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
        <li><a href="../MisionVision/editar_misionvision.php">MisionVision</a></li>
        <li><a href="../Registro/empleados.php">Usuarios</a></li>
        <li><a href="../Ubicacion/listar_ubicaciones.php">Ubicaciones</a></li>
        <li><a href="../Preguntasfrecuentes/index.php">Preguntas Frecuentes</a></li>
        <li><a href="../contacto/admin_contacto.php">Contacto</a></li>
        <li><a href="../terminos/admin_terminos.php">Terminos y Condiciones</a></li>
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


  <main class="admin-detalle">
    <h1>Registro de empleados</h1>

    <?php if (!empty($_GET['ok'])): ?>
      <div class="notice">Operación exitosa.</div>
    <?php endif; ?>
    <?php if (!empty($_GET['err'])): ?>
      <div class="notice notice--err">Error al procesar.</div>
    <?php endif; ?>

    <section class="container2">
      <form action="guardar_empleado.php" method="post">
        <input type="hidden" name="id_empleado" value="<?= $emp['Id_Empleado'] ?? 0 ?>">

        <div class="form-grid">
          <label for="nombre">Nombre
            <input id="nombre" type="text" name="nombre" required
              pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,50}"
              title="Solo letras y espacios, entre 2 y 50 caracteres"
              value="<?= $emp['Nombre'] ?? '' ?>">
          </label>

          <label for="apaterno">Apellido Paterno
            <input id="apaterno" type="text" name="apaterno" required
              pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,50}"
              title="Solo letras y espacios, entre 2 y 50 caracteres"
              value="<?= $emp['Apellido_Paterno'] ?? '' ?>">
          </label>

          <label for="amaterno">Apellido Materno
            <input id="amaterno" type="text" name="amaterno" required
              pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,50}"
              title="Solo letras y espacios, entre 2 y 50 caracteres"
              value="<?= htmlspecialchars($emp['Apellido_Materno'] ?? '') ?>">
          </label>

          <label for="correo">Correo
            <input id="correo" type="email" name="correo" required
              title="Ingrese un correo válido con formato: ejemplo@dominio.com"
              value="<?= htmlspecialchars($emp['Correo'] ?? '') ?>">
          </label>



          <label for="telefono">Teléfono
            <input id="telefono" type="tel" name="telefono" required
              pattern="\d{10}"
              title="Ingrese un número de 10 dígitos"
              value="<?= htmlspecialchars($emp['Telefono'] ?? '') ?>">
          </label>


          <label for="contrasena">Contraseña
            <input id="contrasena" type="password" name="contrasena" required
              pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*#).{8,}"
              title="La contraseña debe tener mínimo 8 caracteres, al menos 1 mayúscula, 1 minúscula, 1 número y 1 carácter especial (#)">
            <button type="button" class="login-toggle-btn" onclick="togglePasswordVisibility()" title="Mostrar contraseña" aria-pressed="false" aria-label="Mostrar contraseña">
              <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false" aria-hidden="true">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <span class="sr-only" style="position: absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0;">Mostrar contraseña</span>
            </button>
          </label>

          <label for="departamento">Departamento
            <select id="departamento" name="departamento" required>
              <option value="">-- Selecciona --</option>
              <?php foreach ($departamentos as $d): ?>
                <option value="<?= (int)$d['Id_Departamento'] ?>"
                  <?= ($emp && (int)$emp['Id_Departamento'] === (int)$d['Id_Departamento']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($d['Departamento']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>

          <label for="puesto">Puesto
            <select id="puesto" name="puesto" required>
              <option value="">-- Selecciona --</option>
              <?php foreach ($puestos as $p): ?>
                <option value="<?= (int)$p['Id_Puesto'] ?>"
                  <?= ($emp && (int)$emp['Id_Puesto'] === (int)$p['Id_Puesto']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($p['Puesto']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>

          <label for="tipo_usuario">Tipo de usuario
            <select id="tipo_usuario" name="tipo_usuario" required>
              <option value="">-- Selecciona --</option>
              <?php foreach ($usuarios as $u): ?>
                <option value="<?= (int)$u['Id_Tipo_Usuario'] ?>"
                  <?= ($emp && (int)$emp['Id_Tipo_Usuario'] === (int)$u['Id_Tipo_Usuario']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($u['Usuario']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>

        <div class="actions-bar">
          <button class="btn btn-primary" type="submit" name="accion" value="actualizar">Actualizar</button>
          <a href="empleados.php" class="btn btn-neutral">Cancelar</a>
        </div>
      </form>
    </section>

    <br><br><br>
  </main>


  <footer>
    <div class="footer-sections"></div>
    <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
  </footer>
  <script>
    function togglePasswordVisibility() {
      var passwordInput = document.getElementById("contrasena");
      var btn = document.querySelector('.login-toggle-btn');
      var eyeIcon = document.getElementById("eye-icon");
      if (!passwordInput || !btn) return;

      var isNowVisible = passwordInput.type === "password";
      passwordInput.type = isNowVisible ? "text" : "password";

      // Actualizar atributos ARIA y texto para lectores de pantalla
      btn.setAttribute('aria-pressed', isNowVisible ? 'true' : 'false');
      btn.setAttribute('aria-label', isNowVisible ? 'Ocultar contraseña' : 'Mostrar contraseña');
      var sr = btn.querySelector('.sr-only');
      if (sr) sr.textContent = isNowVisible ? 'Ocultar contraseña' : 'Mostrar contraseña';

      // Indicador visual
      try {
        eyeIcon.style.color = isNowVisible ? '#ff8c42' : '#666';
      } catch (e) {}

      // Mantener el foco en el botón para accesibilidad
      btn.focus();
    }
  </script>
</body>

</html>