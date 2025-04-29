<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


require_once '../../Php/Objetos/proxmox.php';

$vmid = $_POST['vmid'];
$node = $_POST['node'];
$nombre = $_POST['nombre'];
$cpu = $_POST['cpu'];
$ram = $_POST['ram'];
$teclado = $_POST['teclado'];

echo "Debug: Datos recibidos - VMID: $vmid, Node: $node, Nombre: $nombre, CPU: $cpu, RAM: $ram, Teclado: $teclado.<br>";

echo "Debug: Creando instancia de ProxmoxAPI.<br>";
$proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");

echo "Debug: Llamando a editVM.<br>";
// Actualizar la VM
$proxmox->editVM($node, $vmid, $nombre, $cpu, $ram, $teclado);

echo "Debug: Finalización del script.<br>";
header("Location: " . $_SERVER['HTTP_REFERER']);
?>