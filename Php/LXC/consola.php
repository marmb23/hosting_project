<?php
    require_once '../../Php/Objetos/proxmox.php';
#require_once '../../Php/Objetos/api.php';
#
#// Crear una instancia de la clase PVE2_API
#$proxmox = new PVE2_API('192.168.189.160', 'root', 'pam', 'P@ssw0rd');
#
#// Iniciar sesión
#if ($proxmox->login()) {
#    echo "Inicio de sesión exitoso!<br>";
#    
#    // Obtener el ticket VNC y el puerto para la VM
#    $node = 'proxmoxnode0';  // Nodo de Proxmox
#    $vmid = 104;          // ID de la VM que deseas acceder
#    
#    $vnc_info = $proxmox->get_vnc_ticket($node, $vmid);
#    
#    if ($vnc_info) {
#        $ticket = $vnc_info['ticket'];
#        $port = $vnc_info['port'];
#        
#        // Obtener el iframe para la consola VNC
#        $iframe = $proxmox->getFrame($vmid, $port, $ticket);
#        echo $iframe; // Mostrar el iframe
#    } else {
#        echo "Error obteniendo el ticket VNC para la VM {$vmid}.<br>";
#    }
#} else {
#    echo "Error en el inicio de sesión.<br>";
#}
    $proxmox = new ProxmoxAPI("26.29.68.71", "root@pam!wasa", "27794c83-e74d-42df-ad25-f1d47bbb5633");
    $json = $proxmox->vncTicket("proxmoxnode0", 104);
    print_r($json['port']);
    echo "<br>";
    print_r($json['ticket']);
    echo $proxmox->getFrame('proxmoxnode0', 104, $json['port'], $json['ticket']);
     
    
?>
