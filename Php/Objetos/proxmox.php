<?php
    /**
     * Classe ProxmoxAPI per gestionar les operacions amb l'API de Proxmox i connexions SSH.
     */
    class ProxmoxAPI {
        private $apiUrl;
        private $token;

        /**
        * Constructor de la classe ProxmoxAPI.
        * @param string $host Host del servidor Proxmox.
        * @param string $apiTokenId ID del token de l'API.
        * @param string $apiTokenSecret Secret del token de l'API.
        */
        public function __construct($host, $apiTokenId, $apiTokenSecret) {
            $this->apiUrl = "https://$host:3939/api2/json";
            $this->token = "$apiTokenId=$apiTokenSecret";
        }

        /**
        * Realitza una petició GET a l'API de Proxmox.
        * @param string $endpoint Endpoint de l'API.
        * @return array Resposta de l'API en format array.
        */
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

        /**
        * Obté la llista de nodes disponibles al servidor Proxmox.
        * @return array Llista de nodes.
        */
        public function getNodes() {
            return $this->getRequest("nodes");
        }

        /**
        * Obté la llista de contenidors d'un node específic.
        * @param string $node Nom del node.
        * @return array Llista de contenidors.
        */     
        public function getContainers($node) {
            $response = $this->getRequest("nodes/$node/lxc");
            return $response;
        }

        /**
        * Obté la llista de màquines virtuals d'un node específic.
        * @param string $node Nom del node.
        * @return array Llista de màquines virtuals.
        */
        public function getVM($node) {
            $response = $this->getRequest("nodes/$node/qemu");
            return $response;
        }

        /**
        * Filtra els contenidors associats a un usuari.
        * @param array $contenedores Llista d'IDs de contenidors de l'usuari.
        * @return array Llista de contenidors filtrats.
        */
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
        
        /**
        * Filtra les màquines virtuals associades a un usuari.
        * @param array $vms Llista d'IDs de màquines virtuals de l'usuari.
        * @return array Llista de màquines virtuals filtrades.
        */
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
        
        /**
        * Estableix una connexió SSH amb el servidor Proxmox.
        * @param string $hostname Host del servidor SSH.
        * @param string $username Nom d'usuari SSH.
        * @param string $password Contrasenya SSH.
        * @return resource Connexió SSH.
        */
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

        /**
        * Executa un comandament a través de la connexió SSH.
        * @param resource $connection Connexió SSH.
        * @param string $command Comandament a executar.
        * @return string Resultat del comandament.
        */
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

        /**
        * Apaga una màquina virtual.
        * @param string $node Nom del node.
        * @param int $vmid ID de la màquina virtual.
        * @return string Resultat de l'operació.
        */
        function shutdownVM($node, $vmid) {
            $connection = $this->sshConnection();
            $command = "pvesh create /nodes/$node/qemu/$vmid/status/stop";
            $result = $this->executeCommand($connection, $command);
            ssh2_disconnect($connection);
            return $result;
        }

        /**
        * Encén una màquina virtual.
        * @param string $node Nom del node.
        * @param int $vmid ID de la màquina virtual.
        * @return string Resultat de l'operació.
        */
        function startVM($node, $vmid) {
            $connection = $this->sshConnection();
            $command = "pvesh create /nodes/$node/qemu/$vmid/status/start";
            $result = $this->executeCommand($connection, $command);
            ssh2_disconnect($connection);
            return $result;
        }

        /**
        * Elimina una màquina virtual.
        * @param string $node Nom del node.
        * @param int $vmid ID de la màquina virtual.
        * @return string Resultat de l'operació.
        */
        function deleteVM($node, $vmid) {
            $connection = $this->sshConnection();
            $command = "pvesh delete /nodes/$node/qemu/$vmid";
            $this->shutdownVM($node, $vmid);
            $result = $this->executeCommand($connection, $command);
            ssh2_disconnect($connection);
            return $result;
        }

        /**
        * Reinicia una màquina virtual.
        * @param string $node Nom del node.
        * @param int $vmid ID de la màquina virtual.
        * @return string Resultat de l'operació.
        */
        function restartVM($node, $vmid) {
            $connection = $this->sshConnection();
            $command = "pvesh create /nodes/$node/qemu/$vmid/status/reset";
            $result = $this->executeCommand($connection, $command);
            ssh2_disconnect($connection);
            return $result;
        }

        // no va ok
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
        // Esto tampoco va coñooooooo
        public function setCookie($ticket) {
            if (!$this->check_login_ticket()) {
                throw new PVE2_Exception("Not logged into Proxmox host. No Login access ticket found or ticket expired.", 3);
            }
            setcookie("PVEAuthCookie", $this->login_ticket['ticket'], 0, "/", '.mydomain.com');
            setrawcookie("PVEAuthCookie", $this->login_ticket['ticket'], 0, "/");
        }

        /**
        * Edita la configuració d'una màquina virtual.
        * @param string $node Nom del node.
        * @param int $vmid ID de la màquina virtual.
        * @param string $name Nom de la màquina virtual.
        * @param int $cpu Nombre de CPUs.
        * @param int $ram Quantitat de RAM (en GB).
        * @param string $teclado Configuració del teclat.
        * @return string Resultat de l'operació.
        */
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

        /**
        * Edita la configuració d'un contenidor.
        * @param string $node Nom del node.
        * @param int $lxc ID del contenidor.
        * @param int $cpu Nombre de CPUs.
        * @param int $ram Quantitat de RAM (en GB).
        * @param int $swap Quantitat de memòria swap (en GB).
        * @return string Resultat de l'operació.
        */
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

        /**
        * Obté el nombre de màquines virtuals actives.
        * @param array $vms Llista de màquines virtuals.
        * @return int Nombre de màquines virtuals actives.
        */
        public function getActiveVM($vms) {
            $contador = 0;
            foreach ($vms as $vm) {
                if ($vm['status'] == 'running') {
                    $contador += 1;
                }
            };
            return $contador;
        }

        /**
        * Obté el nombre de contenidors actius.
        * @param array $containers Llista de contenidors.
        * @return int Nombre de contenidors actius.
        */
        public function getActiveContainers($containers) {
            $contador = 0;
            foreach ($containers as $container) {
                if ($container['status'] == 'running') {
                    $contador += 1;
                }
            };
            return $contador;
        }

        /**
        * Apaga un contenidor.
        * @param string $node Nom del node.
        * @param int $vmid ID del contenidor.
        * @return string Resultat de l'operació.
        */
        function shutdownLXC($node, $vmid) {
            $connection = $this->sshConnection();
            $command = "pvesh create /nodes/$node/lxc/$vmid/status/stop";
            $result = $this->executeCommand($connection, $command);
            ssh2_disconnect($connection);
            return $result;
        }
        
        /**
        * Encén un contenidor.
        * @param string $node Nom del node.
        * @param int $vmid ID del contenidor.
        * @return string Resultat de l'operació.
        */
        function startLXC($node, $vmid) {
            $connection = $this->sshConnection();
            $command = "pvesh create /nodes/$node/lxc/$vmid/status/start";
            $result = $this->executeCommand($connection, $command);
            ssh2_disconnect($connection);
            return $result;
        }

        /**
        * Reinicia un contenidor.
        * @param string $node Nom del node.
        * @param int $vmid ID del contenidor.
        * @return string Resultat de l'operació.
        */
        function restartLXC($node, $vmid) {
            $connection = $this->sshConnection();
            $command = "pvesh create /nodes/$node/lxc/$vmid/status/reboot";
            $result = $this->executeCommand($connection, $command);
            ssh2_disconnect($connection);
            return $result;
        }

        /**
        * Elimina un contenidor.
        * @param string $node Nom del node.
        * @param int $vmid ID del contenidor.
        * @return string Resultat de l'operació.
        */
        public function deleteLXC($node, $vmid) {
            $connection = $this->sshConnection();
            $command = "pvesh delete /nodes/$node/lxc/$vmid";
            $this->shutdownLXC($node, $vmid);
            $result = $this->executeCommand($connection, $command);
            ssh2_disconnect($connection);
            return $result;
        }

        /**
        * Obté el node amb menys recursos disponibles.
        * @return string Nom del node amb menys recursos.
        */
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

        /**
        * Crea una nova màquina virtual.
        * @param string $nodo Nom del node.
        * @param int $vmid ID de la màquina virtual.
        * @param string $nombre Nom de la màquina virtual.
        * @param int $cpu Nombre de CPUs.
        * @param int $ram Quantitat de RAM (en GB).
        * @param int $storage Espai d'emmagatzematge (en GB).
        * @param string $os Sistema operatiu.
        * @return string Resultat de l'operació.
        */
        public function newVM($nodo, $vmid, $nombre, $cpu, $ram, $storage, $os){
            $ram = $ram * 1024;
            $connection = $this->sshConnection();
            $command = "pvesh create /nodes/{$nodo}/qemu -vmid {$vmid} -name {$nombre} -cores {$cpu} -memory {$ram} -net0 model=virtio,bridge=vmbr0 -scsihw virtio-scsi-pci -scsi0 smb:{$storage},format=raw -ide0 smb:iso/{$os},media=cdrom";
            $result = $this->executeCommand($connection, $command);
            ssh2_disconnect($connection);
        }

        /**
        * Crea un nou contenidor.
        * @param string $nodoLess Nom del node amb menys recursos.
        * @param int $vmid ID del contenidor.
        * @param string $nombre Nom del contenidor.
        * @param string $password Contrasenya del contenidor.
        * @param int $cpu Nombre de CPUs.
        * @param int $ram Quantitat de RAM (en GB).
        * @param int $storage Espai d'emmagatzematge (en GB).
        * @param string $php Versió de PHP.
        * @return string Resultat de l'operació.
        */
        public function newLXC($nodoLess, $vmid, $nombre, $password, $cpu, $ram, $storage, $php){
            $ram = $ram * 1024;
            $connection = $this->sshConnection();
            $command = "ansible-playbook ansible.yaml -i hosts -e \"node={$nodoLess} ct_id={$vmid} hostname={$nombre} storage=local-lvm password={$password} cores={$cpu} memory={$ram} disk={$storage} php_version={$php}\"";
            $result = $this->executeCommand($connection, $command);
            ssh2_disconnect($connection);
        }
    }
?>
