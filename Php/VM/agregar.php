<?php
    // Activa la visualització d'errors per a depuració
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

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
    // Verifica si el nom de la màquina virtual ja existeix a la base de dades
    if ($database->getName($_POST['nombrevmid']) == false) {
        // Desa un error a la sessió i redirigeix a la pàgina anterior
        $_SESSION['duplicated_name'] = true;
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Verifica si el nom de la màquina virtual conté espais
    if (preg_match('/ /', $_POST['nombrevmid'])) {
        $_SESSION['space_name'] = true;
        // Desa un error a la sessió i redirigeix a la pàgina anterior
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    // Obté el node amb menys recursos disponibles
    $nodoLess = $proxmox->getLessResources();
    // Obté les dades necessàries per crear la màquina virtual
    $username = $_SESSION['cliente']['username'];
    $nombre = $_POST['nombrevmid'];
    $cpu = $_POST['vcpus'];
    $ram = $_POST['ram'];
    $storage = $_POST['storage'];
    $os = $_POST['os'];
    $price = $_POST['price'];
    $date = date('Y-m-d H:i:s');

    // Afegeix la màquina virtual a la base de dades i obté el seu ID
    $vmid = $database->addMachine($username, $nombre, $cpu, $ram, $storage, $price);
    // Crea la màquina virtual utilitzant l'API de Proxmox
    $proxmox->newVM($nodoLess, $vmid, $nombre, $cpu, $ram, $storage, $os);
    // Afegeix una factura associada a la màquina virtual creada
    $database->addInvoice($username, $price, $date, $nombre);
    // Redirigeix a la pàgina anterior després de completar el procés
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
?>
