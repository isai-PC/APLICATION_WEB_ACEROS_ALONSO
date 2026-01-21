<?php
// --- Guardar en: Privado/PrincipalCategorias/listadodetalle.php ---
require_once __DIR__ . '/../../conexion.php';
require_once __DIR__ . '/funcionesdetalle.php';

// Helpers
function h($v)
{
  return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

// --- LÓGICA DE PAGINACIÓN Y FILTRO ---

// Obtener filtro de categoría
$filtro_categoria = isset($_GET['filtro_categoria']) ? (int)$_GET['filtro_categoria'] : 0;

// Configuración de Paginación
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;

$registros_por_pagina = 10; // Tú pediste 10
$inicio = ($pagina_actual - 1) * $registros_por_pagina;

//  Obtener total de registros y calcular páginas
$total_registros = contar_productos($conn, $filtro_categoria);
$total_paginas = ceil($total_registros / $registros_por_pagina);

//  Obtener los productos de la página actual
$lista = listar_productos($conn, $filtro_categoria, $registros_por_pagina, $inicio);
$categorias = listar_categorias_dropdown($conn);

$IMG_DIR_PUBLIC = '../../imagenes/';
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <title>Admin | Listado de Productos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../StylesGenerales.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="styledetalle.css?v=<?php echo time(); ?>">
  <!-- <style>
  
     .alerta-admin { padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center; font-weight: bold;}
    .alerta-exito { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alerta-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    
    .filtro-box {
        padding: 10px;
        background-color: #f9f9f9;
        border: 1px solid #eee;
        border-radius: 5px;
        display: flex; align-items: center; gap: 10px;
    }

      /* ESTILOS DE PAGINACIÓN */
      .paginacion { margin-top: 20px; text-align: center; display: flex; justify-content: center; gap: 5px; }
      .paginacion a, .paginacion span {
          padding: 8px 12px; border: 1px solid #ddd; background: white; color: #333; text-decoration: none; border-radius: 5px;
      }
      .paginacion a:hover { background-color: #eee; }
      .paginacion .activo { background-color: #1a202c; color: white; border-color: #1a202c; }

  </style> -->
</head>

<body>
  <header>
    <section class="logo">
      <img src="../../ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
      <h2>Aceros Alonso</h2>
    </section>
    <nav>
      <ul>
        <li><a href="../../Consultas/consultas.php">Consultas</a></li>
      </ul>
    </nav>
  </header>

  <aside>
    <nav>
      <ul>
        <li><a href="../../Consultas/consultas.php">Comnsultas</a></li>
        <li class="active"><a href="../../Privado/PrincipalCategorias/listadodetalle.php">Productos</a></li>
        <li><a href="../../Privado/PrincipalCategorias/Categorias/listado_Categoria.php">Categorias</a></li>
        <li class="menu">
          <a href="#">Mision Vision</a>
          <ul class="ContenidoMenu">
            <li><a href="../MisionVision/editar_mision.php">Mision</a></li>
            <li><a href="../MisionVision/editar_vision.php">Vision</a></li>
            <li><a href="../MisionVision/editar_info.php">Por que elegirnos</a></li>
          </ul>
        </li>
        <li><a href="../../Registro/empleados.php">Empleados</a></li>
        <li><a href="../../Ubicacion/listar_ubicaciones.php">Ubicaciones</a></li>
        <li><a href="../../Preguntasfrecuentes/index.php">Preguntas Frecuentes</a></li>
        <li><a href="../../contacto/admin_contacto.php">Contacto</a></li>
        <li><a href="../..//terminos/admin_terminos.php">Terminos y Condiciones</a></li>
        <li><a href="../../paginaPrincipal.php">Inicio</a></li>

      </ul>
    </nav>

  </aside>


  <script src="../../Accesibilidad/accesi.js?v=<?php echo time(); ?>"></script>
  <div id="btnAccesibilidad" onclick="event.stopPropagation(); toggleMenuAccesibilidad()">
    <img src="../../Accesibilidad/accesibilidad.png" style="width: 100%; height:100%; object-fit:cover;">
  </div>
  <iframe id="menuAccesibilidad" src="../../Accesibilidad/MenuAccesibilidad.html" class="accesibilidad-frame"></iframe>

  <main class="admin-detalle">
    <h1>Administrar Productos del Catálogo</h1>

    <?php if (isset($_GET['mensaje'])): ?>
      <?php
      $m = $_GET['mensaje'];
      if ($m == 'guardado') echo '<div class="alerta exito">¡Producto registrado exitosamente!</div>';
      elseif ($m == 'actualizado') echo '<div class="alerta exito">¡Producto actualizado correctamente!</div>';
      elseif ($m == 'eliminado') echo '<div class="alerta exito">¡Producto eliminado correctamente!</div>';
      elseif ($m == 'error') echo '<div class="alerta error">Ocurrió un error al procesar la solicitud.</div>';
      ?>
    <?php endif; ?>
    <?php if (isset($_GET['error'])) echo '<div class="alerta error">Error: ID no válido.</div>'; ?>

    <div class="top-bar">
      <h2>Listado de Productos (Total: <?php echo $total_registros; ?>)</h2>
      <a href="editardetalle.php"><button class="btnAgregar">+ Agregar Nuevo Producto</button></a>
    </div>
    <br>

    <div class="filtro-container">
      <label for="filtro">Filtrar por Categoría:</label>
      <form action="" method="GET" style="display:inline; margin:0;">
        <select name="filtro_categoria" id="filtro" onchange="this.form.submit()">
          <option value="0">-- Ver Todos --</option>
          <?php foreach ($categorias as $cat): ?>
            <option value="<?php echo $cat['id_categoria']; ?>"
              <?php if ($filtro_categoria == $cat['id_categoria']) echo 'selected'; ?>>
              <?php echo h($cat['nombre_categoria']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>
    <br>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($lista)): ?>
            <?php foreach ($lista as $r): ?>
              <tr>
                <td><?php echo (int)$r['id_producto']; ?></td>
                <td>
                  <?php if (!empty($r['ImagenesProducto'])): ?>
                    <img class="thumb" src="<?php echo $IMG_DIR_PUBLIC . h($r['ImagenesProducto']); ?>" alt="">
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td><?php echo h($r['nombre_producto']); ?></td>
                <td><?php echo h($r['nombre_categoria']); ?></td>

                <td class="actions">
                  <a href="editardetalle.php?id=<?php echo (int)$r['id_producto']; ?>"><button class="btn btn-edit">Editar</button></a>
                  <form action="guardardetalle.php" method="post" style="display:inline" onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
                    <input type="hidden" name="id_producto" value="<?php echo (int)$r['id_producto']; ?>">
                    <input type="hidden" name="accion" value="eliminar">
                    <button class="btn btn-danger" type="submit">Eliminar</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" style="text-align:center; padding:20px;">No hay productos registrados en esta categoría.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($total_paginas > 1): ?>
      <div class="paginacion">
        <?php if ($pagina_actual > 1): ?>
          <a href="?pagina=<?php echo $pagina_actual - 1; ?>&filtro_categoria=<?php echo $filtro_categoria; ?>">« Anterior</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
          <?php if ($i == $pagina_actual): ?>
            <span class="activo"><?php echo $i; ?></span>
          <?php else: ?>
            <a href="?pagina=<?php echo $i; ?>&filtro_categoria=<?php echo $filtro_categoria; ?>"><?php echo $i; ?></a>
          <?php endif; ?>
        <?php endfor; ?>

        <?php if ($pagina_actual < $total_paginas): ?>
          <a href="?pagina=<?php echo $pagina_actual + 1; ?>&filtro_categoria=<?php echo $filtro_categoria; ?>">Siguiente »</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </main>

  <footer>
    <div class="footer-sections">
      <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
    </div>
  </footer>

</body>

</html>