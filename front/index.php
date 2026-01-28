<?php
$pageTitle = 'Accueil';
include '../includes/header.php';

// Recuperer les livres en vedette
$featuredStmt = $pdo->query("
    SELECT i.*, s.quantity as stock 
    FROM items i 
    LEFT JOIN stock s ON i.id = s.item_id 
    WHERE i.is_featured = 1 
    ORDER BY i.created_at DESC 
    LIMIT 4
");
$featuredBooks = $featuredStmt->fetchAll();

// Recuperer les nouveautes
$newStmt = $pdo->query("
    SELECT i.*, s.quantity as stock 
    FROM items i 
    LEFT JOIN stock s ON i.id = s.item_id 
    ORDER BY i.created_at DESC 
    LIMIT 8
");
$newBooks = $newStmt->fetchAll();

// Recuperer les categories
$categoriesStmt = $pdo->query("
    SELECT category, COUNT(*) as count 
    FROM items 
    WHERE category IS NOT NULL 
    GROUP BY category 
    ORDER BY count DESC 
    LIMIT 6
");
$categories = $categoriesStmt->fetchAll();
?>

<div class="container">
    <!-- Hero Section -->
    <section class="hero" aria-labelledby="hero-title">
        <div class="hero-content">
            <h1 id="hero-title">Bienvenue chez Youlla Books</h1>
            <p>Decouvrez notre univers litteraire unique. Des milliers de livres soigneusement selectionnes pour eveiller votre imagination et nourrir votre esprit.</p>
            <a href="products.php" class="btn btn-primary btn-large">
                Explorer notre collection
            </a>
        </div>
    </section>
    
    <!-- Livres en vedette -->
    <?php if (!empty($featuredBooks)): ?>
    <section class="section" aria-labelledby="featured-title">
        <div class="section-header">
            <h2 id="featured-title" class="section-title">Nos coups de coeur</h2>
            <p class="section-subtitle">Les livres que nous aimons et que nous vous recommandons</p>
        </div>
        
        <div class="products-grid">
            <?php foreach ($featuredBooks as $book): ?>
                <article class="product-card">
                    <div class="product-card-image">
                        <span class="product-badge">Coup de coeur</span>
                        <img src="<?= BASE_URL ?>/assets/img/books/<?= sanitize($book['image']) ?>" 
                             alt="Couverture de <?= sanitize($book['title']) ?>"
                             loading="lazy">
                    </div>
                    <div class="product-card-body">
                        <span class="product-category"><?= sanitize($book['category']) ?></span>
                        <h3><a href="product.php?id=<?= $book['id'] ?>"><?= sanitize($book['title']) ?></a></h3>
                        <p class="product-author">Par <?= sanitize($book['author']) ?></p>
                        <div class="product-card-footer">
                            <span class="product-price"><?= formatPrice($book['price']) ?></span>
                            <a href="product.php?id=<?= $book['id'] ?>" class="btn btn-primary btn-small">Decouvrir</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Categories -->
    <section class="section" aria-labelledby="categories-title">
        <div class="section-header">
            <h2 id="categories-title" class="section-title">Explorez nos univers</h2>
            <p class="section-subtitle">Trouvez le genre qui vous correspond</p>
        </div>
        
        <div class="values-grid">
            <?php 
            $categoryIcons = [
                'Litterature' => '📖',
                'Science-Fiction' => '🚀',
                'Fantasy' => '🐉',
                'Jeunesse' => '🧒',
                'Thriller' => '🔍',
                'Classique' => '📜',
                'Histoire' => '🏛️',
                'Philosophie' => '💭',
                'Bande dessinee' => '💬',
                'Cuisine' => '🍳',
                'Developpement personnel' => '🌟',
                'Aventure' => '🗺️',
                'Poesie' => '🌸'
            ];
            foreach ($categories as $cat): 
                $icon = $categoryIcons[$cat['category']] ?? '📚';
            ?>
                <a href="products.php?category=<?= urlencode($cat['category']) ?>" class="value-card" style="text-decoration: none;">
                    <span class="value-icon" aria-hidden="true"><?= $icon ?></span>
                    <h3><?= sanitize($cat['category']) ?></h3>
                    <p><?= $cat['count'] ?> livre<?= $cat['count'] > 1 ? 's' : '' ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    
    <!-- Nouveautes -->
    <section class="section" aria-labelledby="new-title">
        <div class="section-header">
            <h2 id="new-title" class="section-title">Nos dernieres nouveautes</h2>
            <p class="section-subtitle">Les livres recemment ajoutes a notre catalogue</p>
        </div>
        
        <div class="products-grid">
            <?php foreach ($newBooks as $book): ?>
                <article class="product-card">
                    <div class="product-card-image">
                        <img src="<?= BASE_URL ?>/assets/img/books/<?= sanitize($book['image']) ?>" 
                             alt="Couverture de <?= sanitize($book['title']) ?>"
                             loading="lazy">
                    </div>
                    <div class="product-card-body">
                        <span class="product-category"><?= sanitize($book['category']) ?></span>
                        <h3><a href="product.php?id=<?= $book['id'] ?>"><?= sanitize($book['title']) ?></a></h3>
                        <p class="product-author">Par <?= sanitize($book['author']) ?></p>
                        <div class="product-card-footer">
                            <span class="product-price"><?= formatPrice($book['price']) ?></span>
                            <a href="product.php?id=<?= $book['id'] ?>" class="btn btn-outline btn-small">Voir</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        
        <div style="text-align: center; margin-top: 3rem;">
            <a href="products.php" class="btn btn-primary btn-large">Voir tous nos livres</a>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>