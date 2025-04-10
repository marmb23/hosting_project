<?php
    session_start();
    $redirectUrl = isset($_SESSION['cliente']) ? "User/marketplace.php" : "Auth/login.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hosting MAI - Inicio</title>
    <link rel="stylesheet" href="../Assets/CSS/inicio_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header con el user NO TOCAR -->
    <header>
        <div class="navbar-user">
            <div class="user-info">
                <form action="../Html/Auth/register.php" method="POST">
                    <button type="submit" class="btn-user">Registrarse</button>
                </form>
                <form action="../Html/Auth/login.php" method="POST">
                    <button type="submit" class="btn-user">Iniciar sesión</button>
                </form>
            </div>
        </div>
    </header>
    
        <!-- Hero Section -->
        <section class="hero">
            <h1><img src="../Assets/Fotos/Logo_MAI.png" alt="Icono" class="logo-hover" style="width: 70px; height: 50px; vertical-align: middle;">
            <span class="brand-name">Hosting MAI</span></h1>
                <p>Tu solución confiable y rápida para servidores virtuales, contenedores y más.</p><br>
                <a href="<?php echo $redirectUrl; ?>" class="btn-primary">Explorar Marketplace</a>
        </section>

    <div class="main-content">
        <section class="features">
            <div class="card">
                <i class="fas fa-server"></i>
                <h3>Servidores Virtuales</h3>
                <p>Configura tu servidor virtual desde solo <strong>29,99€/mes</strong>.</p>
                <a href="<?php echo $redirectUrl; ?>#vm-configurator" class="btn-primary">Configurar Ahora</a>
            </div>
            <div class="card">
                <i class="fas fa-box"></i>
                <h3>Contenedores</h3>
                <p>Planes desde <strong>14,99€/mes</strong> con recursos dedicados.</p>
                <a href="<?php echo $redirectUrl; ?>#contenedores" class="btn-primary">Ver Planes</a>
            </div>
            <div class="card">
                <i class="fas fa-tools"></i>
                <h3>Instalación Automática</h3>
                <p>Servicios como WordPress y PrestaShop listos en minutos.</p>
                <a href="<?php echo $redirectUrl; ?>#serv" class="btn-primary">Descubrir Servicios</a>
            </div>
        </section>
    </div>

        <!-- Llamado a la Acción -->
        <section class="cta">
            <h2>Planes desde 14,99€/mes</h2>
            <p>Elige el plan que mejor se adapte a tus necesidades y lanza tu proyecto hoy mismo.</p>
            <a href="<?php echo $redirectUrl; ?>" class="btn-primary">Comenzar Ahora</a>
        </section>

        <!-- Footer -->
        <footer>
            <p>&copy; 2025 Hosting MAI. Todos los derechos reservados.</p>
        </footer>
</body>
</html>