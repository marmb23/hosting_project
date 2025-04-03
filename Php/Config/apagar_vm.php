<?php
session_start();
require_once '../Objetos/proxmox.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vm'])) {
    $vmName = $_POST['vm'];

    $proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");

    try {
        $response = $proxmox->shutdownVM($vmName);
        echo json_encode(["success" => true, "message" => "Máquina '$vmName' apagada correctamente."]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error al apagar: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Solicitud inválida."]);
}
?>
