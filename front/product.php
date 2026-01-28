<?php
include '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Recuperer le livre
$stmt = $pdo->prepare("
    SELECT i.*, s.quantity as stock 
    FROM items i 
    LEFT JOIN stock s ON i.id = s.item_id 
    WHERE i.id = ?
");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    setFlashMessage('error', 'Livre non trouve.');
    redirect('products.php');
}

$pageTitle = $book['title'];

// Ajouter au panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    // Verifier le token CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Erreur de securite. Veuillez reessayer.');
    } else {
        $quantity = max(1, (int)$_POST['quantity']);
        
        if ($quantity > $book['stock']) {
            setFlashMessage('error', 'Quantite demandee non disponible en stock.');
        } else {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id] += $quantity;
            } else {
                $_SESSION['cart'][$id] = $quantity;
            }
            
            setFlashMessage('success', '"' . $book['title'] . '" a ete ajoute a votre panier !');
            redirect($_SERVER['REQUEST_URI']);
        }
    }
}

// Livres similaires
$similarStmt = $pdo->prepare("
    SELECT i.*, s.quantity as stock 
    FROM items i 
    LEFT JOIN stock s ON i.id = s.item_id 
    WHERE i.category = ? AND i.id != ? 
    ORDER BY RAND() 
    LIMIT 4
");
$similarStmt->execute([$book['category'], $id]);
$similarBooks = $similarStmt->fetchAll();

include '../includes/header.php';
?>

<div class="container">
    <!-- Fil d'Ariane -->
    <nav aria-label="Fil d'ariane" style="margin-bottom: 2rem;">
        <a href="products.php">Nos Livres</a> &raquo; 
        <a href="products.php?category=<?= urlencode($book['category']) ?>"><?= sanitize($book['category']) ?></a> &raquo; 
        <span aria-current="page"><?= sanitize($book['title']) ?></span>
    </nav>
    
    <!-- Detail du produit -->
    <div class="product-detail">
        <div class="product-detail-image">
            <img src="<?= BASE_URL ?>/assets/img/books/<?= sanitize($book['image']) ?>" 
                 alt="Couverture de <?= sanitize($book['title']) ?>">
        </div>
        
        <div class="product-detail-info">
            <span class="product-category"><?= sanitize($book['category']) ?></span>
            <h1><?= sanitize($book['title']) ?></h1>
            <p class="product-author">Par <strong><?= sanitize($book['author']) ?></strong></p>
            
            <p class="product-price"><?= formatPrice($book['price']) ?></p>
            
            <!-- Stock -->
            <?php if ($book['stock'] > 0): ?>
                <div class="stock-status in-stock" role="status">
                    <span aria-hidden="true">✓</span> En stock (<?= $book['stock'] ?> disponible<?= $book['stock'] > 1 ? 's' : '' ?>)
                </div>
            <?php else: ?>
                <div class="stock-status out-of-stock" role="status">
                    <span aria-hidden="true">✗</span> Rupture de stock
                </div>
            <?php endif; ?>
            
            <!-- Description -->
            <div class="product-description">
                <p><?= nl2br(sanitize($book['long_description'] ?: $book['description'])) ?></p>
            </div>
            
            <!-- Metadonnees -->
            <div class="product-meta">
                <?php if ($book['isbn']): ?>
                <div class="product-meta-item">
                    <span class="product-meta-label">ISBN</span>
                    <span class="product-meta-value"><?= sanitize($book['isbn']) ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($book['publisher']): ?>
                <div class="product-meta-item">
                    <span class="product-meta-label">Editeur</span>
                    <span class="product-meta-value"><?= sanitize($book['publisher']) ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($book['publication_year']): ?>
                <div class="product-meta-item">
                    <span class="product-meta-label">Annee</span>
                    <span class="product-meta-value"><?= $book['publication_year'] ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($book['pages']): ?>
                <div class="product-meta-item">
                    <span class="product-meta-label">Pages</span>
                    <span class="product-meta-value"><?= $book['pages'] ?></span>
                </div>
                <?php endif; ?>
                
                <div class="product-meta-item">
                    <span class="product-meta-label">Langue</span>
                    <span class="product-meta-value"><?= sanitize($book['language']) ?></span>
                </div>
            </div>
            
            <!-- Ajout au panier -->
            <?php if ($book['stock'] > 0): ?>
                <form method="POST" aria-label="Ajouter au panier">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    
                    <div class="quantity-selector">
                        <label for="quantity">Quantite :</label>
                        <input type="number" 
                               name="quantity" 
                               id="quantity" 
                               value="1" 
                               min="1" 
                               max="<?= $book['stock'] ?>"
                               class="quantity-input"
                               aria-describedby="stock-info">
                        <span id="stock-info" class="form-help">(Max: <?= $book['stock'] ?>)</span>
                    </div>
                    
                    <div class="product-actions">
                        <button type="submit" name="add_to_cart" class="btn btn-primary btn-large">
                            <span aria-hidden="true">🛒</span> Ajouter au panier
                        </button>
                    </div>
                </form>
            <?php endif; ?>
            
            <div style="margin-top: 1.5rem;">
                <a href="products.php" class="btn btn-outline">
                    ← Retour aux livres
                </a>
            </div>
        </div>
    </div>
    
    <!-- Livres similaires -->
    <?php if (!empty($similarBooks)): ?>
    <section class="section" aria-labelledby="similar-title">
        <div class="section-header">
            <h2 id="similar-title" class="section-title">Vous aimerez aussi</h2>
        </div>
        
        <div class="products-grid">
            <?php foreach ($similarBooks as $similar): ?>
                <article class="product-card">
                    <div class="product-card-image">
                        <img src="<?= BASE_URL ?>/assets/img/books/<?= sanitize($similar['image']) ?>" 
                             alt="Couverture de <?= sanitize($similar['title']) ?>"
                             loading="lazy">
                    </div>
                    <div class="product-card-body">
                        <h3><a href="product.php?id=<?= $similar['id'] ?>"><?= sanitize($similar['title']) ?></a></h3>
                        <p class="product-author">Par <?= sanitize($similar['author']) ?></p>
                        <div class="product-card-footer">
                            <span class="product-price"><?= formatPrice($similar['price']) ?></span>
                            <a href="product.php?id=<?= $similar['id'] ?>" class="btn btn-outline btn-small">Voir</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>