const urlApiTermino = "https://acerosalonso.grupoctic.com/terminos/ApiConsultarTermino.php";

const cargarTermino = () => {

    const contenedor = document.getElementById("contenido-terminos");

    // Mensaje de carga
    contenedor.innerHTML = `
        <p style="text-align:center;">Cargando términos...</p>
    `;

    fetch(urlApiTermino)
        .then(res => res.json())
        .then(data => mostrarTermino(data))
        .catch(error => {
            console.error(error);
            contenedor.innerHTML = `
                <p style="text-align:center;">Error al cargar los términos.</p>
            `;
        });
};

const mostrarTermino = (respuesta) => {

    const contenedor = document.getElementById("contenido-terminos");
    contenedor.innerHTML = "";

    // Si no hay datos o viene error
    if (!respuesta || !respuesta.data) {
        contenedor.innerHTML = `
            <p style="text-align:center;">No hay términos disponibles.</p>
        `;
        return;
    }

    const termino = respuesta.data;

    contenedor.innerHTML = `
        <fieldset style="margin-bottom: 20px;">
            <legend>${termino.titulo}</legend>
            <p style="white-space: pre-line;">
                ${termino.contenido}
            </p>
        </fieldset>
    `;
};

// Ejecutar automáticamente al cargar la página
