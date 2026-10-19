<?php 
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
                        'id' => $item['id'],
                        'title' => $item['title'],
                        'price' => $item['price'],
                        'qty' => 1
                    ];
                }
                break;
            }
        }
    }
    header('Location: index.php#creations');
    exit;
}
?>

<section id="accueil" class="hero">
    <div class="hero-content">
        <h1>Bienvenue chez Tricoperle</h1>
        <p>Découvrez nos créations uniques en perles et tricot, confectionnées avec passion.</p>
        <a href="#creations" class="btn-gold">Voir la boutique</a>
    </div>
</section>

<section id="creations" class="section-container">
    <h2 class="section-title">Nos Créations</h2>
    <div class="grid-container">
        <?php if (!empty($store_data['creations'])): ?>
            <?php foreach ($store_data['creations'] as $item): ?>
                <?php 
                    $short_desc = (mb_strlen($item['description']) > 80) 
                        ? mb_strimwidth($item['description'], 0, 80, '...') 
                        : $item['description'];
                ?>
                <div class="card">
                    <div class="card-img-wrapper">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    </div>

                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                        
                        <p class="description"><?php echo htmlspecialchars($short_desc); ?></p>
                        
                        <p class="price"><?php echo number_format($item['price'], 0, ',', ' '); ?> FCFA</p>
                        
                        <button type="button" class="btn-details" onclick="openDetailsModal(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                            🔍 Détails
                        </button>

                        <form action="index.php" method="POST" style="margin-top: 8px;">
                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" name="add_to_cart" class="btn-gold" style="width:100%; border:none; cursor:pointer;">Ajouter au panier</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; width: 100%;">Aucune création disponible pour le moment.</p>
        <?php endif; ?>
    </div>
</section>

<section id="videos" class="section-container bg-alt">
    <h2 class="section-title">Nos Démonstrations & Tutoriels</h2>
    <div class="grid-container">
        <?php if (!empty($store_data['videos'])): ?>
            <?php foreach ($store_data['videos'] as $video): ?>
                <div class="card">
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
            <p style="text-align: center; width: 100%;">Aucune vidéo disponible pour le moment.</p>
        <?php endif; ?>
    </div>
</section>

<!-- FENÊTRE MODALE DÉTAILS -->
<div id="modalDetails" class="modal-backdrop" onclick="closeDetailsModal(event)">
    <div class="modal-box">
        <span class="modal-close" onclick="closeDetailsModal()">&times;</span>
        <div class="modal-content">
            <div style="text-align: center; background: #f8f8f8; padding: 10px; border-radius: 8px;">
                <img id="modalMainImage" src="" alt="" style="width: 100%; max-height: 320px; object-fit: contain;">
            </div>

            <div class="modal-gallery" style="display: flex; gap: 10px; margin-top: 12px; justify-content: center;">
                <img id="modalThumb1" src="" alt="" onclick="switchMainImage(this.src)" class="thumb-img">
                <img id="modalThumb2" src="" alt="" onclick="switchMainImage(this.src)" class="thumb-img">
                <img id="modalThumb3" src="" alt="" onclick="switchMainImage(this.src)" class="thumb-img">
            </div>

            <h2 id="modalTitle" style="margin-top: 15px; color: #222;"></h2>
            <p id="modalPrice" style="font-size: 1.3rem; font-weight: bold; color: #d4af37; margin: 8px 0;"></p>
            <p id="modalDescription" style="color: #555; line-height: 1.6; max-height: 180px; overflow-y: auto; text-align: justify; padding-right: 5px;"></p>
        </div>
    </div>
</div>

<style>
.card-img-wrapper {
    width: 100%;
    height: 250px;
    background-color: #f8f8f8;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px;
    border-bottom: 1px solid #eee;
}

.card-img-wrapper img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: 4px;
}

.video-local-container {
    position: relative;
    width: 100%;
    padding-top: 56.25%;
    background-color: #000;
    overflow: hidden;
    border-radius: 8px 8px 0 0;
}

.video-local-container video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
    background: #000;
}

.btn-details {
    width: 100%;
    background-color: #222;
    color: #fff;
    padding: 0.6rem;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-details:hover {
    background-color: #444;
}

.modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.75);
    z-index: 2000;
    justify-content: center;
    align-items: center;
    padding: 1rem;
}

.modal-box {
    background: #fff;
    padding: 1.8rem;
    border-radius: 12px;
    max-width: 520px;
    width: 100%;
    position: relative;
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    max-height: 90vh;
    overflow-y: auto;
}

.modal-close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 2rem;
    cursor: pointer;
    color: #888;
}

.modal-close:hover {
    color: #000;
}

.thumb-img {
    width: 80px;
    height: 80px;
    object-fit: contain;
    background: #f4f4f4;
    padding: 3px;
    border-radius: 6px;
    cursor: pointer;
    border: 2px solid #ddd;
    transition: transform 0.2s, border-color 0.2s;
}

.thumb-img:hover {
    transform: scale(1.05);
    border-color: #d4af37;
}
</style>

<script>
function openDetailsModal(item) {
    document.getElementById('modalMainImage').src = item.image;
    
    const img1 = item.image;
    const img2 = item.image2 ? item.image2 : item.image;
    const img3 = item.image3 ? item.image3 : item.image;

    document.getElementById('modalThumb1').src = img1;
    document.getElementById('modalThumb2').src = img2;
    document.getElementById('modalThumb3').src = img3;

    document.getElementById('modalTitle').innerText = item.title;
    document.getElementById('modalPrice').innerText = new Intl.NumberFormat('fr-FR').format(item.price) + ' FCFA';
    document.getElementById('modalDescription').innerText = item.description;
    
    document.getElementById('modalDetails').style.display = 'flex';
}

function switchMainImage(src) {
    document.getElementById('modalMainImage').src = src;
}

function closeDetailsModal(event) {
    if (!event || event.target.classList.contains('modal-backdrop') || event.target.classList.contains('modal-close')) {
        document.getElementById('modalDetails').style.display = 'none';
    }
}
</script>

<?php include 'includes/footer.php'; ?>