<?php
    // Inicia la sessió per gestionar les dades de l'usuari
    session_start();

    // Inclou el fitxer de la classe ProxmoxAPI per gestionar les operacions amb Proxmox
    require_once '../../Php/Objetos/proxmox.php';
    // Inclou el fitxer de configuració de la base de dades
    require_once '../../Php/Config/database.php';

    // Crea una instància de l'API de Proxmox amb les credencials corresponents
    $proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
    // Crea una instància de la base de dades i estableix la connexió
    $database = new Database();
    $conn = $database->getConnection();

    // Verifica si el nom del contenidor ja existeix a la base de dades
    if ($database->getName($_POST['name']) == false) {
        // Desa un error a la sessió i redirigeix a la pàgina anterior
        $_SESSION['duplicated_name'] = true;
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Verifica si el nom del contenidor conté espais
    if (preg_match('/ /', $_POST['name'])) {
        // Desa un error a la sessió i redirigeix a la pàgina anterior
        $_SESSION['space_name'] = true;
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Obté el node amb menys recursos disponibles
    $nodoLess = $proxmox->getLessResources();
    // Obté les dades necessàries per crear el contenidor
    $username = $_SESSION['cliente']['username'];
    $nombre = $_POST['name'];
    $password = $_POST['admin_pass'];
    $cpu = 1;
    $ram = 2;
    $storage = 20;
    $php = $_POST['php'];
    $price = 14.99;
    $date = date('Y-m-d H:i:s');

    // Afegeix el contenidor a la base de dades i obté el seu ID
    $lxcid = $database->addContainer($username, $nombre, $cpu, $ram, $storage, $price);
    // Crea el nou contenidor a Proxmox
    $proxmox->newLXC($nodoLess, $lxcid, $nombre, $password, $cpu, $ram, $storage, $php);
    // Afegeix una factura associada al contenidor creat
    $database->addInvoice($username, $price, $date, $nombre);
    // Redirigeix a la pàgina anterior després de completar el procés
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
?>