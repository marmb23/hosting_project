<?php
    // Inclou el fitxer de la classe ProxmoxAPI per gestionar les operacions amb Proxmox
    require_once '../../Php/Objetos/proxmox.php';

    // Decodifica el JSON enviat amb les dades de les màquines virtuals o contenidors
    $vms = json_decode($_POST['vms_json'], true);
    // Crea una instància de l'API de Proxmox amb les credencials corresponents
    $proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
    // Itera sobre cada màquina virtual o contenidor per reiniciar-los
    foreach ($vms as $vm) {
        $node = $vm['node'];
        $vmid = $vm['vmid'];
        // Reinicia el contenidor utilitzant l'API de Proxmox
        $proxmox->restartLXC($node, $vmid);
    }
    // Redirigeix a la pàgina anterior després de completar el reinici
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
?>