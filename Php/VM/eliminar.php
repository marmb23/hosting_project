<?php
require_once '../../Php/Objetos/proxmox.php';
require_once '../../Php/Config/database.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$vms = json_decode($_POST['vms_json'], true);
$proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
$database = new Database();
$database->getConnection();
foreach ($vms as $vm) {
    $node = $vm['node'];
    $vmid = $vm['vmid'];

    $database->deleteVM($vmid);
    $proxmox->deleteVM($node, $vmid);
}
 header("Location: " . $_SERVER['HTTP_REFERER']);