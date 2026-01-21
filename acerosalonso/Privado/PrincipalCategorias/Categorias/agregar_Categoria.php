<?php
// --- Guardar en: Privado/PrincipalCategorias/editar_Categoria.php ---
require_once __DIR__ . '/../../../conexion.php';
require_once 'funciones_Categoria.php';

// Inicializar sesión y usuario
session_start();
$usuarioHeader = '';
if (isset($_SESSION['id_empleado'])) {
    $nombre = $_SESSION['nombre'] ?? 'Usuario';
    $usuarioHeader = "Usuario: " . htmlspecialchars($nombre);
}

function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }

// Detectar si estamos editando (viene ID) o creando
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$cat = $id ? obtener_categoria($conn, $id) : null;

// Rellenar variables (si es nuevo, van vacías)
$nombre = $cat['nombre_categoria'] ?? '';
$texto  = $cat['texto_secundario'] ?? '';
$img    = $cat['imagen_categoria'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Admin | <?php echo $id ? 'Editar' : 'Nueva'; ?> Categoría</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="../../../ACASALogoAcerosA.png">
  <link rel="stylesheet" href="../../StylesGenerales.css?v=<?php echo time(); ?>">
  <style>
      .form-actions { margin-top: 20px; }
      .thumb-preview { max-height: 100px; margin-top: 10px; display: block; }
  </style>
</head>

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
                <li class="active"><a href="listado_Categoria.php">Categorias</a></li>
                <li class="menu">
                    <a href="#">Mision Vision</a>
                    <ul class="ContenidoMenu">
                      
                             <li><a href="../MisionVision/editar_mision.php">Mision</a></li>
                <li><a href="../MisionVision/editar_vision.php">Vision</a></li>
                <li><a href="../MisionVision/editar_info.php">Por que elegirnos</a></li>
                    </ul>
                </li>
                <li><a href="../../../Registro/empleados.php">Usuarios</a></li>
                <li><a href="../../../Ubicacion/listar_ubicaciones.php">Ubicaciones</a></li>
                <li><a href="../../../Preguntasfrecuentes/index.php">Preguntas Frecuentes</a></li>
                <li><a href="../../../contacto/admin_contacto.php">Contacto</a></li>
                <li><a href="../../../terminos/admin_terminos.php">Terminos y Condiciones</a></li>
                <li><a href="../../../paginaPrincipal.php">Inicio</a></li>
                
            </ul>
        </nav>
        
    </aside>
    
  <script src="../../../Accesibilidad/accesi.js?v=<?php echo time(); ?>"></script>
  <div id="btnAccesibilidad" onclick="event.stopPropagation(); toggleMenuAccesibilidad()">
    <img src="../../../Accesibilidad/accesibilidad.png" style="width: 100%; height:100%; object-fit:cover;">
  </div>
  <iframe id="menuAccesibilidad" src="../../../Accesibilidad/MenuAccesibilidad.html" class="accesibilidad-frame"></iframe>

  <main class="admin-detalle">

    <h1><?php echo $id ? 'Editar Categoría' : 'Nueva Categoría'; ?></h1>
    
    <?php if (isset($_GET['error'])): ?>
      <div style="background:#f8d7da; color:#721c24; padding:10px; margin-bottom:15px; border-radius:5px;">
        <?php 
          if ($_GET['error'] === 'vacio') {
            echo 'Error: El nombre es obligatorio.';
          } elseif ($_GET['error'] === 'imagen_invalida') {
            echo 'Error: La imagen no es válida. Solo se aceptan JPG, JPEG, PNG, WEBP o GIF (máx 3MB).';
          } else {
            echo 'Error: Hubo un problema al guardar los datos.';
          }
        ?>
      </div>
    <?php endif; ?>

<div class="container2">
    <form action="guardar_categoria.php" method="post" enctype="multipart/form-data">
      
      <input type="hidden" name="id_categoria" value="<?php echo $id; ?>">
      <input type="hidden" name="accion" value="<?php echo $id ? 'actualizar' : 'crear'; ?>">

      <div class="row2">
        <div>
          <label>Nombre de la categoría </label>
          <input type="text" name="nombre_categoria" required value="<?php echo h($nombre); ?>" style="width:100%; padding:8px;">
        </div>
        <br>
        <div>
          <label>Texto secundario</label>
          <input type="text" name="texto_secundario" value="<?php echo h($texto); ?>" style="width:100%; padding:8px;">
        </div>
        <br>
        <div>
            <label>Imagen (jpg, jpeg, png, webp, gif)</label>
            <?php if ($img): ?>
                <div style="margin:5px 0;">
                  <p style="font-size:0.9em; color:#666;">Imagen actual:</p>
                  <img class="thumb-preview" src="../../../imagenes/<?php echo h($img); ?>" alt="">
                </div>
                <p style="font-size:0.8em;">Selecciona un archivo para cambiarla:</p>
            <?php endif; ?>
            <input type="file" id="inputImagen" name="imagen_categoria" accept="image/jpeg, image/png, image/webp, image/gif">
        </div>
      </div>

      <div class="form-actions">
        <button class="btn btn-primary" type="submit"><?php echo $id ? 'Actualizar' : 'Guardar'; ?></button>
        <a href="listado_Categoria.php" class="btn btn-neutral">Cancelar</a>
      </div>

    </form>
    </div>


  </main>
  
  <footer>
    <div class="footer-sections">
      <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
    </div>
  </footer>

<script>
document.getElementById('inputImagen').addEventListener('change', function() {
    var archivo = this.files[0];
    if (archivo) {
        var tipo = archivo.type;
        var validos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/jpg'];
        
        if (!validos.includes(tipo)) {
            // Alerta del navegador
            alert('FORMATO NO VÁLIDO\nSelecciona solo imágenes (JPG, PNG, WEBP, GIF).');
            this.value = ''; // Borra la selección
        }
    }
});
</script>

</body>
</html>