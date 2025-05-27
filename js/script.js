function abrirMenu() {
    const menu = document.getElementById("menu");
    const botonMenu = document.getElementById("botonMenu");
    const body = document.body;

    botonMenu.style.opacity = "0";

    setTimeout(() => {
        if (botonMenu.classList.contains('fa-bars')) {
            botonMenu.classList.remove('fa-bars');
            botonMenu.classList.add('fa-solid', 'fa-xmark');
        } else {
            botonMenu.classList.remove('fa-solid', 'fa-xmark');
            botonMenu.classList.add('fa', 'fa-bars', 'menu__boton');
        }
        botonMenu.style.opacity = "1";
    }, 500);

    menu.classList.toggle("menu__visible");

    if (menu.classList.contains("menu__visible")) {
        body.style.overflow = "hidden";
    } else {
        body.style.overflow = "auto";
    }
};

function menuDesplegable(id) {
    const menuConocenos = document.getElementById("menuConocenos");
    const menuAccesos = document.getElementById("menuAccesos");

    if (window.innerWidth >= 600) {
        menuAccesos.classList.remove("menu__desplegable--visible");
    menuConocenos.classList.remove("menu__desplegable--visible");
        return};
    if (id === 1) {
        menuAccesos.classList.remove("menu__desplegable--visible");
        menuConocenos.classList.toggle("menu__desplegable--visible");

    } else if (id === 2) {
        menuConocenos.classList.remove("menu__desplegable--visible");
        menuAccesos.classList.toggle("menu__desplegable--visible");

    }
}

function textoBienvenida() {
    const bienvenidaTitulo = document.getElementById("bienvenidaTitulo");
    const bienvenidaSubtitulo = document.getElementById("bienvenidaSubtitulo");

    if (window.innerWidth <= 480) {
        bienvenidaTitulo.textContent = "Bienvenid@s";
        bienvenidaSubtitulo.style.display = "none";
    } else {
        bienvenidaSubtitulo.textContent = "Bienvenidos al";
        bienvenidaTitulo.textContent = "Hospital Luz de Esperanza";
        bienvenidaSubtitulo.style.display = "block";
    }
}

window.addEventListener("DOMContentLoaded", cambiarVideo);
                window.addEventListener("resize", function () {
                    cambiarVideo();
                    menuDesplegable(0);
                });