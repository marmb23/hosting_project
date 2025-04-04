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
        stream_set_blocking($output, true); 
        $result = stream_get_contents($output);
        fclose($output);
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
    
}
?>
