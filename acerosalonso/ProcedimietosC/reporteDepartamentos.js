const urlApiDepartamento = "https://acerosalonso.grupoctic.com/ProcedimietosC/ApiConsultaD.php";

const generarReporte = () => {

    const departamento = document.getElementById("select_depto").value;
    const fechaInicio = document.getElementById("fecha_inicio").value;
    const fechaFin = document.getElementById("fecha_fin").value;

    if (!departamento || !fechaInicio || !fechaFin) {
        alert("Complete todos los campos");
        return;
    }

    const tbody = document.getElementById("tablaReporteBody");
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Cargando...</td></tr>';

    fetch(`${urlApiDepartamento}?departamento=${departamento}&inicio=${fechaInicio}&fin=${fechaFin}`)
        .then(res => res.json())
        .then(data => mostrarDepartamento(data.reporte))
        .catch(error => {
            console.error(error);
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Error al cargar datos</td></tr>';
        });
};


const mostrarDepartamento = (registros) => {

    const tbody = document.getElementById("tablaReporteBody");
    tbody.innerHTML = "";

    if (!registros || registros.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No hay datos para mostrar</td></tr>';
        return;
    }

    registros.forEach(registro => {

        const fila = document.createElement("tr");

        fila.innerHTML = `
            <td>${registro.Fecha}</td>
            <td>${registro.TotalAsistencias}</td>
            <td>${registro.TotalFaltas}</td>
            <td>${registro.TotalRetardos}</td>
            <td>${registro.TotalPermisos}</td>
            <td>${registro.TotalHorasTrabajadas}</td>
        `;

        tbody.appendChild(fila);
    });
};