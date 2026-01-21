// =============================
//  ESTADO GLOBAL
// =============================
let tamañoLetra      = 100;
let contrasteActivo  = false;
let modoSuave        = false;
let modoDaltonico    = false;
let lecturaActiva    = false;   // para el lector
let utterActual      = null;    // instancia de SpeechSynthesis

// =============================
//  AUMENTAR / DISMINUIR LETRA
// =============================
function aumentarLetra() {
    tamañoLetra += 10;
    if (tamañoLetra > 200) tamañoLetra = 200; // límite superior
    aplicarEstadoAccesibilidad();
    guardarPreferencias();
}

function disminuirLetra() {
    tamañoLetra -= 10;
    if (tamañoLetra < 50) tamañoLetra = 50; // límite inferior
    aplicarEstadoAccesibilidad();
    guardarPreferencias();
}

function restaurarLetra() {
    tamañoLetra = 100;
    aplicarEstadoAccesibilidad();
    guardarPreferencias();
}

// =============================
//  CONTRASTE ALTO
// =============================
function toggleContraste() {
    // Alternar estado
    contrasteActivo = !contrasteActivo;

    // Aplicar cambios visuales y persistir
    aplicarEstadoAccesibilidad();
    guardarPreferencias();

    // Exponer estado en el HTML para selectores CSS/JS
    document.documentElement.setAttribute('data-contraste-activo', contrasteActivo ? '1' : '0');

    // Actualizar controles (si existen) con aria-pressed y clase activa
    const controles = Array.from(document.querySelectorAll('[data-acces-action="contraste"], #btnContraste'));
    controles.forEach(btn => {
        try {
            btn.setAttribute('aria-pressed', contrasteActivo ? 'true' : 'false');
            btn.classList.toggle('activo', contrasteActivo);
        } catch (e) {
            // Ignorar si el elemento no acepta atributos
        }
    });

    // Emitir evento personalizado para que otras partes reaccionen
    const ev = new CustomEvent('accesibilidad:contraste', { detail: { activo: contrasteActivo } });
    document.dispatchEvent(ev);

    // Crear o actualizar una región aria-live para notificar a lectores de pantalla
    let live = document.getElementById('accesibilidad-live');
    if (!live) {
        live = document.createElement('div');
        live.id = 'accesibilidad-live';
        live.setAttribute('aria-live', 'polite');
        live.setAttribute('aria-atomic', 'true');
        live.style.position = 'absolute';
        live.style.left = '-9999px';
        live.style.width = '1px';
        live.style.height = '1px';
        live.style.overflow = 'hidden';
        document.body.appendChild(live);
    }
    live.textContent = contrasteActivo ? 'Alto contraste activado' : 'Alto contraste desactivado';
    // Limpiar el texto pasados 1.2s para permitir futuras notificaciones
    setTimeout(() => { if (live) live.textContent = ''; }, 1200);
}

// =============================
//  MODO SUAVE (colores menos saturados)
// =============================
function toggleModoSuave() {
    modoSuave = !modoSuave;
    aplicarEstadoAccesibilidad();
    guardarPreferencias();
}

// =============================
//  MODO DALTONISMO
// =============================
//parent.toggleModoDaltonico()
function toggleModoDaltonico() {
    modoDaltonico = !modoDaltonico;   
    aplicarEstadoAccesibilidad();
    guardarPreferencias();
}

// =============================
//  LECTOR DE PÁGINA
// =============================
function leerPagina() {
    // Si ya está leyendo, detener
    if (lecturaActiva) {
        if (utterActual) {
            utterActual.onend = null;
        }
        window.speechSynthesis.cancel();
        lecturaActiva = false;
        utterActual = null;
        // Exponer estado del lector a la ventana global (para iframes)
        try { window.lecturaActiva = lecturaActiva; } catch (e) { }
        return;
    }

    // Tomar el texto de la página
    const texto = document.body.innerText || document.body.textContent || "";
    if (!texto.trim()) return;

    // Cancelar cualquier lectura previa
    window.speechSynthesis.cancel();

    utterActual = new SpeechSynthesisUtterance(texto);
    utterActual.lang = "es-MX";
    utterActual.rate = 1;
    utterActual.pitch = 1;

    utterActual.onend = function () {
        lecturaActiva = false;
        utterActual = null;
        try { window.lecturaActiva = lecturaActiva; } catch (e) { }
    };

    lecturaActiva = true;
    try { window.lecturaActiva = lecturaActiva; } catch (e) { }
    window.speechSynthesis.speak(utterActual);
}

// =============================
//  APLICAR ESTADO A LA PÁGINA
// =============================
function aplicarEstadoAccesibilidad() {
    // Tamaño de letra
    document.documentElement.style.fontSize = tamañoLetra + "%";

    // Clases en <body>
    document.body.classList.toggle("alto-contraste", contrasteActivo);
    document.body.classList.toggle("modo-suave",      modoSuave);
    document.body.classList.toggle("modo-daltonico",  modoDaltonico);

    // Exponer el estado actual en el objeto global `window` para que
    // iframes (como el menú de accesibilidad) puedan consultarlo
    try {
        window.tamañoLetra = tamañoLetra;
        window.contrasteActivo = contrasteActivo;
        window.modoSuave = modoSuave;
        window.modoDaltonico = modoDaltonico;
    } catch (e) {
        // Ignorar si no se puede escribir en window
    }
}

// =============================
//  GUARDAR / CARGAR PREFERENCIAS
// =============================
function guardarPreferencias() {
    const prefs = {
        tamañoLetra,
        contrasteActivo,
        modoSuave,
        modoDaltonico
    };
    localStorage.setItem("acces_prefs", JSON.stringify(prefs));
}

function cargarPreferencias() {
    const raw = localStorage.getItem("acces_prefs");
    if (!raw) return;

    try {
        const prefs = JSON.parse(raw);
        if (typeof prefs.tamañoLetra === "number") tamañoLetra = prefs.tamañoLetra;
        contrasteActivo = !!prefs.contrasteActivo;
        modoSuave      = !!prefs.modoSuave;
        modoDaltonico  = !!prefs.modoDaltonico;
    } catch (e) {
        console.error("Error al cargar acces_prefs:", e);
    }
}

// =============================
//  INICIALIZAR AL CARGAR LA PÁGINA
// =============================
document.addEventListener("DOMContentLoaded", () => {
    cargarPreferencias();
    aplicarEstadoAccesibilidad();
    // Asegurar que el estado del lector también esté expuesto
    try {
        window.lecturaActiva = lecturaActiva;
        window.tamañoLetra = tamañoLetra;
        window.contrasteActivo = contrasteActivo;
        window.modoSuave = modoSuave;
        window.modoDaltonico = modoDaltonico;
    } catch (e) { }
});

// =============================
//  BOTÓN FLOTANTE abrir cerrar
// =============================
function toggleMenuAccesibilidad() {
    const iframe = document.getElementById("menuAccesibilidad");
    if (!iframe) return;

    const visible = iframe.style.display === "block";
    iframe.style.display = visible ? "none" : "block";
    
}

// Cierra el menú de accesibilidad
function closeMenuAccesibilidad() {
    const iframe = document.getElementById("menuAccesibilidad");
    const btn = document.getElementById('btnAccesibilidad');
    if (!iframe) return;
    iframe.style.display = 'none';
    if (btn) btn.setAttribute('aria-expanded', 'false');
}

// Abrir/Cerrar con control de aria-expanded y evitar cierre inmediato
function toggleMenuAccesibilidad() {
    const iframe = document.getElementById("menuAccesibilidad");
    const btn = document.getElementById('btnAccesibilidad');
    if (!iframe) return;

    const visible = iframe.style.display === "block";
    iframe.style.display = visible ? "none" : "block";
    if (btn) btn.setAttribute('aria-expanded', visible ? 'false' : 'true');
}

// Cerrar al hacer click fuera o al presionar Escape
document.addEventListener('click', function (e) {
    const iframe = document.getElementById('menuAccesibilidad');
    const btn = document.getElementById('btnAccesibilidad');
    if (!iframe) return;

    // Si el iframe está oculto, no hacemos nada
    if (iframe.style.display !== 'block') return;

    // Si el click fue dentro del iframe o en el botón, no cerrar
    const target = e.target;
    if (btn && (btn === target || btn.contains(target))) return;
    if (iframe && (iframe === target || iframe.contains(target))) return;

    // En cualquier otro caso, cerrar
    iframe.style.display = 'none';
    if (btn) btn.setAttribute('aria-expanded', 'false');
});

// Soportar touchstart para móviles
document.addEventListener('touchstart', function (e) {
    const iframe = document.getElementById('menuAccesibilidad');
    const btn = document.getElementById('btnAccesibilidad');
    if (!iframe) return;
    if (iframe.style.display !== 'block') return;
    const target = e.target;
    if (btn && (btn === target || btn.contains(target))) return;
    if (iframe && (iframe === target || iframe.contains(target))) return;
    iframe.style.display = 'none';
    if (btn) btn.setAttribute('aria-expanded', 'false');
}, { passive: true });

// Cerrar con Escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' || e.key === 'Esc') {
        closeMenuAccesibilidad();
    }
});
