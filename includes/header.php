<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['qty'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tricoperle | Perles, Tricots & Passion</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo-container">
                <a href="index.php">
                    <img src="assets/images/logo.png" alt="Tricoperle Logo">
                </a>
            </div>
            <ul class="nav-links" id="navLinks">
                <li><a href="index.php#accueil">Accueil</a></li>
                <li><a href="index.php#creations">Créations</a></li>
                <li><a href="index.php#videos">Vidéos</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li>
                    <a href="cart.php" class="btn-cart">
                        🛒 Panier <span class="cart-badge"><?php echo $cartCount; ?></span>
                    </a>
                </li>
                
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                    <li><a href="dashboard.php" class="btn-nav-admin">Dashboard Admin</a></li>
                    <li><a href="logout.php" class="btn-nav-logout">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn-nav-login">Espace Admin</a></li>
                <?php endif; ?>
            </ul>
            <div class="burger" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>