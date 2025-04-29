<?php
    // Inicia la sessió per accedir a les dades de l'usuari
    session_start();

    // Inclou els fitxers necessaris per a la connexió amb Proxmox i la base de dades
    require_once '../../Php/Objetos/proxmox.php';
    require_once '../../Php/Config/database.php';

    // Crea una instància de l'API de Proxmox i de la base de dades
    $proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
    $db = new Database();
    $conn = $db->getConnection();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Monitor VM</title>
    <link rel="stylesheet" href="../../Assets/CSS/dashboard_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Barra navegació esquerra, és el mateix a totes les pàgines -->
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
            <li><a href="dashboard.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="maquinas.php"><i class="fas fa-server"></i> Mis Maquinas</a></li>
            <li><a href="contenedores.php"><i class="fas fa-box"></i> Mis Contenedores</a></li>
            <li><a href="marketplace.php"><i class="fas fa-store"></i> Marketplace</a></li>
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
                <!-- Dropdown para el menú de usuario -->
                <div class="dropdown-menu">
                    <a href="perfil.php"><i class="fas fa-user"></i> Editar Perfil</a>
                    <a href="../../Php/Auth/cerrar_sesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                </div>
            </div>
        </header>

        <!-- Contingut principal -->
        <main class="container">
            <h1>Dashboard</h1>
            
            <!-- Resum general de les màquines i contenidors de l'usuari -->
            <div class="machines-overview">
                <h2>Resumen de Máquinas Virtuales</h2>
                <div class="overview-grid">
                    <div class="overview-item">
                        <i class="fas fa-server"></i>
                        <div class="overview-info">
                            <h3>Total Máquinas</h3>
                            <!-- Mostra el nombre total de màquines virtuals associades a l'usuari actual -->
                            <p><?php echo($db->getTotalVM($_SESSION['cliente']['username'])); ?></p>
                        </div>
                    </div>
                    <div class="overview-item">
                        <i class="fas fa-check-circle"></i>
                        <div class="overview-info">
                            <h3>Activas</h3>
                            <p>
                                <?php
                                    // Obté les màquines virtuals de l'usuari actual
                                    $bullshit = $db->getVM($_SESSION['cliente']['username']);
                                    $vms = $proxmox->getVmUser($bullshit);
                                    // Mostra el nombre de màquines virtuals actives
                                    echo($proxmox->getActiveVM($vms));
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="overview-item">
                        <i class="fas fa-times-circle"></i>
                        <div class="overview-info">
                            <h3>Inactivas</h3>
                            <p>
                                <?php
                                    // Calcula el nombre de màquines virtuals inactives restant les actives del total
                                    $bullshit = $db->getVM($_SESSION['cliente']['username']);
                                    $vms = $proxmox->getVmUser($bullshit);
                                    echo($db->getTotalVM($_SESSION['cliente']['username']) - $proxmox->getActiveVM($vms))
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resum general de les màquines -->
            <div class="machines-overview">
                <h2>Resumen de Contenedores</h2>
                <div class="overview-grid">
                    <div class="overview-item">
                        <i class="fas fa-server"></i>
                        <div class="overview-info">
                            <h3>Total Contenedores</h3>
                            <!-- Mostra el nombre total de contenidors associats a l'usuari actual -->
                            <p><?php echo($db->getTotalContainers($_SESSION['cliente']['username'])); ?></p>
                        </div>
                    </div>
                    <div class="overview-item">
                        <i class="fas fa-check-circle"></i>
                        <div class="overview-info">
                            <h3>Activos</h3>
                            <p>
                                <?php
                                    // Obté els contenidors de l'usuari actual
                                    $bullshit = $db->getContainers($_SESSION['cliente']['username']);
                                    $vms = $proxmox->getContainersUser($bullshit);
                                    // Mostra el nombre de contenidors actius
                                    echo($proxmox->getActiveContainers($vms));
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="overview-item">
                        <i class="fas fa-times-circle"></i>
                        <div class="overview-info">
                            <h3>Inactivos</h3>
                            <p>
                                <?php
                                    // Calcula el nombre de contenidors inactius restant els actius del total
                                    $bullshit = $db->getContainers($_SESSION['cliente']['username']);
                                    $vms = $proxmox->getContainersUser($bullshit);
                                    echo($db->getTotalContainers($_SESSION['cliente']['username']) - $proxmox->getActiveContainers($vms))
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gràfics de les màquines -->
            <div class="dashboard-grid">
                <!-- Màquina 1 -->
                <div class="machine-card">
                    <div class="machine-header">
                        <h3>Máquina 1</h3>
                        <span class="status-indicator active"></span>
                    </div>
                    <div class="grafana-container">
                        <?php  
                            echo "<iframe src='http://192.168.189.166:3000/d-solo/IfgdXjtnk/proxmox-flux-cluster?orgId=1&from=1742579431960&to=1742580058460&timezone=browser&var-dsProxmox=fegg0t1oop2bkb&var-Bucket=proxmoxmai&var-server=pve&refresh=auto&theme=dark&panelId=2&__feature.dashboardSceneSolo' width='450' height='200' frameborder='0'></iframe>" ?>
                    </div>
                </div>

                <!-- Màquina 2 -->
                <div class="machine-card">
                    <div class="machine-header">
                        <h3>Máquina 2</h3>
                        <span class="status-indicator inactive"></span>
                    </div>
                    <div class="grafana-container">
                        <iframe src="http://localhost:3000/d-solo/your-dashboard-id?orgId=1&panelId=2&refresh=5s&theme=light" 
                                frameborder="0" 
                                width="100%" 
                                height="200">
                        </iframe>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script src="../../Assets/JavaScript/buttons.js"></script>
</body>
</html>