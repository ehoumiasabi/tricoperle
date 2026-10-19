<?php 
include 'includes/header.php'; 

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit;
}

$data = get_store_data();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. AJOUT D'UN PRODUIT (AVEC 3 IMAGES AU TOTAL)
    if (isset($_POST['add_creation'])) {
        $imagePath1 = "assets/images/logo.png";
        $imagePath2 = "";
        $imagePath3 = "";

        $uploadDir = 'assets/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Image principale
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $filename1 = time() . '_1_' . basename($_FILES['image_file']['name']);
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $filename1)) {
                $imagePath1 = $uploadDir . $filename1;
            }
        }

        // Deuxième exemplaire / vue
        if (isset($_FILES['image_file2']) && $_FILES['image_file2']['error'] === UPLOAD_ERR_OK) {
            $filename2 = time() . '_2_' . basename($_FILES['image_file2']['name']);
            if (move_uploaded_file($_FILES['image_file2']['tmp_name'], $uploadDir . $filename2)) {
                $imagePath2 = $uploadDir . $filename2;
            }
        }

        // Troisième exemplaire / vue
        if (isset($_FILES['image_file3']) && $_FILES['image_file3']['error'] === UPLOAD_ERR_OK) {
            $filename3 = time() . '_3_' . basename($_FILES['image_file3']['name']);
            if (move_uploaded_file($_FILES['image_file3']['tmp_name'], $uploadDir . $filename3)) {
                $imagePath3 = $uploadDir . $filename3;
            }
        }

        $new_item = [
            "id" => time(),
            "title" => htmlspecialchars($_POST['title']),
            "price" => floatval($_POST['price']),
            "description" => htmlspecialchars($_POST['description']),
            "image" => $imagePath1,
            "image2" => $imagePath2 ?: $imagePath1,
            "image3" => $imagePath3 ?: $imagePath1
        ];
        $data['creations'][] = $new_item;
        save_store_data($data);
    } 

    // 2. AJOUT D'UNE VIDÉO
    elseif (isset($_POST['add_video'])) {
        $videoPath = "";

        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'assets/videos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $filename = time() . '_' . basename($_FILES['video_file']['name']);
            if (move_uploaded_file($_FILES['video_file']['tmp_name'], $uploadDir . $filename)) {
                $videoPath = $uploadDir . $filename;
            }
        }

        if (!empty($videoPath)) {
            $new_video = [
                "id" => time(),
                "title" => htmlspecialchars($_POST['video_title']),
                "url" => $videoPath
            ];
            $data['videos'][] = $new_video;
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
        <!-- FORMULAIRE PRODUIT AVEC SÉLECTION MULTIPLE D'IMAGES -->
        <div class="card-body" style="background:#f8f8f8; padding: 1.5rem; border-radius: 8px;">
            <h3>Ajouter un Produit</h3>
            <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Titre du produit</label>
                    <input type="text" name="title" required placeholder="Ex: Sac en Perles">
                </div>

                <div class="form-group">
                    <label>Image principale *</label>
                    <input type="file" name="image_file" accept="image/*" required>
                </div>

                <div class="form-group">
                    <label>Deuxième image (Exemplaire 2)</label>
                    <input type="file" name="image_file2" accept="image/*">
                </div>

                <div class="form-group">
                    <label>Troisième image (Exemplaire 3)</label>
                    <input type="file" name="image_file3" accept="image/*">
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

        <!-- FORMULAIRE VIDÉO -->
        <div class="card-body" style="background:#f8f8f8; padding: 1.5rem; border-radius: 8px;">
            <h3>Ajouter une Vidéo</h3>
            <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Titre de la vidéo</label>
                    <input type="text" name="video_title" required placeholder="Ex: Tutoriel Tricot">
                </div>
                <div class="form-group">
                    <label>Choisir une vidéo depuis votre appareil</label>
                    <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg" required>
                </div>
                <button type="submit" name="add_video" class="btn-gold" style="border:none; cursor:pointer; width: 100%;">Ajouter la vidéo</button>
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
                        <div style="display: flex; gap: 4px;">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                            <?php if (!empty($item['image2'])): ?>
                                <img src="<?php echo htmlspecialchars($item['image2']); ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                            <?php endif; ?>
                            <?php if (!empty($item['image3'])): ?>
                                <img src="<?php echo htmlspecialchars($item['image3']); ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                            <?php endif; ?>
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