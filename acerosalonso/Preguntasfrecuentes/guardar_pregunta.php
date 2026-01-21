<?php
include '../conexion.php';

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $pregunta = trim($_POST['pregunta']);
    $respuesta = trim($_POST['respuesta']);

    // VALIDACIONES
    if ($pregunta === "" || $respuesta === "") {
        $error = "No se permiten campos vacíos.";
    } elseif (strlen($pregunta) < 3) {
        $error = "La pregunta debe tener al menos 3 caracteres.";
    } elseif (strlen($respuesta) < 3) {
        $error = "La respuesta debe tener al menos 3 caracteres.";
    } elseif (is_numeric($pregunta)) {
        $error = "La pregunta no puede ser solo números.";
    } elseif (is_numeric($respuesta)) {
        $error = "La respuesta no puede ser solo números.";
    }

    // SI HAY ERROR → mostrar alert EN LA MISMA PÁGINA
    if ($error !== "") {
        echo "<script>alert('$error'); window.history.back();</script>";
        exit;
    }

    // Insertar si todo está bien
    $sql = "INSERT INTO preguntas_frecuentes (pregunta, respuesta) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $pregunta, $respuesta);

    if ($stmt->execute()) {
        header("Location: index.php?msg=ok");
        exit();
    } else {
        echo "<script>alert('Error al guardar en la base de datos.'); window.history.back();</script>";
        exit();
    }
}
?>
