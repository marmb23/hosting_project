<?php
    session_start();
    require_once '../../Php/Objetos/proxmox.php';
    require_once '../../Php/Config/database.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contenedores - Monitor VM</title>
    <link rel="stylesheet" href="../../Assets/CSS/servidores_styles.css">       
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
            <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="maquinas.php"><i class="fas fa-server"></i> Mis Maquinas</a></li>
            <li><a href="contenedores.php" class="active"><i class="fas fa-box"></i> Mis Contenedores</a></li>
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
                <div class="dropdown-menu">
                    <a href="perfil.php"><i class="fas fa-user"></i> Editar Perfil</a>
                    <a href="../../Php/Auth/cerrar_sesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                </div>
            </div>
        </header>

        <!-- Contingut principal -->        
        <main class="container">
            <!-- Botons amb totes les opcions dels contenidors -->
            <h1>Monitorización de contenedores</h1>
            <div class="bulk-actions">
                <button id="btnEncender" class="btn btn-primary" disabled><i class="fas fa-play"></i> Encender</button>
                <button id="btnApagar" class="btn btn-danger" disabled><i class="fas fa-power-off"></i> Apagar</button>
                <button id="btnEditar" class="btn btn-info" disabled><i class="fas fa-edit"></i> Editar</button>
                <button id="btnReiniciar" class="btn btn-secondary" disabled><i class="fas fa-sync"></i> Reiniciar</button>
                <button id="btnConsola" class="btn btn-secondary" disabled><i class="fas fa-terminal"></i> Consola</button>
                <button id="btnEliminar" class="btn btn-warning" disabled><i class="fas fa-trash"></i> Eliminar</button>
            </div>
            <!-- Form ocult per poder enviar les dades als respectius PHPs -->
            <form id="formOculto" method="POST" style="display: none;">
                <input type="hidden" name="vms_json" id="vmsInput">
            </form>
            <!-- Taula amb tots els contenidors de l'usuari -->
            <table class="vm-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Tiempo de actividad</th>
                        <th>Consumo de CPU / Total</th>
                        <th>Consumo de RAM / Total</th>
                        <th>Consumo de disco / Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Php que obté els contenidors de l'usuari per mostrar-los a la taula -->
                    <?php 
                        $proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
                        $db = new Database();
                        $conn = $db->getConnection();
                        $contenedores = $proxmox->getContainersUser($db->getContainers($_SESSION['cliente']['username']));
                        foreach ($contenedores as $contenedor) {
                            $statusClass = ($contenedor['status'] === 'running') ? 'active' : 'inactive';
                            $uptimeHoras = round($contenedor['uptime'] / 3600, 2);
                            $cpuPorcentaje = round(($contenedor['cpu'] / $contenedor['cpus']) * 100, 2);
                        
                            $memGB = round($contenedor['mem'] / (1024 ** 3), 2);
                            $maxMemGB = round($contenedor['maxmem'] / (1024 ** 3), 2);
                        
                            $diskGB = round($contenedor['disk'] / (1024 ** 3), 2);
                            $maxDiskGB = round($contenedor['maxdisk'] / (1024 ** 3), 2);

                            echo "
                            <tr>
                                <td><input type='checkbox' class='vm-select'></td>
                                <td data-id='{$contenedor['vmid']}' data-node='{$contenedor['node']}'>{$contenedor['name']}</td>
                                <td><span class='status-indicator {$statusClass}'></span>{$contenedor['status']}</td>
                                <td>{$uptimeHoras} h</td>
                                <td>{$cpuPorcentaje}%</td>
                                <td>{$memGB} GB / {$maxMemGB} GB</td>
                                <td>{$diskGB} GB / {$maxDiskGB} GB</td>
                            </tr>";
                        }
                    ?>
                </tbody>

                <!-- Aquest és el "tr" que es mostra al donar-li al botó d'editar -->
                <tr id="edit-row" style="display: none;">
                    <td colspan="7">
                        <form id="edit-form" class="edit-form">
                            <label>Cores: <input type="number" id="edit-cpu" min="1" max="100"></label>
                            <label>RAM (GB): <input type="number" id="edit-ram" step="0.1" min="0.16"></label>
                            <label>Swap (GB): <input type="number" id="edit-swap" step="0.1" min="0"></label>
                            </label>
                            <button type="submit" id="btnGuardar">Guardar</button>
                        </form>
                    </td>
                </tr>
            </table>
        </main>
    </div>
    
    <!-- JavaScript -->
    <script src="../../Assets/JavaScript/buttons.js"></script>
    <script src="../../Assets/JavaScript/containers.js"></script>
    <script src="../../Assets/JavaScript/ContainersAndVM.js"></script>
</body>
</html>