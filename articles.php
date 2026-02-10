<?php
/**
 * Page du catalogue des produits
 * Affiche tous les produits disponibles avec filtres et recherche
 */

require_once __DIR__ . '/config/database.php';

// Définir le titre de la page
$pageTitle = 'Nos produits';

// Initialiser les variables de filtrage
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'recent';
$minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 10000;

// Construire la requête SQL avec filtres
$query = "SELECT i.*, COALESCE(s.quantite_stock, 0) AS stock_disponible 
          FROM items i 
          LEFT JOIN stock s ON i.id = s.id_item 
          WHERE i.statut = 'actif' 
          AND i.prix BETWEEN :minPrice AND :maxPrice";

$params = [
    'minPrice' => $minPrice,
    'maxPrice' => $maxPrice
];

// Ajouter le filtre de recherche si présent
if (!empty($search)) {
    $query .= " AND (i.nom LIKE :search OR i.description LIKE :search)";
    $params['search'] = '%' . $search . '%';
}

// Ajouter le tri
switch ($sortBy) {
    case 'price_asc':
        $query .= " ORDER BY i.prix ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY i.prix DESC";
        break;
    case 'name':
        $query .= " ORDER BY i.nom ASC";
        break;
    case 'recent':
    default:
        $query .= " ORDER BY i.date_publication DESC";
        break;
}

// Exécuter la requête
$produits = executeQuery($query, $params);

// Compter le nombre de résultats
$totalProduits = count($produits);

include __DIR__ . '/includes/header.php';
?>

<!-- En-tête de la page -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Nos produits</h1>
        <p class="page-subtitle">Découvrez notre sélection de <?php echo $totalProduits; ?> produit<?php echo $totalProduits > 1 ? 's' : ''; ?></p>
    </div>
</section>

<!-- Section filtres et recherche -->
<section class="filters-section">
    <div class="container">
        <form method="GET" action="articles.php" class="filters-form">
            <div class="filters-row">
                <!-- Barre de recherche -->
                <div class="filter-group filter-search">
                    <label for="search">
                        <i class="fas fa-search"></i>
                    </label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           placeholder="Rechercher un produit..." 
                           value="<?php echo escape($search); ?>">
                </div>
                
                <!-- Tri -->
                <div class="filter-group">
                    <label for="sort">
                        <i class="fas fa-sort"></i> Trier par :
                    </label>
                    <select name="sort" id="sort" onchange="this.form.submit()">
                        <option value="recent" <?php echo $sortBy === 'recent' ? 'selected' : ''; ?>>Plus récents</option>
                        <option value="price_asc" <?php echo $sortBy === 'price_asc' ? 'selected' : ''; ?>>Prix croissant</option>
                        <option value="price_desc" <?php echo $sortBy === 'price_desc' ? 'selected' : ''; ?>>Prix décroissant</option>
                        <option value="name" <?php echo $sortBy === 'name' ? 'selected' : ''; ?>>Nom (A-Z)</option>
                    </select>
                </div>
                
                <!-- Filtre de prix -->
                <div class="filter-group filter-price">
                    <label>
                        <i class="fas fa-euro-sign"></i> Prix :
                    </label>
                    <input type="number" 
                           name="min_price" 
                           placeholder="Min" 
                           value="<?php echo $minPrice > 0 ? $minPrice : ''; ?>" 
                           min="0" 
                           step="10">
                    <span>-</span>
                    <input type="number" 
                           name="max_price" 
                           placeholder="Max" 
                           value="<?php echo $maxPrice < 10000 ? $maxPrice : ''; ?>" 
                           min="0" 
                           step="10">
                </div>
                
                <!-- Bouton filtrer -->
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                
                <!-- Réinitialiser les filtres -->
                <?php if (!empty($search) || $sortBy !== 'recent' || $minPrice > 0 || $maxPrice < 10000): ?>
                    <a href="articles.php" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>

<!-- Section résultats -->
<section class="products-section">
    <div class="container">
        <?php if (!empty($produits)): ?>
            <div class="products-grid">
                <?php foreach ($produits as $produit): ?>
                    <div class="product-card">
                        <!-- Image du produit -->
                        <div class="product-image">
                            <a href="/ecommerce-project/produit.php?id=<?php echo $produit['id']; ?>">
                                <img src="/ecommerce-project/uploads/<?php echo escape($produit['image']); ?>" 
                                     alt="<?php echo escape($produit['nom']); ?>"
                                     onerror="this.src='/ecommerce-project/uploads/default.jpg'">
                            </a>
                            
                            <!-- Badge stock -->
                            <?php if ($produit['stock_disponible'] > 0 && $produit['stock_disponible'] <= 5): ?>
                                <span class="product-badge badge-stock">Plus que <?php echo $produit['stock_disponible']; ?> !</span>
                            <?php elseif ($produit['stock_disponible'] == 0): ?>
                                <span class="product-badge badge-out">Rupture de stock</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Informations du produit -->
                        <div class="product-info">
                            <h3 class="product-name">
                                <a href="/ecommerce-project/produit.php?id=<?php echo $produit['id']; ?>">
                                    <?php echo escape($produit['nom']); ?>
                                </a>
                            </h3>
                            
                            <p class="product-description">
                                <?php 
                                $description = $produit['description'];
                                echo escape(strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description); 
                                ?>
                            </p>
                            
                            <!-- Prix et stock -->
                            <div class="product-meta">
                                <span class="product-price"><?php echo formatPrice($produit['prix']); ?></span>
                                
                                <?php if ($produit['stock_disponible'] > 5): ?>
                                    <span class="stock-status stock-available">
                                        <i class="fas fa-check-circle"></i> En stock
                                    </span>
                                <?php elseif ($produit['stock_disponible'] > 0): ?>
                                    <span class="stock-status stock-low">
                                        <i class="fas fa-exclamation-circle"></i> Stock limité
                                    </span>
                                <?php else: ?>
                                    <span class="stock-status stock-out">
                                        <i class="fas fa-times-circle"></i> Rupture
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Boutons d'action -->
                            <div class="product-actions">
                                <a href="/ecommerce-project/produit.php?id=<?php echo $produit['id']; ?>" 
                                   class="btn btn-primary btn-block">
                                    <i class="fas fa-eye"></i> Voir les détails
                                </a>
                                
                                <?php if ($produit['stock_disponible'] > 0): ?>
                                    <form method="POST" action="/ecommerce-project/panier.php" style="margin-top: 10px;">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="product_id" value="<?php echo $produit['id']; ?>">
                                        <button type="submit" class="btn btn-secondary btn-block">
                                            <i class="fas fa-shopping-cart"></i> Ajouter au panier
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-disabled btn-block" disabled>
                                        <i class="fas fa-ban"></i> Indisponible
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Aucun résultat -->
            <div class="no-results">
                <i class="fas fa-search fa-3x"></i>
                <h3>Aucun produit trouvé</h3>
                <p>Essayez de modifier vos critères de recherche ou de réinitialiser les filtres.</p>
                <a href="articles.php" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Réinitialiser les filtres
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
