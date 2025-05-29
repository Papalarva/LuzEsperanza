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
        return
    };
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


let splideInstance;

function splideServicios() {

    if (splideInstance) {
        splideInstance.destroy(true);
    }

    const colores = ['#731a4b', '#72a69c', '#2192bf', '#025e73'];
    const serviciosEnlace = document.querySelectorAll('.servicios__enlace');
    const servicios = document.querySelectorAll('.servicios__tarjeta');

    servicios.forEach((servicio, index) => {
        servicio.style.backgroundColor = colores[index % colores.length];
    });
    serviciosEnlace.forEach((enlace, index) => {
        enlace.style.color = colores[index % colores.length];
    });

    const ancho = window.innerWidth;

    if (ancho <= 599) {
        document.querySelectorAll('.servicios__icono').forEach(icono => {
            icono.style.display = "none";
        });
        splideInstance = new Splide('#carruselServicios', {
            perPage: 1,
            width: '100vw',
            type: 'loop',
            gap: '1rem',
            arrows: false,
            pagination: false,
            drag: true,
            autoplay: true,
            interval: 4000,
            speed: 2000,
            pauseOnHover: true,
            pauseOnFocus: true,
            rewind: true,
            rewindSpeed: 1000,
        });
        splideInstance.mount();

    } else if (ancho >= 600) {
        document.querySelectorAll('.servicios__icono').forEach(icono => {
            icono.style.display = "block";
        });
        const body = document.body;

        servicios.forEach((servicio, index) => {
            const color = colores[index % colores.length];
            const id = index + 1;
            servicio.style.backgroundColor = "var(--blanco)";
            servicio.style.color = color;
            servicio.style.border = `2px solid ${color}`;

            servicio.addEventListener("mouseenter", () => {
                servicio.style.backgroundColor = color;
                servicio.style.color = "white";
            });

            servicio.addEventListener("mouseleave", () => {
                servicio.style.backgroundColor = "var(--blanco)";
                servicio.style.color = color;
            });

            servicio.addEventListener("click", function () {
                if (window.innerWidth >= 600) {
                    const modal = document.getElementById("modalServicio" + id);
                    if (modal) modal.showModal();
                    body.style.overflow = "hidden";
                }
            });

            const cerrarBtn = document.getElementById("cerrarModal" + id);
            if (cerrarBtn) {
                cerrarBtn.addEventListener("click", function () {
                    const modal = document.getElementById("modalServicio" + id);
                    if (modal) modal.close();
                    body.style.overflowY = "auto";
                });
            }
        });

        splideInstance = new Splide('#carruselServicios', {
            type: 'slide',
            width: '100vw',
            type: 'loop',
            arrows: false,
            gap: '2rem',
            pagination: false,
            drag: true,
            autoplay: true,
            interval: 4000,
            speed: 2000,
            pauseOnHover: true,
            pauseOnFocus: true,
            rewind: true,
            rewindSpeed: 1000,
            grid: {
                rows: 2,
                cols: 3,
                gap: {
                    row: '2rem',
                    col: '2rem',
                },
            },
            breakpoints: {
                1200: {
                    grid: {
                        rows: 2,
                        cols: 2,
                    },
                }
            },
            pagination: false,
            arrows: false,
        });
        splideInstance.mount(window.splide.Extensions);
    }
}

function splideInstalaciones() {
    var splide = new Splide('#carruselInstalaciones', {
        pagination: false,
        type: 'slide',
        width: '100vw',
        type: 'loop',
        gap: '2rem',
        pagination: false,
        drag: true,
        autoplay: true,
        interval: 8000,
        speed: 2000,
        pauseOnHover: true,
        pauseOnFocus: true,
        rewind: true,
        rewindSpeed: 10000,
        classes: {
            arrows: 'splide__arrows instalaciones__arrow',
            arrow: 'splide__arrow instalaciones__arrow',
        },
    });
    var thumbnails = document.getElementsByClassName('thumbnail');
    var current;
    for (var i = 0; i < thumbnails.length; i++) {
        initThumbnail(thumbnails[i], i);
    }
    function initThumbnail(thumbnail, index) {
        thumbnail.addEventListener('click', function () {
            splide.go(index);
        });
    }
    splide.on('mounted move', function () {
        var thumbnail = thumbnails[splide.index];
        if (thumbnail) {
            if (current) {
                current.classList.remove('is-active');
            }
            thumbnail.classList.add('is-active');
            current = thumbnail;
        }
    });


    splide.mount();
}

document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("contactoModal");

  if (modal && typeof modal.showModal === "function") { 
    if (!modal.open) {
      modal.showModal();
      modal.style.display = "flex";
    }
  }
});