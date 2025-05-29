<?php
include("../php/conexion.php");
$errores = [];
$registro_exitoso = false;
$mostrar_modal = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["registro_usuario"])) {
    $nombre = htmlspecialchars(trim($_POST["nombre"]));
    $apellidoPaterno = htmlspecialchars(trim($_POST["apellidoPaterno"]));
    $apellidoMaterno = htmlspecialchars(trim($_POST["apellidoMaterno"]));
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $sexo = $_POST["sexo"];
    $estado_nacimiento = htmlspecialchars(trim($_POST["estado_nacimiento"]));
    $curp = strtoupper(trim($_POST["curp"]));
    $telefono = trim($_POST["telefono"]);
    $correo = strtolower(trim($_POST["correo"]));
    $contraseña = $_POST["contrasena"];

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

    if (empty($correo)) {
        $errores["correo"] = "El correo es obligatorio.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores["correo"] = "Correo inválido.";
    }

    if (empty($contraseña)) {
        $errores["contrasena"] = "La contraseña es obligatorio.";
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
    if (empty($curp)) {
        $errores["curp"] = "El CURP es obligatorio.";
    } elseif (strlen($curp) !== 18) {
        $errores["curp"] = "El CURP debe tener 18 caracteres.";
    }

    if (empty($telefono)) {
        $errores["telefono"] = "El teléfono es obligatorio.";
    } elseif (!preg_match("/^[0-9]{10}$/", $telefono)) {
        $errores["telefono"] = "El teléfono debe tener 10 dígitos.";
    }

    if (empty($errores)) {

        $correo = $conexion->real_escape_string($correo);
        $telefono = $conexion->real_escape_string($telefono);
        $contraseña = $conexion->real_escape_string($contraseña);

        $nombre = $conexion->real_escape_string($nombre);
        $apellidoPaterno = $conexion->real_escape_string($apellidoPaterno);
        $apellidoMaterno = $conexion->real_escape_string($apellidoMaterno);
        $fecha_nacimiento = $conexion->real_escape_string($fecha_nacimiento);
        $sexo = $conexion->real_escape_string($sexo);
        $estado_nacimiento = $conexion->real_escape_string($estado_nacimiento);
        $curp = $conexion->real_escape_string($curp);

        $verificarCorreo = $conexion->query("SELECT correoElectronico FROM usuarios WHERE correoElectronico = '$correo'");
        $verificarCurp = $conexion->query("SELECT CURP FROM pacientes WHERE CURP = '$curp'");

        if ($verificarCorreo->num_rows > 0) {
            $errores['correo'] = "El correo ya está registrado.";
        }

        if ($verificarCurp->num_rows > 0) {
            $errores['curp'] = "El CURP ya está registrado.";
        }

        if (empty($errores)) {
            // Inicia la transacción
            $conexion->begin_transaction();

            try {
                // DEFINIR las sentencias SQL aquí
                $sql_usuario = "INSERT INTO usuarios (correoElectronico, telefono, contraseña)
                        VALUES ('$correo', '$telefono', '$contraseña')";

                $sql_paciente = "INSERT INTO pacientes (nombre, apellidoPaterno, apellidoMaterno, fechaNacimiento, sexo, estadoNacimiento, CURP, telefono, correoElectronico)
                         VALUES ('$nombre', '$apellidoPaterno', '$apellidoMaterno', '$fecha_nacimiento', '$sexo', '$estado_nacimiento', '$curp', '$telefono', '$correo')";

                // Ejecuta las dos consultas
                $conexion->query($sql_usuario);
                $conexion->query($sql_paciente);

                // Si todo va bien, confirma los cambios
                $conexion->commit();
                $registro_exitoso = true;
                $mostrar_modal = true;
            } catch (Exception $e) {
                // Si hay error, revierte los cambios
                $conexion->rollback();
                $errores["bd"] = "Error en la base de datos: " . $conexion->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registro | Hospital Luz de Esperanza</title>
    <meta name="description" content="Regístrate en el Hospital Luz de Esperanza para acceder a servicios personalizados, agendar citas y gestionar tu información médica de forma segura." />
    <meta name="keywords" content="registro, hospital, Luz de Esperanza, crear cuenta, pacientes, cita médica, servicios médicos" />
    <meta name="author" content="Hospital Luz de Esperanza" />
    <meta name="robots" content="index, follow" />
    <link rel="icon" href="../assets/img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
    <link rel="stylesheet" href="../css/responsive.css">
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
                    <li class="menu__elemento"><a class="menu__enlace" href="iniciarSesion.php">Iniciar sesión</a></li>
                </ul>
            </nav>
        </section>
    </header>
    <div class="portada login">
        <section class="login__contenedor">
            <form method="POST" action="" class="contacto__formulario registro__formulario" novalidate>
                <h2 class="login__titulo">Registro</h2>
                <h3 class="registro__titulo">Cuenta</h3>
                <section class="registro__contenedor">
                    <label for="correo" class="contacto__label">
                        <input type="email" name="correo" id="correo" class="contacto__input" required
                            value="<?= htmlspecialchars($correo ?? '') ?>">
                        <span class="contacto__placeholder">Correo Electrónico</span>
                        <span class="contacto__error"><?= $errores['correo'] ?? '' ?></span>
                    </label>

                    <label for="contrasena" class="contacto__label">
                        <input type="password" name="contrasena" id="contrasena" class="contacto__input" required>
                        <span class="contacto__placeholder">Contraseña</span>
                        <span class="contacto__error"><?= $errores['contrasena'] ?? '' ?></span>
                    </label>
                </section>
                <h3 class="registro__titulo">Datos Personales</h3>
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
                                Masculino</option>
                            <option value="Femenino" <?= (isset($sexo) && $sexo === 'Femenino') ? 'selected' : '' ?>>
                                Femenino</option>
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
                    <input type="reset" value="Borrar" class="contacto__boton">
                    <input type="submit" name="registro_usuario" value="Registrarse" class="contacto__boton">
                </div>

                <?php if ($mostrar_modal): ?>
                    <dialog id="registroModal" class="contacto__modal">
                        <h2>Registro exitoso</h2>
                        <i class="fa-solid fa-circle-check contacto__icono"></i>
                        <p>¡El registro se realizó correctamente!</p>
                        <p><strong>Gracias por registrarte.</strong></p>
                        <form method="GET" action="registrar.php">
                            <button type="button" class="contacto__boton"
                                onclick="window.location.href='iniciarSesion.php';">Iniciar sesión</button>
                        </form>
                    </dialog>
                <?php endif; ?>
            </form>
        </section>
    </div>
    <footer class="footer">
        <h2 class="footer__titulo">Hospital Luz de Esperanza</h2>
        <h3 class="footer__subtitulo">Todos los derechos conservados</h3>
    </footer>
    <script src="https://kit.fontawesome.com/d335561c97.js" crossorigin="anonymous"></script>
    <script src="../js/script.js"></script>
</body>

</html>