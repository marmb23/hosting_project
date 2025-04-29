<?php
    // Inclou el fitxer de la classe ProxmoxAPI per gestionar les operacions amb Proxmox
    require_once '../../Php/Objetos/proxmox.php';
    // Inclou el fitxer de configuració de la base de dades
    require_once '../../Php/Config/database.php';

    // Decodifica el JSON enviat amb les dades de les màquines virtuals
    $vms = json_decode($_POST['vms_json'], true);
    // Crea una instància de l'API de Proxmox amb les credencials corresponents
    $proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
    // Crea una instància de la base de dades i estableix la connexió
    $database = new Database();
    $database->getConnection();
    // Itera sobre cada màquina virtual per eliminar-les
    foreach ($vms as $vm) {
        $node = $vm['node'];
        $vmid = $vm['vmid'];

        // Elimina la màquina virtual de la base de dades
        $database->deleteVM($vmid);
        // Elimina la màquina virtual utilitzant l'API de Proxmox
        $proxmox->deleteVM($node, $vmid);
    }
    // Redirigeix a la pàgina anterior després de completar l'eliminació
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
?>