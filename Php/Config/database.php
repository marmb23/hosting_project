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
    
    
}
?>