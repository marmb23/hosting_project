<?php
require_once '../Objetos/proxmox.php';
require_once '../Config/database.php';


try {
    $proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
    $db = new Database();
    $conn = $db->getConnection();
    $user = 'isaacruiiiz';
    $contenedores = $proxmox->getContainersUser($db->getContainers($user));

    echo '<pre>';
    print_r($contenedores);
    echo '</pre>';

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>