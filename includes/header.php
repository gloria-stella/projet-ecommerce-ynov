<?php 
require_once __DIR__ . '/../config/database.php';
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Youlla Books - Votre librairie en ligne accessible a tous. Decouvrez notre selection de livres.">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' | ' : '' ?>Youlla Books</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Source+Sans+3:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    
    <!-- Skip link pour accessibilite -->
    <style>
        .skip-link {
            position: absolute;
            top: -40px;
            left: 0;
            background: var(--primary);
            color: white;
            padding: 8px 16px;
            z-index: 10000;
            transition: top 0.3s;
        }
        .skip-link:focus {
            top: 0;
        }
    </style>
</head>
<body>
    <!-- Skip link pour navigation clavier -->
    <a href="#main-content" class="skip-link">Aller au contenu principal</a>
    
    <!-- Barre d'accessibilite -->
    <div class="accessibility-bar" role="region" aria-label="Options d'accessibilite">
        <div class="container">
            <div class="accessibility-controls">
                <span class="accessibility-label">Accessibilite :</span>
                <button type="button" class="access-btn" id="decrease-font" aria-label="Reduire la taille du texte" title="Reduire la taille du texte">
                    A-
                </button>
                <button type="button" class="access-btn" id="reset-font" aria-label="Taille du texte par defaut" title="Taille du texte par defaut">
                    A
                </button>
                <button type="button" class="access-btn" id="increase-font" aria-label="Augmenter la taille du texte" title="Augmenter la taille du texte">
                    A+
                </button>
                <button type="button" class="access-btn" id="toggle-contrast" aria-label="Mode contraste eleve" title="Mode contraste eleve">
                    <span aria-hidden="true">◐</span> Contraste
                </button>
                <button type="button" class="access-btn" id="toggle-dyslexia" aria-label="Police pour dyslexiques" title="Police pour dyslexiques">
                    Dyslexie
                </button>
            </div>
        </div>
    </div>
    
    <!-- Header principal -->
    <header class="main-header" role="banner">
        <div class="container">
            <a href="<?= BASE_URL ?>/front/index.php" class="logo" aria-label="Youlla Books - Retour a l'accueil">
                <span class="logo-icon" aria-hidden="true">📚</span>
                <span class="logo-text">
                    <span class="logo-main">Youlla Books</span>
                    <span class="logo-tagline">Lire, c'est vivre mille vies</span>
                </span>
            </a>
            
            <nav class="main-nav" role="navigation" aria-label="Navigation principale">
                <ul class="nav-list">
                    <li><a href="<?= BASE_URL ?>/front/index.php" <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'aria-current="page"' : '' ?>>Accueil</a></li>
                    <li><a href="<?= BASE_URL ?>/front/products.php" <?= basename($_SERVER['PHP_SELF']) === 'products.php' ? 'aria-current="page"' : '' ?>>Nos Livres</a></li>
                    <li><a href="<?= BASE_URL ?>/front/about.php" <?= basename($_SERVER['PHP_SELF']) === 'about.php' ? 'aria-current="page"' : '' ?>>Qui sommes-nous</a></li>
                </ul>
            </nav>
            
            <div class="header-actions">
                <!-- Panier -->
                <a href="<?= BASE_URL ?>/front/cart.php" class="cart-link" aria-label="Panier (<?= getCartCount() ?> articles)">
                    <span class="cart-icon" aria-hidden="true">🛒</span>
                    <span class="cart-text">Panier</span>
                    <?php if (getCartCount() > 0): ?>
                        <span class="cart-count" aria-hidden="true"><?= getCartCount() ?></span>
                    <?php endif; ?>
                </a>
                
                <!-- Authentification -->
                <?php if (isLoggedIn()): ?>
                    <div class="user-menu">
                        <span class="user-greeting">Bonjour, <strong><?= sanitize($_SESSION['user_name']) ?></strong></span>
                        <?php if (isAdmin()): ?>
                            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline btn-small">Admin</a>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>/front/logout.php" class="btn btn-outline btn-small">Deconnexion</a>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/front/login.php" class="btn btn-outline">Connexion</a>
                    <a href="<?= BASE_URL ?>/front/register.php" class="btn btn-primary">Inscription</a>
                <?php endif; ?>
            </div>
            
            <!-- Menu burger mobile -->
            <button type="button" class="menu-toggle" id="menu-toggle" aria-expanded="false" aria-controls="mobile-menu" aria-label="Menu">
                <span class="burger-line"></span>
                <span class="burger-line"></span>
                <span class="burger-line"></span>
            </button>
        </div>
    </header>
    
    <!-- Messages flash -->
    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?>" role="alert" aria-live="polite">
            <div class="container">
                <?= sanitize($flash['message']) ?>
                <button type="button" class="alert-close" aria-label="Fermer le message">&times;</button>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Contenu principal -->
    <main id="main-content" class="main-content" role="main">