<?php 
include 'includes/header.php'; 

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit;
}

$data = get_store_data();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. AJOUT D'UN PRODUIT (AVEC PLUSIEURS IMAGES)
    if (isset($_POST['add_creation'])) {
        $uploadedImages = [];
        $uploadDir = 'assets/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Parcourir les fichiers envoyés dans le champ multiple "images_files[]"
        if (isset($_FILES['images_files'])) {
            $fileCount = count($_FILES['images_files']['name']);
            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['images_files']['error'][$i] === UPLOAD_ERR_OK) {
                    $filename = time() . '_' . $i . '_' . basename($_FILES['images_files']['name'][$i]);
                    if (move_uploaded_file($_FILES['images_files']['tmp_name'][$i], $uploadDir . $filename)) {
                        $uploadedImages[] = $uploadDir . $filename;
                    }
                }
            }
        }

        if (empty($uploadedImages)) {
            $uploadedImages[] = "assets/images/logo.png";
        }

        $new_item = [
            "id"          => time(),
            "title"       => htmlspecialchars($_POST['title']),
            "price"       => floatval($_POST['price']),
            "description" => htmlspecialchars($_POST['description']),
            "images"      => $uploadedImages,
            "image"       => $uploadedImages[0] // Compatibilité
        ];
        $data['creations'][] = $new_item;
        save_store_data($data);
    } 

    // 2. AJOUT DE PLUSIEURS VIDÉOS
    elseif (isset($_POST['add_video'])) {
        $uploadDir = 'assets/videos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (isset($_FILES['video_files'])) {
            $fileCount = count($_FILES['video_files']['name']);
            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['video_files']['error'][$i] === UPLOAD_ERR_OK) {
                    $filename = time() . '_' . $i . '_' . basename($_FILES['video_files']['name'][$i]);
                    if (move_uploaded_file($_FILES['video_files']['tmp_name'][$i], $uploadDir . $filename)) {
                        $videoTitle = htmlspecialchars($_POST['video_title']);
                        if ($fileCount > 1) {
                            $videoTitle .= ' (' . ($i + 1) . ')';
                        }
                        $data['videos'][] = [
                            "id"    => time() + $i,
                            "title" => $videoTitle,
                            "url"   => $uploadDir . $filename
                        ];
                    }
                }
            }
            save_store_data($data);
        }
    } 

    // 3. SUPPRESSION PRODUIT
    elseif (isset($_POST['delete_creation'])) {
        $id = intval($_POST['item_id']);
        $data['creations'] = array_values(array_filter($data['creations'], fn($item) => $item['id'] !== $id));
        save_store_data($data);
    } 

    // 4. SUPPRESSION VIDÉO
    elseif (isset($_POST['delete_video'])) {
        $id = intval($_POST['video_id']);
        $data['videos'] = array_values(array_filter($data['videos'], fn($v) => $v['id'] !== $id));
        save_store_data($data);
    }

    header('Location: dashboard.php');
    exit;
}
?>

<section class="section-container">
    <h2 class="section-title">Tableau de bord Administration</h2>

    <div class="grid-container">
        <!-- FORMULAIRE PRODUIT (SÉLECTION MULTIPLE D'IMAGES) -->
        <div class="card-body" style="background:#f8f8f8; padding: 1.5rem; border-radius: 8px;">
            <h3>Ajouter un Produit</h3>
            <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Titre du produit</label>
                    <input type="text" name="title" required placeholder="Ex: Sac en Perles">
                </div>

                <div class="form-group">
                    <label>Images du produit (Sélectionnez une ou plusieurs images)</label>
                    <input type="file" name="images_files[]" accept="image/*" multiple required>
                    <small style="color:#666;">Vous pouvez sélectionner plusieurs images d'un coup (maintenez CTRL ou Maj).</small>
                </div>

                <div class="form-group">
                    <label>Prix (FCFA)</label>
                    <input type="number" name="price" required placeholder="15000">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required placeholder="Description de la création..."></textarea>
                </div>

                <button type="submit" name="add_creation" class="btn-gold" style="border:none; cursor:pointer; width: 100%;">Ajouter le produit</button>
            </form>
        </div>

        <!-- FORMULAIRE VIDÉOS MULTIPLES -->
        <div class="card-body" style="background:#f8f8f8; padding: 1.5rem; border-radius: 8px;">
            <h3>Ajouter une ou plusieurs Vidéos</h3>
            <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Titre / Thème des vidéos</label>
                    <input type="text" name="video_title" required placeholder="Ex: Tutoriel Tricot">
                </div>
                <div class="form-group">
                    <label>Choisir les vidéos (Sélection multiple possible)</label>
                    <input type="file" name="video_files[]" accept="video/mp4,video/webm,video/ogg" multiple required>
                </div>
                <button type="submit" name="add_video" class="btn-gold" style="border:none; cursor:pointer; width: 100%;">Ajouter la/les vidéo(s)</button>
            </form>
        </div>
    </div>

    <!-- TABLEAU PRODUITS -->
    <h3 style="margin-top: 3rem;">Gérer les créations existantes</h3>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Images</th>
                <th>Titre</th>
                <th>Prix</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['creations'] as $item): ?>
                <tr>
                    <td>
                        <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                            <?php foreach ($item['images'] as $img): ?>
                                <img src="<?php echo htmlspecialchars($img); ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td><?php echo number_format($item['price'], 0, ',', ' '); ?> FCFA</td>
                    <td>
                        <form action="dashboard.php" method="POST" style="display:inline;">
                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" name="delete_creation" class="btn-danger" style="border:none; cursor:pointer;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- TABLEAU VIDÉOS -->
    <h3 style="margin-top: 2rem;">Gérer les vidéos</h3>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Fichier</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['videos'] as $video): ?>
                <tr>
                    <td><?php echo htmlspecialchars($video['title']); ?></td>
                    <td><?php echo htmlspecialchars($video['url']); ?></td>
                    <td>
                        <form action="dashboard.php" method="POST" style="display:inline;">
                            <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                            <button type="submit" name="delete_video" class="btn-danger" style="border:none; cursor:pointer;">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- SECTION MESSAGES CLIENTS -->
    <h3 style="margin-top: 3rem;">📬 Messages Reçus des Clients</h3>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Nom</th>
                <th>Contact</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['messages'])): ?>
                <?php foreach (array_reverse($data['messages']) as $msg): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($msg['date']); ?></td>
                        <td><strong><?php echo htmlspecialchars($msg['nom']); ?></strong></td>
                        <td>
                            <?php echo htmlspecialchars($msg['email']); ?><br>
                            <small><?php echo htmlspecialchars($msg['telephone']); ?></small>
                        </td>
                        <td><?php echo nl2br(htmlspecialchars($msg['message'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Aucun message reçu pour le moment.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php include 'includes/footer.php'; ?>