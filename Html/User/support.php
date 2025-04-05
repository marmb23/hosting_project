<?php
    session_start();  
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - Monitor VM</title>
    <link rel="stylesheet" href="../../Assets/CSS/support_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Barra navegación izquierda, es igual en todas las páginas -->
    <nav class="navbar">
        <div class="navbar-brand">
            <span>
                <a href="../inicio.php">
                    <img src="../../Assets/Fotos/Logo_MAI.png" alt="Icono" class="logo-hover" style="width: 40px; height: 25px; vertical-align: middle;">
                    Hosting MAI
                </a>   
            </span>
        </div>
        <ul class="navbar-menu">
            <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="maquinas.php"><i class="fas fa-server"></i> Mis Maquinas</a></li>
            <li><a href="contenedores.php"><i class="fas fa-box"></i> Mis Contenedores</a></li>
            <li><a href="marketplace.php"><i class="fas fa-store"></i> Marketplace</a></li>
            <li><a href="facturacion.php"><i class="fas fa-credit-card"></i> Facturación</a></li>
            <li><a href="support.php" class="active"><i class="fas fa-ticket-alt"></i> Soporte</a></li>
        </ul>
    </nav>

    <!-- Header con el user, es igual en todas las páginas -->
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
                    <a href="perfil.php"><i class="fas fa-user"></i> Editar Perfil</a>
                    <a href="#"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                </div>
            </div>
        </header>

        <!-- Contenido principal -->
        <main class="container">
            <h1>Soporte</h1>
            <p>Si tienes algún problema, por favor completa el siguiente formulario y nuestro equipo de soporte se
                pondrá en contacto contigo lo antes posible.</p>

            <!-- Formulario de soporte -->
            <form id="support-form" class="support-form">
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" placeholder="Tu correo electrónico" required>
                </div>
                <div class="form-group">
                    <label for="subject">Asunto</label>
                    <input type="text" id="subject" name="subject" placeholder="Asunto del problema" required>
                </div>
                <div class="form-group">
                    <label for="message">Descripción del Problema</label>
                    <textarea id="message" name="message" rows="5" cols="5" placeholder="Describe tu problema aquí..."
                        required></textarea>
                </div>
                <button type="submit" class="btn-primary">Enviar Ticket</button>
            </form>
        </main>
    </div>
    
    <!-- JavaScript -->
    <script src="../../Assets/JavaScript/buttons.js"></script>
</body>

</html>