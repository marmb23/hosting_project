<?php
session_start();
require_once '../../Php/Objetos/proxmox.php';
require_once '../../Php/Config/database.php';

error_reporting(E_ALL);

$proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
$db = new Database();
$conn = $db->getConnection();


$userVms = $db->getVM($_SESSION['cliente']['username']);
$vms = $proxmox->getVmUser($userVms);

$result = [];

foreach ($vms as $vm) {
    $result[] = [ 
        'vmid' => $vm['vmid'],
        'status' => $vm['status'],
        'uptime' => round($vm['uptime'] / 3600, 2) . ' h',
        'cpu' => round(($vm['cpu'] / $vm['cpus']) * 100, 2) . '%',
        'mem' => round($vm['mem'] / (1024 ** 3), 2) . ' GB',
        'maxmem' => round($vm['maxmem'] / (1024 ** 3), 2) . ' GB',
        'disk' => round($vm['disk'] / (1024 ** 3), 2) . ' GB',
        'maxdisk' => round($vm['maxdisk'] / (1024 ** 3), 2) . ' GB',
    ];
}

header('Content-Type: application/json');
echo json_encode($result);