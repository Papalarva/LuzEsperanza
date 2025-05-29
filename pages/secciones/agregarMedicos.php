<?php
include("../php/conexion.php");
$errores = [];
$registro_exitoso = false;
$mostrar_modal = false;

$especialidades = [
    "Medicina General",
    "Pediatría",
    "Ginecología",
    "Cardiología",
    "Dermatología",
    "Oftalmología",
    "Neurología",
    "Psiquiatría",
    "Ortopedia",
    "Oncología",
    "Otorrinolaringología",
    "Endocrinología",
    "Urología"
];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["registro_medico"])) {
    $nombre = htmlspecialchars(trim($_POST["nombre"]));
    $apellidoPaterno = htmlspecialchars(trim($_POST["apellidoPaterno"]));
    $apellidoMaterno = htmlspecialchars(trim($_POST["apellidoMaterno"]));
    $especialidad = htmlspecialchars(trim($_POST["especialidad"]));
    $telefono = trim($_POST["telefono"]);
    $sexo = $_POST["sexo"];
    $correo = strtolower(trim($_POST["email"]));
    $fecha_contratacion = $_POST["fechaContratacion"];
    $cedulaProfesional = strtoupper(trim($_POST["cedulaProfesional"]));

    // Validaciones
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

    if (empty($especialidad)) {
        $errores["especialidad"] = "La especialidad es obligatoria.";
    }

    if (empty($telefono)) {
        $errores["telefono"] = "El teléfono es obligatorio.";
    } elseif (!preg_match("/^[0-9]{10}$/", $telefono)) {
        $errores["telefono"] = "El teléfono debe tener 10 dígitos.";
    }

    if (empty($correo)) {
        $errores["correo"] = "El correo es obligatorio.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores["correo"] = "Correo inválido.";
    }

    if (empty($sexo)) {
        $errores['sexo'] = 'Por favor, selecciona una opción.';
    }

    if (empty($fecha_contratacion) || $fecha_contratacion === '0000-00-00') {
        $errores['fecha_contratacion'] = 'Por favor, selecciona una fecha válida.';
    }

    if (empty($cedulaProfesional)) {
        $errores["cedulaProfesional"] = "La cédula profesional es obligatoria.";
    }

    if (empty($errores)) {
        // Escapar datos
        $nombre = $conexion->real_escape_string($nombre);
        $apellidoPaterno = $conexion->real_escape_string($apellidoPaterno);
        $apellidoMaterno = $conexion->real_escape_string($apellidoMaterno);
        $especialidad = $conexion->real_escape_string($especialidad);
        $telefono = $conexion->real_escape_string($telefono);
        $correo = $conexion->real_escape_string($correo);
        $sexo = $conexion->real_escape_string($sexo);
        $fecha_contratacion = $conexion->real_escape_string($fecha_contratacion);
        $cedulaProfesional = $conexion->real_escape_string($cedulaProfesional);

        $verificarCorreo = $conexion->query("SELECT correo FROM doctores WHERE correo = '$correo'");
        $verificarCedula = $conexion->query("SELECT cedulaProfesional FROM doctores WHERE cedulaProfesional = '$cedulaProfesional'");

        if ($verificarCorreo->num_rows > 0) {
            $errores['correo'] = "El correo ya está registrado.";
        }

        if ($verificarCedula->num_rows > 0) {
            $errores['cedulaProfesional'] = "La cédula ya está registrada.";
        }

        if (empty($errores)) {
            try {
                $sql_medico = "INSERT INTO doctores 
                    (nombre, apellidoPaterno, apellidoMaterno, especialidad, telefono, correo, sexo, fechaContratacion, cedulaProfesional)
                    VALUES 
                    ('$nombre', '$apellidoPaterno', '$apellidoMaterno', '$especialidad', '$telefono', '$correo', '$sexo', '$fecha_contratacion', '$cedulaProfesional')";

                if ($conexion->query($sql_medico) === TRUE) {
                    $registro_exitoso = true;
                    $mostrar_modal = true;

                    $nombre = $apellidoPaterno = $apellidoMaterno = $especialidad = $telefono = $correo = $sexo = $fecha_contratacion = $cedulaProfesional = '';
                } else {
                    $errores["bd"] = "Error en la base de datos: " . $conexion->error;
                }
            } catch (Exception $e) {
                $errores["bd"] = "Error al registrar: " . $e->getMessage();
            }
        }
    }
}
?>

<form method="POST" action="" class="contacto__formulario registro__formulario" novalidate>
    <h2 class="login__titulo">Registro</h2>
    <h3 class="registro__titulo">Datos del Médico</h3>
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
        <label for="especialidad" class="contacto__label">
            <select name="especialidad" id="especialidad" class="contacto__input" required>
                <?php foreach ($especialidades as $esp): ?>
                    <option value="<?= $esp ?>" <?= (isset($especialidad) && $especialidad === $esp) ? 'selected' : '' ?>>
                        <?= $esp ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="contacto__placeholder">Especialidad</span>
            <span class="contacto__error"><?= $errores['especialidad'] ?? '' ?></span>
        </label> 

        <label for="telefono" class="contacto__label">
            <input type="number" name="telefono" id="telefono" maxlength="10" pattern="\d{10}" required
                class="contacto__input" value="<?= htmlspecialchars($telefono ?? '') ?>">
            <span class="contacto__placeholder">Teléfono</span>
            <span class="contacto__error"><?= $errores['telefono'] ?? '' ?></span>
        </label>

        <label for="email" class="contacto__label">
            <input type="email" name="email" id="email" class="contacto__input" required
                value="<?= htmlspecialchars($email ?? '') ?>">
            <span class="contacto__placeholder">Correo Electrónico</span>
            <span class="contacto__error"><?= $errores['email'] ?? '' ?></span>
        </label>

        <label for="sexo" class="contacto__label">
            <select name="sexo" id="sexo" class="contacto__input" required>
                <option value="" <?= empty($sexo) ? 'selected' : '' ?>>Selecciona sexo</option>
                <option value="Masculino" <?= (isset($sexo) && $sexo === 'Masculino') ? 'selected' : '' ?>>Masculino
                </option>
                <option value="Femenino" <?= (isset($sexo) && $sexo === 'Femenino') ? 'selected' : '' ?>>Femenino</option>
                <option value="Otro" <?= (isset($sexo) && $sexo === 'Otro') ? 'selected' : '' ?>>Otro</option>
            </select>
            <span class="contacto__placeholder">Sexo</span>
            <span class="contacto__error"><?= $errores['sexo'] ?? '' ?></span>
        </label>

        <label for="fechaContratacion" class="contacto__label">
            <input type="date" name="fechaContratacion" id="fechaContratacion" class="contacto__input" required
                value="<?= htmlspecialchars($fechaContratacion ?? '') ?>" max="<?= date('Y-m-d') ?>">
            <span class="contacto__placeholder">Fecha de Contratación</span>
            <span class="contacto__error"><?= $errores['fechaContratacion'] ?? '' ?></span>
        </label>

        <label for="cedulaProfesional" class="contacto__label">
            <input type="text" name="cedulaProfesional" id="cedulaProfesional" class="contacto__input" required
                value="<?= htmlspecialchars($cedulaProfesional ?? '') ?>">
            <span class="contacto__placeholder">Cédula Profesional</span>
            <span class="contacto__error"><?= $errores['cedulaProfesional'] ?? '' ?></span>
        </label>
    </section>

    <?php if (!empty($errores['bd'])): ?>
        <p class="contacto__error"><?= $errores['bd'] ?></p>
    <?php endif; ?>

    <div class="contacto__botones">
        <input type="reset" value="Borrar" class="contacto__boton">
        <input type="submit" name="registro_medico" value="Registrar Médico" class="contacto__boton">
    </div>

    <?php if ($mostrar_modal): ?>
        <dialog id="registroModal" class="contacto__modal">
            <h2>Registro exitoso</h2>
            <i class="fa-solid fa-circle-check contacto__icono"></i>
            <p>¡El médico ha sido registrado correctamente!</p>
            <form method="GET" action="registrarDoctor.php">
                <button type="button" class="contacto__boton" onclick="window.location.href='admin.php';">Ir al
                    Panel</button>
            </form>
        </dialog>
    <?php endif; ?>
</form>