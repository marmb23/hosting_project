<?php
    // Inclou el fitxer de configuració de la base de dades
    require_once("../../Php/Config/database.php");

    // Inicia la sessió per accedir a les dades de l'usuari
    session_start();

    // Obté la informació de l'usuari actual des de la base de dades
    $database = new Database();
    $conn = $database->getConnection();
    $user = $database->verifyUser($_SESSION['cliente']['username']);
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
    
    <!-- Barra de navegació esquerra, comuna a totes les pàgines -->
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
            <li><a href="support.php"><i class="fas fa-ticket-alt"></i> Soporte</a></li>
        </ul>
    </nav>

        <!-- Header amb la informació de l'usuari, comuna a totes les pàgines -->    <div class="main-content">
        <header>
            <div class="navbar-user">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <span id="username"><?php echo($_SESSION['cliente']['username']);?></span>
                </div>
                <!-- Dropdown pel menú de l'usuari -->
                <div class="dropdown-menu">
                    <a href="perfil.php"><i class="fas fa-user"></i> Editar Perfil</a>
                    <a href="../../Php/Auth/cerrar_sesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                </div>
            </div>
        </header>
        <!-- Contingut principal -->
        <main class="container">
            <h1>Editar Perfil</h1>
            <div class="card">
                <div class="card-header">
                    <!-- Mostra les dades de l'usuari -->
                    <h2>Información del Usuario</h2>
                <!-- Mostra un missatge de confirmació si els canvis s'han guardat correctament -->
                <?php if (isset($_GET['exito'])): ?>
                    <p style="color: white;">Datos cambiados correctamente.</p>
                <?php $_GET['exito'] = null; endif; ?>
                </div>
                <div class="card-body">
                    <!-- Formulari per editar les dades de l'usuari -->
                    <form action="../../Php/Auth/updateUser.php" method="POST" class="form-editar-perfil">
                        <div class="form-group">
                            <label for="username">Nombre de usuario:</label>
                            <!-- Input per editar el nom d'usuari -->
                            <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <!-- Input per editar el nom real de l'usuari -->
                            <input type="text" id="nombre" name="nombre" value="<?php echo $user['forename']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido">Apellido</label>
                            <!-- Input per editar el cognom de l'usuari -->
                            <input type="text" id="apellido" name="apellido" value="<?php echo $user['surname']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="fecha-nacimiento">Fecha de nacimiento:</label>
                            <!-- Input per editar la data de naixement de l'usuari -->
                            <input type="date" id="fecha-nacimiento" name="fecha-nacimiento" value="<?php echo $user['birthdate']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo electrónico:</label>
                            <!-- Input per editar el correu electrònic de l'usuari -->
                            <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono: </label>
                            <!-- Input per editar el número de telèfon de l'usuari -->
                            <input type="text" id="telefono" name="telefono" value="<?php echo $user['phone']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Nueva contraseña:</label>
                            <!-- Input per canviar la contrasenya de l'usuari -->
                            <input type="password" id="password" name="password">
                        </div>
                            <!-- Botó per guardar els canvis realitzats al perfil -->
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript per gestionar els botons i altres funcionalitats -->
    <script src="../../Assets/JavaScript/buttons.js"></script>
</body>
</html>