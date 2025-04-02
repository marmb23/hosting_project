<?php
require_once("../Config/database.php");

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $email = $_POST['email'];
    $telefono = $_POST['tlf'];
    $passwd = $_POST['passwd'];

    $query = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        header("Location: ../../Html/Auth/register.php?error=2");
        exit();
    } else {
        $hashedPassword = password_hash($passwd, PASSWORD_DEFAULT);

        $query = "INSERT INTO user (username, password, forename, surname, birthdate, email, phone) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt->execute([$usuario, $hashedPassword, $nombre, $apellido, $fecha_nacimiento, $email, $telefono])) {
            header("Location: ../../Html/Auth/login.php");
            exit();
        } else {
            header("Location: ../../Html/Auth/register.php?error=3");
            exit();
        }
    }
}
?>
