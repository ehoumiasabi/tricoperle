<?php 
// 1. Démarrage de session et traitement du panier AVANT tout affichage HTML
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si la fonction get_store_data n'est pas encore déclarée, on inclut le fichier nécessaire (ajustez le chemin si besoin)
if (!function_exists('get_store_data') && file_exists('includes/functions.php')) {
    include_once 'includes/functions.php';
}

$store_data = function_exists('get_store_data') ? get_store_data() : [];

// Gestion de l'ajout au panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $id = intval($_POST['item_id']);
    
    if (isset($store_data['creations']) && is_array($store_data['creations'])) {
        foreach ($store_data['creations'] as$item) {
            if ($item['id'] ===$id) {
                if (!isset($_SESSION['cart'])) {$_SESSION['cart'] = [];
                }
                if (isset($_SESSION['cart'][$id])) {
                    $_SESSION['cart'][$id]['qty']++;
                } else {
                    $_SESSION['cart'][$id] = [
                        'id'    => $item['id'],
                        'title' => $item['title'],
                        'price' => $item['price'],
                        'qty'   => 1
                    ];
                }
                break;
            }
        }
    }
    header('Location: index.php#creations');
    exit;
}

// 2. Inclusion de l'en-tête HTML après le traitement PHP
include 'includes/header.php'; 

$hero_bg_image = "assets/images/hero-bg.jpg";
?>

<!-- SECTION HERO / ACCUEIL (Position rehaussée) -->
<section id="accueil" class="hero" style="background-image: url('<?php echo htmlspecialchars($hero_bg_image); ?>');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <span class="hero-badge">✨ Artisanat & Luxe</span>
        <h1>Bienvenue chez Tricoperle</h1>
        <p>Découvrez nos créations uniques en perles et tricot, confectionnées à la main avec passion et élégance.</p>
        <div class="hero-buttons">
            <a href="#creations" class="btn-hero-primary">Voir la boutique</a>
            <a href="#videos" class="btn-hero-secondary">Découvrir les tutoriels</a>
        </div>
    </div>
</section>

<!-- SECTION NOS CRÉATIONS -->
<section id="creations" class="section-container">
    <div class="section-header">
        <h2 class="section-title">Nos Créations</h2>
        <div class="title-underline"></div>
    </div>

    <div class="grid-container">
        <?php if (!empty($store_data['creations'])): ?>
            <?php foreach ($store_data['creations'] as$item): ?>
                <?php 
                    $short_desc = (mb_strlen($item['description']) > 80) 
                        ? mb_strimwidth($item['description'], 0, 80, '...') :$item['description'];
                ?>
                <div class="card card-creation">
                    <div class="card-img-wrapper">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" loading="lazy">
                    </div>

                    <div class="card-body">
                        <h3 class="card-item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                        
                        <p class="description"><?php echo htmlspecialchars($short_desc); ?></p>
                        
                        <div class="price-tag"><?php echo number_format($item['price'], 0, ',', ' '); ?> FCFA</div>
                        
                        <!-- BOUTONS D'ACTION SUR LES CARTES -->
                        <div class="card-actions">
                            <button type="button" class="btn-details" onclick="openDetailsModal(<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); ?>)">
                                🔍 Détails
                            </button>

                            <form action="index.php" method="POST" style="margin: 0;">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="add_to_cart" class="btn-gold btn-cart">Ajouter au panier</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-msg">Aucune création disponible pour le moment.</p>
        <?php endif; ?>
    </div>
</section>

<!-- SECTION DÉMONSTRATIONS & TUTORIELS -->
<section id="videos" class="section-container bg-alt">
    <div class="section-header">
        <h2 class="section-title">Nos Démonstrations & Tutoriels</h2>
        <div class="title-underline"></div>
    </div>

    <div class="grid-container">
        <?php if (!empty($store_data['videos'])): ?>
            <?php foreach ($store_data['videos'] as$video): ?>
                <div class="card card-video">
                    <div class="video-local-container">
                        <video controls poster="assets/images/logo.png">
                            <source src="<?php echo htmlspecialchars($video['url']); ?>" type="video/mp4">
                            Votre navigateur ne prend pas en charge la lecture de cette vidéo.
                        </video>
                    </div>
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($video['title']); ?></h3>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-msg">Aucune vidéo disponible pour le moment.</p>
        <?php endif; ?>
    </div>
</section>

<!-- FENÊTRE MODALE DÉTAILS -->
<div id="modalDetails" class="modal-backdrop" onclick="closeDetailsModal(event)">
    <div class="modal-box" onclick="event.stopPropagation()">
        <span class="modal-close" onclick="closeDetailsModal()">&times;</span>
        <div class="modal-content">
            <div class="modal-main-img-box">
                <img id="modalMainImage" src="" alt="Aperçu produit">
            </div>

            <div class="modal-gallery">
                <img id="modalThumb1" src="" alt="Vignette 1" onclick="switchMainImage(this)" class="thumb-img active">
                <img id="modalThumb2" src="" alt="Vignette 2" onclick="switchMainImage(this)" class="thumb-img">
                <img id="modalThumb3" src="" alt="Vignette 3" onclick="switchMainImage(this)" class="thumb-img">
            </div>

            <h2 id="modalTitle" class="modal-product-title"></h2>
            <p id="modalPrice" class="modal-product-price"></p>
            <p id="modalDescription" class="modal-product-description"></p>
        </div>
    </div>
</div>

<!-- COMPOSANT CSS COMPLET -->
<style>
/* Reset & protection mobile */
html, body {
    width: 100%;
    overflow-x: hidden;
    margin: 0;
    padding: 0;
}

/* --- SECTION HERO (REHAUSSÉE) --- */
.hero {
    position: relative;
    width: 100%;
    min-height: 55vh;
    background-color: #1a1a1a;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 2rem 1rem;
    box-sizing: border-box;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.75);
    z-index: 1;
}

.hero-content {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 750px;
    color: #ffffff;
    box-sizing: border-box;
}

.hero-badge {
    display: inline-block;
    background: rgba(212,