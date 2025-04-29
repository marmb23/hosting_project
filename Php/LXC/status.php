<?php
    // Inicia la sessió per accedir a les dades de l'usuari
    session_start();
    // Inclou el fitxer de la classe ProxmoxAPI per gestionar les operacions amb Proxmox
    require_once '../../Php/Objetos/proxmox.php';
    // Inclou el fitxer de configuració de la base de dades
    require_once '../../Php/Config/database.php';

    // Crea una instància de l'API de Proxmox amb les credencials corresponents
    $proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
    // Crea una instància de la base de dades i estableix la connexió
    $db = new Database();
    $conn = $db->getConnection();
    // Obté els contenidors associats a l'usuari actual
    $userVms = $db->getContainers($_SESSION['cliente']['username']);
    // Obté la informació dels contenidors utilitzant l'API de Proxmox
    $vms = $proxmox->getContainersUser($userVms);

    // Inicialitza un array per emmagatzemar els resultats
    $result = [];

    // Itera sobre cada contenidor per obtenir-ne la informació
    foreach ($vms as $vm) {
        $result[] = [ 
            'vmid' => $vm['vmid'],
            'name' => $vm['name'],
            'status' => $vm['status'],
            'uptime' => round($vm['uptime'] / 3600, 2) . ' h',
            'cpu' => round(($vm['cpu'] / $vm['cpus']) * 100, 2) . '%',
            'mem' => round($vm['mem'] / (1024 ** 3), 2) . ' GB',
            'maxmem' => round($vm['maxmem'] / (1024 ** 3), 2) . ' GB',
            'disk' => round($vm['disk'] / (1024 ** 3), 2) . ' GB',
            'maxdisk' => round($vm['maxdisk'] / (1024 ** 3), 2) . ' GB',
        ];
    }

    // Estableix el tipus de contingut com a JSON
    header('Content-Type: application/json');
    // Retorna els resultats en format JSON
    echo json_encode($result);
    exit();
?>