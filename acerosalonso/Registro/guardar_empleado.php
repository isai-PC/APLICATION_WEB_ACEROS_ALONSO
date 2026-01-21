<?php
include 'funciones_empleados.php';

function volver($ok = true) { //si no se apasa ningun valor al llamar a la función sera true
    $qs = $ok ? "ok=1" : "err=1";
    header("Location: empleados.php?$qs");
    exit;
}

$accion = $_POST['accion'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    volver(false);
}

if ($accion === 'crear') {
    $ok = crear_empleado($conn, $_POST);
    volver($ok);

} elseif ($accion === 'actualizar') {       //verifica si todos los caracteres de la cadena son dígitos del 0 al 9
    if (!isset($_POST['id_empleado']) || !ctype_digit($_POST['id_empleado'])) {
        volver(false);
    }
    $ok = actualizar_empleado($conn, (int)$_POST['id_empleado'], $_POST);
    volver($ok);

} elseif ($accion === 'actualizar_perfil') {
    if (!isset($_POST['id_empleado']) || !ctype_digit($_POST['id_empleado'])) {
        volver(false);
    }
    $ok = actualizar_perfil_empleado($conn, (int)$_POST['id_empleado'], $_POST);
    header("Location:../Consultas/consultas_empleado.php?$qs");

} elseif ($accion === 'eliminar') {
    if (!isset($_POST['id_empleado']) || !ctype_digit($_POST['id_empleado'])) {
        volver(false);
    }
    $resultado = eliminar_empleado($conn, (int)$_POST['id_empleado']);
    if ($resultado === true) {
        volver(true);
    } elseif ($resultado === "NO_BORRADO_RELACIONES") {
        header("Location: empleados.php?err=rel");
        exit;
    } else {
        volver(false);
    }

} else {
    volver(false);
}

?>

