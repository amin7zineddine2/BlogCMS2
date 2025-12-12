<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';
requireLogin();

$pdo = getPDO();
$user_role = getUserRole();
$user_name = getUserName();

// Statistiques selon le rôle
if ($user_role === 'admin') {
    // Admin voit tout
    $total_articles = $pdo->query("SELECT COUNT(*) FROM article")->fetchColumn();
    $published_articles = $pdo->query("SELECT COUNT(*) FROM article WHERE article_status = 'published'")->fetchColumn();
    $total_comments = $pdo->query("SELECT COUNT(*) FROM commentaire")->fetchColumn();
    $pending_comments = $pdo->query("SELECT COUNT(*) FROM commentaire WHERE commentaire_status = 'pending'")->fetchColumn();
    $total_users = $pdo->query("SELECT COUNT(*) FROM utilisateur")->fetchColumn();
} elseif (in_array($user_role, ['editor', 'author'])) {
    // Éditeurs et auteurs voient leurs articles
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM article WHERE utilisateur_nom = :username");
    $stmt->execute([':username' => $user_name]);
    $total_articles = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM article WHERE utilisateur_nom = :username AND article_status = 'published'");
    $stmt->execute([':username' => $user_name]);
    $published_articles = $stmt->fetchColumn();
    
    // Pour les commentaires sur leurs articles
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM commentaire c JOIN article a ON c.article_id = a.article_id WHERE a.utilisateur_nom = :username");
    $stmt->execute([':username' => $user_name]);
    $total_comments = $stmt->fetchColumn();
    
    $pending_comments = 0; // Auteurs ne modèrent pas
} else {
    // Subscribers
    $total_articles = 0;
    $published_articles = 0;
    $total_comments = 0;
    $pending_comments = 0;
}
?>
<?php 
$page_title = 'Tableau de bord';
include 'includes/header.php'; 
?>
<div class="row">
    <div class="col-md-3 d-none d-md-block">
        <div class="sidebar bg-light">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Tableau de bord
                        </a>
                    </li>
                    <?php if (in_array($user_role, ['admin', 'editor', 'author'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="articles/index.php">
                                <i class="bi bi-file-text"></i> Mes articles
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="articles/create.php">
                                <i class="bi bi-plus-circle"></i> Nouvel article
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if ($user_role === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/index.php">
                                <i class="bi bi-shield-check"></i> Administration
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-9 ms-sm-auto col-lg-9 px-md-4">
        <h1 class="h2 mb-4">Tableau de bord</h1>
        
        <div class="row">
            <?php if (in_array($user_role, ['admin', 'editor', 'author'])): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card stat-card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Articles</h5>
                                    <h2 class="mb-0"><?php echo $total_articles; ?></h2>
                                    <small>dont <?php echo $published_articles; ?> publiés</small>
                                </div>
                                <i class="bi bi-file-text display-4 opacity-50"></i>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="articles/index.php" class="text-white">Voir tous <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card stat-card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Commentaires</h5>
                                    <h2 class="mb-0"><?php echo $total_comments; ?></h2>
                                    <?php if ($user_role === 'admin' && $pending_comments > 0): ?>
                                        <small>dont <?php echo $pending_comments; ?> en attente</small>
                                    <?php endif; ?>
                                </div>
                                <i class="bi bi-chat-dots display-4 opacity-50"></i>
                            </div>
                        </div>
                        <?php if ($user_role === 'admin'): ?>
                            <div class="card-footer">
                                <a href="admin/comments/index.php" class="text-white">Gérer <i class="bi bi-arrow-right"></i></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($user_role === 'admin'): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card stat-card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Utilisateurs</h5>
                                    <h2 class="mb-0"><?php echo $total_users; ?></h2>
                                </div>
                                <i class="bi bi-people display-4 opacity-50"></i>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="admin/users/index.php" class="text-dark">Gérer <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (in_array($user_role, ['admin', 'editor', 'author'])): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Mes derniers articles</h5>
                        </div>
                        <div class="card-body">
                            <?php 
                            $stmt = $pdo->prepare("SELECT a.*, c.categorie_nom FROM article a 
                                                  LEFT JOIN categorie c ON a.categorie_id = c.categorie_id 
                                                  WHERE a.utilisateur_nom = :username 
                                                  ORDER BY a.date_de_creation DESC LIMIT 5");
                            $stmt->execute([':username' => $user_name]);
                            $articles = $stmt->fetchAll();
                            ?>
                            
                            <?php if (empty($articles)): ?>
                                <p class="text-muted">Vous n'avez pas encore écrit d'articles.</p>
                                <a href="articles/create.php" class="btn btn-primary">Écrire mon premier article</a>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Titre</th>
                                                <th>Catégorie</th>
                                                <th>Statut</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($articles as $article): ?>
                                                <tr>
                                                    <td><?php echo escape($article['titre']); ?></td>
                                                    <td><?php echo escape($article['categorie_nom']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                                            echo $article['article_status'] === 'published' ? 'success' : 
                                                                ($article['article_status'] === 'draft' ? 'warning' : 'secondary'); 
                                                        ?>">
                                                            <?php echo getArticleStatus($article['article_status']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo formatDate($article['date_de_creation']); ?></td>
                                                    <td>
                                                        <a href="articles/view.php?id=<?php echo $article['article_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="articles/edit.php?id=<?php echo $article['article_id']; ?>" class="btn btn-sm btn-outline-secondary">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>