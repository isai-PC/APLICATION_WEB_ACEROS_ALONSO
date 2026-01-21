<?php
session_start();

require_once('../conexion.php'); // Conexión desde carpeta Login
// Evitar que el usuario regrese al login con "atrás"
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);


    $sql = "SELECT Id_Empleado, Id_Tipo_Usuario, Nombre
            FROM empleados
            WHERE Correo = ? AND Contrasena = MD5(?)";

    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $correo, $contrasena);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        $id = $fila['Id_Empleado'];
        $tipo = $fila['Id_Tipo_Usuario'];

         // GUARDAR DATOS EN SESIÓN
        $_SESSION['id_empleado']   = $id;
        $_SESSION['tipo_usuario']  = $tipo;
        $_SESSION['correo']        = $correo;
        $_SESSION['nombre'] = $fila['Nombre'];//borrar si no funciona


        if ($tipo == 2) {
            header("Location: ../Consultas/consultas.php");
        } elseif ($tipo == 1) {
            header("Location: ../Consultas/consultas_empleado.php");
            
        } else {
            $_SESSION['error'] = "Tipo de usuario no válido.";
            header("Location: Login.php");
        }
        exit;
    } else {
        $_SESSION['error'] = "Correo o contraseña incorrectos.";
        header("Location: Login.php");
        exit;
    }
}
?>

