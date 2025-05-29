<?php
session_start();

include("../php/conexion.php");

if (!isset($_SESSION['correo'])) {
    header("Location: iniciarSesion.php");
    exit;
}

$correo = $_SESSION['correo'];
$errores = [];
$actualizacion_exitosa = false;

// Obtener datos actuales del paciente
$sql = "SELECT * FROM pacientes WHERE correoElectronico = '$correo'";
$resultado = $conexion->query($sql);

if ($resultado && $resultado->num_rows === 1) {
    $paciente = $resultado->fetch_assoc();

    // Asignar valores para mostrar en el formulario
    $nombre = $paciente['nombre'];
    $apellidoPaterno = $paciente['apellidoPaterno'];
    $apellidoMaterno = $paciente['apellidoMaterno'];
    $fecha_nacimiento = $paciente['fechaNacimiento'];
    $sexo = $paciente['sexo'];
    $estado_nacimiento = $paciente['estadoNacimiento'];
    $curp = $paciente['CURP'];
    $telefono = $paciente['telefono'];
} else {
    // Si no existe, redireccionar o mostrar error
    die("Error: No se encontraron datos del paciente.");
}

$mostrar_modal = false;
$errores = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["iniciar_sesion"])) {
    $correo = $_POST["correo"] ?? '';
    $contrasena = $_POST["contrasena"] ?? '';

    if (empty($correo) || empty($contrasena)) {
        $errores[] = "Debes ingresar tu correo y contraseña.";
    } else {
        $sql = "SELECT * FROM usuarios WHERE correoElectronico = '$correo'";
        $resultado = $conexion->query($sql);

        if ($resultado && $resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();

            if ($contrasena === $usuario['contraseña']) {
                $_SESSION['correo'] = $usuario['correoElectronico'];
                $_SESSION['tipoUsuario'] = $usuario['tipoUsuario']; 
                if ($usuario['tipoUsuario'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: perfil.php");
                }
                exit;
            } else {
                $errores[] = "Contraseña incorrecta.";
            }
        } else {
            $errores[] = "No se encontró una cuenta con ese correo.";
        }

        $conexion->close();
    }

    if (!empty($errores)) {
        $mostrar_modal = true;
    }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description"
        content="Inicia sesión en Hospital Luz de Esperanza para acceder a tu perfil y servicios personalizados." />
    <meta name="keywords" content="Hospital, Luz de Esperanza, inicio sesión, login, salud, paciente" />
    <meta name="author" content="Hospital Luz de Esperanza" />
    <title>Iniciar Sesión | Hospital Luz de Esperanza</title>
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
                    <li class="menu__elemento">
                        <a class="menu__enlace" href="#" onclick="menuDesplegable(1)">Conócenos</a>
                        <ul class="menu__desplegable" id="menuConocenos">
                            <li>
                                <a class="menu__enlace" href="nosotros.html">¿Quiénes somos?</a>
                            </li>
                            <li>
                                <a class="menu__enlace" href="../index.php#contacto">Contáctanos</a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu__elemento">
                        <a class="menu__enlace" href="../index.php#servicios">Servicios</a>
                    </li>
                    <li class="menu__elemento">
                        <a class="menu__enlace" onclick="menuDesplegable(2)" href="#">Accesos rápidos</a>
                        <ul class="menu__desplegable" id="menuAccesos"> 
                            <li><a class="menu__enlace" href="agendarCita.php">Agendar cita</a></li>
                            <li><a class="menu__enlace" href="modificarCita.php">Cambiar cita</a></li> 
                        </ul>
                    </li>
                    <li class="menu__elemento">
                        <a class="menu__enlace" href="">Iniciar sesión</a>
                    </li>
                </ul>
            </nav>
        </section>
    </header>

    <div class="portada login">
        <section class="login__contenedor">
            <form method="POST" action="iniciarSesion.php" class="contacto__formulario login__formulario">
                <h2 class="login__titulo">Iniciar sesión</h2>

                <label for="correo" class="contacto__label">
                    <input type="email" name="correo" id="correo" class="contacto__input" required />
                    <span class="contacto__placeholder">Correo Electrónico</span>
                </label>

                <label for="contrasena" class="contacto__label">
                    <input type="password" name="contrasena" id="contrasena" class="contacto__input" required />
                    <span class="contacto__placeholder">Contraseña</span>
                </label>

                <section class="login__botones">
                    <button type="button" class="contacto__boton" onclick="window.location.href='registrar.php';">
                        Registrarse
                    </button>
                    <input type="submit" name="iniciar_sesion" value="Iniciar sesión" class="contacto__boton" />
                </section>
            </form>
        </section>
    </div>


    <?php if ($mostrar_modal): ?>
        <dialog id="contactoModal" class="contacto__modal">
            <h2>Errores al iniciar sesión</h2>
            <i class="fa-solid fa-circle-exclamation contacto__icono"></i>
            <?php foreach ($errores as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
            <button class="contacto__boton" onclick="window.location.href='iniciarSesion.php';">
                Cerrar
            </button>
        </dialog>
    <?php endif; ?>

    <footer class="footer">
        <h2 class="footer__titulo">Hospital Luz de Esperanza</h2>
        <h3 class="footer__subtitulo">Todos los derechos conservados</h3>
    </footer>

    <script src="https://kit.fontawesome.com/d335561c97.js" crossorigin="anonymous"></script>
    <script src="../js/script.js"></script>

</body>

</html>