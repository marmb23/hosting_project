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
$nombre = $_POST['name'];
$password = $_POST['admin_pass'];
$cpu = 1;
$ram = 2;
$storage = 20;
$php = $_POST['php'];
$price = 14.99;
$date = date('Y-m-d H:i:s');
echo $proxmox->getLessResources();
echo '<br>';
echo $database->getMaxLXCID()[0];
echo '<br>';

echo $database->getMaxVMID()[0];
echo '<br>';

$lxcid = $database->addContainer($username, $nombre, $cpu, $ram, $storage, $price);
echo $php;
echo '<br>';

echo $proxmox->newLXC($nodoLess, $lxcid, $nombre, $password, $cpu, $ram, $storage, $php);
$database->addInvoice($username, $price, $date, $nombre);




?>
