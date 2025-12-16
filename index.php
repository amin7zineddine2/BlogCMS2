<?php
require_once 'includes/header.php';

// Récupérer les articles
$articles = getAllArticles(12, 0);
$categories = getAllCategories();
?>

<div class="content-wrapper">
    <div>
        <h1 class="page-title">Articles récents</h1>
        
        <!-- Barre de recherche -->
        <div class="search-bar mb-4">
            <form action="search.php" method="GET" class="search-form">
                <div class="form-group" style="display: flex; gap: 10px;">
                    <input type="text" name="q" placeholder="Rechercher des articles..." class="form-control" style="flex: 1;">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </div>
            </form>
        </div>
        
        <!-- Articles Grid -->
        <div class="articles-grid" id="articlesGrid">
            <?php if (empty($articles)): ?>
                <div class="no-articles" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                    <p style="font-size: 1.2rem; color: var(--gray); margin-bottom: 20px;">
                        Aucun article disponible pour le moment.
                    </p>
                    <?php if (isLoggedIn()): ?>
                        <a href="create_article.php" class="btn btn-primary">Créer le premier article</a>
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
                                <?php if ($article['CATEGORIE_NOM']): ?>
                                    <span class="article-category"><?php echo htmlspecialchars($article['CATEGORIE_NOM']); ?></span>
                                <?php endif; ?>
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
                                    <i class="fas fa-comment"></i>
                                    <span>
                                        <?php 
                                        // Compter les commentaires pour cet article
                                        try {
                                            $comments = getCommentsByArticleId($article['ARTICLE_ID']);
                                            echo count($comments);
                                        } catch (Exception $e) {
                                            echo '0';
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Pagination simple -->
        <div class="pagination" style="display: flex; justify-content: center; gap: 10px; margin-top: 40px;">
            <a href="#" class="btn btn-outline" style="padding: 10px 20px;">Précédent</a>
            <a href="#" class="btn btn-primary" style="padding: 10px 20px;">1</a>
            <a href="#" class="btn btn-outline" style="padding: 10px 20px;">Suivant</a>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div>
        <!-- Catégories -->
        <div class="categories-sidebar" style="background-color: white; border-radius: var(--border-radius); box-shadow: var(--shadow); padding: 25px; margin-bottom: 30px;">
            <h3 class="sidebar-title" style="font-size: 1.4rem; margin-bottom: 20px; color: var(--dark);">Catégories</h3>
            <ul class="categories-list" style="list-style: none;">
                <?php foreach ($categories as $category): ?>
                    <li style="margin-bottom: 10px;">
                        <a href="category.php?id=<?php echo $category['CATEGORIE_ID']; ?>" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 15px; background-color: var(--light-gray); border-radius: var(--border-radius); text-decoration: none; color: var(--dark); transition: all 0.3s;">
                            <span><?php echo htmlspecialchars($category['CATEGORIE_NOM']); ?></span>
                            <span class="category-count" style="background-color: white; color: var(--dark); padding: 3px 8px; border-radius: 20px; font-size: 0.8rem;">
                                <?php echo $category['ARTICLE_COUNT'] ?: 0; ?>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <!-- Articles populaires -->
        <div class="categories-sidebar" style="background-color: white; border-radius: var(--border-radius); box-shadow: var(--shadow); padding: 25px;">
            <h3 class="sidebar-title" style="font-size: 1.4rem; margin-bottom: 20px; color: var(--dark);">Articles populaires</h3>
            <div id="popularArticles">
                <?php 
                // Récupérer les articles les plus vus
                try {
                    $popularArticles = getAllArticles(3, 0);
                    foreach ($popularArticles as $article): 
                ?>
                    <div class="popular-article mb-4" style="margin-bottom: 30px;">
                        <a href="view_article.php?id=<?php echo $article['ARTICLE_ID']; ?>" style="text-decoration: none;">
                            <h4 style="font-size: 1.1rem; margin-bottom: 5px; color: var(--primary);"><?php echo htmlspecialchars($article['TITRE']); ?></h4>
                        </a>
                        <div class="article-meta" style="display: flex; justify-content: space-between; font-size: 0.9rem; color: var(--gray);">
                            <span><?php echo htmlspecialchars($article['AUTEUR_NOM']); ?></span>
                            <span><?php echo $article['VIEW_COUNT'] ?: 0; ?> vues</span>
                        </div>
                    </div>
                <?php endforeach; } catch (Exception $e) { ?>
                    <p style="color: var(--gray);">Impossible de charger les articles populaires.</p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>