<?php
require_once '../../Php/Objetos/proxmox.php';

// Decodificar los datos JSON
$data = json_decode($_POST['json_data'], true);
// Extraer los datos
$vmid = $data['vmid'];
$node = $data['node'];
$nombre = $data['nombre'];
$cpu = $data['cpu'];
$ram = $data['ram'];
$teclado = $data['teclado'];

echo "<pre>";
print_r($data);
echo "</pre>";
$proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
// Actualizar la VM
$proxmox->editVM($node, $vmid, $nombre, $cpu, $ram, $teclado);


header("Location: " . $_SERVER['HTTP_REFERER']);

?>