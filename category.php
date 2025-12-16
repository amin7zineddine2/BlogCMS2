<?php
require_once 'includes/header.php';

// Vérifier si une catégorie est spécifiée
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$categorieId = $_GET['id'];

// Récupérer les articles de la catégorie
try {
    global $db;
    $sql = "SELECT a.*, u.nom as auteur_nom, c.categorie_nom 
            FROM article a 
            JOIN utilisateur u ON a.utilisateur_nom = u.nom 
            JOIN categorie c ON a.categorie_id = c.categorie_id 
            WHERE a.categorie_id = :categorie_id 
            AND a.article_status = 'published' 
            ORDER BY a.date_de_creation DESC";
    
    $params = [':categorie_id' => $categorieId];
    $articles = $db->executeQuery($sql, $params);
    
    // Récupérer les informations de la catégorie
    $sqlCategorie = "SELECT * FROM categorie WHERE categorie_id = :categorie_id";
    $categorieResult = $db->executeQuery($sqlCategorie, $params);
    $categorie = !empty($categorieResult) ? $categorieResult[0] : null;
    
    if (!$categorie) {
        $_SESSION['error_message'] = 'Catégorie non trouvée.';
        header('Location: index.php');
        exit();
    }
    
} catch (Exception $e) {
    $_SESSION['error_message'] = 'Erreur lors de la récupération des articles.';
    header('Location: index.php');
    exit();
}
?>

<div class="content-wrapper">
    <div>
        <h1 class="page-title">Catégorie: <?php echo htmlspecialchars($categorie['CATEGORIE_NOM']); ?></h1>
        
        <?php if ($categorie['CATEGORIE_DESCRIPTION']): ?>
            <p class="category-description" style="color: var(--gray); margin-bottom: 30px; font-size: 1.1rem;">
                <?php echo htmlspecialchars($categorie['CATEGORIE_DESCRIPTION']); ?>
            </p>
        <?php endif; ?>
        
        <!-- Articles de la catégorie -->
        <div class="articles-grid" id="articlesGrid">
            <?php if (empty($articles)): ?>
                <div class="no-articles" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                    <p style="font-size: 1.2rem; color: var(--gray);">
                        Aucun article dans cette catégorie pour le moment.
                    </p>
                    <?php if (isLoggedIn()): ?>
                        <a href="create_article.php" class="btn btn-primary">Créer un article</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($articles as $article): ?>
                    <div class="article-card">
                        <div class="article-image" style="background-image: url('<?php echo htmlspecialchars($article['IMG_URL'] ?: 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'); ?>');"></div>
                        <div class="article-content">
                            <div class="article-meta">
                                <div class="article-author">
                                    <div class="avatar"><?php echo getInitial($article['AUTEUR_NOM']); ?></div>
                                    <span><?php echo htmlspecialchars($article['AUTEUR_NOM']); ?></span>
                                </div>
                            </div>
                            <a href="view_article.php?id=<?php echo $article['ARTICLE_ID']; ?>" class="article-title">
                                <?php echo htmlspecialchars($article['TITRE']); ?>
                            </a>
                            <p class="article-excerpt">
                                <?php 
                                $contenu = $article['CONTENU'];
                                if (strlen($contenu) > 150) {
                                    echo htmlspecialchars(substr($contenu, 0, 150)) . '...';
                                } else {
                                    echo htmlspecialchars($contenu);
                                }
                                ?>
                            </p>
                            <div class="article-footer">
                                <span class="article-date"><?php echo formatDate($article['DATE_DE_CREATION']); ?></span>
                                <div class="article-comments">
                                    <i class="fas fa-eye"></i>
                                    <span><?php echo $article['VIEW_COUNT']; ?> vues</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div>
        <div class="categories-sidebar" style="background-color: white; border-radius: var(--border-radius); box-shadow: var(--shadow); padding: 25px;">
            <h3 class="sidebar-title" style="font-size: 1.4rem; margin-bottom: 20px; color: var(--dark);">Autres catégories</h3>
            <ul class="categories-list" style="list-style: none;">
                <?php 
                $allCategories = getAllCategories();
                foreach ($allCategories as $cat): 
                    if ($cat['CATEGORIE_ID'] != $categorieId):
                ?>
                    <li style="margin-bottom: 10px;">
                        <a href="category.php?id=<?php echo $cat['CATEGORIE_ID']; ?>" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 15px; background-color: var(--light-gray); border-radius: var(--border-radius); text-decoration: none; color: var(--dark); transition: all 0.3s;">
                            <span><?php echo htmlspecialchars($cat['CATEGORIE_NOM']); ?></span>
                            <span class="category-count" style="background-color: white; color: var(--dark); padding: 3px 8px; border-radius: 20px; font-size: 0.8rem;">
                                <?php echo $cat['ARTICLE_COUNT'] ?: 0; ?>
                            </span>
                        </a>
                    </li>
                <?php 
                    endif;
                endforeach; 
                ?>
            </ul>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>