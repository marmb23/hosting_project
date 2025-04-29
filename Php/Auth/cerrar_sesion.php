<?php
    // Inicia la sessió per poder destruir-la
    session_start();
    // Destrueix totes les dades de la sessió actual
    session_destroy();
    // Redirigeix l'usuari a la pàgina d'inici
    header("Location: ../../Html/inicio.php");
?>