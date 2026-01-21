<?php
// --- Guardar en: Privado/PrincipalCategorias/listado_categorias.php ---
require_once 'funciones_Categoria.php';

// Inicializar sesión y usuario
session_start();
$usuarioHeader = '';
if (isset($_SESSION['id_empleado'])) {
  $nombre = $_SESSION['nombre'] ?? 'Usuario';
  $usuarioHeader = "Usuario: " . htmlspecialchars($nombre);
}

// Helpers
function h($v)
{
  return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}
// Página actual (basename) para marcar el enlace activo en el aside
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
/* PAGINACION  */
// OBTENER EL FILTRO
$id_filtro = isset($_GET['filtro_id']) ? (int)$_GET['filtro_id'] : 0;

$pagina_actual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
$por_pagina = 10; // Cantidad de registros por página que se muestran
$inicio = ($pagina_actual - 1) * $por_pagina;
/* CALCULAR PAGINAS */

// CONSULTAS
// La lista para llenar el <select>
$categorias_para_select = listar_todas_las_categorias_simple($conn);

// Obtener filas paginadas y total
$pag = listar_categorias_paginated($conn, $id_filtro, $inicio, $por_pagina);
$lista_tabla = $pag['rows'];
$total_registros = $pag['total'];
$total_paginas = $total_registros > 0 ? (int)ceil($total_registros / $por_pagina) : 1;

$IMG_DIR_PUBLIC = '../../../imagenes/';
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">

  <title>Admin | Listado Categorías</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="../../../ACASALogoAcerosA.png">
  <link rel="stylesheet" href="../../StylesGenerales.css?v=<?php echo time(); ?>">

  <style>
    .alerta-admin {
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 5px;
      text-align: center;
      font-weight: bold;
    }

    .alerta-exito {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .alerta-error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .top-bar {
      /*display: flex; 
        justify-content: space-between; 
        align-items: center; */
      margin-bottom: 20px;
      /* flex-wrap: wrap; 
        gap: 15px;*/
    }

    /* .filtro-box {
        background-color: #f9f9f9; 
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }*/

    /*.thumb { max-height: 50px; object-fit: contain; }*/
  </style>

<body>
  <header>
    <section class="logo">
      <img src="../../../ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
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
        <li><a href="../../../Consultas/consultas.php">Consultas</a></li>
        <li><a href="../../../Privado/PrincipalCategorias/listadodetalle.php">Productos</a></li>
        <li class="active"><a href="">Categorias</a></li>
        <li class="menu">
          <a href="#">Mision Vision</a>
          <ul class="ContenidoMenu">
            <li><a href="../MisionVision/editar_mision.php">Mision</a></li>
            <li><a href="../MisionVision/editar_vision.php">Vision</a></li>
            <li><a href="../MisionVision/editar_info.php">Por que elegirnos</a></li>
          </ul>
        <li><a href="../../../Registro/empleados.php">Empleados</a></li>
        <li><a href="../../../Ubicacion/listar_ubicaciones.php">Ubicaciones</a></li>
        <li><a href="../../../Preguntasfrecuentes/index.php">Preguntas Frecuentes</a></li>
        <li><a href="../../../contacto/admin_contacto.php">Contacto</a></li>
        <li><a href="../../../terminos/admin_terminos.php">Terminos y Condiciones</a></li>
        <li><a href="../../../paginaPrincipal.php">Inicio</a></li>

      </ul>
    </nav>

  </aside>
  <!-- ========================================================================================= -->
  <script src="../../../Accesibilidad/accesi.js?v=<?php echo time(); ?>"></script>
  <div id="btnAccesibilidad" onclick="event.stopPropagation(); toggleMenuAccesibilidad()">
    <img src="../../../Accesibilidad/accesibilidad.png" style="width: 100%; height:100%; object-fit:cover;">
  </div>
  <iframe id="menuAccesibilidad" src="../../../Accesibilidad/MenuAccesibilidad.html" class="accesibilidad-frame"></iframe>
  <!-- {}}}}}}======================================================================================= -->
  <main class="admin-detalle">

    <h1>Administrar Categorías</h1>

    <?php if (isset($_GET['mensaje'])): ?>
      <div class="alerta-admin <?php echo ($_GET['mensaje'] == 'error') ? 'alerta-error' : 'alerta-exito'; ?>">
        <?php
        $m = $_GET['mensaje'];
        if ($m == 'guardado') echo '¡Categoría creada correctamente!';
        elseif ($m == 'actualizado') echo '¡Categoría actualizada correctamente!';
        elseif ($m == 'eliminado') echo '¡Categoría eliminada correctamente!';
        else echo 'Ocurrió un error.';
        ?>
      </div>
    <?php endif; ?>

    <h2>Listado de Categorías(Total: <?php echo $total_registros; ?>)</h2>

    <div class="top-bar">
      <a href="agregar_Categoria.php"><button class="btnAgregar">Agregar Categoría</button></a>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Texto Secundario</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($lista_tabla)): ?>
            <?php foreach ($lista_tabla as $r): ?>
              <tr>
                <td><?php echo (int)$r['id_categoria']; ?></td>
                <td>
                  <?php if (!empty($r['imagen_categoria'])): ?>
                    <img class="thumb" src="<?php echo $IMG_DIR_PUBLIC . h($r['imagen_categoria']); ?>" alt="">
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td><?php echo h($r['nombre_categoria']); ?></td>
                <td><?php echo h($r['texto_secundario']); ?></td>

                <td class="actions">
                  <a href="agregar_Categoria.php?id=<?php echo (int)$r['id_categoria']; ?>" class="btn btn-edit">
                    Editar
                  </a>
                  <form action="guardar_categoria.php" method="post" style="display:inline" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?');">
                    <input type="hidden" name="id_categoria" value="<?php echo (int)$r['id_categoria']; ?>">
                    <input type="hidden" name="accion" value="eliminar">
                    <button class="btn btn-danger" type="submit">Eliminar</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5">No se encontraron resultados con ese filtro.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if (!empty($total_paginas) && $total_paginas > 1): ?>
      <div class="paginacion">
        <?php if ($pagina_actual > 1): ?>
          <a href="?pagina=<?php echo $pagina_actual - 1; ?>&filtro_id=<?php echo $id_filtro; ?>">« Anterior</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
          <?php if ($i == $pagina_actual): ?>
            <span class="activo"><?php echo $i; ?></span>
          <?php else: ?>
            <a href="?pagina=<?php echo $i; ?>&filtro_id=<?php echo $id_filtro; ?>"><?php echo $i; ?></a>
          <?php endif; ?>
        <?php endfor; ?>

        <?php if ($pagina_actual < $total_paginas): ?>
          <a href="?pagina=<?php echo $pagina_actual + 1; ?>&filtro_id=<?php echo $id_filtro; ?>">Siguiente »</a>
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