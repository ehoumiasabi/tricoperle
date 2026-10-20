<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/db.php';

$store_data = get_store_data();

// Traitement du panier
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

include 'includes/header.php'; 
$hero_bg_image = "assets/images/hero-bg.jpg";
?>

<style>
/* STYLE BASE (DESKTOP) */
.hero {
    position: relative;
    width: 100%;
    min-height: 70vh;
    background-color: #1a1a1a;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 3rem 1.5rem;
    color: #ffffff;
    box-sizing: border-box;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.65);
    z-index: 1;
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 800px;
    width: 100%;
    padding-top: 1.5rem;
}

.hero-badge {
    display: inline-block;
    background: #d4af37;
    color: #fff;
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.hero-content h1 {
    font-size: 2.8rem;
    margin-bottom: 1rem;
    font-weight: bold;
    line-height: 1.2;
    letter-spacing: 1px;
}

.hero-content p {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    color: #f1f1f1;
    line-height: 1.5;
}

.hero-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}

.btn-hero-primary {
    background: #d4af37;
    color: white;
    padding: 0.8rem 1.8rem;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s;
    display: inline-block;
}

.btn-hero-primary:hover {
    background: #b89528;
}

.btn-hero-secondary {
    background: transparent;
    color: white;
    border: 2px solid white;
    padding: 0.8rem 1.8rem;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s, color 0.3s;
    display: inline-block;
}

.btn-hero-secondary:hover {
    background: white;
    color: #222;
}

/* Empêcher la coupe des images dans les cartes */
.card-img-wrapper {
    width: 100%;
    height: 250px;
    background: #f9f9f9;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.card-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

/* STYLE DE LA FENÊTRE MODALE (CACHÉE PAR DÉFAUT AVEC !IMPORTANT) */
.modal-backdrop {
    display: none !important;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal-box {
    background: white;
    width: 100%;
    max-width: 600px;
    border-radius: 8px;
    padding: 2rem;
    position: relative;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 1.8rem;
    cursor: pointer;
    color: #333;
}

.modal-main-img-box {
    width: 100%;
    height: 350px;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 1rem;
}

.modal-main-img-box img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

/* 📱 AJUSTEMENT RESPONSIVE SPÉCIFIQUE POUR MOBILE & ANDROID */
@media (max-width: 768px) {
    .hero {
        padding: 2.5rem 1rem;
        min-height: auto;
    }

    .hero-badge {
        font-size: 0.8rem;
        padding: 0.3rem 0.8rem;
        margin-bottom: 0.8rem;
    }

    .hero-content h1 {
        font-size: 1.8rem;
        margin-bottom: 0.8rem;
    }

    .hero-content p {
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
    }

    .hero-buttons {
        flex-direction: column;
        width: 100%;
        gap: 0.8rem;
    }

    .btn-hero-primary,
    .btn-hero-secondary {
        width: 100%;
        box-sizing: border-box;
        text-align: center;
    }
}
</style>

<!-- SECTION HERO / ACCUEIL -->
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
                    $product_images = [];
                    if (!empty($item['images']) && is_array($item['images'])) {
                        $product_images = $item['images'];
                    } else {
                        if (!empty($item['image'])) $product_images[] = $item['image'];
                        if (!empty($item['image2'])) $product_images[] = $item['image2'];
                        if (!empty($item['image3'])) $product_images[] = $item['image3'];
                    }
                    if (empty($product_images)) {
                        $product_images[] = 'assets/images/logo.png';
                    }

                    $main_img = $product_images[0];
                    $short_desc = (mb_strlen($item['description']) > 80) 
                        ? mb_strimwidth($item['description'], 0, 80, '...') : $item['description'];
                ?>
                <div class="card card-creation">
                    <div class="card-img-wrapper">
                        <img src="<?php echo htmlspecialchars($main_img); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" loading="lazy">
                    </div>

                    <div class="card-body">
                        <h3 class="card-item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p class="description"><?php echo htmlspecialchars($short_desc); ?></p>
                        <div class="price-tag"><?php echo number_format($item['price'], 0, ',', ' '); ?> FCFA</div>
                        
                        <div class="card-actions" style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                            <button type="button" class="btn-details" onclick="openDetailsModal(<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); ?>)" style="background:#333; color:#fff; border:none; padding:0.6rem 1rem; border-radius:4px; cursor:pointer;">
                                🔍 Détails
                            </button>

                            <form action="index.php" method="POST" style="margin: 0;">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="add_to_cart" class="btn-gold btn-cart" style="border:none; cursor:pointer;">Ajouter au panier</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-msg" style="text-align: center; grid-column: 1 / -1;">Aucune création disponible pour le moment.</p>
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
                    <div class="video-local-container" style="background:#000; display:flex; align-items:center; justify-content:center;">
                        <video controls poster="assets/images/logo.png" style="width: 100%; max-height: 250px; object-fit: contain;">
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
            <p class="empty-msg" style="text-align: center; grid-column: 1 / -1;">Aucune vidéo disponible pour le moment.</p>
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

            <!-- Galerie dynamique -->
            <div id="modalGallery" class="modal-gallery" style="display: flex; gap: 8px; margin-top: 10px; justify-content: center; flex-wrap: wrap;"></div>

            <h2 id="modalTitle" class="modal-product-title" style="margin-top: 1rem; font-size: 1.5rem;"></h2>
            <p id="modalPrice" class="modal-product-price" style="color:#d4af37; font-weight:bold; font-size:1.3rem; margin: 0.5rem 0;"></p>
            <p id="modalDescription" class="modal-product-description" style="color: #555; line-height: 1.5;"></p>
        </div>
    </div>
</div>

<script>
function openDetailsModal(item) {
    document.getElementById('modalTitle').textContent = item.title;
    document.getElementById('modalPrice').textContent = new Intl.NumberFormat('fr-FR').format(item.price) + ' FCFA';
    document.getElementById('modalDescription').textContent = item.description;

    const mainImg = document.getElementById('modalMainImage');
    const gallery = document.getElementById('modalGallery');
    gallery.innerHTML = '';

    let images = [];
    if (item.images && Array.isArray(item.images) && item.images.length > 0) {
        images = item.images;
    } else {
        if (item.image) images.push(item.image);
        if (item.image2) images.push(item.image2);
        if (item.image3) images.push(item.image3);
    }
    if (images.length === 0) {
        images = ['assets/images/logo.png'];
    }

    mainImg.src = images[0];

    images.forEach((imgSrc, index) => {
        const thumb = document.createElement('img');
        thumb.src = imgSrc;
        thumb.style.width = '60px';
        thumb.style.height = '60px';
        thumb.style.objectFit = 'contain';
        thumb.style.backgroundColor = '#f9f9f9';
        thumb.style.cursor = 'pointer';
        thumb.style.borderRadius = '4px';
        thumb.style.border = index === 0 ? '2px solid #d4af37' : '1px solid #ccc';

        thumb.onclick = function() {
            mainImg.src = imgSrc;
            Array.from(gallery.children).forEach(child => child.style.border = '1px solid #ccc');
            thumb.style.border = '2px solid #d4af37';
        };

        gallery.appendChild(thumb);
    });

    // Affiche la modale en modifiant le style en 'flex' lors du clic
    document.getElementById('modalDetails').style.setProperty('display', 'flex', 'important');
}

function closeDetailsModal(e) {
    document.getElementById('modalDetails').style.setProperty('display', 'none', 'important');
}
</script>

<?php include 'includes/footer.php'; ?>