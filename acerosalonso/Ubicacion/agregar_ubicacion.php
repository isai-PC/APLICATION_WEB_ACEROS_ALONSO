<?php
session_start();
$usuarioHeader = '';
if (isset($_SESSION['id_empleado'])) {
  $nombre = $_SESSION['nombre'] ?? 'Usuario';
  $usuarioHeader = "Usuario: " . htmlspecialchars($nombre);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Ubicación</title>
    <link rel="stylesheet" href="../Privado/StylesGenerales.css?v=<?php echo time();?>">
    <link rel="stylesheet" href="ubicaciones.css">
     <link rel="icon" href="../ACASALogoAcerosA.png" type="image/png" sizes="16px">
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
<div class="container">
    <h1>Agregar Ubicación</h1>

    <form action="procesar_agregar.php" method="POST" enctype="multipart/form-data" class="formPregunta">
       <label>Descripción:</label>
            <textarea name="descripcion"
              required
              pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s,.\-]+$"
              title="Solo letras, espacios y signos básicos. No se permiten números."></textarea>

        <label>Imagen:</label>
        <input type="file" name="imagen" id="imagen" required accept="image/*">
    
        <label>URL Google Maps:</label>
        <input type="url"
               name="url"
               required
               pattern="https?://.*"
               title="Debe ser una URL válida.">
    
        <div class="button-container">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="listar_ubicaciones.php" class="btn btn-neutral">Cancelar</a>
        </div>
    </form>
</div>
<footer>
    <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
</footer>
</body>
</html>
