<?php
session_start();
require_once("../Config/database.php");

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuari = $_POST['usuari'];
    $passwd = $_POST['passwd'];

    $query = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$usuari]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        if (password_verify($passwd, $user['password'])) {
            $_SESSION['cliente'] = ['email' => $user['email'], 'username' => $user['username'], 'phone' => $user['phone'], 'forename' => $user['forename'], 'surname' => $user['surname']];
            header("Location: ../../Html/User/dashboard.php");
            exit();
        }
    }
    
    header("Location: ../../Html/Auth/login.php?error=1");
    exit();
}
?>