<?php

include("php/conexion.php");
$nombre = $email = $telefono = $asunto = $mensaje = "";
$errores = [];
$mostrar_modal = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['confirmar_insert'])) {
        $nombre = $conexion->real_escape_string($_POST['nombre']);
        $email = $conexion->real_escape_string($_POST['email']);
        $telefono = $conexion->real_escape_string($_POST['telefono']);
        $asunto = $conexion->real_escape_string($_POST['asunto']);
        $mensaje = $conexion->real_escape_string($_POST['mensaje']);

        $sql = "INSERT INTO contactos (nombre, email, telefono, asunto, mensaje) VALUES ('$nombre', '$email', '$telefono', '$asunto', '$mensaje')";

        if ($conexion->query($sql) === TRUE) {
            header("Location: index.php");
            exit;
        } else {
            $errores['bd'] = "Error al guardar los datos: " . $conexion->error;
        }

    } else {
        $nombre = htmlspecialchars(mb_strtoupper(trim($_POST['nombre']), 'UTF-8'));
        $email = htmlspecialchars(trim($_POST['email']));
        $telefono = htmlspecialchars(trim($_POST['telefono']));
        $asunto = htmlspecialchars(mb_strtoupper(trim($_POST['asunto']), 'UTF-8'));
        $mensaje = htmlspecialchars(trim($_POST['mensaje']));

        if (!preg_match('/^[a-zA-ZÁÉÍÓÚÜÑáéíóúüñ\s]+$/u', $nombre)) {
            $errores['nombre'] = 'Este campo no puede estar vacío ni contener números o símbolos.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'Correo electrónico no válido.';
        }
        if (!preg_match('/^[0-9]{10}$/', $telefono)) {
            $errores['telefono'] = 'El teléfono debe tener de 10 dígitos.';
        }
        if (!preg_match('/\S+/', $asunto)) {
            $errores['asunto'] = 'El asunto no puede estar vacío.';
        }
        if (!preg_match('/\S+/', $mensaje)) {
            $errores['mensaje'] = 'El mensaje no puede estar vacío.';
        }

        if (empty($errores)) {
            $mostrar_modal = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Luz de Esperanza</title>
    <meta name="description"
        content="Hospital privado en Ciudad Juárez. Cuidamos tu salud con excelencia médica, atención integral, trato humano y tecnología de vanguardia desde 2005.">
    <meta name="keywords"
        content="Hospital, Ciudad Juárez, atención médica, salud, médicos, especialidades, contacto, instalaciones, servicios médicos">
    <meta name="author" content="Hospital Luz de Esperanza">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#2192bf">
    <link rel="icon" href="assets/img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">

</head>

<body>
    <header class="header">
        <section class="cabecera">
            <div href="index.php" class="cabecera__logo">
                <a href="">
                    <img class="cabecera__img" src="assets/img/logo.png" alt="Logo de Hospital Luz de Esperanza">
                </a>
                <p class="cabecera__nombre">Hospital Luz de Esperanza</p>
                <i id="botonMenu" class="fa fa-bars menu__boton" onclick="abrirMenu()" aria-hidden="true"></i>
            </div>
            <nav id="menu" class="menu">
                <ul class="menu__lista">
                    <li class="menu__elemento"><a class="menu__enlace" href="#"
                            onclick="menuDesplegable(1)">Conócenos</a>
                        <ul class="menu__desplegable" id="menuConocenos">
                            <li><a class="menu__enlace" href="pages/nosotros.html">¿Quiénes somos?</a></li>
                            <li><a class="menu__enlace" href="#contacto">Contáctanos</a></li>
                        </ul>
                    </li>
                    <li class="menu__elemento"><a class="menu__enlace" href="#servicios">Servicios</a></li>
                    <li class="menu__elemento"><a class="menu__enlace" onclick="menuDesplegable(2)" href="#">Accesos
                            rápidos</a>
                        <ul class="menu__desplegable" id="menuAccesos">
                            <li><a class="menu__enlace" href="">Resultados</a></li>
                            <li><a class="menu__enlace" href="pages/agendarCita.php">Agendar cita</a></li>
                            <li><a class="menu__enlace" href="pages/modificarCita.php">Cambiar cita</a></li> 
                        </ul>
                    </li>
                    <li class="menu__elemento"><a class="menu__enlace" href="pages/iniciarSesion.php">Iniciar sesión</a>
                    </li>
                </ul>
            </nav>
        </section>
        <div class="portada">
            <div class="portada__texto">
                <h3 class="portada__frase">"Cuidamos tu salud con dedicación y compromiso, porque tu bienestar es
                    nuestra prioridad."</h3>
                <p class="portada__subfrase">Atención médica de calidad, con un enfoque humano.</p>
            </div>
            <video class="portada__video" id="portadaVideo" src="" autoplay muted playsinline
                oncontextmenu="return false;"></video>
            <script>
                function cambiarVideo() {
                    const video = document.querySelector("#portadaVideo");
                    const nuevoSrc = window.innerWidth <= 480
                        ? "assets/video/portada__movil.mp4"
                        : "assets/video/portada__desktop.mp4";

                    if (video.getAttribute("src") !== nuevoSrc) {
                        video.setAttribute("src", nuevoSrc);
                    }
                }

                window.addEventListener("DOMContentLoaded", function () {
                    cambiarVideo();
                    menuDesplegable(0);
                    textoBienvenida();
                    splideServicios();
                    splideInstalaciones();
                });
                window.addEventListener("resize", function () {
                    cambiarVideo();
                    menuDesplegable(0);
                    textoBienvenida();
                    splideServicios();
                    splideInstalaciones();
                });
            </script>
        </div>
    </header>
    <main class="main">
        <section class="bienvenida">
            <div class="bienvenida__texto">
                <h3 class="bienvenida__subtitulo" id="bienvenidaSubtitulo">Bienvenidos al</h3>
                <h2 class="bienvenida__titulo" id="bienvenidaTitulo">Bienvenid@s</h2>
                <p class="bienvenida__parrafo">En Hospital Luz de Esperanza cuidamos tu salud con excelencia médica y un
                    trato verdaderamente humano. Desde 2005, ofrecemos atención integral y accesible con tecnología de
                    vanguardia y un equipo comprometido con tu bienestar. Somos un hospital privado con corazón, donde
                    cada paciente es una prioridad y cada vida, una esperanza.</p>
            </div>
            <picture class="bienvenida__imagen">
                <source media="(min-width: 1024px)" srcset="assets/img/bienvenida__grande.png">
                <source media="(min-width: 600px)" srcset="assets/img/bienvenida__mediana.png">
                <source media="(max-width: 599px)" srcset="assets/img/logo.png">
                <img class="bienvenida__logo" src="assets/img/logo.png" alt="Imagen adaptable">
            </picture>
        </section>
        <section id="servicios" class="servicios">
            <h3 class="secciones__subtitulo">Conoce nuestros</h3>
            <h2 class="secciones__titulo">Servicios</h2>
            <section id="carruselServicios" class="splide" aria-label="Beautiful Images">
                <div class="splide__track">
                    <ul class="splide__list">
                        <li class="splide__slide servicios__tarjeta">
                            <i class="servicios__icono fa-solid fa-truck-medical"></i>
                            <img class="servicios__imagen" src="assets/img/urgenciasEmergencias.jpg" alt="">
                            <h3>Urgencias y Emergencias</h3>
                            <p class="servicios__parrafo">Servicio disponible las 24 horas para atender casos críticos
                                como accidentes, infartos,
                                traumatismos graves o complicaciones médicas repentinas. Equipado con tecnología
                                avanzada y personal especializado para estabilizar pacientes y derivarlos a las áreas
                                correspondientes.</p>
                            <a class="servicios__enlace" href="">Agendar cita</a>
                        </li>
                        <dialog id="modalServicio1" class="servicios__modal">
                            <h2>Urgencias y Emergencias</h2>
                            <p>Servicio disponible las 24 horas para atender casos críticos como accidentes, infartos,
                                traumatismos graves o complicaciones médicas repentinas. Equipado con tecnología
                                avanzada y personal especializado para estabilizar pacientes y derivarlos a las áreas
                                correspondientes.</p>
                            <div class="servicios__botones">
                                <a class="servicios__boton" href="">Agendar cita</a><a class="servicios__boton"
                                    id="cerrarModal1">Cerrar</a>
                            </div>
                        </dialog>
                        <li class="splide__slide servicios__tarjeta">
                            <i class="servicios__icono fa-solid fa-stethoscope"></i>
                            <img class="servicios__imagen" src="assets/img/cirujiaGeneralEspecialidad.jpg" alt="">
                            <h3>Cirugía General y Especializada</h3>
                            <p class="servicios__parrafo">Abarca procedimientos quirúrgicos programados o de emergencia,
                                desde apendicectomías
                                hasta operaciones cardíacas. Incluye cirugía laparoscópica mínimamente invasiva y
                                seguimiento postoperatorio para una recuperación segura.</p>
                            <a class="servicios__enlace" href="">Agendar cita</a>
                        </li>
                        <dialog id="modalServicio2" class="servicios__modal">
                            <h2>Cirugía General y Especializada</h2>
                            <p>Abarca procedimientos quirúrgicos programados o de emergencia,
                                desde apendicectomías
                                hasta operaciones cardíacas. Incluye cirugía laparoscópica mínimamente invasiva y
                                seguimiento postoperatorio para una recuperación segura.</p>
                            <div class="servicios__botones">
                                <a class="servicios__boton" href="">Agendar cita</a><a class="servicios__boton"
                                    id="cerrarModal2">Cerrar</a>
                            </div>
                        </dialog>
                        <li class="splide__slide servicios__tarjeta">
                            <i class="servicios__icono fa-solid fa-person-breastfeeding"></i>
                            <img class="servicios__imagen" src="assets/img/maternidadNeonatologia.jpg" alt="">
                            <h3>Maternidad y Neonatología</h3>
                            <p class="servicios__parrafo">Atención integral para embarazos de bajo y alto riesgo, partos
                                naturales, cesáreas y
                                cuidados posnatales. Incluye unidad de neonatología para recién nacidos prematuros o con
                                complicaciones, con incubadoras y monitoreo constante.</p>
                            <a class="servicios__enlace" href="">Agendar cita</a>
                        </li>
                        <dialog id="modalServicio3" class="servicios__modal">
                            <h2>Maternidad y Neonatología</h2>
                            <p>Atención integral para embarazos de bajo y alto riesgo, partos
                                naturales, cesáreas y
                                cuidados posnatales. Incluye unidad de neonatología para recién nacidos prematuros o con
                                complicaciones, con incubadoras y monitoreo constante.</p>
                            <div class="servicios__botones">
                                <a class="servicios__boton" href="">Agendar cita</a><a class="servicios__boton"
                                    id="cerrarModal3">Cerrar</a>
                            </div>
                        </dialog>
                        <li class="splide__slide servicios__tarjeta">
                            <i class="servicios__icono fa-solid fa-baby"></i>
                            <img class="servicios__imagen" src="assets/img/pediatria.jpg" alt="">
                            <h3>Pediatría</h3>
                            <p class="servicios__parrafo">Servicio dedicado a la salud infantil, enfocado en prevención,
                                diagnóstico y tratamiento
                                de enfermedades en pacientes desde recién nacidos hasta adolescentes. Incluye
                                vacunación, control de crecimiento y manejo de patologías crónicas.</p>
                            <a class="servicios__enlace" href="">Agendar cita</a>
                        </li>
                        <dialog id="modalServicio4" class="servicios__modal">
                            <h2>Pediatría</h2>
                            <p>Servicio dedicado a la salud infantil, enfocado en prevención,
                                diagnóstico y tratamiento
                                de enfermedades en pacientes desde recién nacidos hasta adolescentes. Incluye
                                vacunación, control de crecimiento y manejo de patologías crónicas.</p>
                            <div class="servicios__botones">
                                <a class="servicios__boton" href="">Agendar cita</a><a class="servicios__boton"
                                    id="cerrarModal4">Cerrar</a>
                            </div>
                        </dialog>
                        <li class="splide__slide servicios__tarjeta">
                            <i class="servicios__icono fa-solid fa-virus"></i>
                            <img class="servicios__imagen" src="assets/img/oncologia.jpg" alt="">
                            <h3>Oncología</h3>
                            <p class="servicios__parrafo">Diagnóstico y tratamiento multidisciplinario del cáncer, con
                                terapias como quimioterapia,
                                radioterapia e inmunoterapia. Ofrece acompañamiento psicológico y programas de
                                rehabilitación para mejorar la calidad de vida del paciente.</p>
                            <a class="servicios__enlace" href="">Agendar cita</a>
                        </li>
                        <dialog id="modalServicio5" class="servicios__modal">
                            <h2>Oncología</h2>
                            <p>Diagnóstico y tratamiento multidisciplinario del cáncer, con
                                terapias como quimioterapia,
                                radioterapia e inmunoterapia. Ofrece acompañamiento psicológico y programas de
                                rehabilitación para mejorar la calidad de vida del paciente.</p>
                            <div class="servicios__botones">
                                <a class="servicios__boton" href="">Agendar cita</a><a class="servicios__boton"
                                    id="cerrarModal5">Cerrar</a>
                            </div>
                        </dialog>
                        <li class="splide__slide servicios__tarjeta">
                            <i class="servicios__icono fa-solid fa-heart"></i>
                            <img class="servicios__imagen" src="assets/img/cardiologia.jpg" alt="">
                            <h3>Cardiología</h3>
                            <p class="servicios__parrafo">Prevención, evaluación y manejo de enfermedades
                                cardiovasculares. Incluye pruebas como
                                electrocardiogramas, ecocardiogramas, cateterismos y rehabilitación cardíaca
                                post-infarto.</p>
                            <a class="servicios__enlace" href="">Agendar cita</a>
                        </li>
                        <dialog id="modalServicio6" class="servicios__modal">
                            <h2>Cardiología</h2>
                            <p>Prevención, evaluación y manejo de enfermedades
                                cardiovasculares. Incluye pruebas como
                                electrocardiogramas, ecocardiogramas, cateterismos y rehabilitación cardíaca
                                post-infarto.</p>
                            <div class="servicios__botones">
                                <a class="servicios__boton" href="">Agendar cita</a><a class="servicios__boton"
                                    id="cerrarModal6">Cerrar</a>
                            </div>
                        </dialog>
                        <li class="splide__slide servicios__tarjeta">
                            <i class="servicios__icono fa-solid fa-crutch"></i>
                            <img class="servicios__imagen" src="assets/img/terapiaFisica.jpg" alt="">
                            <h3>Terapia Física y Rehabilitación</h3>
                            <p class="servicios__parrafo">Programas personalizados para recuperar movilidad tras
                                cirugías, accidentes o
                                enfermedades neurológicas. Utiliza técnicas como hidroterapia, electroestimulación y
                                ejercicios adaptados.</p>
                            <a class="servicios__enlace" href="">Agendar cita</a>
                        </li>
                        <dialog id="modalServicio7" class="servicios__modal">
                            <h2>Terapia Física y Rehabilitación</h2>
                            <p>Programas personalizados para recuperar movilidad tras
                                cirugías, accidentes o
                                enfermedades neurológicas. Utiliza técnicas como hidroterapia, electroestimulación y
                                ejercicios adaptados.</p>
                            <div class="servicios__botones">
                                <a class="servicios__boton" href="">Agendar cita</a><a class="servicios__boton"
                                    id="cerrarModal7">Cerrar</a>
                            </div>
                        </dialog>
                        <li class="splide__slide servicios__tarjeta">
                            <i class="servicios__icono fa-solid fa-brain"></i>
                            <img class="servicios__imagen" src="assets/img/psicologia.jpg" alt="">
                            <h3>Salud Mental</h3>
                            <p class="servicios__parrafo">Atención psiquiátrica y psicológica para trastornos como
                                depresión, ansiedad, bipolaridad
                                o adicciones. Incluye terapia individual, grupal y hospitalización breve en casos de
                                crisis agudas.</p>
                            <a class="servicios__enlace" href="">Agendar cita</a>
                        </li>
                        <dialog id="modalServicio8" class="servicios__modal">
                            <h2>Salud Mental</h2>
                            <p>Atención psiquiátrica y psicológica para trastornos como
                                depresión, ansiedad, bipolaridad
                                o adicciones. Incluye terapia individual, grupal y hospitalización breve en casos de
                                crisis agudas.</p>
                            <div class="servicios__botones">
                                <a class="servicios__boton" href="">Agendar cita</a><a class="servicios__boton"
                                    id="cerrarModal8">Cerrar</a>
                            </div>
                        </dialog>
                        <li class="splide__slide servicios__tarjeta">
                            <i class="servicios__icono fa-solid fa-briefcase-medical"></i>
                            <img class="servicios__imagen" src="assets/img/medicinaInterna.jpg" alt="">
                            <h3>Medicina Interna</h3>
                            <p class="servicios__parrafo">Diagnóstico y tratamiento integral de enfermedades que afectan
                                a órganos internos, como
                                diabetes, hipertensión, enfermedades respiratorias y gastrointestinales. El servicio
                                actúa como eje coordinador entre distintas especialidades para un abordaje clínico
                                completo.</p>
                            <a class="servicios__enlace" href="">Agendar cita</a>
                        </li>
                        <dialog id="modalServicio9" class="servicios__modal">
                            <h2>Medicina Interna</h2>
                            <p>Diagnóstico y tratamiento integral de enfermedades que afectan
                                a órganos internos, como
                                diabetes, hipertensión, enfermedades respiratorias y gastrointestinales. El servicio
                                actúa como eje coordinador entre distintas especialidades para un abordaje clínico
                                completo.</p>
                            <div class="servicios__botones">
                                <a class="servicios__boton" href="">Agendar cita</a><a class="servicios__boton"
                                    id="cerrarModal9">Cerrar</a>
                            </div>
                        </dialog>
                        <li class="splide__slide servicios__tarjeta">
                            <i class="servicios__icono fa-solid fa-hospital"></i>
                            <img class="servicios__imagen" src="assets/img/unidadCuidadosIntensivos.jpg" alt="">
                            <h3>Unidad de Cuidados Intensivos</h3>
                            <p class="servicios__parrafo">Área especializada en la atención de pacientes en estado
                                crítico que requieren vigilancia
                                continua, soporte vital avanzado y monitoreo permanente. Cuenta con equipo médico
                                multidisciplinario y tecnología de punta para intervenciones inmediatas.</p>
                            <a class="servicios__enlace" href="">Agendar cita</a>
                        </li>
                        <dialog id="modalServicio10" class="servicios__modal">
                            <h2>Unidad de Cuidados Intensivos</h2>
                            <p>Área especializada en la atención de pacientes en estado
                                crítico que requieren vigilancia
                                continua, soporte vital avanzado y monitoreo permanente. Cuenta con equipo médico
                                multidisciplinario y tecnología de punta para intervenciones inmediatas.</p>
                            <div class="servicios__botones">
                                <a class="servicios__boton" href="">Agendar cita</a><a class="servicios__boton"
                                    id="cerrarModal10">Cerrar</a>
                            </div>
                        </dialog>
                        <li class="splide__slide servicios__tarjeta">
                            <i class="servicios__icono fa-solid fa-venus-mars"></i>
                            <img class="servicios__imagen" src="assets/img/ginecologiaSaludFemenina.jpeg" alt="">
                            <h3>Ginecología y Salud Femenina</h3>
                            <p class="servicios__parrafo">Atención especializada en salud reproductiva y ginecológica.
                                Incluye control prenatal,
                                detección de cáncer ginecológico, tratamientos hormonales, planificación familiar y
                                manejo de enfermedades como endometriosis o síndrome de ovario poliquístico.</p>
                            <a class="servicios__enlace" href="">Agendar cita</a>
                        </li>
                        <dialog id="modalServicio11" class="servicios__modal">
                            <h2>Ginecología y Salud Femenina</h2>
                            <p>Atención especializada en salud reproductiva y ginecológica.
                                Incluye control prenatal,
                                detección de cáncer ginecológico, tratamientos hormonales, planificación familiar y
                                manejo de enfermedades como endometriosis o síndrome de ovario poliquístico.</p>
                            <div class="servicios__botones">
                                <a class="servicios__boton" href="">Agendar cita</a><a class="servicios__boton"
                                    id="cerrarModal11">Cerrar</a>
                            </div>
                        </dialog>
                        <li class="splide__slide servicios__tarjeta">
                            <i class="servicios__icono fa-solid fa-x-ray"></i>
                            <img class="servicios__imagen" src="assets/img/imagenologiaDiagnostico.jpg" alt="">
                            <h3>Imagenología y Diagnóstico por Imágenes</h3>
                            <p class="servicios__parrafo">Servicio equipado con tecnología como rayos X, ultrasonido,
                                tomografía computarizada (CT)
                                y resonancia magnética (RM). Facilita diagnósticos precisos y oportunos para diversas
                                condiciones médicas, apoyando a todas las especialidades clínicas.</p>
                            <a class="servicios__enlace" href="">Agendar cita</a>
                        </li>
                        <dialog id="modalServicio12" class="servicios__modal">
                            <h2>Imagenología y Diagnóstico por Imágenes</h2>
                            <p>Servicio equipado con tecnología como rayos X, ultrasonido,
                                tomografía computarizada (CT)
                                y resonancia magnética (RM). Facilita diagnósticos precisos y oportunos para diversas
                                condiciones médicas, apoyando a todas las especialidades clínicas.</p>
                            <div class="servicios__botones">
                                <a class="servicios__boton" href="">Agendar cita</a><a class="servicios__boton"
                                    id="cerrarModal12">Cerrar</a>
                            </div>
                        </dialog>
                    </ul>
                </div>
            </section>
        </section>
        <?php

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

        $especialidad = $_GET['especialidad'] ?? '';
        ?>
        <section id="especialidades">
            <h3 class="secciones__subtitulo">Encuentra a tu</h3>
            <h2 class="secciones__titulo">Médico</h2>

            <form method="GET" action="index.php#especialidades" class="contacto__formulario">
                <label for="especialidad" class="contacto__label">
                    <select name="especialidad" id="especialidad" class="contacto__input" required>
                        <option value="" disabled <?= empty($especialidad) ? 'selected' : '' ?>>Selecciona una
                            especialidad</option>
                        <?php foreach ($especialidades as $esp): ?>
                            <option value="<?= $esp ?>" <?= ($especialidad === $esp) ? 'selected' : '' ?>>
                                <?= $esp ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="contacto__placeholder">Especialidad</span>
                    <span class="contacto__error"></span>
                </label>

                <button type="submit" class="contacto__boton">Buscar</button>
            </form>
        </section>
        <style>
            .doctores__especialidad {
                display: flex;
                align-items: stretch; 
                flex-wrap: wrap;
                padding: 1rem;
                border: 2px solid var(--rosa);
                border-radius: 1rem;
                max-width: min-content;
                width: auto; 
                height: min-content;
                justify-content: center;
                align-items: center;
                gap: 1rem;            
            }
            .doctores__imagen {
                object-fit: cover;   
                aspect-ratio: 1 / 1; 
                max-height: 250px;
                width: auto;
            }
            .doctores__texto{
                display: flex;
                flex-direction: column;
                gap: 1rem;
                height: max-content;
            }
            .doctores__titulo{
                color: var(--rosa);
            }
        </style>

        <?php
        if (!empty($especialidad)) {
            $consulta = "SELECT * FROM doctores WHERE especialidad = '$especialidad'";
            $resultado = mysqli_query($conexion, $consulta);

            echo "<section class='resultado__doctores'>";
            echo "<h3>Doctores en: <strong>$especialidad</strong></h3>";

            if (mysqli_num_rows($resultado) > 0) {
                while ($doctor = mysqli_fetch_assoc($resultado)) {
                    echo "
                    <div class = 'doctores__especialidad'>
                        <img class = 'doctores__imagen' src='assets/img/indexDoctor.png' alt=''>
                        <div class = 'doctores__texto'>
                            <h2 class='doctores__titulo'>" . $doctor['nombre'] . "</h2>
                            <p>$especialidad</p>
                            <h2 class='doctores__titulo'>Formación</h2>
                            <p>Ced. Prof. " . $doctor['cedulaProfesional'] . "</p>
                            <p>Correo. " . $doctor['correoElectronico'] . "</p>
                            <p>Tel. " . $doctor['telefono'] . "</p>

                        </div>
                    </div>";
                }
            } else {
                echo "<p>No se encontraron doctores con esa especialidad.</p>";
            }

            echo "</section>";
        }
        ?>

        <section>
            <h3 class="secciones__subtitulo">Conoce nuestras</h3>
            <h2 class="secciones__titulo">Instalaciones</h2>
            <section id="carruselInstalaciones" class="splide" aria-label="My Awesome Gallery">

                <div class="splide__track">
                    <ul class="splide__list">
                        <li class="splide__slide">
                            <img class="instalaciones__imagen" src="assets/img/instalacionUno.jpg" alt="">
                        </li>
                        <li class="splide__slide">
                            <img class="instalaciones__imagen" src="assets/img/instalacionDos.jpg" alt="">
                        </li>
                        <li class="splide__slide">
                            <img class="instalaciones__imagen" src="assets/img/instalacionTres.jpg" alt="">
                        </li>
                        <li class="splide__slide">
                            <img class="instalaciones__imagen" src="assets/img/instalacionCuatro.jpg" alt="">
                        </li>
                        <li class="splide__slide">
                            <img class="instalaciones__imagen" src="assets/img/instalacionCinco.jpg" alt="">
                        </li>
                        <li class="splide__slide">
                            <img class="instalaciones__imagen" src="assets/img/instalacionSeis.jpg" alt="">
                        </li>
                    </ul>
                </div>
            </section>
            <ul id="thumbnails" class="thumbnails instalaciones__miniatura">
                <li class="thumbnail instalaciones__mini">
                    <img src="assets/img/instalacionUno.jpg" alt="">
                </li>
                <li class="thumbnail instalaciones__mini">
                    <img src="assets/img/instalacionDos.jpg" alt="">
                </li>
                <li class="thumbnail instalaciones__mini">
                    <img src="assets/img/instalacionTres.jpg" alt="">
                </li>
                <li class="thumbnail instalaciones__mini">
                    <img src="assets/img/instalacionCuatro.jpg" alt="">
                </li>
                <li class="thumbnail instalaciones__mini">
                    <img src="assets/img/instalacionCinco.jpg" alt="">
                </li>
                <li class="thumbnail instalaciones__mini">
                    <img src="assets/img/instalacionSeis.jpg" alt="">
                </li>
            </ul>
        </section>
        <div class="contactoUbicacion">
            <section id="contacto" class="contacto">
                <h3 class="secciones__subtitulo">No dudes en </h3>
                <h2 class="secciones__titulo">Contáctanos</h2>

                <form method="POST" action="index.php#contacto" class="contacto__formulario" novalidate>
                    <label for="nombre" class="contacto__label">
                        <input placeholder="" type="text" name="nombre" id="nombre" class="contacto__input"
                            value="<?= htmlspecialchars($nombre) ?>">
                        <span class="contacto__placeholder">Nombre Completo</span>
                        <span class="contacto__error"><?= $errores['nombre'] ?? '' ?></span>
                    </label>

                    <label for="email" class="contacto__label">
                        <input placeholder="" type="email" name="email" id="email" class="contacto__input"
                            value="<?= htmlspecialchars($email) ?>">
                        <span class="contacto__placeholder">Correo</span>
                        <span class="contacto__error"><?= $errores['email'] ?? '' ?></span>
                    </label>

                    <label for="telefono" class="contacto__label">
                        <input placeholder="" type="number" name="telefono" id="telefono" class="contacto__input"
                            maxlength="10" value="<?= htmlspecialchars($telefono) ?>">
                        <span class="contacto__placeholder">Teléfono</span>
                        <span class="contacto__error"><?= $errores['telefono'] ?? '' ?></span>
                    </label>

                    <label for="asunto" class="contacto__label">
                        <input placeholder="" type="text" name="asunto" id="asunto" class="contacto__input"
                            value="<?= htmlspecialchars($asunto) ?>">
                        <span class="contacto__placeholder">Asunto</span>
                        <span class="contacto__error"><?= $errores['asunto'] ?? '' ?></span>
                    </label>

                    <label for="mensaje" class="contacto__label">
                        <textarea name="mensaje" id="mensaje"
                            class="contacto__input contacto__textarea"><?= htmlspecialchars($mensaje) ?></textarea>
                        <span class="contacto__placeholder">Mensaje</span>
                        <span class="contacto__error"><?= $errores['mensaje'] ?? '' ?></span>
                    </label>

                    <?php if (!empty($errores['bd'])): ?>
                        <p class="contacto__error"><?= $errores['bd'] ?></p>
                    <?php endif; ?>

                    <div class="contacto__botones">
                        <input class="contacto__boton" type="reset" value="Borrar">
                        <input class="contacto__boton" type="submit" value="Enviar">
                    </div>

                    <?php if ($mostrar_modal): ?>
                        <dialog id="contactoModal" class="contacto__modal">
                            <h2>Envio exitoso</h2>
                            <i class="fa-solid fa-circle-check contacto__icono"></i>
                            <p>Se envió correctamente el mensaje. </p>
                            <p><strong>¡Gracias por contactarnos!</strong></p>

                            <form method="POST" action="index.php">
                                <input type="hidden" name="nombre" value="<?= htmlspecialchars($nombre) ?>">
                                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                                <input type="hidden" name="telefono" value="<?= htmlspecialchars($telefono) ?>">
                                <input type="hidden" name="asunto" value="<?= htmlspecialchars($asunto) ?>">
                                <input type="hidden" name="mensaje" value="<?= htmlspecialchars($mensaje) ?>">
                                <input type="hidden" name="confirmar_insert" value="1">
                                <button type="submit" class="contacto__boton">Cerrar</button>
                            </form>
                        </dialog>
                    <?php endif; ?>

                </form>
            </section>

            <section class="ubicacion">
                <h3 class="secciones__subtitulo">No dudes en </h3>
                <h2 class="secciones__titulo">Venir</h2>
                <iframe class="ubicacion__mapa"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d424.14474166476697!2d-106.45901897611866!3d31.738889845652793!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x86e75946126b362d%3A0x690eacbaf9651bc5!2sCentro%20M%C3%A9dico%20de%20Especialidades%20de%20Ciudad%20Ju%C3%A1rez!5e0!3m2!1ses!2smx!4v1748407231205!5m2!1ses!2smx"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </section>
        </div>
    </main>
    <footer class="footer">
        <h2 class="footer__titulo">Hospital Luz de Esperanza</h2>
        <h3 class="footer__subtitulo">Todos los derechos conservados</h3>
    </footer>
    <script src="js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/@splidejs/splide-extension-grid@0.4.1/dist/js/splide-extension-grid.min.js"></script>
    <script src="https://kit.fontawesome.com/d335561c97.js" crossorigin="anonymous"></script>

</body>

</html>