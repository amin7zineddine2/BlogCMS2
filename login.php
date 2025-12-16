<?php
require_once 'includes/header.php';

// Si l'utilisateur est déjà connecté, rediriger vers l'accueil
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $user = verifyUserCredentials($email, $password);
    
    if ($user) {
        $_SESSION['user_nom'] = $user['NOM'];
        $_SESSION['user_email'] = $user['EMAIL'];
        $_SESSION['user_role'] = $user['UTILISATEUR_ROLE'];
        
        $_SESSION['success_message'] = 'Connexion réussie ! Bienvenue ' . $user['NOM'];
        header('Location: index.php');
        exit();
    } else {
        $error = 'Email ou mot de passe incorrect.';
    }
}
?>

<div class="auth-container" style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
    <div class="auth-card" style="background-color: white; border-radius: var(--border-radius); box-shadow: var(--shadow); padding: 40px; width: 100%; max-width: 500px;">
        <h2 class="modal-title" style="font-size: 1.8rem; margin-bottom: 30px; color: var(--primary); text-align: center;">Connexion</h2>
        
        <?php if (isset($error)): ?>
            <div class="message error" style="margin-bottom: 20px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 8px; font-weight: 500;">Email</label>
                <input type="email" id="email" name="email" class="form-control" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: var(--border-radius); font-size: 1rem;">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="password" style="display: block; margin-bottom: 8px; font-weight: 500;">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: var(--border-radius); font-size: 1rem;">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Se connecter</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px; color: var(--gray);">
            Pas encore de compte ? <a href="register.php" style="color: var(--primary); text-decoration: none;">Inscrivez-vous</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>