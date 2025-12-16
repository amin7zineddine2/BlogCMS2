<?php
require_once 'includes/header.php';

// Vérifier si un ID d'article est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = 'Article non trouvé.';
    header('Location: index.php');
    exit();
}

$articleId = $_GET['id'];

// Récupérer l'article
$article = getArticleById($articleId);

if (!$article) {
    $_SESSION['error_message'] = 'Article non trouvé.';
    header('Location: index.php');
    exit();
}

// Incrémenter le compteur de vues
incrementViewCount($articleId);

// Récupérer les commentaires
$comments = getCommentsByArticleId($articleId);
?>

<div class="article-detail" style="background-color: white; border-radius: var(--border-radius); box-shadow: var(--shadow); padding: 40px; margin-bottom: 40px;">
    <div class="article-header" style="margin-bottom: 30px;">
        <h1 class="article-detail-title" style="font-size: 2.2rem; margin-bottom: 15px; line-height: 1.3;">
            <?php echo htmlspecialchars($article['TITRE']); ?>
        </h1>
        <div class="article-detail-meta" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid var(--light-gray);">
            <div class="article-detail-author" style="display: flex; align-items: center; gap: 15px;">
                <div class="avatar" style="width: 50px; height: 50px;"><?php echo getInitial($article['AUTEUR_NOM']); ?></div>
                <div class="author-info">
                    <h4 style="margin-bottom: 5px;"><?php echo htmlspecialchars($article['AUTEUR_NOM']); ?></h4>
                    <p style="color: var(--gray); font-size: 0.9rem;">
                        <?php echo formatDate($article['DATE_DE_CREATION']); ?> • 
                        <?php echo $article['VIEW_COUNT']; ?> vues • 
                        <?php echo htmlspecialchars($article['CATEGORIE_NOM']); ?>
                    </p>
                </div>
            </div>
            <a href="index.php" class="btn btn-outline">Retour aux articles</a>
        </div>
    </div>
    
    <div class="article-body" style="font-size: 1.1rem; line-height: 1.8; margin-bottom: 40px;">
        <?php echo nl2br(htmlspecialchars($article['CONTENU'])); ?>
    </div>
    
    <div class="comments-section" style="margin-top: 60px;">
        <h2 class="comments-title" style="font-size: 1.8rem; margin-bottom: 30px; color: var(--dark);">
            Commentaires (<?php echo count($comments); ?>)
        </h2>
        
        <!-- Formulaire de commentaire -->
        <div class="comment-form" style="background-color: white; border-radius: var(--border-radius); padding: 30px; box-shadow: var(--shadow); margin-bottom: 40px;">
            <h3 class="form-title" style="font-size: 1.4rem; margin-bottom: 20px;">Ajouter un commentaire</h3>
            
            <form method="POST" action="add_comment.php">
                <input type="hidden" name="article_id" value="<?php echo $articleId; ?>">
                
                <?php if (!isLoggedIn()): ?>
                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label for="comment_name" style="display: block; margin-bottom: 8px; font-weight: 500;">Nom</label>
                            <input type="text" id="comment_name" name="name" class="form-control" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: var(--border-radius); font-size: 1rem;">
                        </div>
                        <div class="form-group">
                            <label for="comment_email" style="display: block; margin-bottom: 8px; font-weight: 500;">Email</label>
                            <input type="email" id="comment_email" name="email" class="form-control" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: var(--border-radius); font-size: 1rem;">
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="comment_content" style="display: block; margin-bottom: 8px; font-weight: 500;">Commentaire</label>
                    <textarea id="comment_content" name="content" class="form-control" required style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: var(--border-radius); font-size: 1rem; min-height: 120px; resize: vertical;"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Publier le commentaire</button>
            </form>
        </div>
        
        <!-- Liste des commentaires -->
        <div class="comments-list" style="display: flex; flex-direction: column; gap: 25px;">
            <?php if (empty($comments)): ?>
                <p style="text-align: center; color: var(--gray); padding: 20px;">
                    Aucun commentaire pour le moment. Soyez le premier à commenter !
                </p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment" style="background-color: white; border-radius: var(--border-radius); padding: 25px; box-shadow: var(--shadow);">
                        <div class="comment-header" style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                            <div class="comment-author" style="display: flex; align-items: center; gap: 10px;">
                                <div class="avatar" style="width: 40px; height: 40px; background-color: var(--success);">
                                    <?php echo getInitial($comment['UTILISATEUR_NOM'] ?: $comment['EMAIL']); ?>
                                </div>
                                <div>
                                    <h4 style="font-size: 1.1rem;">
                                        <?php echo htmlspecialchars($comment['UTILISATEUR_NOM'] ?: $comment['EMAIL']); ?>
                                    </h4>
                                    <?php if (!$comment['UTILISATEUR_NOM']): ?>
                                        <span class="visitor-badge" style="background-color: var(--light-gray); color: var(--gray); padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 500;">Visiteur</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="comment-meta" style="color: var(--gray); font-size: 0.9rem;">
                                <?php echo formatDate($comment['COMMENTAIRE_DATE']); ?>
                            </div>
                        </div>
                        <div class="comment-body" style="line-height: 1.6;">
                            <?php echo nl2br(htmlspecialchars($comment['CONTENU'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>