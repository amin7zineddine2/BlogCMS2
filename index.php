<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$articles = getArticles(10);
?>
<?php include 'includes/header.php'; ?>
<div class="row">
    <div class="col-lg-8">
        <h1 class="mb-4">Derniers articles</h1>
        
        <?php if (empty($articles)): ?>
            <div class="alert alert-info">
                Aucun article publié pour le moment.
            </div>
        <?php else: ?>
            <?php foreach ($articles as $article): ?>
                <div class="card mb-4">
                    <?php if ($article['img_url']): ?>
                        <img src="uploads/<?php echo escape($article['img_url']); ?>" class="card-img-top" alt="<?php echo escape($article['titre']); ?>" style="max-height: 300px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <h2 class="card-title"><?php echo escape($article['titre']); ?></h2>
                        <p class="card-text">
                            <?php 
                            $content = strip_tags($article['contenu']);
                            echo strlen($content) > 200 ? substr($content, 0, 200) . '...' : $content;
                            ?>
                        </p>
                        <div class="text-muted mb-3">
                            <i class="bi bi-person"></i> <?php echo escape($article['auteur']); ?> |
                            <i class="bi bi-calendar"></i> <?php echo formatDate($article['date_de_creation']); ?> |
                            <i class="bi bi-folder"></i> <?php echo escape($article['categorie_nom']); ?> |
                            <i class="bi bi-eye"></i> <?php echo $article['view_count']; ?> vues
                        </div>
                        <a href="articles/view.php?id=<?php echo $article['article_id']; ?>" class="btn btn-primary">Lire la suite</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Catégories</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <?php foreach (getCategories() as $category): ?>
                        <li>
                            <a href="articles/index.php?category=<?php echo $category['categorie_id']; ?>">
                                <?php echo escape($category['categorie_nom']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        
        <?php if (!isLoggedIn()): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Connexion</h5>
                </div>
                <div class="card-body">
                    <p>Connectez-vous pour accéder à toutes les fonctionnalités.</p>
                    <a href="login.php" class="btn btn-primary w-100">Se connecter</a>
                    <hr>
                    <p class="mb-0">Pas encore de compte ?</p>
                    <a href="register.php" class="btn btn-outline-primary w-100 mt-2">S'inscrire</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>