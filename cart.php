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

// Remplacez '22900000000' par votre VRAI numéro WhatsApp (Exemple pour le Bénin: 229XXXXXXXX)
$mon_numero_whatsapp = "229 93 92 07 05"; 
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

<?php include 'includes/footer.php'; ?>