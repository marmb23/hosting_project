<?php
    /**
     * Classe Database per gestionar la connexió i les operacions amb la base de dades.
     */
    class Database {
        private $host = 'localhost'; // Host de la base de dades
        private $db_name = 'hosting_mai'; // Nom de la base de dades
        private $username = 'mai'; // Usuari de la base de dades
        private $password = 'mai'; // Contrasenya de la base de dades
        public $conn; // Connexió PDO

        /**
         * Estableix la connexió amb la base de dades.
         * @return PDO|null Retorna l'objecte PDO o null si hi ha un error.
         */
        public function getConnection() {
            $this->conn = null;
            try {
                $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $exception) {
                echo "Connection error: " . $exception->getMessage();
            }
            return $this->conn;
        }

        /**
         * Tanca la connexió amb la base de dades.
         */
        public function closeConnection() {
            $this->conn = null;
        }

        /**
         * Obté els contenidors associats a un usuari.
         * @param string $user Nom d'usuari.
         * @return array Llista d'IDs de contenidors.
         */
        public function getContainers($user) {
            $statement = $this->conn->prepare("SELECT container.lxcid FROM container INNER JOIN user ON container.user_id = user.id WHERE user.username = ?");
            $statement->execute([$user]);
            return $statement->fetchAll(PDO::FETCH_COLUMN);
        }

        /**
         * Obté les màquines virtuals associades a un usuari.
         * @param string $user Nom d'usuari.
         * @return array Llista d'IDs de màquines virtuals.
         */
        public function getVM($user) {
            $statement = $this->conn->prepare("SELECT virtual_machine.vmid FROM virtual_machine INNER JOIN user ON virtual_machine.user_id = user.id WHERE user.username = ?");
            $statement->execute([$user]);
            return $statement->fetchAll(PDO::FETCH_COLUMN);
        }

        /**
         * Verifica si un nom ja existeix a la base de dades.
         * @param string $name Nom a verificar.
         * @return bool Retorna false si el nom ja existeix, true en cas contrari.
         */
        public function getName($name) {
            $statementlxc = $this->conn->prepare("SELECT COUNT(container.name) FROM container WHERE container.name = ?");
            $statementvm = $this->conn->prepare("SELECT COUNT(virtual_machine.name) FROM virtual_machine WHERE virtual_machine.name = ?");
            $statementlxc->execute([$name]);
            $statementvm->execute([$name]);
            $lxc = $statementlxc->fetchAll(PDO::FETCH_COLUMN)[0];
            $vm = $statementvm->fetchAll(PDO::FETCH_COLUMN)[0];
            return $lxc > 0 ? false : ($vm > 0 ? false : true); 
        }

        /**
         * Obté les factures associades a un usuari.
         * @param string $user Nom d'usuari.
         * @return array Llista de factures amb els seus detalls.
         */
        public function getInvoiceUser($user) {
            $statement = $this->conn->prepare("SELECT amount, date, paid, description FROM invoice i INNER JOIN user u ON i.user_id = u.id WHERE u.username = ?");
            $statement->execute([$user]);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }

        /**
         * Obté el nombre total de màquines virtuals d'un usuari.
         * @param string $user Nom d'usuari.
         * @return int Nombre total de màquines virtuals.
         */
        public function getTotalVM($user) {
            $statement = $this->conn->prepare("SELECT count(vm.vmid) FROM virtual_machine AS vm INNER JOIN user ON vm.user_id = user.id WHERE user.username = ?");
            $statement->execute([$user]);
            return $statement->fetchAll(PDO::FETCH_COLUMN)[0];
        }

        /**
         * Obté el nombre total de contenidors d'un usuari.
         * @param string $user Nom d'usuari.
         * @return int Nombre total de contenidors.
         */
        public function getTotalContainers($user) {
            $statement = $this->conn->prepare("SELECT count(cont.lxcid) FROM container AS cont INNER JOIN user ON cont.user_id = user.id WHERE user.username = ?");
            $statement->execute([$user]);
            return $statement->fetchAll(PDO::FETCH_COLUMN)[0];
        }
        
        /**
         * Elimina una màquina virtual de la base de dades.
         * @param int $vmid ID de la màquina virtual.
         * @return bool Retorna true si s'ha eliminat correctament.
         */
        public function deleteVM($vmid) {
            $statement = $this->conn->prepare("DELETE FROM virtual_machine WHERE vmid = ?");
            return $statement->execute([$vmid]);
        }

        /**
         * Actualitza les dades d'un usuari.
         * @param string $usuario Nom d'usuari.
         * @param string $nombre Nom real de l'usuari.
         * @param string $apellido Cognom de l'usuari.
         * @param string $fecha_nacimiento Data de naixement.
         * @param string $email Correu electrònic.
         * @param string $telefono Número de telèfon.
         * @param string $hashedPassword Contrasenya encriptada (opcional).
         * @return bool Retorna true si s'ha actualitzat correctament.
         */
        public function updateUserData($usuario, $nombre, $apellido, $fecha_nacimiento, $email, $telefono, $hashedPassword) {
            if ($hashedPassword != "") {
                $statement = $this->conn->prepare("UPDATE user SET password = ?, forename = ?, surname = ?, birthdate = ?, email = ?, phone = ? WHERE username = ?");
                return $statement->execute([$hashedPassword, $nombre, $apellido, $fecha_nacimiento, $email, $telefono, $usuario]);
            } else {
                $statement = $this->conn->prepare("UPDATE user SET forename = ?, surname = ?, birthdate = ?, email = ?, phone = ? WHERE username = ?");
                return $statement->execute([$nombre, $apellido, $fecha_nacimiento, $email, $telefono, $usuario]);
            }
        }

        /**
         * Afegeix una factura a la base de dades.
         * @param string $user Nom d'usuari.
         * @param float $amount Import de la factura.
         * @param string $date Data de la factura.
         * @param string $desc Descripció de la factura.
         * @return bool Retorna true si s'ha afegit correctament.
         */
        public function addInvoice($user, $amount, $date, $desc) {
            $statement = $this->conn->prepare("SELECT id FROM user WHERE username = ?");
            $statement->execute([$user]);
            $user_id = $statement->fetchAll(PDO::FETCH_COLUMN)[0];
            $statement = $this->conn->prepare("INSERT INTO invoice (user_id, amount, date, description) VALUES (?, ?, ?, ?)");
            return $statement->execute([$user_id, $amount, $date, $desc]);
        }

        /**
         * Verifica si un usuari existeix a la base de dades.
         * @param string $usuari Nom d'usuari.
         * @return array|null Retorna les dades de l'usuari o null si no existeix.
         */
        public function verifyUser($usuari) {
            $query = "SELECT * FROM user WHERE username = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$usuari]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user;
        }

        /**
         * Elimina un contenidor de la base de dades.
         * @param int $vmid ID del contenidor.
         * @return bool Retorna true si s'ha eliminat correctament.
         */
        public function deleteLXC($vmid) {
            $statement = $this->conn->prepare("DELETE FROM container WHERE lxcid = ?");
            return $statement->execute([$vmid]);
        }

        /**
         * Obté el màxim ID de màquines virtuals.
         * @return int Retorna el màxim ID de màquines virtuals.
         */
        public function getMaxVMID(){
            $statement = $this->conn->prepare("SELECT MAX(vmid) FROM virtual_machine");
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_COLUMN);
        }

        /**
         * Obté el màxim ID de contenidors.
         * @return int Retorna el màxim ID de contenidors.
         */
        public function getMaxLXCID(){
            $statement = $this->conn->prepare("SELECT MAX(lxcid) FROM container");
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_COLUMN);
        }
        
        /**
         * Afegeix una màquina virtual a la base de dades.
         * @param string $usuari Nom d'usuari.
         * @param string $name Nom de la màquina virtual.
         * @param int $cpu Nombre de CPUs.
         * @param int $memory Quantitat de memòria.
         * @param int $disk Espai d'emmagatzematge.
         * @param float $price Preu de la màquina virtual.
         * @return int Retorna l'ID de la màquina virtual creada.
         */
        public function addMachine($usuari, $name, $cpu, $memory, $disk, $price){
            $idUser = $this->verifyUser($usuari)['id'];
            $idLXC = $this->getMaxLXCID()[0];
            $idVM = $this-> getMaxVMID()[0];
            $max = $idLXC > $idVM ? $idLXC + 1 : $idVM + 1;
            $statement = $this->conn->prepare("INSERT INTO `virtual_machine` (`name`, `vmid`, `user_id`, `cpu`, `memory`, `disk`, `price`) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $statement->execute([$name, $max, $idUser, $cpu, $memory, $disk, $price]);
            return $max;
        }

        /**
         * Afegeix un contenidor a la base de dades.
         * @param string $usuari Nom d'usuari.
         * @param string $name Nom del contenidor.
         * @param int $cpu Nombre de CPUs.
         * @param int $memory Quantitat de memòria.
         * @param int $disk Espai d'emmagatzematge.
         * @param float $price Preu del contenidor.
         * @return int Retorna l'ID del contenidor creat.
         */
        public function addContainer($usuari, $name, $cpu, $memory, $disk, $price){
            $idUser = $this->verifyUser($usuari)['id'];
            $idLXC = $this->getMaxLXCID()[0];
            $idVM = $this-> getMaxVMID()[0];
            $max = $idLXC > $idVM ? $idLXC + 1 : $idVM + 1;
            $statement = $this->conn->prepare("INSERT INTO `container` (`name`, `lxcid`, `user_id`, `cpu`, `memory`, `disk`, `price`) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $statement->execute([$name, $max, $idUser, $cpu, $memory, $disk, $price]);
            return $max;
        }
    }
?>