<?php
    session_start();  
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturación - Monitor VM</title>
    <link rel="stylesheet" href="../../Assets/CSS/facturacion_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Barra navegación izquierda NO TOCAR -->
    <nav class="navbar">
        <div class="navbar-brand">
            <span>
                <a href="../inicio.php">
                    <img src="../../Assets/Fotos/Logo_MAI.png" alt="Icono" class="logo-hover" style="width: 40px; height: 25px; vertical-align: middle;">
                    Hosting MAI
                </a>
            </span>
        </div>
        <ul class="navbar-menu">
            <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="maquinas.php"><i class="fas fa-server"></i> Mis Maquinas</a></li>
            <li><a href="contenedores.php"><i class="fas fa-box"></i> Mis Contenedores</a></li>
            <li><a href="marketplace.php"><i class="fas fa-store"></i> Marketplace</a></li>
            <li><a href="facturacion.php" class="active"><i class="fas fa-credit-card"></i> Facturación</a></li>
            <li><a href="support.php"><i class="fas fa-ticket-alt"></i> Soporte</a></li>
        </ul>
    </nav>

    <!-- Header con el user NO TOCAR -->
    <div class="main-content">
        <header>
            <div class="navbar-user">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <span id="username"><?php echo($_SESSION['cliente']['username']);?></span>
                </div>
                <div class="dropdown-menu">
                    <a href="#"><i class="fas fa-user"></i> Editar Perfil</a>
                    <a href="#"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                </div>
            </div>
        </header>

        <!-- Contenido principal: es lo que va cambiando según la página que sea -->
        <!-- Tabla de facturas -->
        <main class="container">
            <h1>Facturación</h1>

            <!-- Tabla de facturas -->
            <section class="billing-section">
                <h2>Historial de Facturación</h2>
                <table class="billing-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Importe</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>01/03/2025</td>
                            <td>Servidor Virtual - 4 vCPUs, 16GB RAM</td>
                            <td>49,99€</td>
                            <td>Pagado</td>
                            <td><button class="btn-download">Descargar PDF</button></td>
                        </tr>
                        <tr>
                            <td>01/02/2025</td>
                            <td>Servidor Virtual - 2 vCPUs, 8GB RAM</td>
                            <td>29,99€</td>
                            <td>Pagado</td>
                            <td><button class="btn-download">Descargar PDF</button></td>
                        </tr>
                        <tr>
                            <td>01/01/2025</td>
                            <td>Servidor Virtual - 1 vCPU, 4GB RAM</td>
                            <td>19,99€</td>
                            <td>Pagado</td>
                            <td><button class="btn-download"><i class="fa-file-pdf-o"></i>Descargar PDF</button></td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <!-- Resumen de facturación -->
            <section class="billing-summary">
                <h2>Resumen</h2>
                <p>Total Facturado: <strong>99,97€</strong></p>
                <button class="btn-download-all">Descargar Todo</button>
            </section>
        </main>
    </div>
    <!-- JavaScript -->
    <script src="../../Assets/JavaScript/script.js"></script>
</body>

</html>