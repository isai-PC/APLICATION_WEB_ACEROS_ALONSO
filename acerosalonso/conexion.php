<?php
// Datos de conexión
$host = "127.0.0.1";       // Servidor local
$usuario = "u138650717_root2";     
//$usuario = "u138650717_root";         // Usuario por defecto en XAMPP
$password = "R_asi1234";     
$baseDeDatos = "u138650717_asistenciasMov";// Contraseña por defecto vacía
//$baseDeDatos = "u138650717_r_asi"; // Nombre de la base de datos que creaste

// Crear conexión
$conn = new mysqli($host, $usuario, $password, $baseDeDatos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} 


//$conn->close();
?>

