// validacion_contrasena.js
document.addEventListener("DOMContentLoaded", () => {
    const pass = document.getElementById("contrasena");
    const popup = document.getElementById("popup-reglas");

    const rMayus = document.getElementById("regla-mayus");
    const rMinus = document.getElementById("regla-minus");
    const rNumero = document.getElementById("regla-numero");
    const rEspecial = document.getElementById("regla-especial");
    const rLongitud = document.getElementById("regla-longitud");

    function validar() {
        const v = pass.value;

        rMayus.classList.toggle("ok", /[A-Z]/.test(v));
        rMinus.classList.toggle("ok", /[a-z]/.test(v));
        rNumero.classList.toggle("ok", /\d/.test(v));
        rEspecial.classList.toggle("ok", /#/.test(v));
        rLongitud.classList.toggle("ok", v.length >= 8);
    }

    pass.addEventListener("focus", () => { popup.style.display = "block"; });
    pass.addEventListener("blur", () => { popup.style.display = "none"; });
    pass.addEventListener("input", validar);
});

