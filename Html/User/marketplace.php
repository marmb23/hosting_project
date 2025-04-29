<?php
    // Inicia la sessió per accedir a les dades de l'usuari
    session_start();

    // Mostra un missatge d'error si el nom d'una màquina o d'un contenidor ja existeix
    if ($_SESSION['duplicated_name']) {
        echo "<script>alert('El nombre ya existe');</script>";
        unset($_SESSION['duplicated_name']);
    }

    // Mostra un missatge d'error si el nom d'una màquina o d'un contenidor conté espais
    if ($_SESSION['space_name']) {
        echo "<script>alert('El nombre no puede contener espacios');</script>";
        unset($_SESSION['space_name']);
    }
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - Monitor VM</title>
    <link rel="stylesheet" href="../../Assets/CSS/marketplace_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    
    <!-- Barra de navegació esquerra, és el mateix a totes les pàgines -->
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

    <!-- Header amb l'usuari, és el mateix a totes les pàgines -->
    <div class="main-content">
        <header>
            <div class="navbar-user">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <span id="username"><?php echo($_SESSION['cliente']['username']);?></span>
                </div>
                <!-- Desplegable de l'usuari -->
                <div class="dropdown-menu">
                    <a href="perfil.php"><i class="fas fa-user"></i> Editar Perfil</a>
                    <a href="../../Php/Auth/cerrar_sesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                </div>
            </div>
        </header>

    <!-- Contingut principal -->
        <main class="container">
            <h1>Marketplace</h1>

            <!-- Configurador de Servidors Virtuals -->
            <section class="marketplace-section">
                <h2>Configurador de Servidor Virtual</h2>
                <div class="configurator-container">
                    <div class="configurator-form">
                        <!-- Formulari per configurar i crear un nou servidor virtual -->
                        <form id="vm-configurator" action="../../Php/VM/agregar.php" method="POST">
                            <div class="config-grid">
                                <div class="config-group">
                                    <!-- Inputs per configurar el nom del servidor -->
                                    <h3>Nombre</h3>
                                    <div class="config-option">
                                        <label>⠀</label>
                                         <div class="input-group">
                                            <input type="text" id="vmid" name="nombrevmid" required weight="1">
                                         </div>
                                    </div>
                                </div>

                                <!-- Inputs per configurar el nombre de vCPUs -->
                                <div class="config-group">
                                    <h3>CPU</h3>
                                    <div class="config-option">
                                        <label>vCPUs</label>
                                        <div class="input-group">
                                            <button type="button" class="btn-minus" onclick="adjustValue('vcpus', -1)">-</button>
                                            <input type="number" id="vcpus" name="vcpus" min="1" max="32" value="2">
                                            <button type="button" class="btn-plus" onclick="adjustValue('vcpus', 1)">+</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Inputs per configurar la quantitat de RAM -->
                                <div class="config-group">
                                    <h3>RAM</h3>
                                    <div class="config-option">
                                        <label>GB</label>
                                        <div class="input-group">
                                            <button type="button" class="btn-minus" onclick="adjustValue('ram', -1)">-</button>
                                            <input type="number" id="ram" name="ram" min="1" max="128" value="4">
                                            <button type="button" class="btn-plus" onclick="adjustValue('ram', 1)">+</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Inputs per configurar l'emmagatzematge SSD NVMe -->
                                <div class="config-group">
                                    <h3>Almacenamiento</h3>
                                    <div class="config-option">
                                        <label>GB SSD NVMe</label>
                                        <div class="input-group">
                                            <button type="button" class="btn-minus" onclick="adjustValue('storage', -10)">-</button>
                                            <input type="number" id="storage" name="storage" min="20" max="2000" value="50">
                                            <button type="button" class="btn-plus" onclick="adjustValue('storage', 10)">+</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Inputs per seleccionar el sistema operatiu -->
                                <div class="config-group">
                                    <h3>Imagen</h3>
                                    <div class="config-option">
                                        <label>Sistema Operativo</label>
                                        <div class="input-group">
                                        <select id="os" name="os" class="custom-select">
                                            <option value="alpine-standard-3.21.3-x86_64.iso">Alpine 3.21.3</option>
                                            <option value="debian">Debian 12.7</option>
                                        </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mostra el preu estimat del servidor configurat -->
                            <div class="config-summary">
                                <div class="price-summary">
                                    <h3>Precio Estimado</h3>
                                    <div class="price">
                                        <span class="amount" name="price" id="price">29,99€</span>
                                        <span class="period">/mes</span>
                                    </div>
                                </div>
                                <input type="hidden" name="price" id="hidden-price" value="29.99">
                                <button type="submit" class="btn-primary">Crear Servidor</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Plans dels Contenidors -->
            <section class="marketplace-section">
                <h2>Contenedor básico y servicios</h2>
                <div class="plans-grid">
                    <!-- Formulari per contractar un contenidor bàsic -->
                    <div class="plan-card">
                        <div class="plan-header">
                            <h3>Contenedor Básico</h3>
                            <div class="price">
                                <span class="amount">14.99€</span>
                                <span class="period">/mes</span>
                            </div>
                        </div>
                        <div class="plan-features">
                            <ul>
                                <li><i class="fas fa-check"></i> 1 vCPU</li>
                                <li><i class="fas fa-check"></i> 2GB RAM</li>
                                <li><i class="fas fa-check"></i> 20GB SSD NVMe</li>
                                <li><i class="fas fa-check"></i> Panel de control</li>
                            </ul>
                        </div>
                        <div class="service-form">
                            <h4>Configuración Inicial</h4>
                            <form id="vm-configurator" action="../../Php/LXC/agregar.php" method="POST">
                                <div class="form-group">
                                    <!-- Inputs per configurar el nom i la contrasenya del contenidor -->
                                    <label>Nombre del contenedor</label>
                                    <input type="text" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="password" name="admin_pass" required>
                                </div>
                                <div class="form-group">
                                    <!-- Inputs per seleccionar la versió de PHP -->
                                    <select id="php" name="php" class="custom-select">
                                        <option value="php8.2">PHP 8.2</option>
                                        <option value="php8.3">PHP 8.3</option>
                                        <option value="php8.4">PHP 8.4</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn-primary">Contratar</button>
                            </form>
                        </div>
                    </div>
                    <div class="plans-grid">
                    <!-- WordPress -->
                    <div class="service-card">
                        <div class="service-header">
                            <i class="fab fa-wordpress"></i>
                            <h3>WordPress</h3>
                        </div>
                        <div class="service-features">
                            <ul>
                                <li><i class="fas fa-check"></i> Instalación automática</li>
                                <li><i class="fas fa-check"></i> Configuración inicial</li>
                                <li><i class="fas fa-check"></i> Temas populares incluidos</li>
                                <li><i class="fas fa-check"></i> Plugins esenciales</li>
                            </ul>
                        </div>
                        <div class="service-form">
                            <h4>Configuración Inicial</h4>
                            <!-- Formulari per instal·lar WordPress amb configuració inicial -->
                            <form id="wordpress-form">
                                <!-- Inputs per configurar el nom del lloc, usuari administrador i contrasenya -->
                                <div class="form-group">
                                    <label>Nombre del sitio</label>
                                    <input type="text" name="site_name" required>
                                </div>
                                <div class="form-group">
                                    <label>Usuario administrador</label>
                                    <input type="text" name="admin_user" required>
                                </div>
                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="password" name="admin_pass" required>
                                </div>
                                <!-- Inputs per configurar el correu electrònic de l'administrador -->
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="admin_email" required>
                                </div>
                                <button type="submit" class="btn-primary">Instalar WordPress</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    
    <!-- JavaScript per calcular preus i gestionar els botons -->
    <script src="../../Assets/JavaScript/calculatePrices.js"></script>
    <script src="../../Assets/JavaScript/buttons.js"></script>
</body>
</html>