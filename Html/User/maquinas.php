<?php
    // Inicia la sessió per accedir a les dades de l'usuari
    session_start();
    // Inclou els fitxers necessaris per a la connexió amb Proxmox i la base de dades
    require_once '../../Php/Objetos/proxmox.php';
    require_once '../../Php/Config/database.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Monitor VM</title>
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
            <li><a href="maquinas.php" class="active"><i class="fas fa-server"></i> Mis Maquinas</a></li>
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
                <!-- Dropdown de l'usuari -->
                <div class="dropdown-menu">
                    <a href="perfil.php"><i class="fas fa-user"></i> Editar Perfil</a>
                    <a href="../../Php/Auth/cerrar_sesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                </div>
            </div>
        </header>

        <!-- Contingut principal -->
        <main class="container">
            <h1>Monitorización de máquinas virtuales</h1>
            <!-- Botons d'acció masiva -->
            <div class="bulk-actions">
                <button id="btnEncender" class="btn btn-primary" disabled><i class="fas fa-play"></i> Encender</button>
                <button id="btnApagar" class="btn btn-danger" disabled><i class="fas fa-power-off"></i> Apagar</button>
                <button id="btnEditar" class="btn btn-info" disabled><i class="fas fa-edit"></i> Editar</button>
                <button id="btnReiniciar" class="btn btn-secondary" disabled><i class="fas fa-sync"></i> Reiniciar</button>
                <button id="btnConsola" class="btn btn-secondary" disabled><i class="fas fa-terminal"></i> Consola</button>
                <button id="btnEliminar" class="btn btn-warning" disabled><i class="fas fa-trash"></i> Eliminar</button>
            </div>
            <!-- Form ocult per enviar les dades quan s'utilitza cada botó -->
            <form id="formOculto" method="POST" style="display: none;">
                <input type="hidden" name="vms_json" id="vmsInput">
            </form>
            <table class="vm-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Tiempo de actividad</th>
                        <th>Consumo de CPU</th>
                        <th>Consumo de RAM / Total</th>
                        <th>Consumo de disco / Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Php per obtenir les màquines virtuals -->
                    <?php 
                        // Crea una instància de l'API de Proxmox i de la base de dades
                        $proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
                        $db = new Database();
                        $conn = $db->getConnection();
                        // Obté les màquines virtuals associades a l'usuari actual
                        $bullshit = $db->getVM($_SESSION['cliente']['username']);
                        $vms = $proxmox->getVmUser($bullshit);
                        // Itera sobre les màquines virtuals per mostrar-les a la taula
                        foreach ($vms as $vm) {
                            // Determina la classe CSS segons l'estat de la màquina (activa o inactiva)
                            $statusClass = ($vm['status'] === 'running') ? 'active' : 'inactive';
                            // Calcula el temps d'activitat en hores
                            $uptimeHoras = round($vm['uptime'] / 3600, 2);
                            // Calcula el percentatge d'ús de CPU
                            $cpuPorcentaje = round(($vm['cpu'] / $vm['cpus']) * 100, 2);
                        
                            // Calcula el consum de memòria en GB
                            $memGB = round($vm['mem'] / (1024 ** 3), 2);
                            $maxMemGB = round($vm['maxmem'] / (1024 ** 3), 2);
                        
                            // Calcula el consum de disc en GB
                            $diskGB = round($vm['disk'] / (1024 ** 3), 2);
                            $maxDiskGB = round($vm['maxdisk'] / (1024 ** 3), 2);
                            // Mostra la informació de la màquina virtual a la taula
                            echo "
                            <tr id='machines'>
                                <td><input type='checkbox' class='vm-select'></td>
                                <td data-id='{$vm['vmid']}' data-node='{$vm['node']}'>{$vm['name']}</td>
                                <td><span class='status-indicator {$statusClass}'></span>{$vm['status']}</td>
                                <td>{$uptimeHoras} h</td>
                                <td>{$cpuPorcentaje}%</td>
                                <td>{$memGB} GB / {$maxMemGB} GB</td>
                                <td>{$diskGB} GB / {$maxDiskGB} GB</td>
                            </tr>";
                        }
                    ?>
                </tbody>
                <!-- TR que es mostra per editar la màquina -->
                <tr id="edit-row" style="display: none;">
                    <td colspan="7">
                        <!-- Formulari per editar les propietats d'una màquina virtual -->
                        <form id="edit-form" class="edit-form" method="POST" action="../../Php/VM/editar.php">
                            <input type="hidden" id="edit-vmid" name="vmid">
                            <input type="hidden" id="edit-node" name="node">
                            <label>Nombre: <input type="text" id="edit-nombre" name="nombre"></label>
                            <label>Cores: <input type="number" id="edit-cpu" min="1" max="100" name="cpu"></label>
                            <label>RAM (GB): <input type="number" id="edit-ram" name="ram" step="0.1" min="0"></label>
                            <!-- Opcions de teclat disponibles per configurar la màquina virtual -->
                            <label>Teclado:
                                <select id="edit-teclado" name="teclado">
                                    <option value="de">Alemán</option>
                                    <option value="de-ch">Alemán (Suiza)</option>
                                    <option value="da">Danés</option>
                                    <option value="en-gb">Inglés (UK)</option>
                                    <option value="en-us">Inglés (US)</option>
                                    <option value="es">Español</option>
                                    <option value="fi">Finlandés</option>
                                    <option value="fr">Francés</option>
                                    <option value="fr-be">Francés (Bélgica)</option>
                                    <option value="fr-ca">Francés (Canadá)</option>
                                    <option value="fr-ch">Francés (Suiza)</option>
                                    <option value="hu">Húngaro</option>
                                    <option value="is">Islandés</option>
                                    <option value="it">Italiano</option>
                                    <option value="jp">Japonés</option>
                                    <option value="lt">Lituano</option>
                                    <option value="mk">Macedonio</option>
                                    <option value="no">Noruego</option>
                                    <option value="pl">Polaco</option>
                                    <option value="pt">Portugués</option>
                                    <option value="pt-br">Portugués (Brasil)</option>
                                    <option value="sv">Sueco</option>
                                    <option value="sl">Eslovaco</option>
                                    <option value="tr">Turco</option>
                                </select>
                            </label>
                            <button id="btnGuardar" type="submit">Guardar</button>
                        </form>
                    </td>
                </tr>
            </table>
        </main>
    </div>

    <!-- JavaScript per gestionar els botons i les accions de les màquines virtuals -->
    <script src="../../Assets/JavaScript/buttons.js"></script>
    <script src="../../Assets/JavaScript/machines.js"></script>
    <script src="../../Assets/JavaScript/ContainersAndVM.js"></script>
</body>
</html>