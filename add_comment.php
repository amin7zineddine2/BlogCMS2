<?php
require_once 'includes/functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$articleId = $_POST['article_id'] ?? 0;
$contenu = $_POST['content'] ?? '';

if (empty($articleId) || empty($contenu)) {
    $_SESSION['error_message'] = 'Veuillez remplir tous les champs.';
    header('Location: view_article.php?id=' . $articleId);
    exit();
}

// Déterminer l'auteur du commentaire
$auteurNom = null;
$email = null;

if (isLoggedIn()) {
    $userInfo = getCurrentUserInfo();
    $auteurNom = $userInfo['NOM'];
} else {
    $auteurNom = null;
    $email = $_POST['email'] ?? '';
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = 'Veuillez fournir un email valide.';
        header('Location: view_article.php?id=' . $articleId);
        exit();
    }
}

// Ajouter le commentaire
$success = addComment($articleId, $auteurNom, $email, $contenu);

if ($success) {
    $_SESSION['success_message'] = 'Commentaire ajouté avec succès ! Il sera visible après modération.';
} else {
    $_SESSION['error_message'] = 'Une erreur est survenue lors de l\'ajout du commentaire.';
}

header('Location: view_article.php?id=' . $articleId);
exit();
?>