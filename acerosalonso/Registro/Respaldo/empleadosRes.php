
<?php
//====================================
//YA NO SIRVE ESTA OAGINA SE USABA ANTES
//====================================


include 'funciones_empleados.php';

// Si hay un ID, cargamos los datos de ese empleado
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$emp = $id ? obtener_empleado($conn, $id) : null;

// Cargamos listas para los select
$departamentos = obtener_departamentos($conn);
$puestos = obtener_puestos($conn);
$usuarios = obtener_tipos_usuario($conn);
$lista = listar_empleados($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Empleados</title>
  <link rel="stylesheet" href="empleados.css?=<?= time() ?>">
</head>
<body>

<header>
  <section class="logo">
    <img src="ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
    <h2>Aceros Alonso</h2>
  </section>
  <nav>
    <ul>
      <li><a href="../paginaPrincipal.php">Inicio</a></li>
      <li><a href="../Consultas/consultas.php">Consultas</a></li>
    </ul>
  </nav>
</header>

<main class="admin-detalle">
  <h1>Registro de empleados</h1>

  <?php if (!empty($_GET['ok'])): ?>
    <div class="notice">Operación exitosa.</div>
  <?php endif; ?>
  <?php if (!empty($_GET['err'])): ?>
    <div class="notice notice--err">Error al procesar.</div>
  <?php endif; ?>

  <form action="guardar_empleado.php" method="post">
    <input type="hidden" name="id_empleado" value="<?= $emp['Id_Empleado'] ?? 0 ?>">

    <div class="form-grid">
      <label for="nombre">Nombre
        <input id="nombre" type="text" name="nombre" required value="<?= $emp['Nombre'] ?? '' ?>">
      </label>

      <label for="apaterno">Apellido Paterno
        <input id="apaterno" type="text" name="apaterno" required value="<?=$emp['Apellido_Paterno'] ?? '' ?>">
      </label>

      <label for="amaterno">Apellido Materno
        <input id="amaterno" type="text" name="amaterno" required value="<?= htmlspecialchars($emp['Apellido_Materno'] ?? '') ?>">
      </label>

      <label for="correo">Correo
        <input id="correo" type="email" name="correo" required value="<?= htmlspecialchars($emp['Correo'] ?? '') ?>">
      </label>

      <label for="telefono">Teléfono
        <input id="telefono" type="text" name="telefono" required value="<?= htmlspecialchars($emp['Telefono'] ?? '') ?>">
      </label>

      <label for="contrasena">Contraseña
        <input id="contrasena" type="password" name="contrasena" <?= $id ? '' : 'required' ?>>
      </label>

      <label for="departamento">Departamento
        <select id="departamento" name="departamento" required>
          <option value="">-- Selecciona --</option>
          <?php foreach ($departamentos as $d): ?>
            <option value="<?= (int)$d['Id_Departamento'] ?>"
              <?= ($emp && (int)$emp['Id_Departamento'] === (int)$d['Id_Departamento']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($d['Departamento']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label for="puesto">Puesto
        <select id="puesto" name="puesto" required>
          <option value="">-- Selecciona --</option>
          <?php foreach ($puestos as $p): ?>
            <option value="<?= (int)$p['Id_Puesto'] ?>"
              <?= ($emp && (int)$emp['Id_Puesto'] === (int)$p['Id_Puesto']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($p['Puesto']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label for="tipo_usuario">Tipo de usuario
        <select id="tipo_usuario" name="tipo_usuario" required>
          <option value="">-- Selecciona --</option>
          <?php foreach ($usuarios as $u): ?>
            <option value="<?= (int)$u['Id_Tipo_Usuario'] ?>"
              <?= ($emp && (int)$emp['Id_Tipo_Usuario'] === (int)$u['Id_Tipo_Usuario']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($u['Usuario']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>

    <div class="actions-bar">
      <?php if ($id): ?>
        <button class="btn btn-primary" type="submit" name="accion" value="actualizar">Actualizar</button>
        <button class="btn btn-danger"  type="submit" name="accion" value="eliminar" onclick="return confirm('¿Eliminar este empleado?')">Eliminar</button>
        <a class="btn btn-neutral" href="empleados.php">Nuevo</a>
      <?php else: ?>
        <button class="btn btn-primary" type="submit" name="accion" value="crear">Guardar</button>
      <?php endif; ?>
    </div>
  </form>

  <h2>Lista de empleados</h2>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Nombre</th><th>Correo</th><th>Teléfono</th>
          <th>Departamento</th><th>Puesto</th><th>Usuario</th><th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lista as $r): ?>
          <tr>
            <td><?= (int)$r['Id_Empleado'] ?></td>
            <td><?= htmlspecialchars($r['Nombre'].' '.$r['Apellido_Paterno']) ?></td>
            <td><?= htmlspecialchars($r['Correo']) ?></td>
            <td><?= htmlspecialchars($r['Telefono']) ?></td>
            <td><?= htmlspecialchars($r['Departamento']) ?></td>
            <td><?= htmlspecialchars($r['Puesto']) ?></td>
            <td><?= htmlspecialchars($r['Usuario']) ?></td>
            <td>
              <div class="row-actions">
                <a href="empleados.php?id=<?= (int)$r['Id_Empleado'] ?>">Editar</a>
                <form action="guardar_empleado.php" method="post" onsubmit="return confirm('¿Seguro que deseas eliminar este empleado?')">
                  <input type="hidden" name="id_empleado" value="<?= (int)$r['Id_Empleado'] ?>">
                  <input type="hidden" name="accion" value="eliminar">
                  <button class="btn btn-danger" type="submit">Borrar</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

<footer>
  <div class="footer-sections"></div>
  <p class="copy">Todos los derechos reservados © 2025 Aceros Alonso</p>
</footer>

</body>
</html>