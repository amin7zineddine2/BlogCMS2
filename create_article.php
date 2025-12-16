<?php
require_once 'includes/header.php';

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    $_SESSION['error_message'] = 'Vous devez être connecté pour créer un article.';
    header('Location: login.php');
    exit();
}

// Récupérer les catégories
$categories = getAllCategories();

// Traitement du formulaire de création d'article
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? '';
    $contenu = $_POST['contenu'] ?? '';
    $categorie_id = $_POST['categorie_id'] ?? null;
    $img_url = $_POST['img_url'] ?? null;
    
    // Validation
    $errors = [];
    
    if (empty($titre)) {
        $errors[] = 'Le titre est requis.';
    }
    
    if (empty($contenu)) {
        $errors[] = 'Le contenu est requis.';
    }
    
    if (empty($errors)) {
        $userInfo = getCurrentUserInfo();
        $success = createArticle($titre, $contenu, $userInfo['NOM'], $categorie_id, $img_url);
        
        if ($success) {
            $_SESSION['success_message'] = 'Article créé avec succès !';
            header('Location: index.php');
            exit();
        } else {
            $errors[] = 'Une erreur est survenue lors de la création de l\'article.';
        }
    }
}
?>

<div class="create-article-container" style="max-width: 800px; margin: 0 auto;">
    <h1 class="page-title">Créer un nouvel article</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="message error" style="margin-bottom: 20px;">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="create-article-form" style="background-color: white; border-radius: var(--border-radius); box-shadow: var(--shadow); padding: 40px;">
        <form method="POST" action="create_article.php">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="titre" style="display: block; margin-bottom: 8px; font-weight: 500;">Titre</label>
                <input type="text" id="titre" name="titre" class="form-control" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: var(--border-radius); font-size: 1rem;">
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="categorie_id" style="display: block; margin-bottom: 8px; font-weight: 500;">Catégorie</label>
                <select id="categorie_id" name="categorie_id" class="form-control" style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: var(--border-radius); font-size: 1rem;">
                    <option value="">Sélectionnez une catégorie</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['CATEGORIE_ID']; ?>">
                            <?php echo htmlspecialchars($category['CATEGORIE_NOM']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="contenu" style="display: block; margin-bottom: 8px; font-weight: 500;">Contenu</label>
                <textarea id="contenu" name="contenu" class="form-control" rows="10" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: var(--border-radius); font-size: 1rem; resize: vertical;"></textarea>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="img_url" style="display: block; margin-bottom: 8px; font-weight: 500;">URL de l'image (optionnel)</label>
                <input type="text" id="img_url" name="img_url" class="form-control" placeholder="https://example.com/image.jpg" style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: var(--border-radius); font-size: 1rem;">
            </div>
            
            <div class="form-group" style="margin-bottom: 20px; display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Publier l'article</button>
                <a href="index.php" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>