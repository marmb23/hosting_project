<?php
    // Inclou el fitxer de configuració de la base de dades
    require_once("../Config/database.php");

    // Crea una instància de la base de dades i estableix la connexió
    $database = new Database();
    $conn = $database->getConnection();

    // Verifica si la sol·licitud és de tipus POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obté les dades enviades pel formulari de registre
        $usuario = $_POST['usuario'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $email = $_POST['email'];
        $telefono = $_POST['tlf'];
        $passwd = $_POST['passwd'];

        // Verifica si l'usuari ja existeix a la base de dades
        $user = $database->verifyUser($usuari);

        if ($user) {
            // Redirigeix a la pàgina de registre amb un error si l'usuari ja existeix
            header("Location: ../../Html/Auth/register.php?error=2");
            exit();
        } else {
            // Encripta la contrasenya abans de desar-la a la base de dades
            $hashedPassword = password_hash($passwd, PASSWORD_DEFAULT);

            // Consulta SQL per inserir un nou usuari a la base de dades
            $query = "INSERT INTO user (username, password, forename, surname, birthdate, email, phone) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);

            // Executa la consulta i verifica si s'ha inserit correctament
            if ($stmt->execute([$usuario, $hashedPassword, $nombre, $apellido, $fecha_nacimiento, $email, $telefono])) {
                // Redirigeix a la pàgina d'inici de sessió si l'usuari s'ha creat correctament
                header("Location: ../../Html/Auth/login.php");
                exit();
            } else {
                // Redirigeix a la pàgina de registre amb un error si hi ha hagut un problema
                header("Location: ../../Html/Auth/register.php?error=3");
                exit();
            }
        }
    }
?>
