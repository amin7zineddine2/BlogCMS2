<?php
require_once 'includes/header.php';

$keyword = $_GET['q'] ?? '';
$categorieId = $_GET['categorie'] ?? null;

$results = [];
if (!empty($keyword)) {
    $results = searchArticles($keyword, $categorieId);
}

$categories = getAllCategories();
?>

<div class="content-wrapper">
    <div>
        <h1 class="page-title">Résultats de recherche</h1>
        
        <!-- Formulaire de recherche -->
        <div class="search-bar mb-4" style="margin-bottom: 40px;">
            <form action="search.php" method="GET" class="search-form">
                <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end;">
                    <div style="flex: 1;">
                        <label for="q" style="display: block; margin-bottom: 8px; font-weight: 500;">Rechercher</label>
                        <input type="text" id="q" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Rechercher des articles..." class="form-control" style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: var(--border-radius); font-size: 1rem;">
                    </div>
                    <div style="width: 200px;">
                        <label for="categorie" style="display: block; margin-bottom: 8px; font-weight: 500;">Catégorie</label>
                        <select id="categorie" name="categorie" class="form-control" style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: var(--border-radius); font-size: 1rem;">
                            <option value="">Toutes les catégories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['CATEGORIE_ID']; ?>" <?php echo $categorieId == $category['CATEGORIE_ID'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['CATEGORIE_NOM']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">Rechercher</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Résultats -->
        <?php if (empty($keyword)): ?>
            <div class="no-results" style="text-align: center; padding: 40px;">
                <p style="font-size: 1.2rem; color: var(--gray);">
                    Entrez un terme de recherche pour trouver des articles.
                </p>
            </div>
        <?php elseif (empty($results)): ?>
            <div class="no-results" style="text-align: center; padding: 40px;">
                <p style="font-size: 1.2rem; color: var(--gray);">
                    Aucun article trouvé pour "<?php echo htmlspecialchars($keyword); ?>"
                </p>
            </div>
        <?php else: ?>
            <div class="results-count" style="color: var(--gray); margin-bottom: 20px;">
                <?php echo count($results); ?> résultat(s) trouvé(s) pour "<?php echo htmlspecialchars($keyword); ?>"
            </div>
            
            <div class="articles-grid">
                <?php foreach ($results as $article): ?>
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
                                    <i class="fas fa-eye"></i>
                                    <span><?php echo $article['VIEW_COUNT']; ?> vues</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar -->
    <div>
        <div class="categories-sidebar" style="background-color: white; border-radius: var(--border-radius); box-shadow: var(--shadow); padding: 25px;">
            <h3 class="sidebar-title" style="font-size: 1.4rem; margin-bottom: 20px; color: var(--dark);">Conseils de recherche</h3>
            <ul style="list-style: none; color: var(--gray);">
                <li style="margin-bottom: 10px;">• Utilisez des mots-clés précis</li>
                <li style="margin-bottom: 10px;">• Essayez différentes orthographes</li>
                <li style="margin-bottom: 10px;">• Limitez votre recherche par catégorie</li>
                <li style="margin-bottom: 10px;">• Utilisez des guillemets pour les phrases exactes</li>
            </ul>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>