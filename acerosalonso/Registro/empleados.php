<?php
include 'funciones_empleados.php';
// Inicializar sesión y usuario
session_start();
$usuarioHeader = '';
if (isset($_SESSION['id_empleado'])) {
  $nombre = $_SESSION['nombre'] ?? 'Usuario';
  $usuarioHeader = "Usuario: " . htmlspecialchars($nombre);
}

// Paginación
$pagina_actual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
$por_pagina = 10;
$inicio = ($pagina_actual - 1) * $por_pagina;

$pag = listar_empleados_paginated($conn, $inicio, $por_pagina);
$lista = $pag['rows'];
$total_registros = $pag['total'];
$total_paginas = $total_registros > 0 ? (int)ceil($total_registros / $por_pagina) : 1;

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Empleados</title>
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
        <li class="menu">
          <a href="#">Mision Vision</a>
          <ul class="ContenidoMenu">

            <li><a href="../MisionVision/editar_mision.php">Mision</a></li>
            <li><a href="../MisionVision/editar_vision.php">Vision</a></li>
            <li><a href="../MisionVision/editar_info.php">Por que elegirnos</a></li>
          </ul>
        </li>
        <li><a href="#">Empleados</a></li>
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

    <?php if (!empty($_GET['err']) && $_GET['err'] === 'rel'): ?>
      <div class="notice notice--err">
        No se puede eliminar este empleado porque tiene registros relacionados.
      </div>
    <?php endif; ?>

    <?php if (!empty($_GET['err']) && $_GET['err'] !== 'rel'): ?>
      <div class="notice notice--err">Error al procesar.</div>
    <?php endif; ?>





    <h2>Lista de empleados(Total: <?php echo $total_registros ?>)</h2>
    <div>
      <!-- ================================================ LLevar al formulario para crear uno nuevo ============================================== -->
      <a href="formulario.php">
        <button class="btnAgregar" type="button" name="accion" value="crear" title="Añadir nuevo empleado">Agregar Empleado</button>
      </a>
      <!-- ============================================================================================== -->
    </div>
    <br>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Departamento</th>
            <th>Puesto</th>
            <th>Usuario</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <!-- ============================================================================================== -->
        <tbody>
          <?php foreach ($lista as $r): ?>
            <tr>
              <td><?= (int)$r['Id_Empleado'] ?></td>
              <td><?= htmlspecialchars($r['Nombre'] . ' ' . $r['Apellido_Paterno']) ?></td>
              <td><?= htmlspecialchars($r['Correo']) ?></td>
              <td><?= htmlspecialchars($r['Telefono']) ?></td>
              <td><?= htmlspecialchars($r['Departamento']) ?></td>
              <td><?= htmlspecialchars($r['Puesto']) ?></td>
              <td><?= htmlspecialchars($r['Usuario']) ?></td>
              <td>
                <div class="row-actions">
                  <!-- ================================================ ENVIA AL FOMRULARIO EDITAR ENVIANDO EL ID============================================== -->
                  <a href="editar.php?id=<?= (int)$r['Id_Empleado'] ?>" class="btn btn-edit">Editar</a>
                  <!-- ============================================================================================== -->

                  <form action="guardar_empleado.php" method="post" onsubmit="return confirm('¿Seguro que deseas eliminar este empleado?')">
                    <input type="hidden" name="id_empleado" value="<?= (int)$r['Id_Empleado'] ?>">
                    <input type="hidden" name="accion" value="eliminar">
                    <button class="btn btn-danger" type="submit">Borrar</button>
                  </form>
                </div>

              </td>
            </tr>
          <?php endforeach; ?>
          <!-- ============================================================================================== -->
        </tbody>
      </table>
    </div>
    <?php if (!empty($total_paginas) && $total_paginas > 1): ?>
      <div class="paginacion" style="margin-top:16px; text-align:center;">
        <?php if ($pagina_actual > 1): ?>
          <a href="?pagina=<?php echo $pagina_actual - 1; ?>">« Anterior</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
          <?php if ($i == $pagina_actual): ?>
            <span class="activo" style="margin:0 6px; font-weight:bold;"><?php echo $i; ?></span>
          <?php else: ?>
            <a href="?pagina=<?php echo $i; ?>" style="margin:0 6px;"><?php echo $i; ?></a>
          <?php endif; ?>
        <?php endfor; ?>

        <?php if ($pagina_actual < $total_paginas): ?>
          <a href="?pagina=<?php echo $pagina_actual + 1; ?>">Siguiente »</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </main>

  <footer>
    <div class="footer-sections"></div>
    <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
  </footer>

</body>

</html>