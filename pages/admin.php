<?php
session_start();

// Verifica si el usuario ha iniciado sesión y es admin
if (!isset($_SESSION['correo']) || $_SESSION['tipoUsuario'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Panel de administrador del Hospital Luz de Esperanza" />
    <meta name="keywords" content="admin, panel, hospital, gestión, administración" />
    <meta name="author" content="Hospital Luz de Esperanza" />
    <title>Panel de Administrador | Hospital Luz de Esperanza</title>
    <link rel="icon" href="../assets/img/logo.png" type="image/x-icon" />
    <link rel="stylesheet" href="../css/normalize.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/pages.css" />
    <link rel="stylesheet" href="../css/responsive.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous" />
</head>

<body>
    <header class="header">
        <section class="cabecera">
            <div class="cabecera__logo">
                <a href="../index.php">
                    <img class="cabecera__img" src="../assets/img/logo.png" alt="Logo de Hospital Luz de Esperanza" />
                </a>
                <p class="cabecera__nombre">Hospital Luz de Esperanza</p>
                <i id="botonMenu" class="fa fa-bars menu__boton" onclick="abrirMenu()" aria-hidden="true"></i>
            </div>
            <nav id="menu" class="menu">
                <ul class="menu__lista">
                    <li class="menu__elemento"><a class="menu__enlace" href="admin.php">Inicio Admin</a></li>
                    <li class="menu__elemento"><a class="menu__enlace" href="admin.php?seccion=mensajes">Mensajes</a></li>
                    <li class="menu__elemento"><a class="menu__enlace" href="admin.php?seccion=agregarMedicos">Agregar Médicos</a></li>
                    <li class="menu__elemento"><a class="menu__enlace" href="admin.php?seccion=modificarMedicos">Actualizar Médicos</a></li>
                    <li class="menu__elemento"><a class="menu__enlace" href="../php/cerrarSesion.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </nav>
        </section>
    </header>

    <main class="main perfil">
        <?php
        $seccion = $_GET['seccion'] ?? '';

        if ($seccion === 'mensajes') {
            include("secciones/mensajes.php");
        } elseif ($seccion === 'agregarMedicos') {
            include("secciones/agregarMedicos.php");
        } elseif ($seccion === 'modificarMedicos') {
            include("secciones/modificarMedicos.php");
        } else {
            echo "<h2>Bienvenido al panel de administración</h2>";
        }
        ?>
    </main>


    <footer class="footer">
        <h2 class="footer__titulo">Hospital Luz de Esperanza</h2>
        <h3 class="footer__subtitulo">Todos los derechos conservados</h3>
    </footer>


    <script src="https://kit.fontawesome.com/d335561c97.js" crossorigin="anonymous"></script>
    <script src="../js/script.js"></script>
</body>

</html>