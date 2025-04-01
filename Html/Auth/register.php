<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
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
            <p>Por favor, introduce los datos del nuevo usuario</p>

            <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
                <p style="color: red;">Credenciales incorrectas. Inténtalo de nuevo.</p>
            <?php $_GET['error'] = null; endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] == '2'): ?>
                <p style="color: red;">Usuario ya existente, inicia sesión.</p>
            <?php $_GET['error'] = null; endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] == '3'): ?>
                <p style="color: red;">Algo ha fallado, vuelve a intentarlo.</p>
            <?php $_GET['error'] = null; endif; ?>

            <form action="../../Php/Auth/createUser.php" method="POST">
                <input type="text" placeholder="Usuario" name="usuario" required>
                <input type="text" placeholder="Nombre" name="nombre" required>
                <input type="text" placeholder="Apellido" name="apellido" required>
                <input type="date" placeholder="Fecha de Nacimiento" name="fecha_nacimiento" required>
                <input type="email" placeholder="Email" name="email" required>
                <input type="number" placeholder="Teléfono" name="tlf" required id="telefono">
                <script>
                    document.getElementById("telefono").addEventListener("input", function () {
                        if (this.value.length > 9) {
                        this.value = this.value.slice(0, 9);
                        }
                    });
                </script>
                <input type="password" placeholder="Contraseña" name="passwd" required>
                <button type="submit">Registrar</button>
            </form>
        </div>
    </div>
</body>
</html>