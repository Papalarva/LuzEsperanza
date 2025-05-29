<?php 
$conexion = new mysqli("localhost", "root", "", "luzesperanza");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>