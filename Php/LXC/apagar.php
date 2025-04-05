<?php
require_once '../../Php/Objetos/proxmox.php';

$vms = json_decode($_POST['vms_json'], true);
print_r($vms);
echo "<br>";
$proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
print_r($proxmox);
echo "<br>";

foreach ($vms as $vm) {
    $node = $vm['node'];
    echo $node;
    echo "<br>";

    $vmid = $vm['vmid'];
    echo $vmid;
    echo "<br>";

    $proxmox->shutdownLXC($node, $vmid);
}
header("Location: " . $_SERVER['HTTP_REFERER']);