<?php
require_once 'functions.php';

// Vérifier si l'utilisateur est connecté
$userInfo = getCurrentUserInfo();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArticleHub - Plateforme d'articles</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Styles CSS identiques au frontend */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --accent: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --success: #4cc9f0;
            --border-radius: 8px;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        body {
            background-color: #f5f7ff;
            color: var(--dark);
            line-height: 1.6;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header */
        header {
            background-color: white;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }
        
        .logo i {
            color: var(--accent);
        }
        
        .nav-links {
            display: flex;
            gap: 25px;
            align-items: center;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: var(--primary);
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 1rem;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
        }
        
        .btn-outline {
            background-color: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        
        .btn-outline:hover {
            background-color: var(--primary);
            color: white;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        /* Messages */
        .message {
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
        }
        
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .message.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        /* Main content */
        main {
            padding: 40px 0;
        }
        
        .page-title {
            font-size: 2.5rem;
            margin-bottom: 30px;
            color: var(--dark);
        }
        
        /* Articles grid */
        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        
        .article-card {
            background-color: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s;
        }
        
        .article-card:hover {
            transform: translateY(-5px);
        }
        
        .article-image {
            height: 200px;
            background-color: var(--light-gray);
            background-size: cover;
            background-position: center;
        }
        
        .article-content {
            padding: 25px;
        }
        
        .article-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: var(--gray);
        }
        
        .article-author {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .article-author .avatar {
            width: 30px;
            height: 30px;
            font-size: 0.8rem;
        }
        
        .article-category {
            background-color: var(--light-gray);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .article-title {
            font-size: 1.4rem;
            margin-bottom: 10px;
            color: var(--dark);
            text-decoration: none;
            display: block;
        }
        
        .article-title:hover {
            color: var(--primary);
        }
        
        .article-excerpt {
            color: var(--gray);
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .article-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--light-gray);
            padding-top: 15px;
        }
        
        .article-comments {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--gray);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 20px;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .articles-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Utility classes */
        .hidden {
            display: none !important;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-4 {
            margin-top: 40px;
        }
        
        .mb-4 {
            margin-bottom: 40px;
        }
        
        .content-wrapper {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 40px;
        }
        
        @media (max-width: 992px) {
            .content-wrapper {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    <i class="fas fa-newspaper"></i>
                    ArticleHub
                </a>
                <div class="nav-links">
                    <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Accueil</a>
                    
                    <?php if (isLoggedIn()): ?>
                        <a href="create_article.php">Nouvel article</a>
                    <?php endif; ?>
                    
                    <a href="#categories">Catégories</a>
                    
                    <div class="user-info">
                        <div class="avatar">
                            <?php echo isLoggedIn() ? getInitial($userInfo['NOM']) : 'V'; ?>
                        </div>
                        <span>
                            <?php echo isLoggedIn() ? htmlspecialchars($userInfo['NOM']) : 'Visiteur'; ?>
                        </span>
                        
                        <?php if (isLoggedIn()): ?>
                            <a href="logout.php" class="btn btn-outline">Déconnexion</a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline">Connexion</a>
                            <a href="register.php" class="btn btn-primary">Inscription</a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="container">
            <div class="message success">
                <?php echo $_SESSION['success_message']; ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="container">
            <div class="message error">
                <?php echo $_SESSION['error_message']; ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        </div>
    <?php endif; ?>

    <main class="container">