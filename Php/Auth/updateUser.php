<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once("../Config/database.php");

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = $_POST['username'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $fecha_nacimiento = $_POST['fecha-nacimiento'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $passwd = $_POST['password'];

    $user = $database->verifyUser($usuario);


    if(!$user) {
        header("Location: ../../Html/User/perfil.php?error=1");
        exit();
    }

    if ($passwd) {
        $hashedPassword = password_hash($passwd, PASSWORD_DEFAULT);
        $database->updateUserData($usuario, $nombre, $apellido, $fecha_nacimiento, $email, $telefono, $hashedPassword);
    } else {
        $hashedPassword = password_hash($passwd, PASSWORD_DEFAULT);
        $database->updateUserData($usuario, $nombre, $apellido, $fecha_nacimiento, $email, $telefono , "");
    }
    header("Location: " . $_SERVER['HTTP_REFERER'] . "?exito=1");
    exit();

}
?>
