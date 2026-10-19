<?php 
include 'includes/header.php'; 

$message_sent = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $message = htmlspecialchars($_POST['message']);
    
    // Récupération des données du site
    $store_data = get_store_data();

    // Initialisation du tableau des messages s'il n'existe pas
    if (!isset($store_data['messages']) || !is_array($store_data['messages'])) {
        $store_data['messages'] = [];
    }

    // Ajout du nouveau message avec date et heure
    $new_message = [
        "id" => time(),
        "date" => date('d/m/Y H:i'),
        "nom" => $nom,
        "email" => $email,
        "telephone" => $telephone,
        "message" => $message
    ];

    $store_data['messages'][] = $new_message;
    save_store_data($store_data);

    $message_sent = true;
}
?>

<section class="section-container">
    <h2 class="section-title">Contactez-nous</h2>
    <p class="section-subtitle">Une question sur nos créations en tricot et perles ? Écrivez-nous !</p>

    <div class="form-container">
        <?php if ($message_sent): ?>
            <div style="background: #D4EDDA; color: #155724; padding: 1.2rem; border-radius: 8px; text-align: center; margin-bottom: 1.5rem;">
                Merci <strong><?php echo $nom; ?></strong> ! Votre message a bien été envoyé. Nous vous répondrons très rapidement.
            </div>
        <?php endif; ?>

        <form action="contact.php" method="POST">
            <div class="form-group">
                <label for="nom">Nom complet *</label>
                <input type="text" id="nom" name="nom" required placeholder="Votre nom et prénom">
            </div>

            <div class="form-group">
                <label for="email">Adresse Email *</label>
                <input type="email" id="email" name="email" required placeholder="exemple@email.com">
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone / WhatsApp</label>
                <input type="text" id="telephone" name="telephone" placeholder="Ex: +229 90 00 00 00">
            </div>

            <div class="form-group">
                <label for="message">Votre Message / Demande spéciale *</label>
                <textarea id="message" name="message" rows="5" required placeholder="Décrivez votre besoin..."></textarea>
            </div>

            <button type="submit" class="btn-gold" style="width: 100%; border: none; cursor: pointer;">
                ✉️ Envoyer le message
            </button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>