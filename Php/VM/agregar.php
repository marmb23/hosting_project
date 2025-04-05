<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

require_once '../../Php/Objetos/proxmox.php';
require_once '../../Php/Config/database.php';


$proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
$database = new Database();
$conn = $database->getConnection();

$nodoLess = $proxmox->getLessResources();
$username = $_SESSION['cliente']['username'];
$nombre = $_POST['nombrevmid'];
$cpu = $_POST['vcpus'];
$ram = $_POST['ram'];
$storage = $_POST['storage'];
$os = $_POST['os'];
$price = $_POST['price'];

$vmid = $database->addMachine($username, $nombre, $cpu, $ram, $storage, $price);
$proxmox->newVM($nodoLess, $vmid, $nombre, $cpu, $ram, $storage, $os);

header("Location: " . $_SERVER['HTTP_REFERER']);
?>
