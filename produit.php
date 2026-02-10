<?php
/**
 * Page de détails d'un produit
 * Affiche toutes les informations d'un produit spécifique
 */

require_once __DIR__ . '/config/database.php';

// Vérifier si un ID est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: /ecommerce-project/articles.php');
    exit;
}

$productId = intval($_GET['id']);

// Récupérer les informations du produit
$query = "SELECT i.*, COALESCE(s.quantite_stock, 0) AS stock_disponible 
          FROM items i 
          LEFT JOIN stock s ON i.id = s.id_item 
          WHERE i.id = :id AND i.statut = 'actif'";
$produit = executeQuery($query, ['id' => $productId]);

// Vérifier si le produit existe
if (empty($produit)) {
    header('Location: /ecommerce-project/articles.php');
    exit;
}

$produit = $produit[0];

// Définir le titre de la page
$pageTitle = $produit['nom'];

// Récupérer des produits similaires (même gamme de prix)
$prixMin = $produit['prix'] * 0.7;
$prixMax = $produit['prix'] * 1.3;
$query = "SELECT i.*, COALESCE(s.quantite_stock, 0) AS stock_disponible 
          FROM items i 
          LEFT JOIN stock s ON i.id = s.id_item 
          WHERE i.statut = 'actif' 
          AND i.id != :id 
          AND i.prix BETWEEN :prixMin AND :prixMax 
          ORDER BY RAND() 
          LIMIT 3";
$produitsSimilaires = executeQuery($query, [
    'id' => $productId,
    'prixMin' => $prixMin,
    'prixMax' => $prixMax
]);

// Gérer l'ajout au panier
$message = '';
$messageType = '';
if (isset($_SESSION['cart_message'])) {
    $message = $_SESSION['cart_message'];
    $messageType = $_SESSION['cart_message_type'];
    unset($_SESSION['cart_message'], $_SESSION['cart_message_type']);
}

include __DIR__ . '/includes/header.php';
?>

<!-- Fil d'Ariane (Breadcrumb) -->
<nav class="breadcrumb">
    <div class="container">
        <a href="/ecommerce-project/index.php"><i class="fas fa-home"></i> Accueil</a>
        <span class="separator">/</span>
        <a href="/ecommerce-project/articles.php">Produits</a>
        <span class="separator">/</span>
        <span class="current"><?php echo escape($produit['nom']); ?></span>
    </div>
</nav>

<!-- Message de confirmation -->
<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <div class="container">
            <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo escape($message); ?>
        </div>
    </div>
<?php endif; ?>

<!-- Section détails du produit -->
<section class="product-detail-section">
    <div class="container">
        <div class="product-detail-grid">
            <!-- Galerie d'images -->
            <div class="product-gallery">
                <div class="main-image">
                    <img id="mainProductImage" 
                         src="/ecommerce-project/uploads/<?php echo escape($produit['image']); ?>" 
                         alt="<?php echo escape($produit['nom']); ?>"
                         onerror="this.src='/ecommerce-project/uploads/default.jpg'">
                    
                    <!-- Badge stock -->
                    <?php if ($produit['stock_disponible'] > 0 && $produit['stock_disponible'] <= 5): ?>
                        <span class="product-badge badge-stock">Plus que <?php echo $produit['stock_disponible']; ?> en stock !</span>
                    <?php elseif ($produit['stock_disponible'] == 0): ?>
                        <span class="product-badge badge-out">Rupture de stock</span>
                    <?php endif; ?>
                </div>
                
                <!-- Miniatures (pour l'instant, on affiche juste l'image principale) -->
                <div class="thumbnail-gallery">
                    <img src="/ecommerce-project/uploads/<?php echo escape($produit['image']); ?>" 
                         alt="<?php echo escape($produit['nom']); ?>"
                         class="thumbnail active"
                         onerror="this.src='/ecommerce-project/uploads/default.jpg'">
                </div>
            </div>
            
            <!-- Informations du produit -->
            <div class="product-details">
                <h1 class="product-title"><?php echo escape($produit['nom']); ?></h1>
                
                <!-- Prix -->
                <div class="product-price-section">
                    <span class="product-price-large"><?php echo formatPrice($produit['prix']); ?></span>
                    <span class="product-tax">TTC</span>
                </div>
                
                <!-- Statut du stock -->
                <div class="product-stock">
                    <?php if ($produit['stock_disponible'] > 5): ?>
                        <span class="stock-badge stock-available">
                            <i class="fas fa-check-circle"></i> En stock (<?php echo $produit['stock_disponible']; ?> disponibles)
                        </span>
                    <?php elseif ($produit['stock_disponible'] > 0): ?>
                        <span class="stock-badge stock-low">
                            <i class="fas fa-exclamation-circle"></i> Stock limité (<?php echo $produit['stock_disponible']; ?> restants)
                        </span>
                    <?php else: ?>
                        <span class="stock-badge stock-out">
                            <i class="fas fa-times-circle"></i> Rupture de stock
                        </span>
                    <?php endif; ?>
                </div>
                
                <!-- Description -->
                <div class="product-description">
                    <h3>Description</h3>
                    <p><?php echo nl2br(escape($produit['description'])); ?></p>
                </div>
                
                <!-- Caractéristiques -->
                <div class="product-features">
                    <h3>Caractéristiques</h3>
                    <ul>
                        <li><i class="fas fa-check"></i> Produit authentique</li>
                        <li><i class="fas fa-check"></i> Garantie constructeur</li>
                        <li><i class="fas fa-check"></i> Livraison gratuite dès 50€</li>
                        <li><i class="fas fa-check"></i> Retour sous 30 jours</li>
                    </ul>
                </div>
                
                <!-- Formulaire d'ajout au panier -->
                <?php if ($produit['stock_disponible'] > 0): ?>
                    <form method="POST" action="/ecommerce-project/panier.php" class="add-to-cart-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $produit['id']; ?>">
                        
                        <div class="quantity-selector">
                            <label for="quantity">Quantité :</label>
                            <div class="quantity-controls">
                                <button type="button" class="qty-btn" onclick="decreaseQty()">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" 
                                       id="quantity" 
                                       name="quantity" 
                                       value="1" 
                                       min="1" 
                                       max="<?php echo $produit['stock_disponible']; ?>" 
                                       readonly>
                                <button type="button" class="qty-btn" onclick="increaseQty(<?php echo $produit['stock_disponible']; ?>)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-shopping-cart"></i> Ajouter au panier
                        </button>
                    </form>
                <?php else: ?>
                    <div class="out-of-stock-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Ce produit est actuellement en rupture de stock.</p>
                        <button class="btn btn-secondary btn-block" disabled>
                            <i class="fas fa-ban"></i> Indisponible
                        </button>
                    </div>
                <?php endif; ?>
                
                <!-- Informations supplémentaires -->
                <div class="product-extra-info">
                    <div class="info-item">
                        <i class="fas fa-truck"></i>
                        <div>
                            <strong>Livraison rapide</strong>
                            <p>Expédition sous 24h</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-shield-alt"></i>
                        <div>
                            <strong>Paiement sécurisé</strong>
                            <p>Transactions 100% sécurisées</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-undo"></i>
                        <div>
                            <strong>Retour gratuit</strong>
                            <p>Sous 30 jours</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section produits similaires -->
<?php if (!empty($produitsSimilaires)): ?>
<section class="similar-products-section">
    <div class="container">
        <h2 class="section-title">Produits similaires</h2>
        
        <div class="products-grid products-grid-3">
            <?php foreach ($produitsSimilaires as $similaire): ?>
                <div class="product-card">
                    <div class="product-image">
                        <a href="/ecommerce-project/produit.php?id=<?php echo $similaire['id']; ?>">
                            <img src="/ecommerce-project/uploads/<?php echo escape($similaire['image']); ?>" 
                                 alt="<?php echo escape($similaire['nom']); ?>"
                                 onerror="this.src='/ecommerce-project/uploads/default.jpg'">
                        </a>
                    </div>
                    
                    <div class="product-info">
                        <h3 class="product-name">
                            <a href="/ecommerce-project/produit.php?id=<?php echo $similaire['id']; ?>">
                                <?php echo escape($similaire['nom']); ?>
                            </a>
                        </h3>
                        
                        <p class="product-description">
                            <?php echo escape(substr($similaire['description'], 0, 80)) . '...'; ?>
                        </p>
                        
                        <div class="product-footer">
                            <span class="product-price"><?php echo formatPrice($similaire['prix']); ?></span>
                            
                            <a href="/ecommerce-project/produit.php?id=<?php echo $similaire['id']; ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
// Fonctions pour gérer la quantité
function increaseQty(max) {
    const qtyInput = document.getElementById('quantity');
    let currentQty = parseInt(qtyInput.value);
    if (currentQty < max) {
        qtyInput.value = currentQty + 1;
    }
}

function decreaseQty() {
    const qtyInput = document.getElementById('quantity');
    let currentQty = parseInt(qtyInput.value);
    if (currentQty > 1) {
        qtyInput.value = currentQty - 1;
    }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
