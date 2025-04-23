<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'hosting_mai';
    private $username = 'mai';
    private $password = 'mai';
    public $conn;

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

    public function closeConnection() {
        $this->conn = null;
    }

    public function getContainers($user) {
        $statement = $this->conn->prepare("SELECT container.lxcid FROM container INNER JOIN user ON container.user_id = user.id WHERE user.username = ?");
        $statement->execute([$user]);
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getVM($user) {
        $statement = $this->conn->prepare("SELECT virtual_machine.vmid FROM virtual_machine INNER JOIN user ON virtual_machine.user_id = user.id WHERE user.username = ?");
        $statement->execute([$user]);
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getInvoiceUser($user) {
        $statement = $this->conn->prepare("SELECT amount, date, paid, description FROM invoice i INNER JOIN user u ON i.user_id = u.id WHERE u.username = ?");
        $statement->execute([$user]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalVM($user) {
        $statement = $this->conn->prepare("SELECT count(vm.vmid) FROM virtual_machine AS vm INNER JOIN user ON vm.user_id = user.id WHERE user.username = ?");
        $statement->execute([$user]);
        return $statement->fetchAll(PDO::FETCH_COLUMN)[0];
    }

    public function getTotalContainers($user) {
        $statement = $this->conn->prepare("SELECT count(cont.lxcid) FROM container AS cont INNER JOIN user ON cont.user_id = user.id WHERE user.username = ?");
        $statement->execute([$user]);
        return $statement->fetchAll(PDO::FETCH_COLUMN)[0];
    }
    
    public function deleteVM($vmid) {
        $statement = $this->conn->prepare("DELETE FROM virtual_machine WHERE vmid = ?");
        return $statement->execute([$vmid]);
    }

    public function updateUserData($usuario, $nombre, $apellido, $fecha_nacimiento, $email, $telefono, $hashedPassword) {
        if ($hashedPassword != "") {
            $statement = $this->conn->prepare("UPDATE user SET password = ?, forename = ?, surname = ?, birthdate = ?, email = ?, phone = ? WHERE username = ?");
            return $statement->execute([$hashedPassword, $nombre, $apellido, $fecha_nacimiento, $email, $telefono, $usuario]);
        } else {
            $statement = $this->conn->prepare("UPDATE user SET forename = ?, surname = ?, birthdate = ?, email = ?, phone = ? WHERE username = ?");
            return $statement->execute([$nombre, $apellido, $fecha_nacimiento, $email, $telefono, $usuario]);
        }
    }

    public function addInvoice($user, $amount, $date, $desc) {
        $statement = $this->conn->prepare("SELECT id FROM user WHERE username = ?");
        $statement->execute([$user]);
        $user_id = $statement->fetchAll(PDO::FETCH_COLUMN)[0];
        $statement = $this->conn->prepare("INSERT INTO invoice (user_id, amount, date, description) VALUES (?, ?, ?, ?)");
        return $statement->execute([$user_id, $amount, $date, $desc]);
    }

    public function verifyUser($usuari) {
        $query = "SELECT * FROM user WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$usuari]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    public function deleteLXC($vmid) {
        $statement = $this->conn->prepare("DELETE FROM container WHERE lxcid = ?");
        return $statement->execute([$vmid]);
    }

    public function getMaxVMID(){
        $statement = $this->conn->prepare("SELECT MAX(vmid) FROM virtual_machine");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getMaxLXCID(){
        $statement = $this->conn->prepare("SELECT MAX(lxcid) FROM container");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function addMachine($usuari, $name, $cpu, $memory, $disk, $price){
        $idUser = $this->verifyUser($usuari)['id'];
        $idLXC = $this->getMaxLXCID()[0];
        $idVM = $this-> getMaxVMID()[0];
        $max = $idLXC > $idVM ? $idLXC + 1 : $idVM + 1;
        $statement = $this->conn->prepare("INSERT INTO `virtual_machine` (`name`, `vmid`, `user_id`, `cpu`, `memory`, `disk`, `price`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $statement->execute([$name, $max, $idUser, $cpu, $memory, $disk, $price]);
        return $max;
    }

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