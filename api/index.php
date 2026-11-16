<?php
/**
 * TECH BARBERÍA (UTN) - Frontend
 * Diseño minimalista, moderno y de lujo discreto.
 */
?>
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Barbería — UTN</title>

    <!-- Google Fonts: Playfair Display + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN con configuración personalizada -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['"Playfair Display"', 'serif'],
                        sans: ['"Inter"', 'sans-serif'],
                    },
                    colors: {
                        ink: '#0A0A0A',
                        bone: '#FAF8F3',
                        gold: '#C9A24B',
                        'line-light': '#E6DFD0',
                        'line-dark': '#262626',
                        muted: {
                            light: '#6B6558',
                            dark: '#A39D8F',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #0A0A0A;
            color: #FAF8F3;
            font-family: 'Inter', sans-serif;
        }

        /* Enfoque accesible visible */
        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible {
            outline: 2px solid #C9A24B;
            outline-offset: 2px;
        }

        /* Scrollbar sutil */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0A0A0A;
        }
        ::-webkit-scrollbar-thumb {
            background: #262626;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #C9A24B;
        }

        /* Ocultar scrollbar en carrusel horizontal */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="font-sans antialiased text-bone bg-ink min-h-screen flex flex-col selection:bg-gold selection:text-ink">

    <!-- NAVBAR (Sticky con fondo translúcido) -->
    <?php include_once __DIR__ . '/../components/navbar.php'; ?>

    <!-- HERO SECTION (Dos columnas: texto en Playfair y foto en blanco y negro) -->
    <?php include_once __DIR__ . '/../components/hero.php'; ?>

    <!-- SECCIÓN DE SERVICIOS (Fondo Bone / Claro) -->
    <?php include_once __DIR__ . '/../components/servicios.php'; ?>

    <!-- SECCIÓN DE BARBEROS (Fondo Ink / Oscuro) -->
    <?php include_once __DIR__ . '/../components/barberos.php'; ?>

    <!-- TERMINAL DE RESERVAS (Fondo Bone / Claro) -->
    <?php include_once __DIR__ . '/../components/reservas.php'; ?>

    <!-- CONSULTAR CITA Y MI CUENTA (Fondo Ink / Oscuro) -->
    <?php include_once __DIR__ . '/../components/gestion_citas.php'; ?>

    <!-- FOOTER (Horario, UTN, Contacto y Enlace a Admin) -->
    <?php include_once __DIR__ . '/../components/footer.php'; ?>

    <!-- Lógica JavaScript intacta para peticiones y eventos -->
    <script src="../js/landing.js"></script>
</body>

</html>
