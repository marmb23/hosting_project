<?php
    //ini_set('display_errors', '1');
    //ini_set('display_startup_errors', '1');
    //error_reporting(E_ALL);

    // Inclou el fitxer de la classe ProxmoxAPI per gestionar les operacions amb Proxmox
    require_once '../../Php/Objetos/proxmox.php';

    // Obté les dades enviades pel formulari
    $vmid = $_POST['vmid'];
    $node = $_POST['node'];
    $nombre = $_POST['nombre'];
    $cpu = $_POST['cpu'];
    $ram = $_POST['ram'];
    $teclado = $_POST['teclado'];

    // Crea una instància de l'API de Proxmox amb les credencials corresponents
    $proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");

    // Actualitza la configuració de la màquina virtual
    $proxmox->editVM($node, $vmid, $nombre, $cpu, $ram, $teclado);

    // Redirigeix a la pàgina anterior després de completar l'edició
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
?>