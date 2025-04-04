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
    
    public function getUserData($username) {
        $statement = $this->conn->prepare("SELECT * FROM user WHERE username = ?");
        $statement->execute([$username]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUserData($username, $nombre, $apellido, $fecha_nacimiento, $email, $telefono) {
        $statement = $this->conn->prepare("UPDATE user SET nombre = ?, apellido = ?, fecha_nacimiento = ?, email = ?, telefono = ? WHERE username = ?");
        return $statement->execute([$nombre, $apellido, $fecha_nacimiento, $email, $telefono, $username]);
    }
}
?>