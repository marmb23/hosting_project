<?php
require_once 'objetos/proxmox.php';
require_once 'objetos/database.php';


try {
    $proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
    $db = new Database();
    $conn = $db->getConnection();
    $user = 'anderson';
    $containers = $db->getContainers($user);

    print_r($containers);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>