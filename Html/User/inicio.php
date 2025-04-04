<?php
    session_start();  
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hosting MAI - Inicio</title>
    <link rel="stylesheet" href="../../Assets/CSS/inicio_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header con el user NO TOCAR -->
    <div class="main-content">
        <header>
            <div class="navbar-user">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <span id="username"><?php echo($_SESSION['cliente']['username']);?></span>
                </div>
                <div class="dropdown-menu">
                    <a href="#"><i class="fas fa-user"></i> Editar Perfil</a>
                    <a href="#"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                </div>
            </div>
        </header>

         <!-- Página de inicio de Hosting ficticio -->
         <section class="hero">
            <h1>Bienvenido a Hosting MAI</h1>
            <p>Tu solución confiable y rápida para alojamiento web</p>
            <a href="#" class="btn-primary">Comienza Ahora</a>
        </section>

        <section class="features">
            <div class="feature">
                <i class="fas fa-server"></i>
                <h3>99.9% Uptime Garantizado</h3>
                <p>Nuestros servidores están diseñados para mantener tu sitio activo siempre.</p>
            </div>
            <div class="feature">
                <i class="fas fa-lock"></i>
                <h3>Seguridad de Nivel Empresarial</h3>
                <p>Protección con SSL gratuito y cortafuegos avanzados.</p>
            </div>
            <div class="feature">
                <i class="fas fa-headset"></i>
                <h3>Soporte 24/7</h3>
                <p>Nuestro equipo está disponible en todo momento para ayudarte.</p>
            </div>
        </section>

        <section class="cta">
            <h2>Planes desde $2.99/mes</h2>
            <p>Elige el plan que mejor se adapte a tus necesidades y lanza tu web hoy.</p>
            <a href="#" class="btn-secondary">Ver Planes</a>
        </section>

        <footer>
            <p>&copy; 2025 Hosting MAI. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>