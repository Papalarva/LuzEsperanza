<?php
include("../php/conexion.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$cedulaProfesional = '';
$errores = [];
$actualizacion_exitosa = false;
$medico = null;
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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['buscar_medico'])) {
        $cedulaProfesional = isset($_POST['cedulaProfesional']) ? strtoupper(trim($_POST['cedulaProfesional'])) : '';

        if (empty($cedulaProfesional)) {
            $errores['buscar'] = "La cédula profesional es obligatoria para buscar.";
        } else {
            $sql = "SELECT * FROM doctores WHERE cedulaProfesional = '$cedulaProfesional'";
            $resultado = $conexion->query($sql);
            if ($resultado && $resultado->num_rows === 1) {
                $medico = $resultado->fetch_assoc();
                $nombre = $medico['nombre'];
                $apellidoPaterno = $medico['apellidoPaterno'];
                $apellidoMaterno = $medico['apellidoMaterno'];
                $especialidad = $medico['especialidad'];
                $telefono = $medico['telefono'];
                $correo = $medico['correoElectronico'];
            } else {
                $errores['buscar'] = "No se encontró ningún médico con esa cédula profesional.";
            }
        }
    }

    if (isset($_POST['actualizar_medico'])) {
        $cedulaProfesional = isset($_POST['cedulaProfesional']) ? strtoupper(trim($_POST['cedulaProfesional'])) : '';
        $nombre = htmlspecialchars(trim($_POST['nombre']));
        $apellidoPaterno = htmlspecialchars(trim($_POST['apellidoPaterno']));
        $apellidoMaterno = htmlspecialchars(trim($_POST['apellidoMaterno']));
        $especialidad = htmlspecialchars(trim($_POST['especialidad']));
        $telefono = trim($_POST['telefono']);
        $correo = trim($_POST['correo']);

        if (empty($nombre))
            $errores['nombre'] = "El nombre es obligatorio.";
        if (empty($apellidoPaterno))
            $errores['apellidoPaterno'] = "El apellido paterno es obligatorio.";
        if (empty($especialidad))
            $errores['especialidad'] = "La especialidad es obligatoria.";
        if (!preg_match('/^\d{10}$/', $telefono))
            $errores['telefono'] = "El teléfono debe tener 10 dígitos.";
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL))
            $errores['correo'] = "El correo no es válido.";

        if (empty($errores)) {
            $sql_update = "UPDATE doctores SET
                nombre='$nombre',
                apellidoPaterno='$apellidoPaterno',
                apellidoMaterno='$apellidoMaterno',
                especialidad='$especialidad',
                telefono='$telefono',
                correoElectronico='$correo'
                WHERE cedulaProfesional='$cedulaProfesional'";

            if ($conexion->query($sql_update)) {
                $actualizacion_exitosa = true;
                $mostrar_modal = true;
            } else {
                $errores['bd'] = "Error al actualizar: " . $conexion->error;
            }
        }
    }
}

$conexion->close();
?>

<?php if ($mostrar_modal): ?>
    <dialog id="actualizacionModal" class="contacto__modal">
        <h2>Actualización exitosa</h2>
        <i class="fa-solid fa-circle-check contacto__icono"></i>
        <p>¡Los datos del médico han sido actualizados correctamente!</p>
        <form method="GET" action="editarDoctor.php">
            <button type="button" class="contacto__boton" onclick="window.location.href='admin.php';">
                Ir al Panel
            </button>
        </form>
    </dialog>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('actualizacionModal');
            if (modal) {
                modal.showModal();
                modal.style.display = "flex";
            }
        });
    </script>
<?php endif; ?>

<h1>Buscar médico por cédula profesional</h1>
<form method="POST" action="">
    <section class="registro__contenedor">
        <label for="cedulaProfesional" class="contacto__label">
                <input type="text" name="cedulaProfesional" id="cedulaProfesional" class="contacto__input" required placeholder=""
                    value="<?= htmlspecialchars($cedulaProfesional ?? '') ?>">
                <span class="contacto__placeholder">cedulaProfesional</span>
                <span class="contacto__error"><?= $errores['cedulaProfesional'] ?? '' ?></span>
        </label> 
        <input type="submit" name="buscar_medico" class="contacto__boton" value="Buscar Médico" />
        <br />
        <span style="color:red;"><?= $errores['buscar'] ?? '' ?></span>
    </section>

</form>

<?php if (!empty($medico)): ?>

    <form method="POST" action="" class="contacto__formulario registro__formulario" novalidate>
        <h2 class="login__titulo">Editar Médico</h2>
        <h3 class="registro__titulo">Datos del Médico</h3>
        <section class="registro__contenedor">

            <input type="hidden" name="cedulaProfesional" value="<?= htmlspecialchars($cedulaProfesional) ?>">

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

            <label for="correo" class="contacto__label">
                <input type="email" name="correo" id="correo" class="contacto__input" required
                    value="<?= htmlspecialchars($correo ?? '') ?>">
                <span class="contacto__placeholder">Correo Electrónico</span>
                <span class="contacto__error"><?= $errores['correo'] ?? '' ?></span>
            </label>

        </section>

        <?php if (!empty($errores['bd'])): ?>
            <p class="contacto__error"><?= $errores['bd'] ?></p>
        <?php endif; ?>

        <div class="contacto__botones"> 
            <input type="submit" name="actualizar_medico" value="Actualizar Médico" class="contacto__boton">
        </div>

    </form>
<?php endif; ?>



</body>

</html>