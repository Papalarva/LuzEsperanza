<?php
session_start();
include("../php/conexion.php");

// Verificar si está logueado
if (!isset($_SESSION['correo'])) {
    header("Location: iniciarSesion.php");
    exit;
}

$correo = $_SESSION['correo'];
$errores = [];
$idMedico = 0;
$fecha = "";
$hora = "";
$motivo = "";
$registro_exitoso = false;

// Obtener lista de médicos para el select
$doctores = [];
$result = $conexion->query("SELECT idDoctor, nombre FROM doctores ORDER BY nombre");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $doctores[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["crear_cita"])) {
    // Asignar y limpiar entradas
    $idMedico = isset($_POST["idMedico"]) ? intval($_POST["idMedico"]) : 0;
    $fecha = isset($_POST["fecha"]) ? trim($_POST["fecha"]) : "";
    $hora = isset($_POST["hora"]) ? trim($_POST["hora"]) : "";
    $motivo = isset($_POST["motivo"]) ? htmlspecialchars(trim($_POST["motivo"])) : "";

    // Validaciones correo (desde sesión)
    if (empty($correo)) {
        $errores["correo"] = "El correo del paciente es obligatorio.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores["correo"] = "Correo electrónico no válido.";
    } else {
        // Verificar que el correo existe en la tabla usuario
        $verificarCorreo = $conexion->query("SELECT correoElectronico FROM usuarios WHERE correoElectronico = '" . $conexion->real_escape_string($correo) . "'");
        if ($verificarCorreo->num_rows == 0) {
            $errores["correo"] = "El correo del paciente no está registrado.";
        }
    }

    // Validación médico
    if ($idMedico <= 0) {
        $errores["idMedico"] = "Debes seleccionar un médico.";
    } else {
        $verificarMedico = $conexion->query("SELECT idDoctor FROM doctores WHERE idDoctor = $idMedico");
        if ($verificarMedico->num_rows == 0) {
            $errores["idMedico"] = "El médico seleccionado no existe.";
        }
    }

    // Validar fecha
    if (empty($fecha)) {
        $errores["fecha"] = "La fecha es obligatoria.";
    } elseif ($fecha < date('Y-m-d')) {
        $errores["fecha"] = "La fecha no puede ser anterior a hoy.";
    }

    // Validar hora
    if (empty($hora)) {
        $errores["hora"] = "La hora es obligatoria.";
    }

    // Validar motivo
    if (empty($motivo)) {
        $errores["motivo"] = "El motivo es obligatorio.";
    }

    // Si no hay errores, insertar en la base de datos
    if (empty($errores)) {
        $correoEsc = $conexion->real_escape_string($correo);
        $motivoEsc = $conexion->real_escape_string($motivo);
        $fechaEsc = $conexion->real_escape_string($fecha);
        $horaEsc = $conexion->real_escape_string($hora);

        $sql = "INSERT INTO citas (correoPaciente, idMedico, fecha, hora, motivo)
                VALUES ('$correo', $idMedico, '$fechaEsc', '$horaEsc', '$motivoEsc')";

        if ($conexion->query($sql)) {
            $registro_exitoso = true;
            // Limpiar formulario
            $idMedico = 0;
            $fecha = "";
            $hora = "";
            $motivo = "";
        } else {
            $errores["bd"] = "Error al guardar la cita: " . $conexion->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Agendar Cita | Hospital Luz de Esperanza</title>
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
                    <li class="menu__elemento"><a class="menu__enlace" href="iniciarSesion.php">Iniciar sesión</a></li>
                </ul>
            </nav>
        </section>
    </header>

    <main class="portada login">
        <section class="login__contenedor">
            <form method="POST" action="" class="contacto__formulario registro__formulario" novalidate>
                <h2 class="login__titulo">Agendar Cita Médica</h2>

                <label for="correo" class="contacto__label">
                    <input type="email" name="correo" id="correo" class="contacto__input" required readonly
                        value="<?= htmlspecialchars($correo) ?>">
                    <span class="contacto__placeholder">Correo Electrónico del Paciente</span>
                    <span class="contacto__error"><?= $errores['correo'] ?? '' ?></span>
                </label>

                <label for="idMedico" class="contacto__label">
                    <select name="idMedico" id="idMedico" class="contacto__input" required>
                        <option value="0" <?= $idMedico == 0 ? 'selected' : '' ?>>Selecciona un Médico</option>
                        <?php foreach ($doctores as $doc): ?>
                            <option value="<?= $doc['idDoctor'] ?>" <?= $idMedico == $doc['idDoctor'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($doc['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="contacto__placeholder">Médico</span>
                    <span class="contacto__error"><?= $errores['idMedico'] ?? '' ?></span>
                </label>

                <label for="fecha" class="contacto__label">
                    <input type="date" name="fecha" id="fecha" class="contacto__input" required
                        value="<?= htmlspecialchars($fecha) ?>" min="<?= date('Y-m-d') ?>">
                    <span class="contacto__placeholder">Fecha</span>
                    <span class="contacto__error"><?= $errores['fecha'] ?? '' ?></span>
                </label>

                <label for="hora" class="contacto__label">
                    <input type="time" name="hora" id="hora" class="contacto__input" required
                        value="<?= htmlspecialchars($hora) ?>">
                    <span class="contacto__placeholder">Hora</span>
                    <span class="contacto__error"><?= $errores['hora'] ?? '' ?></span>
                </label>

                <label for="motivo" class="contacto__label">
                    <textarea name="motivo" id="motivo" class="contacto__input" required rows="3"
                        placeholder="Motivo de la cita"><?= htmlspecialchars($motivo) ?></textarea>
                    <span class="contacto__error"><?= $errores['motivo'] ?? '' ?></span>
                </label>

                <?php if (!empty($errores['bd'])): ?>
                    <p class="contacto__error"><?= $errores['bd'] ?></p>
                <?php endif; ?>

                <div class="contacto__botones">
                    <input type="reset" value="Borrar" class="contacto__boton" />
                    <input type="submit" name="crear_cita" value="Agendar Cita" class="contacto__boton" />
                </div>

                <?php if ($registro_exitoso): ?>
                    <dialog id="actualizarDatos" class="contacto__modal">
                        <h2>Cita agendada</h2>
                        <i class="fa-solid fa-circle-check contacto__icono"></i>
                        <p>La cita se ha registrado correctamente.</p>
                        <button type="button" class="contacto__boton"
                            onclick="window.location.href='agendarCita.php';">Cerrar</button>
                    </dialog>
                <?php endif; ?>
                <script>
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
            </form>
        </section>
    </main>
    <footer class="footer">
        <h2 class="footer__titulo">Hospital Luz de Esperanza</h2>
        <h3 class="footer__subtitulo">Todos los derechos conservados</h3>
    </footer>
    <script src="https://kit.fontawesome.com/d335561c97.js" crossorigin="anonymous"></script>
    <script src="../js/script.js"></script>
</body>

</html>