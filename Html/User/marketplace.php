<?php
    session_start();  
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
            <li><a href="marketplace.php"  class="active"><i class="fas fa-store"></i> Marketplace</a></li>
            <li><a href="facturacion.php"><i class="fas fa-credit-card"></i> Facturación</a></li>
            <li><a href="support.php"><i class="fas fa-ticket-alt"></i> Soporte</a></li>
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
                <!-- Desplegable del usuario -->
                <div class="dropdown-menu">
                    <a href="perfil.php"><i class="fas fa-user"></i> Editar Perfil</a>
                    <a href="../../Php/Auth/cerrar_sesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                </div>
            </div>
        </header>

    <!-- Contenido principal -->
        <main class="container">
            <h1>Marketplace</h1>

            <!-- Configurador de Servidores Virtuales -->
            <section class="marketplace-section">
                <h2>Configurador de Servidor Virtual</h2>
                <div class="configurator-container">
                    <div class="configurator-form">
                        <!-- Form que envía los datos al PHP -->
                        <form id="vm-configurator" action="../../Php/VM/agregar.php" method="POST">
                            <div class="config-grid">
                                <div class="config-group">
                                    <h3>Nombre</h3>
                                    <div class="config-option">
                                        <label>⠀</label>
                                         <div class="input-group">
                                            <input type="text" id="vmid" name="nombrevmid" required weight="1">
                                         </div>
                                    </div>
                                </div>

                                <!-- CPU -->
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

                                <!-- RAM -->
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

                                <!-- Almacenamiento -->
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

                                <!-- Sistema Operativo -->
                                <div class="config-group">
                                    <h3>Imagen</h3>
                                    <div class="config-option">
                                        <label>Sistema Operativo</label>
                                        <div class="input-group">
                                        <select id="os" name="os" class="custom-select">
                                            <option value="alpine-standard-3.21.3-x86_64.iso">Alpine 3.21.3</option>
                                            <option value="debian">Debian</option>
                                        </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Resumen y Precio -->
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

            <!-- Planes de Contenedores -->
            <section class="marketplace-section">
                <h2>Contenedores</h2>
                <div class="plans-grid">
                    <!-- Contenedor Básico -->
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
                                <li>⠀</li>

                            </ul>
                        </div>
                        <button class="btn btn-primary">Contratar</button>
                    </div>

                    <!-- Contenedor Pro -->
                    <div class="plan-card">
                        <div class="plan-header">
                            <h3>Contenedor Pro</h3>
                            <div class="price">
                                <span class="amount">29.99€</span>
                                <span class="period">/mes</span>
                            </div>
                        </div>
                        <div class="plan-features">
                            <ul>
                                <li><i class="fas fa-check"></i> 2 vCPUs</li>
                                <li><i class="fas fa-check"></i> 4GB RAM</li>
                                <li><i class="fas fa-check"></i> 40GB SSD NVMe</li>
                                <li><i class="fas fa-check"></i> Panel de Control</li>
                                <li><i class="fas fa-check"></i> Backup Semanal</li>
                            </ul>
                        </div>
                        <button class="btn btn-primary">Contratar</button>
                    </div>
                </div>
            </section>

            <!-- Servicios de Instalación Automática -->
            <section class="marketplace-section">
                <h2>Servicios de Instalación Automática</h2>
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
                            <form id="wordpress-form">
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
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="admin_email" required>
                                </div>
                                <button type="submit" class="btn-primary">Instalar WordPress</button>
                            </form>
                        </div>
                    </div>

                    <!-- PrestaShop -->
                    <div class="service-card">
                        <div class="service-header">
                            <i class="fas fa-shopping-cart"></i>
                            <h3>PrestaShop</h3>
                        </div>
                        <div class="service-features">
                            <ul>
                                <li><i class="fas fa-check"></i> Instalación automática</li>
                                <li><i class="fas fa-check"></i> Configuración inicial</li>
                                <li><i class="fas fa-check"></i> Módulos básicos</li>
                                <li><i class="fas fa-check"></i> Temas responsive</li>
                            </ul>
                        </div>
                        <div class="service-form">
                            <h4>Configuración Inicial</h4>
                            <form id="prestashop-form">
                                <div class="form-group">
                                    <label>Nombre de la tienda</label>
                                    <input type="text" name="shop_name" required>
                                </div>
                                <div class="form-group">
                                    <label>Usuario administrador</label>
                                    <input type="text" name="admin_user" required>
                                </div>
                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="password" name="admin_pass" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="admin_email" required>
                                </div>
                                <button type="submit" class="btn-primary">Instalar PrestaShop</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    
    <!-- JavaScript -->
    <script src="../../Assets/JavaScript/calculatePrices.js"></script>
    <script src="../../Assets/JavaScript/buttons.js"></script>
</body>
</html>