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

// Procesar actualización
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["actualizar_paciente"])) {
    // Limpiar y validar datos
    $nombre = htmlspecialchars(trim($_POST["nombre"]));
    $apellidoPaterno = htmlspecialchars(trim($_POST["apellidoPaterno"]));
    $apellidoMaterno = htmlspecialchars(trim($_POST["apellidoMaterno"]));
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $sexo = $_POST["sexo"];
    $estado_nacimiento = htmlspecialchars(trim($_POST["estado_nacimiento"]));
    $curp_post = strtoupper(trim($_POST["curp"]));
    $telefono = trim($_POST["telefono"]);

    if (empty($nombre)) {
        $errores["nombre"] = "El nombre es obligatorio.";
    } elseif (!preg_match("/^[a-zA-ZÁÉÍÓÚÜÑáéíóúüñ\s]+$/u", $nombre)) {
        $errores["nombre"] = "Nombre no válido.";
    }

    if (empty($apellidoPaterno)) {
        $errores["apellidoPaterno"] = "El primer apellido es obligatorio.";
    } elseif (!preg_match("/^[a-zA-ZÁÉÍÓÚÜÑáéíóúüñ\s]+$/u", $apellidoPaterno)) {
        $errores["apellidoPaterno"] = "Primer apellido no válido.";
    }

    if (empty($estado_nacimiento)) {
        $errores["estado_nacimiento"] = "El estado de nacimiento es obligatorio.";
    } elseif (!preg_match("/^[a-zA-ZÁÉÍÓÚÜÑáéíóúüñ\s]+$/u", $estado_nacimiento)) {
        $errores["estado_nacimiento"] = "Estado no válido.";
    }

    if (empty($fecha_nacimiento) || $fecha_nacimiento === '0000-00-00') {
        $errores['fecha_nacimiento'] = 'Por favor, selecciona una fecha válida.';
    }

    if (empty($sexo)) {
        $errores['sexo'] = 'Por favor, selecciona una opción.';
    }

    if (empty($curp_post)) {
        $errores["curp"] = "El CURP es obligatorio.";
    } elseif (strlen($curp_post) !== 18) {
        $errores["curp"] = "El CURP debe tener 18 caracteres.";
    } elseif ($curp_post !== $curp) {
        // Si el CURP cambia, verificar que no exista en la BD
        $verificarCurp = $conexion->query("SELECT CURP FROM pacientes WHERE CURP = '$curp_post'");
        if ($verificarCurp->num_rows > 0) {
            $errores['curp'] = "El CURP ya está registrado para otro paciente.";
        }
    }

    if (empty($telefono)) {
        $errores["telefono"] = "El teléfono es obligatorio.";
    } elseif (!preg_match("/^[0-9]{10}$/", $telefono)) {
        $errores["telefono"] = "El teléfono debe tener 10 dígitos.";
    }

    if (empty($errores)) {
        // Preparar para actualizar
        $nombre = $conexion->real_escape_string($nombre);
        $apellidoPaterno = $conexion->real_escape_string($apellidoPaterno);
        $apellidoMaterno = $conexion->real_escape_string($apellidoMaterno);
        $fecha_nacimiento = $conexion->real_escape_string($fecha_nacimiento);
        $sexo = $conexion->real_escape_string($sexo);
        $estado_nacimiento = $conexion->real_escape_string($estado_nacimiento);
        $curp_post = $conexion->real_escape_string($curp_post);
        $telefono = $conexion->real_escape_string($telefono);

        // Actualizar paciente (usar correo electrónico como llave para encontrar registro)
        $sql_update = "UPDATE pacientes SET
            nombre = '$nombre',
            apellidoPaterno = '$apellidoPaterno',
            apellidoMaterno = '$apellidoMaterno',
            fechaNacimiento = '$fecha_nacimiento',
            sexo = '$sexo',
            estadoNacimiento = '$estado_nacimiento',
            CURP = '$curp_post',
            telefono = '$telefono'
            WHERE correoElectronico = '$correo'";

        if ($conexion->query($sql_update)) {
            $actualizacion_exitosa = true;
            $curp = $curp_post; // Actualizar CURP local si cambió
        } else {
            $errores["bd"] = "Error al actualizar los datos: " . $conexion->error;
        }
    }
}
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Perfil | Hospital Luz de Esperanza</title>
    <meta name="description" content="Perfil de <?= htmlspecialchars(
        ($paciente['nombre'] ?? 'No proporcionado') . ' ' . ($paciente['apellido'] ?? '')
    ) ?> en Hospital Luz de Esperanza. Consulta y actualiza tus datos personales y contacto." />

    <meta name="keywords"
        content="perfil paciente, Hospital Luz de Esperanza, datos personales, información de paciente" />
    <meta name="author" content="Hospital Luz de Esperanza" />
    <meta name="robots" content="noindex, nofollow" />
    <link rel="icon" href="../assets/img/logo.png" type="image/x-icon" />
    <link rel="stylesheet" href="../css/normalize.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/pages.css" />
    <link rel="stylesheet" href="../css/responsive.css" />
</head>

<body>
    <header class="header">
        <section class="cabecera">
            <div class="cabecera__logo">
                <a href="../index.php">
                    <img class="cabecera__img" src="../assets/img/logo.png" alt="Logo de Hospital Luz de Esperanza">
                </a>
                <p class="cabecera__nombre">Hospital Luz de Esperanza</p>
                <i id="botonMenu" class="fa fa-bars menu__boton" onclick="abrirMenu()" aria-hidden="true"></i>
            </div>
            <nav id="menu" class="menu">
                <ul class="menu__lista">
                    <li class="menu__elemento"><a class="menu__enlace" href="#"
                            onclick="menuDesplegable(1)">Conócenos</a>
                        <ul class="menu__desplegable" id="menuConocenos">
                            <li><a class="menu__enlace" href="nosotros.html">¿Quiénes somos?</a></li>
                            <li><a class="menu__enlace" href="../index.php#contacto">Contáctanos</a></li>
                        </ul>
                    </li>
                    <li class="menu__elemento"><a class="menu__enlace" href="../index.php#servicios">Servicios</a></li>
                    <li class="menu__elemento"><a class="menu__enlace" onclick="menuDesplegable(2)" href="#">Accesos
                            rápidos</a>
                        <ul class="menu__desplegable" id="menuAccesos">
                            <li><a class="menu__enlace" href="agendarCita.php">Agendar cita</a></li>
                            <li><a class="menu__enlace" href="modificarCita.php">Cambiar cita</a></li> 
                        </ul>
                    </li>
                    <li class="menu__elemento"><a class="menu__enlace" href="../php/cerrarSesion.php">Cerrar sesión</a>
                    </li>
                </ul>
            </nav>
        </section>
    </header>
    <main class="main perfil">
        <section class="perfil__introducción">
            <?php if ($actualizacion_exitosa): ?>
                <dialog id="actualizarDatos" class="contacto__modal">
                    <h2>Actualización exitosa</h2>
                    <i class="fa-solid fa-circle-check contacto__icono"></i>
                    <p>¡Se actualizarón los datos correctamente!</p>
                    <form method="GET" action="registrar.php">
                        <button type="button" class="contacto__boton"
                            onclick="window.location.href='perfil.php'; document.querySelector('#actualizarDatos').classList.remove('actualizarVisible');">
                            Aceptar</button>
                    </form>
                </dialog>
            <?php endif; ?>
            <div class="perfil__presentacion">
                <i class="fa-regular fa-heart pefil__icono"></i>
                <span class="perfil__datosGenerales">
                    <h2 class="perfil__nombre"><?= $paciente['nombre'] . " " . $paciente['apellidoPaterno'] ?></h2>
                    <h3><?= $paciente['fechaNacimiento'] ?></h3>
                </span>
            </div>
            <?php
            $fechaNacimiento = new DateTime($paciente['fechaNacimiento']);
            $hoy = new DateTime();
            $edad = $hoy->diff($fechaNacimiento)->y;
            ?>
            <div class="perfil__datos">
                <div>
                    <p><strong>EDAD: </strong><?= $edad ?></p>
                    <p><strong>SEXO: </strong><?= $paciente['sexo'] ?></p>
                    <p><strong>ESTADO: </strong><?= $paciente['estadoNacimiento'] ?></p>
                    <p><strong>CURP: </strong><?= $paciente['CURP'] ?></p>
                    <p><strong>TELEFONO: </strong><?= $paciente['telefono'] ?></p>
                </div>
            </div>
            <div class="perfil__botones">
                <a class="perfil__boton"
                    onclick="document.querySelector('#actualizarDatos').classList.add('actualizarVisible');">Actualizar
                    datos</a>
                <a class="perfil__boton" href="">Expendiente</a>
                <a class="perfil__boton" href="agendarCita.php">Agendar cita</a>
            </div>
            <form id="actualizarDatos" method="POST" action=""
                class="contacto__formulario registro__formulario actualizarDatos" novalidate>
                <h2 class="login__titulo">Actualizar Datos</h2>
                <h3 class="registro__titulo" style="margin-bottom: 2rem">Datos Personales</h3>
                <section class="registro__contenedor">
                    <label for="nombre" class="contacto__label">
                        <input type="text" name="nombre" id="nombre" class="contacto__input" required
                            value="<?= htmlspecialchars($nombre ?? '') ?>">
                        <span class="contacto__placeholder">Nombre</span>
                        <span class="contacto__error"><?= $errores['nombre'] ?? '' ?></span>
                    </label>

                    <label for="apellidoPaterno" class="contacto__label">
                        <input type="text" name="apellidoPaterno" id="apellidoPaterno" class="contacto__input" required
                            value="<?= htmlspecialchars($apellidoPaterno ?? '') ?>">
                        <span class="contacto__placeholder">Primer Apellido</span>
                        <span class="contacto__error"><?= $errores['apellidoPaterno'] ?? '' ?></span>
                    </label>

                    <label for="apellidoMaterno" class="contacto__label">
                        <input type="text" name="apellidoMaterno" id="apellidoMaterno" class="contacto__input"
                            value="<?= htmlspecialchars($apellidoMaterno ?? '') ?>">
                        <span class="contacto__placeholder">Segundo Apellido</span>
                        <span class="contacto__error"><?= $errores['apellidoMaterno'] ?? '' ?></span>
                    </label>

                    <label for="fecha_nacimiento" class="contacto__label">
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="contacto__input"
                            required value="<?= htmlspecialchars($fecha_nacimiento ?? '') ?>" min="1925-01-01"
                            max="<?= date('Y-m-d') ?>">
                        <span class="contacto__placeholder">Fecha de Nacimiento</span>
                        <span class="contacto__error"><?= $errores['fecha_nacimiento'] ?? '' ?></span>
                    </label>

                    <label for="sexo" class="contacto__label">
                        <select name="sexo" id="sexo" class="contacto__input" required>
                            <option value="" <?= empty($sexo) ? 'selected' : '' ?>>Selecciona sexo</option>
                            <option value="Masculino" <?= (isset($sexo) && $sexo === 'Masculino') ? 'selected' : '' ?>>
                                Masculino
                            </option>
                            <option value="Femenino" <?= (isset($sexo) && $sexo === 'Femenino') ? 'selected' : '' ?>>
                                Femenino
                            </option>
                            <option value="Otro" <?= (isset($sexo) && $sexo === 'Otro') ? 'selected' : '' ?>>Otro</option>
                        </select>
                        <span class="contacto__placeholder">Sexo</span>
                        <span class="contacto__error"><?= $errores['sexo'] ?? '' ?></span>
                    </label>

                    <label for="estado_nacimiento" class="contacto__label">
                        <input type="text" name="estado_nacimiento" id="estado_nacimiento" class="contacto__input"
                            required value="<?= htmlspecialchars($estado_nacimiento ?? '') ?>">
                        <span class="contacto__placeholder">Estado de Nacimiento</span>
                        <span class="contacto__error"><?= $errores['estado_nacimiento'] ?? '' ?></span>
                    </label>

                    <label for="curp" class="contacto__label">
                        <input type="text" name="curp" id="curp" maxlength="18" class="contacto__input" required
                            value="<?= htmlspecialchars($curp ?? '') ?>">
                        <span class="contacto__placeholder">CURP</span>
                        <span class="contacto__error"><?= $errores['curp'] ?? '' ?></span>
                    </label>

                    <label for="telefono" class="contacto__label">
                        <input type="number" name="telefono" id="telefono" maxlength="10" pattern="\d{10}" required
                            class="contacto__input" value="<?= htmlspecialchars($telefono ?? '') ?>">
                        <span class="contacto__placeholder">Teléfono</span>
                        <span class="contacto__error"><?= $errores['telefono'] ?? '' ?></span>
                    </label>
                </section>

                <?php if (!empty($errores['bd'])): ?>
                    <p class="contacto__error"><?= $errores['bd'] ?></p>
                <?php endif; ?>

                <div class="contacto__botones"> 
                    <input type="submit" name="actualizar_paciente" value="Actualizar" class="contacto__boton">
                </div>
            </form>
        </section>

    </main>

    <footer class="footer">
        <h2 class="footer__titulo">Hospital Luz de Esperanza</h2>
        <h3 class="footer__subtitulo">Todos los derechos conservados</h3>
    </footer>
    <script src="https://kit.fontawesome.com/d335561c97.js" crossorigin="anonymous"></script>
    <script src="../js/script.js"></script>
    <script>
        const colores = ['#731a4b', '#72a69c', '#2192bf', '#025e73'];
        const perfilBoton = document.querySelectorAll('.perfil__boton');

        perfilBoton.forEach((boton, index) => {
            const color = colores[index % colores.length];
            boton.style.backgroundColor = "var(--blanco)";
            boton.style.color = color;
            boton.style.border = `2px solid ${color}`;;
            boton.addEventListener("mouseenter", () => {
                boton.style.backgroundColor = color;
                boton.style.color = "white";
            });

            boton.addEventListener("mouseleave", () => {
                boton.style.backgroundColor = "var(--blanco)";
                boton.style.color = color;
            });
        });
        document.addEventListener("DOMContentLoaded", () => {
            const modal = document.getElementById("actualizarDatos");

            if (modal && typeof modal.showModal === "function") {
                if (!modal.open) {
                    modal.showModal();
                    modal.style.display = "flex";
                }
            }
        });


    </script>
</body>

</html>