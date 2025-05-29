<?php
include("../php/conexion.php");

// Eliminar contacto si se recibe ID por GET
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM contactos WHERE id = $id";
    if ($conexion->query($sql)) {
        header("Location: admin.php?seccion=mensajes");
        exit;
    } else {
        echo "Error al eliminar: " . $conexion->error;
    }
}

// Obtener todos los contactos
$sql = "SELECT * FROM contactos";
$resultado = $conexion->query($sql);
?>

<h2>MENSAJES</h2>
<style>
    .mensajes {
        display: flex;
        flex-direction: column;
        padding: 1rem;
        border: 2px solid var(--azul);
        border-radius: 1rem;
        width: 100%;
        justify-content: center;
        align-items: center;
        gap: 1rem;
    }

    .mensaje__contenido {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        flex: 1;
        min-width: 300px;
    }

    .mensaje__boton {
        flex: 1;
        display: flex;
        text-decoration: none;
        align-items: center;
        justify-content: center;
        min-width: 300px;
        width: 100%;
        height: 100%;
        border-radius: 1rem;
        font-size: 2rem;
        transition: var(--transition);
        cursor: pointer;
    }

    .mensaje__boton:hover {
        transform: scale(0.9);
    }

    @media (min-width: 600px) {
        .mensajes {
            flex-direction: row;
        }

        .mensaje__contenido,
        .mensaje__boton {
            min-width: 250px;
        }
    }
</style>
<?php while ($fila = $resultado->fetch_assoc()): ?>
    <div class="mensajes">
        <div class="mensaje__contenido">
            <h2>NOMBRE: <?= $fila['nombre'] ?></h2>
            <p>CORREO: <?= $fila['email'] ?></p>
            <p>TELEFONO: <?= $fila['telefono'] ?></p>
            <p>ASUNTO: <?= $fila['asunto'] ?></p>
            <p>MENSAJE: <?= $fila['mensaje'] ?></p>
        </div>
        <a class="mensaje__boton" 
           href="admin.php?seccion=mensajes&id=<?= $fila['id'] ?>" 
           onclick="return confirm('¿Marcar este mensaje como resuelto?');">
            Resuelto
        </a>
    </div>
<?php endwhile; ?>

<script>
    const colores = ['#731a4b', '#72a69c', '#2192bf', '#025e73'];
    const mensajes = document.querySelectorAll('.mensajes');
    const botones = document.querySelectorAll('.mensaje__boton');

    mensajes.forEach((mensaje, index) => {
        const color = colores[index % colores.length];
        const id = index + 1;
        mensaje.style.backgroundColor = "var(--blanco)"; 
        mensaje.style.border = `2px solid ${color}`;
    });
    botones.forEach((boton, index) => {
        const color = colores[index % colores.length];
        const id = index + 1;
        boton.style.backgroundColor = color;
        boton.style.color = "var(--blanco)";
        boton.style.border = `2px solid ${color}`;

        boton.addEventListener("mouseleave", () => {
            boton.style.backgroundColor = color;
            boton.style.color = "white";
        });

        boton.addEventListener("mouseenter", () => {
            boton.style.backgroundColor = "var(--blanco)";
            boton.style.color = color;
        });

        boton.addEventListener("click", function () {
            if (window.innerWidth >= 600) {
                const modal = document.getElementById("modalServicio" + id);
                if (modal) modal.showModal();
                body.style.overflow = "hidden";
            }
        });
    });
</script>

<?php $conexion->close(); ?>