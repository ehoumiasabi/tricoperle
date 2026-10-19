<?php 
include 'includes/header.php'; 

// Gestion de la suppression d'un article du panier
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
    $remove_id = intval($_GET['id']);
    unset($_SESSION['cart'][$remove_id]);
    header('Location: cart.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;

// Numéro sans espaces pour assurer le bon fonctionnement du lien wa.me sur Android
$mon_numero_whatsapp = "22993920705"; 
?>

<section class="section-container">
    <h2 class="section-title">Votre Panier</h2>

    <?php if (empty($cart)): ?>
        <p style="text-align: center;">Votre panier est vide. <a href="index.php#creations">Parcourir la boutique</a></p>
    <?php else: ?>
        <div class="cart-table-container">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Prix Unit.</th>
                        <th>Quantité</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $whatsapp_text = "Bonjour Tricoperle, je souhaite commander :\n\n";
                    foreach ($cart as $item): 
                        $subtotal = $item['price'] * $item['qty'];
                        $total += $subtotal;
                        $whatsapp_text .= "• " . $item['title'] . " (Quantité: " . $item['qty'] . ") - " . number_format($subtotal, 0, ',', ' ') . " FCFA\n";
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><?php echo number_format($item['price'], 0, ',', ' '); ?> FCFA</td>
                            <td><?php echo $item['qty']; ?></td>
                            <td><?php echo number_format($subtotal, 0, ',', ' '); ?> FCFA</td>
                            <td><a href="cart.php?action=remove&id=<?php echo $item['id']; ?>" class="btn-danger">Supprimer</a></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php $whatsapp_text .= "\nTotal Général : " . number_format($total, 0, ',', ' ') . " FCFA\n\nMerci de me recontacter pour finaliser ma commande !"; ?>
                </tbody>
            </table>
        </div>

        <div class="cart-summary" style="text-align: right; margin-top: 2rem;">
            <h3 style="font-size: 1.4rem; margin-bottom: 1rem; color: #222;">
                Total Général : <span style="color: #d4af37;"><?php echo number_format($total, 0, ',', ' '); ?> FCFA</span>
            </h3>
            <a href="https://wa.me/<?php echo $mon_numero_whatsapp; ?>?text=<?php echo urlencode($whatsapp_text); ?>" target="_blank" class="btn-gold" style="display: inline-block; padding: 0.8rem 1.8rem; text-decoration: none;">
                📱 Commander sur WhatsApp
            </a>
        </div>
    <?php endif; ?>
</section>

<!-- RÈGLES CSS POUR EMPÊCHER TOUT CONTENU COUPÉ SUR ANDROID/MOBILE -->
<style>
/* Empêche le débordement horizontal sur l'ensemble de la page */
html, body {
    width: 100%;
    overflow-x: hidden;
    margin: 0;
    padding: 0;
}

.section-container {
    width: 100%;
    max-width: 1100px;
    margin: 0 auto;
    padding: 1.5rem 1rem 3rem 1rem;
    box-sizing: border-box;
}

/* Permet au tableau de défiler horizontalement sur Android si l'écran est petit */
.cart-table-container {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin-bottom: 1rem;
}

.cart-table {
    width: 100%;
    min-width: 500px; /* Conserve une largeur minimale pour ne pas écraser les colonnes */
    border-collapse: collapse;
}

.cart-table th, .cart-table td {
    padding: 10px 12px;
    white-space: nowrap; /* Empêche le texte de se couper brutalement */
}

/* Adaptation mobile du bloc résumé et du bouton WhatsApp */
@media (max-width: 576px) {
    .cart-summary {
        text-align: center !important;
    }
    
    .cart-summary h3 {
        font-size: 1.2rem !important;
    }

    .btn-gold {
        display: block !important;
        width: 100% !important;
        box-sizing: border-box !important;
        text-align: center !important;
        padding: 0.9rem 1rem !important;
        white-space: normal !important;
    }
}
</style>

<?php include 'includes/footer.php'; ?>