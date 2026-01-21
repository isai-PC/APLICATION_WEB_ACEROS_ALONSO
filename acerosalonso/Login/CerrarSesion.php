<?php
session_start();

// Borrar variables de sesión
session_unset();
// Destruir sesión
session_destroy();

// Evitar que el navegador guarde la página anterior
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Redirigir al login
header("Location: ../paginaPrincipal.php");
exit;
?>
