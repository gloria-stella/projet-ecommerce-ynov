<?php
$pageTitle = 'Nos Livres';
include '../includes/header.php';

// Parametres de filtrage
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : '';
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : '';

// Construction de la requete
$sql = "SELECT i.*, s.quantity as stock FROM items i LEFT JOIN stock s ON i.id = s.item_id WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (i.title LIKE ? OR i.author LIKE ? OR i.description LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if ($category) {
    $sql .= " AND i.category = ?";
    $params[] = $category;
}

if ($minPrice !== '') {
    $sql .= " AND i.price >= ?";
    $params[] = $minPrice;
}

if ($maxPrice !== '') {
    $sql .= " AND i.price <= ?";
    $params[] = $maxPrice;
}

// Tri
switch ($sort) {
    case 'title_asc':
        $sql .= " ORDER BY i.title ASC";
        break;
    case 'title_desc':
        $sql .= " ORDER BY i.title DESC";
        break;
    case 'price_asc':
        $sql .= " ORDER BY i.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY i.price DESC";
        break;
    case 'author':
        $sql .= " ORDER BY i.author ASC, i.title ASC";
        break;
    case 'newest':
    default:
        $sql .= " ORDER BY i.created_at DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

// Recuperer les categories pour le filtre
$categoriesStmt = $pdo->query("SELECT DISTINCT category FROM items WHERE category IS NOT NULL ORDER BY category");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container">
    <div class="section-header">
        <h1 class="section-title">Notre Collection</h1>
        <p class="section-subtitle">
            <?= count($books) ?> livre<?= count($books) > 1 ? 's' : '' ?> a decouvrir
            <?= $category ? ' dans la categorie "' . sanitize($category) . '"' : '' ?>
            <?= $search ? ' pour "' . sanitize($search) . '"' : '' ?>
        </p>
    </div>
    
    <!-- Filtres -->
    <section class="filters-section" aria-label="Filtres de recherche">
        <form class="filters-form" method="GET" role="search">
            <div class="filter-group" style="flex: 2;">
                <label for="search">Rechercher</label>
                <input type="search" 
                       name="search" 
                       id="search" 
                       value="<?= sanitize($search) ?>" 
                       placeholder="Titre, auteur, mot-cle..."
                       aria-label="Rechercher un livre">
            </div>
            
            <div class="filter-group">
                <label for="category">Categorie</label>
                <select name="category" id="category" aria-label="Filtrer par categorie">
                    <option value="">Toutes les categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= sanitize($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                            <?= sanitize($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="sort">Trier par</label>
                <select name="sort" id="sort" aria-label="Trier les resultats">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Plus recents</option>
                    <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>Titre A-Z</option>
                    <option value="title_desc" <?= $sort === 'title_desc' ? 'selected' : '' ?>>Titre Z-A</option>
                    <option value="author" <?= $sort === 'author' ? 'selected' : '' ?>>Auteur A-Z</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Prix croissant</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Prix decroissant</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Filtrer</button>
            
            <?php if ($search || $category || $sort !== 'newest'): ?>
                <a href="products.php" class="btn btn-outline">Reinitialiser</a>
            <?php endif; ?>
        </form>
    </section>
    
    <!-- Resultats -->
    <?php if (empty($books)): ?>
        <div class="empty-state">
            <div class="empty-state-icon" aria-hidden="true">📚</div>
            <h2>Aucun livre trouve</h2>
            <p>Essayez de modifier vos criteres de recherche ou explorez notre catalogue complet.</p>
            <a href="products.php" class="btn btn-primary">Voir tous les livres</a>
        </div>
    <?php else: ?>
        <div class="products-grid" role="list" aria-label="Liste des livres">
            <?php foreach ($books as $book): ?>
                <article class="product-card" role="listitem">
                    <div class="product-card-image">
                        <?php if ($book['is_featured']): ?>
                            <span class="product-badge">Coup de coeur</span>
                        <?php endif; ?>
                        <img src="<?= getBookImage($book['image']) ?>" 
                             alt="Couverture de <?= sanitize($book['title']) ?>"
                             loading="lazy">
                    </div>
                    <div class="product-card-body">
                        <span class="product-category"><?= sanitize($book['category']) ?></span>
                        <h3><a href="product.php?id=<?= $book['id'] ?>"><?= sanitize($book['title']) ?></a></h3>
                        <p class="product-author">Par <?= sanitize($book['author']) ?></p>
                        <div class="product-card-footer">
                            <span class="product-price"><?= formatPrice($book['price']) ?></span>
                            <a href="product.php?id=<?= $book['id'] ?>" class="btn btn-primary btn-small">
                                Voir le livre
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
