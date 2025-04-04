<?php
require_once '../../Php/Objetos/api.php';


$param = [
    'hostname' => '192.168.189.161', // required (domain name or IPv4)
    'username' => 'root', // required
    'password' => 'P@ssw0rd', // required
    'realm' => 'pam', // pam or pve auth type
    'port' => 8006, // if the port is changed. optional
    'ssl' => true // not required if false. optional
];

$pve = new ProxmoxVE_API($param);

$node = 'proxmoxnode0'; // node name
$vmid = '104'; // VM uniq ID

if ($pve->login()) {
    // noVNC
    echo '<iframe src="'.$pve->noVNC($node, $vmid).'" frameborder="0" scrolling="no" width="100%" height="100%"></iframe>';

}
?>