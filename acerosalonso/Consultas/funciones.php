<?php
// Incluir la conexión
require_once(__DIR__ . '/../conexion.php');
/**
 * Función para generar reporte de un empleado en un rango de fechas
 */
function obtenerReporteEmpleado($idEmpleado, $fechaInicio, $fechaFin)
{
    global $conn; // <-- importante: usa la conexión global
    $reporte = [];

    $fechaActual = $fechaInicio;
    $fechaFinObj = new DateTime($fechaFin);

    while (new DateTime($fechaActual) <= $fechaFinObj) {
        $horaEntrada = "00:00:00";
        $horaSalida = "00:00:00";
        $horasTrabajadas = 0;
        $horasExtra = 0;
        $tipoAsistencia = "SIN REGISTRO";
        $horas = [];

        // Movimientos
        $stmtMov = $conn->prepare("SELECT Hora FROM movimientos WHERE Fecha=? AND Id_Empleado=? ORDER BY Hora ASC");
        $stmtMov->bind_param("ss", $fechaActual, $idEmpleado);
        $stmtMov->execute();
        $resultadoMov = $stmtMov->get_result();
        while ($fila = $resultadoMov->fetch_assoc()) {
            $horas[] = $fila['Hora'];
        }
        $stmtMov->close();

        // Incidencias
        $tipo = 0;
        $stmtInc = $conn->prepare("SELECT Id_Tipo_Incidencia FROM incidencias WHERE Fecha=? AND Id_Empleado=?");
        $stmtInc->bind_param("ss", $fechaActual, $idEmpleado);
        $stmtInc->execute();
        $resultadoInc = $stmtInc->get_result();
        if ($filaInc = $resultadoInc->fetch_assoc()) {
            $tipo = $filaInc['Id_Tipo_Incidencia'];
        }
        $stmtInc->close();

        // Calcular horas trabajadas
        if (count($horas) > 0) {
            $horaEntrada = $horas[0];
            $horaSalida = $horas[count($horas) - 1];
            if (count($horas) % 2 == 0) {
                for ($i = 0; $i < count($horas); $i += 2) {
                    $entrada = new DateTime($horas[$i]);
                    $salida = new DateTime($horas[$i + 1]);
                    $interval = $salida->getTimestamp() - $entrada->getTimestamp();
                    $horasTrabajadas += $interval / 3600;
                }
            }
        }

        // Tipo de asistencia y horas extra
        switch ($tipo) {
            case 1:
                $tipoAsistencia = "FALTA";
                break;
            case 2:
                $tipoAsistencia = "RETARDO";
                $horasExtra = max(0, $horasTrabajadas - 9);
                break;
            case 3:
                $tipoAsistencia = "PERMISO";
                break;
            case 4:
                $tipoAsistencia = "ASISTENCIA";
                $horasExtra = max(0, $horasTrabajadas - 9);
                break;
        }

        $reporte[] = [
            'Fecha' => (new DateTime($fechaActual))->format("d/m/Y"),
            'Hora Entrada' => $horaEntrada,
            'Hora Salida' => $horaSalida,
            'Horas Trabajadas' => floor($horasTrabajadas),
            'Horas Extra' => $horasExtra,
            'Tipo' => $tipoAsistencia
        ];

        $fechaObj = new DateTime($fechaActual);
        $fechaObj->modify('+1 day');
        $fechaActual = $fechaObj->format("Y-m-d");
    }

    return $reporte;
}

/**
 * FUNCIÓN: obtenerDepartamentos
 * Llena el combo box con ID y nombre del departamento.
 */
function obtenerDepartamentos()
{
    global $conn; // <-- clave: usa la conexión existente

    $departamentos = [];
    $sql = "SELECT Id_Departamento, Departamento FROM departamentos";
    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $departamentos[] = $fila;
        }
    }

    return $departamentos;
}





function ReporteDepartamentos($idDepartamento, $fechaInicio, $fechaFin)
{
    global $conn; // usa la conexión ya existente
    $tabla = [];

    // Obtener empleados del departamento
    $listaEmpleados = [];
    $sqlEmps = "SELECT Id_Empleado FROM empleados WHERE Id_Departamento = ?";
    $stmtEmps = $conn->prepare($sqlEmps);
    $stmtEmps->bind_param("i", $idDepartamento);
    $stmtEmps->execute();
    $resEmps = $stmtEmps->get_result();
    while ($row = $resEmps->fetch_assoc()) {
        $listaEmpleados[] = $row['Id_Empleado'];
    }
    $stmtEmps->close();

    if (empty($listaEmpleados)) {
        return []; // no hay empleados en el departamento
    }

    // Recorrer rango de fechas
    $inicio = new DateTime($fechaInicio);
    $fin = new DateTime($fechaFin);

    $fechaActual = clone $inicio;
    while ($fechaActual <= $fin) {
        $fechaStr = $fechaActual->format('Y-m-d');

        $totalAsistencias = 0;
        $totalFaltas = 0;
        $totalPermisos = 0;
        $totalRetardos = 0;
        $horasT = 0;

        // Recorrer cada empleado del departamento
        foreach ($listaEmpleados as $idEmp) {

            // Incidencias del empleado en la fecha
            $sqlInc = "SELECT Id_Tipo_Incidencia FROM incidencias WHERE Fecha = ? AND Id_Empleado = ?";
            $stmtInc = $conn->prepare($sqlInc);
            $stmtInc->bind_param("si", $fechaStr, $idEmp);
            $stmtInc->execute();
            $resInc = $stmtInc->get_result();
            while ($rowInc = $resInc->fetch_assoc()) {
                switch ((int)$rowInc['Id_Tipo_Incidencia']) {
                    case 1:
                        $totalFaltas++;
                        break;
                    case 2:
                        $totalRetardos++;
                        break;
                    case 3:
                        $totalPermisos++;
                        break;
                    case 4:
                        $totalAsistencias++;
                        break;
                }
            }
            $stmtInc->close();

            // Movimientos del empleado
            $sqlMov = "SELECT Hora FROM movimientos WHERE Fecha = ? AND Id_Empleado = ? ORDER BY Hora ASC";
            $stmtMov = $conn->prepare($sqlMov);
            $stmtMov->bind_param("si", $fechaStr, $idEmp);
            $stmtMov->execute();
            $resMov = $stmtMov->get_result();

            $horas = [];
            while ($mov = $resMov->fetch_assoc()) {
                $horas[] = $mov['Hora'];
            }
            $stmtMov->close();

            // Calcular horas trabajadas (pares de entrada/salida)
            $count = count($horas);
            if ($count > 0 && $count % 2 == 0) {
                for ($i = 0; $i < $count; $i += 2) {
                    $entrada = new DateTime($horas[$i]);
                    $salida  = new DateTime($horas[$i + 1]);
                    $diffSegundos = $salida->getTimestamp() - $entrada->getTimestamp();
                    $horasT += $diffSegundos / 3600; // convertir a horas
                }
            }
        }

        // Agregar fila a la tabla
        $tabla[] = [
            'Fecha' => $fechaActual->format('d/m/Y'),
            'Asistencias' => $totalAsistencias,
            'Faltas' => $totalFaltas,
            'Retardos' => $totalRetardos,
            'Permisos' => $totalPermisos,
            'HorasTrabajadas' => floor($horasT)
        ];

        $fechaActual->modify('+1 day');
    }

    return $tabla;
}


function obtenerDatosEmpleado($idEmpleado)
{
    global $conn;

    $datos = [
        'nombre' => '',
        'puesto' => '',
        'departamento' => ''
    ];

    $sql = "SELECT 
                e.Nombre,
                e.Apellido_Paterno,
                e.Apellido_Materno,
                d.Departamento AS Departamento,
                p.Puesto AS Puesto
            FROM empleados e
            INNER JOIN departamentos d ON e.Id_Departamento = d.Id_Departamento
            INNER JOIN puestos p ON e.Id_Puesto = p.Id_Puesto
            WHERE e.Id_Empleado = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idEmpleado);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        $datos['nombre'] = $fila['Nombre'] . " " . $fila['Apellido_Paterno'] . " " . $fila['Apellido_Materno'];
        $datos['puesto'] = $fila['Puesto'];
        $datos['departamento'] = $fila['Departamento'];
    }

    $stmt->close();

    return $datos;
}

//funciones nuevas agregadas mejoras
function obtenerPersonalCritico()
{
    global $conn;
    $sql = "SELECT CONCAT(e.Nombre, ' ', e.Apellido_Paterno) AS Empleado, d.Departamento, COUNT(i.Id_Incidencia) AS Total_Faltas 
            FROM incidencias i 
            JOIN empleados e ON i.Id_Empleado = e.Id_Empleado 
            JOIN departamentos d ON e.Id_Departamento = d.Id_Departamento 
            WHERE i.Id_Tipo_Incidencia = 1 
            GROUP BY e.Id_Empleado 
            HAVING Total_Faltas > ( 
                SELECT AVG(conteo_faltas) FROM ( 
                    SELECT COUNT(Id_Incidencia) as conteo_faltas FROM incidencias WHERE Id_Tipo_Incidencia = 1 GROUP BY Id_Empleado 
                ) as tabla_promedios 
            ) ORDER BY Total_Faltas DESC";
    $res = $conn->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function obtenerResumenPorDepartamento()
{
    global $conn;
    $sql = "SELECT d.Departamento, COUNT(e.Id_Empleado) AS total_empleados 
            FROM departamentos d 
            LEFT JOIN empleados e ON d.Id_Departamento = e.Id_Departamento 
            GROUP BY d.Id_Departamento";
    $res = $conn->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function obtenerAsistenciasPorMes()
{
    global $conn;
    $sql = "SELECT d.Departamento, MONTH(i.Fecha) as Mes, COUNT(i.Id_Incidencia) as Total_Asistencias 
            FROM incidencias i 
            LEFT JOIN empleados e ON i.Id_Empleado = e.Id_Empleado 
            LEFT JOIN departamentos d ON e.Id_Departamento = d.Id_Departamento 
            WHERE i.Id_Tipo_Incidencia = 4 AND YEAR(i.Fecha) = 2025 
            GROUP BY d.Id_Departamento, MONTH(i.Fecha) 
            ORDER BY Mes ASC, d.Departamento ASC";
    $res = $conn->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

function obtenerDiasSinRegistro()
{
    global $conn;
    // Lógica para detectar huecos en el calendario de asistencia por empleado
    $sql = "SELECT e.Id_Empleado, e.Nombre, e.Apellido_Paterno, f.Fecha
            FROM empleados e
            JOIN (SELECT DISTINCT Fecha FROM movimientos) f
            WHERE NOT EXISTS (
                SELECT 1
                FROM movimientos m
                WHERE m.Id_Empleado = e.Id_Empleado
                AND m.Fecha = f.Fecha
            )
            ORDER BY e.Id_Empleado, f.Fecha";
    $res = $conn->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
function obtenerAsistenciasPorDepatamentoMesEspecifico($mes, $anio)
{
    global $conn;

    $sql = "SELECT 
                MONTH(i.Fecha) AS Mes,
                d.Departamento,
                COUNT(*) AS Total_Asistencias
            FROM empleados e 
            INNER JOIN incidencias i ON i.Id_Empleado = e.Id_Empleado 
            INNER JOIN departamentos d ON d.Id_Departamento = e.Id_Departamento 
            WHERE i.Id_Tipo_Incidencia = 4 
            AND MONTH(i.Fecha) = $mes
            AND YEAR(i.Fecha) = $anio
            GROUP BY d.Id_Departamento, MONTH(i.Fecha)
            ORDER BY Total_Asistencias DESC";

    $res = $conn->query($sql);

    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

?>