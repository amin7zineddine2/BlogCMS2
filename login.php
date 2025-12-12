<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        
        if ($user && verifyPassword($password, $user['mot_de_passe'])) {
            $_SESSION['user'] = $user;
            $_SESSION['success'] = 'Connexion réussie !';
            redirect('dashboard.php');
        } else {
            $error = 'Email ou mot de passe incorrect';
        }
    }
}
?>
<?php 
$page_title = 'Connexion';
include 'includes/header.php'; 
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Connexion</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo escape($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>
                
                <hr>
                <p class="text-center mb-0">
                    Pas encore de compte ? <a href="register.php">S'inscrire</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>