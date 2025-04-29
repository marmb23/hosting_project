<?php

class ProxmoxAPI {
    private $apiUrl;
    private $token;

    public function __construct($host, $apiTokenId, $apiTokenSecret) {
        $this->apiUrl = "https://$host:3939/api2/json";
        $this->token = "$apiTokenId=$apiTokenSecret";
    }

    private function getRequest($endpoint) {
        $url = "$this->apiUrl/$endpoint";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: PVEAPIToken $this->token",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
        return json_decode($response, true);
    }

    public function getNodes() {
        return $this->getRequest("nodes");
    }
    
    public function getContainers($node) {
        $response = $this->getRequest("nodes/$node/lxc");
        return $response;
    }

    public function getVM($node) {
        $response = $this->getRequest("nodes/$node/qemu");
        return $response;
    }

    public function getContainersUser($contenedores) {
        $nodes = $this->getNodes();
        $result = [];
        
        if (isset($nodes['data'])) {
            foreach ($nodes['data'] as $node) {
                $nodeName = $node['node'];
                $lxcs = $this->getContainers($nodeName);
                $filtered = [];
                
                foreach ($lxcs['data'] as $contenedor) {
                    if (in_array($contenedor['vmid'], $contenedores)) {
                        $contenedor['node'] = $nodeName;
                        $filtered[] = $contenedor;
                    }
                }
                
                if (!empty($filtered)) {
                    $result = array_merge($result, $filtered);
                }
            }
        }
        
        usort($result, function($a, $b) {
            return strcmp(strtolower($a['name']), strtolower($b['name']));
        });
        return $result;
    }
    
    public function getVmUser($vms) {
        $nodes = $this->getNodes();
        $result = [];
        
        if (isset($nodes['data'])) {
            foreach ($nodes['data'] as $node) {
                $nodeName = $node['node'];
                $qemu = $this->getVM($nodeName);
                $filtered = [];
                
                foreach ($qemu['data'] as $vm) {
                    if (in_array($vm['vmid'], $vms)) {
                        $vm['node'] = $nodeName;
                        $filtered[] = $vm;
                    }
                }
                if (!empty($filtered)) {
                    $result = array_merge($result, $filtered);
                }
            }
        }
        
        usort($result, function($a, $b) {
            return strcmp(strtolower($a['name']), strtolower($b['name']));
        });
        return $result;
    }
    
    function sshConnection($hostname = '192.168.189.160', $username = 'root', $password = 'P@ssw0rd') {
        $connection = ssh2_connect($hostname, 22);
        if (!$connection) {
            die('No se pudo conectar al servidor Proxmox');
        }

        if (!ssh2_auth_password($connection, $username, $password)) {
            die('Autenticación fallida');
        }

        return $connection;
    }

    function executeCommand($connection, $command) {
        $output = ssh2_exec($connection, $command);
        $errorStream = ssh2_fetch_stream($output, SSH2_STREAM_STDERR);
    
        stream_set_blocking($output, true);
        stream_set_blocking($errorStream, true);
    
        $result = stream_get_contents($output);
        $errors = stream_get_contents($errorStream);
    
        fclose($output);
        fclose($errorStream);
    
        if (!empty($errors)) {
            echo "Error: $errors";
        }
    
        return $result;
    }

    function shutdownVM($node, $vmid) {
        $connection = $this->sshConnection();
        $command = "pvesh create /nodes/$node/qemu/$vmid/status/stop";
        $result = $this->executeCommand($connection, $command);
        ssh2_disconnect($connection);
        return $result;
    }

    function startVM($node, $vmid) {
        $connection = $this->sshConnection();
        $command = "pvesh create /nodes/$node/qemu/$vmid/status/start";
        $result = $this->executeCommand($connection, $command);
        ssh2_disconnect($connection);
        return $result;
    }

    // Función para eliminar la máquina virtual
    function deleteVM($node, $vmid) {
        $connection = $this->sshConnection();
        $command = "pvesh delete /nodes/$node/qemu/$vmid";
        $this->shutdownVM($node, $vmid);
        $result = $this->executeCommand($connection, $command);
        ssh2_disconnect($connection);
        return $result;
    }

    function restartVM($node, $vmid) {
        $connection = $this->sshConnection();
        $command = "pvesh create /nodes/$node/qemu/$vmid/status/reset";
        $result = $this->executeCommand($connection, $command);
        ssh2_disconnect($connection);
        return $result;
    }

    public function vncTicket($node, $vmid) {
        $connection = $this->sshConnection();
        $command = "pvesh create /nodes/$node/qemu/$vmid/vncproxy --output-format json";
        $result = $this->executeCommand($connection, $command);
        $result = json_decode($result, true);
        ssh2_disconnect($connection);
        return $result;
    }
    
    # https://github.com/CpuID/pve2-api-php-client que no coñoooooooooooooooo
    public function getFrame($node, $vmid, $port, $ticket){
        return "<iframe src='http://192.168.189.161:8006/?console=kvm&novnc=1&node={$node}&vmid={$vmid}&path=api2/json/nodes/{$node}/qemu/{$vmid}/vncwebsocket?port={$port}&vncticket={$ticket}'></iframe>";
        #return "<iframe style='width: 900px; height: 900px;' src='https://{$this->hostname}:{$this->port}/?console=kvm&novnc=1&vmid={$vmid}&node=alpha&resize=off&cmd=' frameborder='0' scrolling='no'></iframe>";
    }

    public function setCookie($ticket) {
		if (!$this->check_login_ticket()) {
			throw new PVE2_Exception("Not logged into Proxmox host. No Login access ticket found or ticket expired.", 3);
		}
        setcookie("PVEAuthCookie", $this->login_ticket['ticket'], 0, "/", '.mydomain.com');
		setrawcookie("PVEAuthCookie", $this->login_ticket['ticket'], 0, "/");
	}

    public function editVM($node, $vmid, $name, $cpu, $ram, $teclado) {
        $params = '';
        if (!empty($name)) {
            $params .= "--name $name ";
        }
        if (!empty($cpu)) {
            $params .= "--cores $cpu ";
        }
        if (!empty($ram)) {
            $ram = $ram * 1024; // Convertir a MB
            $params .= "--memory $ram ";
        }
        if (!empty($teclado)) {
            $params .= "--keyboard $teclado ";
        }

        if (empty($node) || empty($vmid)) {
            echo "Node and VMID must not be empty.";
            echo $node;
            echo $vmid;
        }

        $connection = $this->sshConnection();
 
        $command = "pvesh set /nodes/$node/qemu/$vmid/config $params";
        echo $command;
        $result = $this->executeCommand($connection, $command);
        ssh2_disconnect($connection);
        return $result;
    }

    public function editLXC($node, $lxc, $cpu, $ram, $swap) {
        $params = '';
        if (!empty($cpu)) {
            $params .= "--cores $cpu ";
        }
        if (!empty($ram)) {
            $ram = $ram * 1024;
            $params .= "--memory $ram ";
        }
        if (!empty($swap)) {
            $swap = $swap * 1024;
            $params .= "--swap $swap ";
        }

        $connection = $this->sshConnection();
        $command = "pvesh set /nodes/$node/lxc/$lxc/config $params";
        $result = $this->executeCommand($connection, $command);
        ssh2_disconnect($connection);
        return $result;
    }

    public function getActiveVM($vms) {
        $contador = 0;
        foreach ($vms as $vm) {
            if ($vm['status'] == 'running') {
                $contador += 1;
            }
        };
        return $contador;
    }

    public function getActiveContainers($containers) {
        $contador = 0;
        foreach ($containers as $container) {
            if ($container['status'] == 'running') {
                $contador += 1;
            }
        };
        return $contador;
    }

    function shutdownLXC($node, $vmid) {
        $connection = $this->sshConnection();
        $command = "pvesh create /nodes/$node/lxc/$vmid/status/stop";
        $result = $this->executeCommand($connection, $command);
        ssh2_disconnect($connection);
        return $result;
    }
    
    function startLXC($node, $vmid) {
        $connection = $this->sshConnection();
        $command = "pvesh create /nodes/$node/lxc/$vmid/status/start";
        $result = $this->executeCommand($connection, $command);
        ssh2_disconnect($connection);
        return $result;
    }

    function restartLXC($node, $vmid) {
        $connection = $this->sshConnection();
        $command = "pvesh create /nodes/$node/lxc/$vmid/status/reboot";
        $result = $this->executeCommand($connection, $command);
        ssh2_disconnect($connection);
        return $result;
    }

    public function deleteLXC($node, $vmid) {
        $connection = $this->sshConnection();
        $command = "pvesh delete /nodes/$node/lxc/$vmid";
        $this->shutdownLXC($node, $vmid);
        $result = $this->executeCommand($connection, $command);
        ssh2_disconnect($connection);
        return $result;
    }

    public function getLessResources() {
        $connection = $this->sshConnection();
        $command = "pvesh get /nodes --output-format json";
        $result = $this->executeCommand($connection, $command);
        ssh2_disconnect($connection);
        $jsonResult = json_decode($result, true);
        if ($jsonResult[0]['mem'] > $jsonResult[1]['mem']) {
            return $jsonResult[1]['node'];
        } else {
            return $jsonResult[0]['node'];
        }
        return $result; 
    }

    public function newVM($nodo, $vmid, $nombre, $cpu, $ram, $storage, $os){
        $ram = $ram * 1024;
        $connection = $this->sshConnection();
        $command = "pvesh create /nodes/{$nodo}/qemu -vmid {$vmid} -name {$nombre} -cores {$cpu} -memory {$ram} -net0 model=virtio,bridge=vmbr0 -scsihw virtio-scsi-pci -scsi0 smb:{$storage},format=raw -ide0 smb:iso/{$os},media=cdrom";
        $result = $this->executeCommand($connection, $command);
        ssh2_disconnect($connection);
    }

    public function newLXC($nodoLess, $vmid, $nombre, $password, $cpu, $ram, $storage, $php){
        $ram = $ram * 1024;
        $connection = $this->sshConnection();
        $command = "ansible-playbook ansible.yaml -i hosts -e \"node={$nodoLess} ct_id={$vmid} hostname={$nombre} storage=local-lvm password={$password} cores={$cpu} memory={$ram} disk={$storage} php_version={$php}\"";
        $result = $this->executeCommand($connection, $command);
        ssh2_disconnect($connection);
    }
}
?>
