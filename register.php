<?php
require_once 'includes/header.php';

// Si l'utilisateur est déjà connecté, rediriger vers l'accueil
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    $errors = [];
    
    if (empty($nom)) {
        $errors[] = 'Le nom est requis.';
    }
    
    if (empty($email)) {
        $errors[] = 'L\'email est requis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'L\'email n\'est pas valide.';
    }
    
    if (empty($password)) {
        $errors[] = 'Le mot de passe est requis.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }
    
    if (empty($errors)) {
        $success = createUser($nom, $email, $password);
        
        if ($success) {
            $_SESSION['success_message'] = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
            header('Location: login.php');
            exit();
        } else {
            $errors[] = 'Cet email ou nom d\'utilisateur est déjà utilisé.';
        }
    }
}
?>

<div class="auth-container" style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
    <div class="auth-card" style="background-color: white; border-radius: var(--border-radius); box-shadow: var(--shadow); padding: 40px; width: 100%; max-width: 500px;">
        <h2 class="modal-title" style="font-size: 1.8rem; margin-bottom: 30px; color: var(--primary); text-align: center;">Inscription</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="message error" style="margin-bottom: 20px;">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="register.php">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="nom" style="display: block; margin-bottom: 8px; font-weight: 500;">Nom d'utilisateur</label>
                <input type="text" id="nom" name="nom" class="form-control" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: var(--border-radius); font-size: 1rem;">
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 8px; font-weight: 500;">Email</label>
                <input type="email" id="email" name="email" class="form-control" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: var(--border-radius); font-size: 1rem;">
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="password" style="display: block; margin-bottom: 8px; font-weight: 500;">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: var(--border-radius); font-size: 1rem;">
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="confirm_password" style="display: block; margin-bottom: 8px; font-weight: 500;">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: var(--border-radius); font-size: 1rem;">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">S'inscrire</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px; color: var(--gray);">
            Déjà un compte ? <a href="login.php" style="color: var(--primary); text-decoration: none;">Connectez-vous</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>