<?php
include 'funciones_faq.php';
$datos = obtenerPreguntaPorId($conn, $_GET['id']);
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
    <title>Editar Pregunta</title>
    <!-- <link rel="stylesheet" href="faq.css?v=<?php echo time();?>"> -->
     <link rel="stylesheet" href="../Privado/StylesGenerales.css?v=<?php echo time();?>">
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
    <h1>Editar Pregunta</h1>

    <form action="procesar_editar.php" method="POST" class="formPregunta">
        <input type="hidden" name="id" value="<?php echo $datos['id']; ?>">

        <label>Pregunta:</label>
        <input type="text" name="pregunta" value="<?php echo $datos['pregunta']; ?>" required>

        <label>Respuesta:</label>
        <textarea name="respuesta" required><?php echo $datos['respuesta']; ?></textarea>

        <div class="button-container">
            <button type="submit" class="btnGuardar">Actualizar</button>
            <a href="index.php" class="btn btn-neutral">Cancelar</a>
        </div>
    </form>
</div>
<footer>
    <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
</footer>

</body>
</html>
