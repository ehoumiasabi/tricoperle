<?php 
include 'includes/header.php'; 

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['is_admin'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Identifiants incorrects.';
    }
}
?>

<section class="section-container">
    <div class="form-container">
        <h2>Connexion Admin</h2>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Nom d'utilisateur</label>
                <input type="text" name="username" value="" placeholder="Entrez votre nom d'utilisateur" required>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" value="" placeholder="Entrez votre mot de passe" required>
            </div>
            <button type="submit" class="btn-gold" style="width: 100%; border: none; cursor: pointer;">Se connecter</button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>