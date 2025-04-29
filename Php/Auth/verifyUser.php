<?php
    // Inicia la sessió per gestionar les dades de l'usuari autenticat
    session_start();
    // Inclou el fitxer de configuració de la base de dades
    require_once("../Config/database.php");

    // Crea una instància de la base de dades i estableix la connexió
    $database = new Database();
    $conn = $database->getConnection();

    // Verifica si la sol·licitud és de tipus POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obté les credencials enviades pel formulari d'inici de sessió
        $usuari = $_POST['usuari'];
        $passwd = $_POST['passwd'];
        // Verifica si l'usuari existeix a la base de dades
        $user = $database->verifyUser($usuari);
        if ($user) {
            // Comprova si la contrasenya proporcionada coincideix amb la desada
            if (password_verify($passwd, $user['password'])) {
                // Desa les dades de l'usuari a la sessió
                $_SESSION['cliente'] = ['email' => $user['email'], 'username' => $user['username'], 'phone' => $user['phone'], 'forename' => $user['forename'], 'surname' => $user['surname'], 'birthdate' => $user['birthdate']];
                header("Location: ../../Html/User/dashboard.php");
                exit();
            }
        }
        // Redirigeix a la pàgina del dashboard si l'autenticació és correcta
        header("Location: ../../Html/Auth/login.php?error=1");
        exit();
    }
?>