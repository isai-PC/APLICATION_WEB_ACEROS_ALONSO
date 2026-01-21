<?php
// vitacatalog.php
include 'conexion.php'; // Asegúrate de que la ruta sea correcta

// Verificamos si llega el parámetro "id_categoria" en la URL
if (isset($_GET['id_categoria'])) {
    $id_categoria = (int) $_GET['id_categoria']; // Convertimos a número por seguridad
} else {
    $id_categoria = null;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo por Categoría</title>
    <link rel="stylesheet" href="Estilos/StyleProductos.css?v=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fa;
            text-align: center;
            padding: 50px;
        }
        .contenedor {
            background: #fff;
            border-radius: 15px;
            padding: 40px;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .id {
            font-size: 22px;
            font-weight: bold;
            color: #007bff;
            margin-top: 10px;
        }
        a {
            display: inline-block;
            margin-top: 25px;
            text-decoration: none;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background 0.3s;
        }
        a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <?php if ($id_categoria !== null): ?>
            <h1>Catálogo de Categoría Seleccionada</h1>
            <p class="id">ID de categoría recibido: <?php echo htmlspecialchars($id_categoria); ?></p>
        <?php else: ?>
            <h1>No se ha recibido ninguna categoría.</h1>
        <?php endif; ?>

        <a href="paginaPrincipal.php">⬅ Volver a inicio</a>
    </div>
</body>
</html>
