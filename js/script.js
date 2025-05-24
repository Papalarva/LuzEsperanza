function abrirMenu() {
    const menu = document.getElementById("menu");
    const botonMenu = document.getElementById("botonMenu");
    botonMenu.classList.add('menu__boton--oculto');
    setTimeout(() => {
        if (botonMenu.classList.contains('fa-bars')) {
            
                botonMenu.classList.remove('fa-bars');
                botonMenu.classList.add('fa-solid', 'fa-xmark');
            
        } else {
            
                botonMenu.classList.remove('fa-solid', 'fa-xmark');
                botonMenu.classList.add('fa', 'fa-bars', 'menu__boton');
            
        }
        botonMenu.classList.remove('menu__boton--oculto');
    }, 500);
    document.body.classList.toggle("no-scroll");
    menu.classList.toggle("menu__visible");
};

function menuDesplegable(id) {
    if (window.innerWidth >= 768) return;
    const menuConocenos = document.getElementById("menuConocenos");
    const menuAccesos = document.getElementById("menuAccesos");

    if (id === 1) {
        menuAccesos.classList.remove("menuVisible");
        menuConocenos.classList.toggle("menuVisible");
    } else if (id === 2) {
        menuConocenos.classList.remove("menuVisible");
        menuAccesos.classList.toggle("menuVisible");
    }
}
