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
$mensaje = "";

// Eliminar cita si se recibe id por GET y pertenece al usuario
if (isset($_GET['eliminar_id'])) {
    $idEliminar = intval($_GET['eliminar_id']);
    // Validar que la cita pertenece al usuario
    $check = $conexion->query("SELECT * FROM citas WHERE idCita = $idEliminar AND correoPaciente = '" . $conexion->real_escape_string($correo) . "'");
    if ($check->num_rows > 0) {
        $del = $conexion->query("DELETE FROM citas WHERE idCita = $idEliminar");
        if ($del) {
            $mensaje = "Cita eliminada correctamente.";
        } else {
            $errores['bd'] = "Error al eliminar la cita: " . $conexion->error;
        }
    } else {
        $errores['bd'] = "No tienes permiso para eliminar esta cita.";
    }
}

// Actualizar cita si se envió el formulario de edición
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["editar_cita"])) {
    $idCita = intval($_POST["idCita"]);
    $idMedico = intval($_POST["idMedico"]);
    $fecha = trim($_POST["fecha"]);
    $hora = trim($_POST["hora"]);
    $motivo = htmlspecialchars(trim($_POST["motivo"]));

    // Validaciones básicas (similar a crear)
    if ($idMedico <= 0) {
        $errores["idMedico"] = "Debes seleccionar un médico.";
    } else {
        $verificarMedico = $conexion->query("SELECT idDoctor FROM doctores WHERE idDoctor = $idMedico");
        if ($verificarMedico->num_rows == 0) {
            $errores["idMedico"] = "El médico seleccionado no existe.";
        }
    }
    if (empty($fecha)) {
        $errores["fecha"] = "La fecha es obligatoria.";
    } elseif ($fecha < date('Y-m-d')) {
        $errores["fecha"] = "La fecha no puede ser anterior a hoy.";
    }
    if (empty($hora)) {
        $errores["hora"] = "La hora es obligatoria.";
    }
    if (empty($motivo)) {
        $errores["motivo"] = "El motivo es obligatorio.";
    }

    // Verificar que la cita pertenece al usuario antes de actualizar
    $check = $conexion->query("SELECT * FROM citas WHERE idCita = $idCita AND correoPaciente = '" . $conexion->real_escape_string($correo) . "'");
    if ($check->num_rows == 0) {
        $errores['bd'] = "No tienes permiso para modificar esta cita.";
    }

    if (empty($errores)) {
        $sqlUpdate = "UPDATE citas SET idMedico=$idMedico, fecha='" . $conexion->real_escape_string($fecha) . "', hora='" . $conexion->real_escape_string($hora) . "', motivo='" . $conexion->real_escape_string($motivo) . "' WHERE idCita = $idCita";
        if ($conexion->query($sqlUpdate)) {
            $mensaje = "Cita actualizada correctamente.";
        } else {
            $errores['bd'] = "Error al actualizar la cita: " . $conexion->error;
        }
    }
}

// Obtener citas del usuario junto con el nombre del doctor
$sql = "SELECT c.idCita, c.fecha, c.hora, c.motivo, d.nombre AS nombreDoctor, c.idMedico
        FROM citas c
        JOIN doctores d ON c.idMedico = d.idDoctor
        WHERE c.correoPaciente = '" . $conexion->real_escape_string($correo) . "'
        ORDER BY c.fecha, c.hora";
$resultado = $conexion->query($sql);

// Obtener lista de doctores para selects de edición
$doctores = [];
$resDoc = $conexion->query("SELECT idDoctor, nombre FROM doctores ORDER BY nombre");
if ($resDoc) {
    while ($row = $resDoc->fetch_assoc()) {
        $doctores[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mis Citas | Hospital Luz de Esperanza</title>
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
            <h2 class="login__titulo">Mis Citas Médicas</h2>

            <?php if ($mensaje): ?>
                <p style="color: green; font-weight: bold; margin-bottom:1rem;"><?= htmlspecialchars($mensaje) ?></p>
            <?php endif; ?>

            <?php if (!empty($errores['bd'])): ?>
                <p class="contacto__error"><?= $errores['bd'] ?></p>
            <?php endif; ?>

            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php
                $colores = ['#731a4b', '#72a69c', '#2192bf', '#025e73'];
                $index = 0;
                ?>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <?php
                    $color = $colores[$index % count($colores)];
                    $index++;
                    ?>
                    <div class="mensajes" style="border-color: <?= $color ?>; background-color: var(--blanco);">
                        <form method="POST" class="mensaje__contenido" novalidate>
                            <input type="hidden" name="idCita" value="<?= $fila['idCita'] ?>" />
                            
                            <label for="idMedico_<?= $fila['idCita'] ?>" class="contacto__label">
                                <select name="idMedico" id="idMedico_<?= $fila['idCita'] ?>" class="contacto__input" required>
                                    <option value="0">Selecciona un Médico</option>
                                    <?php foreach ($doctores as $doc): ?>
                                        <option value="<?= $doc['idDoctor'] ?>" <?= $fila['idMedico'] == $doc['idDoctor'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($doc['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </label>

                            <label for="fecha_<?= $fila['idCita'] ?>" class="contacto__label">
                                <input type="date" name="fecha" id="fecha_<?= $fila['idCita'] ?>" class="contacto__input" required
                                       value="<?= htmlspecialchars($fila['fecha']) ?>" min="<?= date('Y-m-d') ?>" />
                            </label>

                            <label for="hora_<?= $fila['idCita'] ?>" class="contacto__label">
                                <input type="time" name="hora" id="hora_<?= $fila['idCita'] ?>" class="contacto__input" required
                                       value="<?= htmlspecialchars($fila['hora']) ?>" />
                            </label>

                            <label for="motivo_<?= $fila['idCita'] ?>" class="contacto__label">
                                <textarea name="motivo" id="motivo_<?= $fila['idCita'] ?>" class="contacto__input" rows="3" required><?= htmlspecialchars($fila['motivo']) ?></textarea>
                            </label>

                            <div class="contacto__botones" style="gap:0.5rem;">
                                <button type="submit" name="editar_cita" class="contacto__boton" style="flex:1;">
                                    Guardar Cambios
                                </button>
                                <a href="?eliminar_id=<?= $fila['idCita'] ?>"
                                   class="mensaje__boton"
                                   style="flex:1; background-color:#b02a37; border-color:#b02a37; color:#fff; font-size:1rem; text-align:center; padding:0.75rem; border-radius:1rem;"
                                   onclick="return confirm('¿Eliminar esta cita?');">
                                    Eliminar
                                </a>
                            </div>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No tienes citas agendadas.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">
        <h2 class="footer__titulo">Hospital Luz de Esperanza</h2>
        <h3 class="footer__subtitulo">Todos los derechos conservados</h3>
    </footer>

    <script>
        // Aplica los estilos a los botones y mensajes al estilo del ejemplo
        const colores = ['#731a4b', '#72a69c', '#2192bf', '#025e73'];
        const mensajes = document.querySelectorAll('.mensajes');
        const
