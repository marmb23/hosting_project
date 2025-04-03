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

    private function postRequest($endpoint, $data = []) {
        $url = $this->apiUrl . '/' . $endpoint;
        echo "POST to: $url" . PHP_EOL;
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: PVEAPIToken $this->token",
            "Content-Type: application/json",
        ]);
        if (!empty($data)) {
            $jsonData = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            echo "Payload: $jsonData" . PHP_EOL;
        }
    
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // << AQUI
    
        echo "HTTP status code: $httpCode" . PHP_EOL;
    
        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch) . PHP_EOL;
        } else {
            echo 'Raw response: ' . $response . PHP_EOL;
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
                    $result = $filtered;
                }
            }
        }
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
                        $filtered[] = $vm;
                    }
                }
                if (!empty($filtered)) {
                    $result = $filtered;
                }
            }
        }
        return $result;
    }

    public function shutdownVM($node = 'pve', $vmid = 104) {
        $endpoint = "nodes/$node/qemu/$vmid/status/stop";
        $response = $this->postRequest($endpoint, []);
        var_dump($response); 
        return $response;
    }

}
?>

