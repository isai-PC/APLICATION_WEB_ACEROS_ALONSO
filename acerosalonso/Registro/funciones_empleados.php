<?php
require_once "../conexion.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ===================== LISTAR EMPLEADOS =====================
function listar_empleados($conn) {
    $sql = "SELECT e.Id_Empleado, e.Nombre, e.Apellido_Paterno, e.Apellido_Materno, 
                   e.Correo, e.Telefono, d.Departamento, p.Puesto, t.Usuario
            FROM empleados e
            INNER JOIN departamentos d ON e.Id_Departamento = d.Id_Departamento
            INNER JOIN puestos p ON e.Id_Puesto = p.Id_Puesto
            INNER JOIN tipo_usuario t ON e.Id_Tipo_Usuario = t.Id_Tipo_Usuario
            ORDER BY e.Id_Empleado ASC";
    $res = $conn->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    //Para evitar bucles use MYSQLI_ASSOC que obtiene todas las filas como un arreglo asociativo
}

// ===================== LISTAR EMPLEADOS PAGINADOS =====================
function listar_empleados_paginated($conn, int $inicio = 0, int $por_pagina = 10): array {
    // Total
    $resCount = $conn->query("SELECT COUNT(*) AS cnt FROM empleados");
    $total = 0;
    if ($resCount) {
        $r = $resCount->fetch_assoc();
        $total = isset($r['cnt']) ? (int)$r['cnt'] : 0;
        if (method_exists($resCount, 'free')) $resCount->free();
    }

    $sql = "SELECT e.Id_Empleado, e.Nombre, e.Apellido_Paterno, e.Apellido_Materno, 
                   e.Correo, e.Telefono, d.Departamento, p.Puesto, t.Usuario
            FROM empleados e
            INNER JOIN departamentos d ON e.Id_Departamento = d.Id_Departamento
            INNER JOIN puestos p ON e.Id_Puesto = p.Id_Puesto
            INNER JOIN tipo_usuario t ON e.Id_Tipo_Usuario = t.Id_Tipo_Usuario
            ORDER BY e.Id_Empleado ASC
            LIMIT " . (int)$inicio . "," . (int)$por_pagina;

    $res = $conn->query($sql);
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    if ($res && method_exists($res, 'free')) $res->free();

    return ['rows' => $rows, 'total' => $total];
}

// ===================== OBTENER UN EMPLEADO =====================
function obtener_empleado($conn, $id) {
    $id = trim($id);
    if (!is_numeric($id)) return null;

    $id = intval($id);

    $stmt = $conn->prepare("SELECT * FROM empleados WHERE Id_Empleado=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $emp = $stmt->get_result()->fetch_assoc(); //Aqui si lo uso por que solo es uno
    $stmt->close();
    return $emp;
}

// ===================== CREAR EMPLEADO =====================
function crear_empleado($conn, $datos) {

    // ========== VALIDACIONES ==========
    $nombre   = trim($datos['nombre']);
    $apaterno = trim($datos['apaterno']);
    $amaterno = trim($datos['amaterno']);
    $correo   = trim($datos['correo']);
    $telefono = trim($datos['telefono']);
    $contra   = trim($datos['contrasena']);

    $tipo  = trim($datos['tipo_usuario']);
    $depto = trim($datos['departamento']);
    $puesto = trim($datos['puesto']);

    // Validar numéricos
    if (!is_numeric($tipo) || !is_numeric($depto) || !is_numeric($puesto)) return false;

    // Validar teléfono numérico
    if (!is_numeric($telefono)) return false;

    // Validar correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) return false;

    $sql = "INSERT INTO empleados 
            (Nombre, Apellido_Paterno, Apellido_Materno, Correo, Telefono, Contrasena, 
            Id_Tipo_Usuario, Id_Departamento, Id_Puesto)
            VALUES (?, ?, ?, ?, ?, MD5(?), ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssiii",
        $nombre, $apaterno, $amaterno, $correo, $telefono,
        $contra, $tipo, $depto, $puesto
    );

    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

// ===================== ACTUALIZAR EMPLEADO =====================
function actualizar_empleado($conn, $id, $datos) {

    $id = trim($id);
    if (!is_numeric($id)) return false;
    $id = intval($id);

    // Validaciones
    $nombre   = trim($datos['nombre']);
    $apaterno = trim($datos['apaterno']);
    $amaterno = trim($datos['amaterno']);
    $correo   = trim($datos['correo']);
    $telefono = trim($datos['telefono']);
    $contra   = trim($datos['contrasena']);

    $tipo  = trim($datos['tipo_usuario']);
    $depto = trim($datos['departamento']);
    $puesto = trim($datos['puesto']);

    if (!is_numeric($tipo) || !is_numeric($depto) || !is_numeric($puesto)) return false;
    if (!is_numeric($telefono)) return false;
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) return false;

    $sql = "UPDATE empleados SET 
            Nombre=?, Apellido_Paterno=?, Apellido_Materno=?, Correo=?, Telefono=?, 
            Contrasena=MD5(?), Id_Tipo_Usuario=?, Id_Departamento=?, Id_Puesto=? 
            WHERE Id_Empleado=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssiiii",
        $nombre, $apaterno, $amaterno, $correo, $telefono,
        $contra, $tipo, $depto, $puesto, $id
    );

    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

// ===================== ELIMINAR EMPLEADO =====================
function eliminar_empleado($conn, $id) {

    $id = trim($id);
    if (!is_numeric($id)) return false;

    $id = intval($id);

    try {
        $stmt = $conn->prepare("DELETE FROM empleados WHERE Id_Empleado=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        return true;

    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) {
            return "NO_BORRADO_RELACIONES";
        }
        return "ERROR: " . $e->getMessage();
    }
}

// ===================== CARGAR COMBOS =====================
function obtener_departamentos($conn) {
    $res = $conn->query("SELECT Id_Departamento, Departamento FROM departamentos");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function obtener_puestos($conn) {
    $res = $conn->query("SELECT Id_Puesto, Puesto FROM puestos");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function obtener_tipos_usuario($conn) {
    $res = $conn->query("SELECT Id_Tipo_Usuario, Usuario FROM tipo_usuario");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

// ===================== ACTUALIZAR PERFIL =====================
function actualizar_perfil_empleado($conn, $id, $datos) {

    $id = trim($id);
    if (!is_numeric($id)) return false;
    $id = intval($id);

    $correo   = trim($datos['correo']);
    $telefono = trim($datos['telefono']);

    if (!is_numeric($telefono)) return false;
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) return false;

    if (empty($datos['contrasena'])) {

        $sql = "UPDATE empleados SET
                Correo=?, Telefono=?
                WHERE Id_Empleado=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $correo, $telefono, $id);

    } else {

        $contra = trim($datos['contrasena']);

        $sql = "UPDATE empleados SET
                Correo=?, Telefono=?, Contrasena=MD5(?)
                WHERE Id_Empleado=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $correo, $telefono, $contra, $id);
    }

    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}
?>

