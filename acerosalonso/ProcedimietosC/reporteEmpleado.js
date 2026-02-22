const urlApi = "https://acerosalonso.grupoctic.com/ProcedimietosC/ApiConsultaE.php";
const urlEmpleado = "https://acerosalonso.grupoctic.com/ProcedimietosC/ApiDatoE.php";

function cargarReporteEmpleado() {

    const idEmpleado = document.getElementById("id_empleado").value.trim();
    const fechaInicio = document.getElementById("fecha_inicio").value;
    const fechaFin = document.getElementById("fecha_fin").value;

    if (!idEmpleado || !fechaInicio || !fechaFin) {
        alert("Complete todos los campos");
        return;
    }

    // Limpiar datos empleado
    document.getElementById("nombre_empleado").value = '';
    document.getElementById("puesto_empleado").value = '';
    document.getElementById("departamento_empleado").value = '';

    // Obtener datos del empleado
    fetch(`${urlEmpleado}?id=${idEmpleado}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById("nombre_empleado").value = data.nombre || '';
            document.getElementById("puesto_empleado").value = data.puesto || '';
            document.getElementById("departamento_empleado").value = data.departamento || '';
        })
        .catch(error => {
            console.error(error);
        });

    const tbody = document.getElementById("tabla-empleado");
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Cargando...</td></tr>';

    fetch(`${urlApi}?empleado=${idEmpleado}&inicio=${fechaInicio}&fin=${fechaFin}`)
        .then(res => res.json())
        .then(data => {
            mostrarEmpleado(data.reporte);
        })
        .catch(error => {
            console.error(error);
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Error al cargar datos</td></tr>';
        });
}

function mostrarEmpleado(registros) {

    const tbody = document.getElementById("tabla-empleado");
    tbody.innerHTML = "";

    if (!registros || registros.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No hay datos para mostrar</td></tr>';
        return;
    }

    registros.forEach(registro => {

        const fila = document.createElement("tr");

        fila.innerHTML = `
            <td>${registro.Fecha}</td>
            <td>${registro.HoraEntrada || '--:--:--'}</td>
            <td>${registro.HoraSalida || '--:--:--'}</td>
            <td>${registro.TotalHoras}</td>
            <td>${calcularHorasExtra(registro.TotalHoras)}</td>
            <td>${registro.Incidencia}</td>
        `;

        tbody.appendChild(fila);
    });
}

function calcularHorasExtra(totalHoras) {

    if (!totalHoras || totalHoras === '00:00:00') return '00:00:00';

    const partes = totalHoras.split(':');
    const horas = parseInt(partes[0]);
    const minutos = parseInt(partes[1]);

    const totalMinutos = (horas * 60) + minutos;

    if (totalMinutos <= 480) return '00:00:00';

    const extraMin = totalMinutos - 480;
    const extraHoras = Math.floor(extraMin / 60);
    const extraMinRest = extraMin % 60;

    return `${extraHoras.toString().padStart(2,'0')}:${extraMinRest.toString().padStart(2,'0')}:00`;
}