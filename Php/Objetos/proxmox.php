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
                    $result = $filtered;
                }
            }
        }
        return $result;
    }
    
}
?>

