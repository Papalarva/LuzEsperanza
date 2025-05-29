<?php
include("php/conexion.php");
$errores = [];
$registro_exitoso = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["registro_usuario"])) {
    // Recolección de datos
    $nombre = htmlspecialchars(trim($_POST["nombre"]));
    $primer_apellido = htmlspecialchars(trim($_POST["primer_apellido"]));
    $segundo_apellido = htmlspecialchars(trim($_POST["segundo_apellido"]));
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $sexo = $_POST["sexo"];
    $estado_nacimiento = htmlspecialchars(trim($_POST["estado_nacimiento"]));
    $curp = strtoupper(trim($_POST["curp"]));
    $telefono = trim($_POST["telefono"]);
    $correo = strtolower(trim($_POST["correo"]));
    $contraseña = $_POST["contrasena"];
    $confirmar = $_POST["confirmar_contrasena"];

    // Validaciones
    if (!preg_match("/^[a-zA-ZÁÉÍÓÚÜÑáéíóúüñ\s]+$/u", $nombre)) {
        $errores["nombre"] = "Nombre no válido.";
    }
    if (!preg_match("/^[a-zA-ZÁÉÍÓÚÜÑáéíóúüñ\s]+$/u", $primer_apellido)) {
        $errores["primer_apellido"] = "Primer apellido no válido.";
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores["correo"] = "Correo inválido.";
    }
    if (strlen($curp) !== 18) {
        $errores["curp"] = "El CURP debe tener 18 caracteres.";
    }
    if (!preg_match("/^[0-9]{10}$/", $telefono)) {
        $errores["telefono"] = "Teléfono inválido (10 dígitos).";
    }
    if ($contraseña !== $confirmar) {
        $errores["contrasena"] = "Las contraseñas no coinciden.";
    }

    // Si no hay errores, se insertan en la base de datos
    if (empty($errores)) {
        $nombre_completo = "$nombre $primer_apellido $segundo_apellido";
        $hash = password_hash($contraseña, PASSWORD_DEFAULT);

        $sql_usuario = "INSERT INTO Usuarios (CorreoElectronico, NombreCompleto, Telefono, ContraseñaHash)
                        VALUES ('$correo', '$nombre_completo', '$telefono', '$hash')";
        $sql_paciente = "INSERT INTO Pacientes (Nombre, PrimerApellido, SegundoApellido, FechaNacimiento, Sexo, EstadoNacimiento, CURP, Telefono)
                        VALUES ('$nombre', '$primer_apellido', '$segundo_apellido', '$fecha_nacimiento', '$sexo', '$estado_nacimiento', '$curp', '$telefono')";

        if ($conexion->query($sql_usuario) === TRUE && $conexion->query($sql_paciente) === TRUE) {
            $registro_exitoso = true;
        } else {
            $errores["bd"] = "Error en la base de datos: " . $conexion->error;
        }
    }
}
?>
