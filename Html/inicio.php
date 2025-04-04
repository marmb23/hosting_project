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
    <div class="main-content">
        <header>
            <div class="navbar-user">
                <div class="user-info">
                    <form action="../../Html/Auth/login.php" method="POST">
                        <button type="submit" class="btn-user">Iniciar Sesión</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero">
            <h1>Bienvenido a Hosting MAI</h1>
            <p>Tu solución confiable y rápida para servidores virtuales, contenedores y más.</p>
            <a href="marketplace.php" class="btn-primary">Explorar Marketplace</a>
        </section>

        <!-- Productos Destacados -->
        <section class="features">
            <div class="feature">
                <i class="fas fa-server"></i>
                <h3>Servidores Virtuales</h3>
                <p>Configura tu servidor virtual desde solo <strong>29,99€/mes</strong>.</p>
                <a href="marketplace.php#vm-configurator" class="btn-secondary">Configurar Ahora</a>
            </div>
            <div class="feature">
                <i class="fas fa-box"></i>
                <h3>Contenedores</h3>
                <p>Planes desde <strong>14,99€/mes</strong> con recursos dedicados.</p>
                <a href="marketplace.php#contenedores" class="btn-secondary">Ver Planes</a>
            </div>
            <div class="feature">
                <i class="fas fa-tools"></i>
                <h3>Instalación Automática</h3>
                <p>Servicios como WordPress y PrestaShop listos en minutos.</p>
                <a href="marketplace.php#servicios" class="btn-secondary">Descubrir Servicios</a>
            </div>
        </section>

        <!-- Llamado a la Acción -->
        <section class="cta">
            <h2>Planes desde 14,99€/mes</h2>
            <p>Elige el plan que mejor se adapte a tus necesidades y lanza tu proyecto hoy mismo.</p>
            <a href="marketplace.php" class="btn-primary">Comenzar Ahora</a>
        </section>

        <!-- Footer -->
        <footer>
            <p>&copy; 2025 Hosting MAI. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>