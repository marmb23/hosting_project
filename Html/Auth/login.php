<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
    <link rel="stylesheet" href="../../Assets/CSS/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="../../Assets/Fotos/Logo_MAI_azul_oscuro.png" alt="Logo">
                <h1>Bienvenido/a</h1>
            </div>
            <p>Por favor, inicia sesión para continuar</p>

            <!-- Verifica si hi ha un error en les credencials, mostra un missatge d'error i neteja el paràmetre d'error -->
            <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
                <p style="color: red;">Credenciales incorrectas. Inténtalo de nuevo.</p>
            <?php $_GET['error'] = null; endif; ?>

            <!-- Formulari d'inici de sessió -->
            <form action="../../Php/Auth/verifyUser.php" method="POST">
                <input type="text" placeholder="Usuario" name="usuari" required>
                <input type="password" placeholder="Contraseña" name="passwd" required>
                <button type="submit">Entrar</button>
            </form>
            <button onclick="window.location.href='register.php'" class="register-button">Registrarse</button>
        </div>
    </div>

</body>
</html>