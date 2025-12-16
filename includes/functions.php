<?php
require_once 'config/database.php';
session_start();

// Fonction pour récupérer tous les articles publiés
function getAllArticles($limit = 10, $offset = 0) {
    global $db;
    
    $sql = "SELECT a.*, u.nom as auteur_nom, c.categorie_nom 
            FROM article a 
            JOIN utilisateur u ON a.utilisateur_nom = u.nom 
            LEFT JOIN categorie c ON a.categorie_id = c.categorie_id 
            WHERE a.article_status = 'published' 
            ORDER BY a.date_de_creation DESC";
    
    if ($limit > 0) {
        $sql .= " OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";
        $params = [':offset' => $offset, ':limit' => $limit];
        return $db->executeQuery($sql, $params);
    }
    
    return $db->executeQuery($sql);
}

// Fonction pour récupérer un article par son ID
function getArticleById($articleId) {
    global $db;
    
    $sql = "SELECT a.*, u.nom as auteur_nom, u.email as auteur_email, 
                   c.categorie_nom, c.categorie_description 
            FROM article a 
            JOIN utilisateur u ON a.utilisateur_nom = u.nom 
            LEFT JOIN categorie c ON a.categorie_id = c.categorie_id 
            WHERE a.article_id = :article_id";
    
    $params = [':article_id' => $articleId];
    $result = $db->executeQuery($sql, $params);
    
    return !empty($result) ? $result[0] : null;
}

// Fonction pour récupérer les commentaires d'un article
function getCommentsByArticleId($articleId) {
    global $db;
    
    $sql = "SELECT c.*, u.nom as utilisateur_nom, u.email as utilisateur_email 
            FROM commentaire c 
            LEFT JOIN utilisateur u ON c.auteur_nom = u.nom 
            WHERE c.article_id = :article_id 
            AND c.commentaire_status = 'approved' 
            ORDER BY c.commentaire_date DESC";
    
    $params = [':article_id' => $articleId];
    return $db->executeQuery($sql, $params);
}

// Fonction pour récupérer toutes les catégories
function getAllCategories() {
    global $db;
    
    $sql = "SELECT c.*, 
            (SELECT COUNT(*) FROM article a WHERE a.categorie_id = c.categorie_id AND a.article_status = 'published') as article_count 
            FROM categorie c 
            ORDER BY c.categorie_nom";
    
    return $db->executeQuery($sql);
}

// Fonction pour ajouter un commentaire
function addComment($articleId, $auteurNom, $email, $contenu) {
    global $db;
    
    $commentId = $db->getNextSequenceValue('seq_commentaire_id');
    
    $sql = "INSERT INTO commentaire (commentaire_id, auteur_nom, email, contenu, article_id) 
            VALUES (:commentaire_id, :auteur_nom, :email, :contenu, :article_id)";
    
    $params = [
        ':commentaire_id' => $commentId,
        ':auteur_nom' => $auteurNom,
        ':email' => $email,
        ':contenu' => $contenu,
        ':article_id' => $articleId
    ];
    
    return $db->executeNonQuery($sql, $params);
}

// Fonction pour créer un nouvel article
function createArticle($titre, $contenu, $utilisateurNom, $categorieId = null, $imgUrl = null) {
    global $db;
    
    $articleId = $db->getNextSequenceValue('seq_article_id');
    
    $sql = "INSERT INTO article (article_id, titre, contenu, utilisateur_nom, categorie_id, img_url, article_status) 
            VALUES (:article_id, :titre, :contenu, :utilisateur_nom, :categorie_id, :img_url, 'published')";
    
    $params = [
        ':article_id' => $articleId,
        ':titre' => $titre,
        ':contenu' => $contenu,
        ':utilisateur_nom' => $utilisateurNom,
        ':categorie_id' => $categorieId,
        ':img_url' => $imgUrl
    ];
    
    return $db->executeNonQuery($sql, $params);
}

// Fonction pour vérifier les identifiants utilisateur
function verifyUserCredentials($email, $password) {
    global $db;
    
    $sql = "SELECT nom, email, mot_de_passe, utilisateur_role 
            FROM utilisateur 
            WHERE email = :email";
    
    $params = [':email' => $email];
    $result = $db->executeQuery($sql, $params);
    
    if (!empty($result)) {
        $user = $result[0];
        // Vérifier le mot de passe (dans votre base, les mots de passe sont hashés)
        if (password_verify($password, $user['MOT_DE_PASSE'])) {
            return $user;
        }
    }
    
    return null;
}

// Fonction pour créer un nouvel utilisateur
function createUser($nom, $email, $password, $role = 'subscriber') {
    global $db;
    
    // Vérifier si l'utilisateur existe déjà
    $checkSql = "SELECT COUNT(*) as count FROM utilisateur WHERE email = :email OR nom = :nom";
    $checkParams = [':email' => $email, ':nom' => $nom];
    $checkResult = $db->executeQuery($checkSql, $checkParams);
    
    if ($checkResult[0]['COUNT'] > 0) {
        return false; // Utilisateur existe déjà
    }
    
    // Hasher le mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO utilisateur (nom, email, mot_de_passe, utilisateur_role) 
            VALUES (:nom, :email, :mot_de_passe, :utilisateur_role)";
    
    $params = [
        ':nom' => $nom,
        ':email' => $email,
        ':mot_de_passe' => $hashedPassword,
        ':utilisateur_role' => $role
    ];
    
    return $db->executeNonQuery($sql, $params) > 0;
}

// Fonction pour incrémenter le compteur de vues
function incrementViewCount($articleId) {
    global $db;
    
    $sql = "UPDATE article SET view_count = view_count + 1 WHERE article_id = :article_id";
    $params = [':article_id' => $articleId];
    return $db->executeNonQuery($sql, $params);
}

// Fonction pour formater la date Oracle
function formatDate($dateString) {
    if (empty($dateString)) return '';
    
    try {
        $date = new DateTime($dateString);
        return $date->format('d/m/Y à H:i');
    } catch (Exception $e) {
        return $dateString;
    }
}

// Fonction pour obtenir l'initiale d'un nom
function getInitial($name) {
    if (empty($name)) return '?';
    return strtoupper(substr($name, 0, 1));
}

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_nom']);
}

// Fonction pour obtenir l'utilisateur actuel
function getCurrentUser() {
    return isset($_SESSION['user_nom']) ? $_SESSION['user_nom'] : null;
}

// Fonction pour obtenir les informations de l'utilisateur connecté
function getCurrentUserInfo() {
    if (!isset($_SESSION['user_nom'])) {
        return null;
    }
    
    global $db;
    
    $sql = "SELECT nom, email, utilisateur_role FROM utilisateur WHERE nom = :nom";
    $params = [':nom' => $_SESSION['user_nom']];
    $result = $db->executeQuery($sql, $params);
    
    return !empty($result) ? $result[0] : null;
}

// Fonction pour rechercher des articles
function searchArticles($keyword, $categorieId = null) {
    global $db;
    
    $sql = "SELECT a.*, u.nom as auteur_nom, c.categorie_nom 
            FROM article a 
            JOIN utilisateur u ON a.utilisateur_nom = u.nom 
            LEFT JOIN categorie c ON a.categorie_id = c.categorie_id 
            WHERE a.article_status = 'published' 
            AND (LOWER(a.titre) LIKE LOWER(:keyword) OR LOWER(a.contenu) LIKE LOWER(:keyword))";
    
    $params = [':keyword' => '%' . $keyword . '%'];
    
    if ($categorieId) {
        $sql .= " AND a.categorie_id = :categorie_id";
        $params[':categorie_id'] = $categorieId;
    }
    
    $sql .= " ORDER BY a.date_de_creation DESC";
    
    return $db->executeQuery($sql, $params);
}

// Fonction pour obtenir les statistiques
function getStats() {
    global $db;
    
    $stats = [];
    
    try {
        $articlesCount = $db->executeQuery("SELECT COUNT(*) as count FROM article WHERE article_status = 'published'");
        $usersCount = $db->executeQuery("SELECT COUNT(*) as count FROM utilisateur");
        $commentsCount = $db->executeQuery("SELECT COUNT(*) as count FROM commentaire WHERE commentaire_status = 'approved'");
        
        $stats['articles'] = $articlesCount[0]['COUNT'];
        $stats['users'] = $usersCount[0]['COUNT'];
        $stats['comments'] = $commentsCount[0]['COUNT'];
    } catch (Exception $e) {
        $stats['articles'] = 0;
        $stats['users'] = 0;
        $stats['comments'] = 0;
    }
    
    return $stats;
}
?>