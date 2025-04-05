<?php
require_once '../../Php/Objetos/proxmox.php';
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

error_reporting(E_ALL);
// Decodificar los datos JSON
$data = json_decode($_POST['json_data'], true);
// Extraer los datos
$vmid = $data['vmid'];
$node = $data['node'];
$cpu = $data['cpu'];
$ram = $data['ram'];
$swap = $data['swap'];

$proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
// Actualizar la VM
$proxmox->editLXC($node, $vmid, $cpu, $ram, $swap);

header("Location: " . $_SERVER['HTTP_REFERER']);

?>