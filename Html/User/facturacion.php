<?php
    // Inicia la sessió per accedir a les dades de l'usuari
    session_start();

    // Inclou el fitxer de configuració de la base de dades
    require_once '../../Php/Config/database.php';

    // Crea una connexió amb la base de dades
    $db = new Database();
    $conn = $db->getConnection();

    // Obté les factures associades a l'usuari actual
    $invoices = $db->getInvoiceUser($_SESSION['cliente']['username']);
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
    <!-- Barra navegació esquerra, és el mateix a totes les pàgines -->
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

    <!-- Header amb l'usuari, és el mateix a totes les pàgines -->
    <div class="main-content">
        <header>
            <div class="navbar-user">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <span id="username"><?php echo($_SESSION['cliente']['username']);?></span>
                </div>
                <!-- Dropdown per l'usuari -->
                <div class="dropdown-menu">
                    <a href="perfil.php"><i class="fas fa-user"></i> Editar Perfil</a>
                    <a href="../../Php/Auth/cerrar_sesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                </div>
            </div>
        </header>

        <!-- Contingut principal -->
        <!-- Taula de factures -->
        <main class="container">
            <h1>Facturación</h1>

            <!-- Taula de factures -->
            <section class="billing-section">
                <h2>Historial de Facturación</h2>
                <table class="billing-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Importe</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($invoices)): ?>
                            <?php
                            // Calcula el total facturat sumant els imports de totes les factures
                            $total = 0;
                            foreach ($invoices as $invoice):
                                $amount = floatval(str_replace(',', '.', $invoice['amount']));
                                $total += $amount;
                            ?>
                                <tr>
                                    <!-- Mostra la data de la factura -->
                                    <td><?php echo htmlspecialchars($invoice['date']); ?></td>
                                    <!-- Mostra la descripció de la factura -->
                                    <td><?php echo htmlspecialchars($invoice['description']); ?></td>
                                    <!-- Mostra l'import de la factura -->
                                    <td><?php echo htmlspecialchars($invoice['amount']); ?></td>
                                    <!-- Mostra l'estat de la factura: "Pagada" si està pagada, "No pagada" en cas contrari -->
                                    <td><?php echo $invoice['paid'] ? 'Pagada' : 'No pagada'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="5">No hay facturas disponibles.</td>
                                </tr>
                                <!-- Mostra un missatge si no hi ha factures disponibles -->
                            <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <!-- Resum de facturació -->
            <section class="billing-summary">
                <h2>Resumen</h2>
                <!-- Mostra el total facturat formatat amb dos decimals i el símbol d'euro -->
                <p>Total Facturado: <strong><?php echo number_format($total ?? 0, 2, ',', '') . '€'; ?></strong></p>
            </section>
        </main>
    </div>

    <!-- JavaScript -->
    <script src="../../Assets/JavaScript/buttons.js"></script>
</body>
</html>