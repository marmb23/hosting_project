<?php
    session_start();  
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar perfil</title>
    <link rel="stylesheet" href="../../Assets/CSS/perfil_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    
    <!-- Barra navegación izquierda NO TOCAR -->
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
            <li><a href="marketplace.php"  class="active"><i class="fas fa-store"></i> Marketplace</a></li>
            <li><a href="facturacion.php"><i class="fas fa-credit-card"></i> Facturación</a></li>
            <li><a href="support.php"><i class="fas fa-ticket-alt"></i> Soporte</a></li>
        </ul>
    </nav>

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
                    <a href="perfil.php"><i class="fas fa-user"></i> Editar Perfil</a>
                    <a href="#"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                </div>
            </div>
        </header>
<!-- Main content -->
        <main class="container">
            <h1>Editar Perfil</h1>
            <div class="card">
                <div class="card-header">
                    <h2>Información del Usuario</h2>
                </div>
                <div class="card-body">
                    <form action="actualizar_perfil.php" method="POST" class="form-editar-perfil">
                        <div class="form-group">
                            <label for="username">Nombre de usuario:</label>
                            <input type="text" id="username" name="username" value="<?php echo $_SESSION['cliente']['username']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Dejar en blanco para no cambiar">
                        </div>
                        <div class="form-group">
                            <label for="apellido">Apellido</label>
                            <input type="text" id="apellido" name="apellido" placeholder="Dejar en blanco para no cambiar">
                        </div>
                        <div class="form-group">
                            <label for="fecha-nacimiento">Fecha de nacimiento:</label>
                            <input type="date" id="fecha-nacimiento" name="fecha-nacimiento">
                        </div>
                        <div class="form-group">
                            <label for="email">Correo electrónico:</label>
                            <input type="email" id="email" name="email" value="<?php echo $_SESSION['cliente']['email']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono: </label>
                            <input type="text" id="telefono" name="telefono" placeholder="Dejar en blanco para no cambiar">
                        </div>
                        <div class="form-group">
                            <label for="password">Nueva contraseña:</label>
                            <input type="password" id="password" name="password" placeholder="Dejar en blanco para no cambiar">
                        </div>
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>

        




    <!-- JavaScript -->
    <script src="../../Assets/JavaScript/script.js"></script>
</body>
</html>