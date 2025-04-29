<?php
    // Activa la visualització d'errors per a depuració
    //ini_set('display_errors', '1');
    //ini_set('display_startup_errors', '1');
    //error_reporting(E_ALL);

    // Inclou el fitxer de configuració de la base de dades
    require_once("../Config/database.php");

    // Crea una instància de la base de dades i estableix la connexió
    $database = new Database();
    $conn = $database->getConnection();

    // Verifica si la sol·licitud és de tipus POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obté les dades enviades pel formulari d'actualització
        $usuario = $_POST['username'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $fecha_nacimiento = $_POST['fecha-nacimiento'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        $passwd = $_POST['password'];

        // Verifica si l'usuari existeix a la base de dades
        $user = $database->verifyUser($usuario);

        if(!$user) {
            // Redirigeix a la pàgina de perfil amb un error si l'usuari no existeix
            header("Location: ../../Html/User/perfil.php?error=1");
            exit();
        }

        if ($passwd) {
            // Encripta la nova contrasenya abans de desar-la
            $hashedPassword = password_hash($passwd, PASSWORD_DEFAULT);
            $database->updateUserData($usuario, $nombre, $apellido, $fecha_nacimiento, $email, $telefono, $hashedPassword);
        } else {
            // Actualitza les dades de l'usuari sense modificar la contrasenya
            $hashedPassword = password_hash($passwd, PASSWORD_DEFAULT);
            $database->updateUserData($usuario, $nombre, $apellido, $fecha_nacimiento, $email, $telefono , "");
        }
        // Redirigeix a la pàgina anterior amb un missatge d'èxit
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?exito=1");
        exit();
    }
?>
