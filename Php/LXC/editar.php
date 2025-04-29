<?php
    //ini_set('display_errors', '1');
    //ini_set('display_startup_errors', '1');
    //error_reporting(E_ALL);

    // Inclou el fitxer de la classe ProxmoxAPI per gestionar les operacions amb Proxmox
    require_once '../../Php/Objetos/proxmox.php';

    // Decodifica les dades JSON enviades pel formulari
    $data = json_decode($_POST['json_data'], true);
    // Extreu les dades necessàries per editar el contenidor
    $vmid = $data['vmid']; 
    $node = $data['node']; 
    $cpu = $data['cpu']; 
    $ram = $data['ram']; 
    $swap = $data['swap']; 

    // Crea una instància de l'API de Proxmox amb les credencials corresponents
    $proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
    // Actualitza la configuració del contenidor utilitzant l'API de Proxmox
    $proxmox->editLXC($node, $vmid, $cpu, $ram, $swap);

    // Redirigeix a la pàgina anterior després de completar l'edició
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
?>