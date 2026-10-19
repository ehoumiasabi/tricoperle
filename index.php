<?php 
// Active le tampon de sortie pour autoriser header() après les includes
ob_start();

include 'includes/header.php'; 
$store_data = get_store_data();

// Gestion de l'ajout au panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $id = intval($_POST['item_id']);
    
    if (isset($store_data['creations']) && is_array($store_data['creations'])) {
        foreach ($store_data['creations'] as $item) {
            if ($item['id'] === $id) {
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
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
            <?php foreach ($store_data['creations'] as $item): ?>
                <?php 
                    $short_desc = (mb_strlen($item['description']) > 80) 
                        ? mb_strimwidth($item['description'], 0, 80, '...') : $item['description'];
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
            <?php foreach ($store_data['videos'] as $video): ?>
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
    background: rgba(212, 175, 55, 0.25);
    border: 1px solid #d4af37;
    color: #f1c40f;
    padding: 4px 12px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    border-radius: 50px;
    margin-bottom: 0.8rem;
}

.hero-content h1 {
    font-size: 2.2rem;
    font-weight: 800;
    margin-bottom: 0.8rem;
    line-height: 1.2;
    color: #ffffff;
}

.hero-content p {
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 1.5rem;
    color: #e0e0e0;
}

.hero-buttons {
    display: flex;
    gap: 12px;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    width: 100%;
}

.btn-hero-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.8rem 1.8rem;
    border-radius: 30px;
    font-weight: 700;
    text-decoration: none;
    font-size: 0.95rem;
    box-sizing: border-box;
    text-align: center;
    background-color: #d4af37;
    color: #ffffff !important;
    border: 2px solid #d4af37;
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
    transition: all 0.3s ease;
}

.btn-hero-primary:hover {
    background-color: #b89528;
    border-color: #b89528;
}

.btn-hero-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.8rem 1.8rem;
    border-radius: 30px;
    font-weight: 700;
    text-decoration: none;
    font-size: 0.95rem;
    box-sizing: border-box;
    text-align: center;
    background-color: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 2px solid #ffffff;
    backdrop-filter: blur(5px);
    transition: all 0.3s ease;
}

.btn-hero-secondary:hover {
    background-color: #ffffff;
    color: #111111 !important;
}

/* --- SECTIONS ET CARTES PRODUITS --- */
.section-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: #222222;
    margin-bottom: 8px;
}

.title-underline {
    width: 70px;
    height: 4px;
    background-color: #d4af37;
    margin: 0 auto;
    border-radius: 2px;
}

.bg-alt {
    background-color: #f9f9f9;
}

.empty-msg {
    text-align: center;
    width: 100%;
    color: #777;
    font-style: italic;
    padding: 2rem 0;
}

.card {
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

.card-img-wrapper {
    width: 100%;
    height: 240px;
    background-color: #f8f8f8;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px;
}

.card-img-wrapper img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.card-body {
    padding: 1.2rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.card-item-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #222;
    margin-bottom: 6px;
}

.description {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.4;
    margin-bottom: 1rem;
    flex-grow: 1;
}

.price-tag {
    font-size: 1.25rem;
    font-weight: 800;
    color: #d4af37;
    margin-bottom: 1rem;
}

.card-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.btn-details {
    width: 100%;
    background-color: #2c2c2c;
    color: #ffffff;
    padding: 0.65rem;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s ease;
}

.btn-details:hover {
    background-color: #444444;
}

.btn-gold.btn-cart {
    width: 100%;
    background-color: #d4af37;
    color: #ffffff;
    padding: 0.65rem;
    border: none;
    border-radius: 6px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s ease;
}

.btn-gold.btn-cart:hover {
    background-color: #b89528;
}

.video-local-container {
    position: relative;
    width: 100%;
    padding-top: 56.25%;
    background-color: #000;
}

.video-local-container video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
}

/* --- MODALE DÉTAILS --- */
.modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 2000;
    justify-content: center;
    align-items: center;
    padding: 1rem;
    box-sizing: border-box;
}

.modal-box {
    background: #ffffff;
    padding: 1.5rem;
    border-radius: 12px;
    max-width: 500px;
    width: 100%;
    position: relative;
    max-height: 85vh;
    overflow-y: auto;
    box-sizing: border-box;
}

.modal-close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 2rem;
    cursor: pointer;
    color: #888;
}

.modal-main-img-box img {
    width: 100%;
    max-height: 250px;
    object-fit: contain;
}

.modal-gallery {
    display: flex;
    gap: 8px;
    margin-top: 10px;
    justify-content: center;
}

.thumb-img {
    width: 60px;
    height: 60px;
    object-fit: contain;
    background: #f4f4f4;
    border-radius: 6px;
    border: 2px solid #ddd;
    cursor: pointer;
}

.thumb-img.active {
    border-color: #d4af37;
}

/* --- RESPONSIVE MOBILE ANDROID --- */
@media (max-width: 576px) {
    .hero {
        padding-top: 2rem;
        min-height: 48vh;
    }
    .hero-content h1 {
        font-size: 1.7rem;
    }
    .hero-content p {
        font-size: 0.88rem;
        margin-bottom: 1.2rem;
    }
    .hero-buttons {
        flex-direction: column;
        gap: 10px;
    }
    .btn-hero-primary, .btn-hero-secondary {
        width: 100%;
    }
}
</style>

<!-- SCRIPT JS -->
<script>
function openDetailsModal(item) {
    const modal = document.getElementById('modalDetails');
    const mainImg = document.getElementById('modalMainImage');
    
    mainImg.src = item.image;
    
    const img1 = item.image;
    const img2 = item.image2 ? item.image2 : item.image;
    const img3 = item.image3 ? item.image3 : item.image;

    const thumb1 = document.getElementById('modalThumb1');
    const thumb2 = document.getElementById('modalThumb2');
    const thumb3 = document.getElementById('modalThumb3');

    thumb1.src = img1;
    thumb2.src = img2;
    thumb3.src = img3;

    [thumb1, thumb2, thumb3].forEach(t => t.classList.remove('active'));
    thumb1.classList.add('active');

    document.getElementById('modalTitle').innerText = item.title;
    document.getElementById('modalPrice').innerText = new Intl.NumberFormat('fr-FR').format(item.price) + ' FCFA';
    document.getElementById('modalDescription').innerText = item.description || '';

    modal.style.display = 'flex';
}

function switchMainImage(thumbElement) {
    document.getElementById('modalMainImage').src = thumbElement.src;
    const thumbs = document.querySelectorAll('.thumb-img');
    thumbs.forEach(t => t.classList.remove('active'));
    thumbElement.classList.add('active');
}

function closeDetailsModal(event) {
    if (!event || event.target.id === 'modalDetails' || event.target.classList.contains('modal-close')) {
        document.getElementById('modalDetails').style.display = 'none';
    }
}
</script>

<?php 
include 'includes/footer.php'; 
// Envoie le contenu conservé en mémoire au navigateur
ob_end_flush(); 
?>