<?php
/**
 * Page d'accueil du site e-commerce
 * Affiche une bannière, des produits en avant et une description du site
 */

require_once __DIR__ . '/config/database.php';

// Définir le titre de la page
$pageTitle = 'Accueil';

// Récupérer les 6 produits les plus récents pour la section "Nouveautés"
$query = "SELECT i.*, COALESCE(s.quantite_stock, 0) AS stock_disponible 
          FROM items i 
          LEFT JOIN stock s ON i.id = s.id_item 
          WHERE i.statut = 'actif' 
          ORDER BY i.date_publication DESC 
          LIMIT 6";
$produitsNouveautes = executeQuery($query);

// Récupérer 3 produits aléatoires pour la section "Nos coups de cœur"
$query = "SELECT i.*, COALESCE(s.quantite_stock, 0) AS stock_disponible 
          FROM items i 
          LEFT JOIN stock s ON i.id = s.id_item 
          WHERE i.statut = 'actif' 
          ORDER BY RAND() 
          LIMIT 3";
$produitsCoups = executeQuery($query);

include __DIR__ . '/includes/header.php';
?>

<!-- Section Hero/Bannière -->
<section class="hero-section">
    <div class="hero-overlay">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Bienvenue chez TechStore</h1>
                <p class="hero-subtitle">Découvrez notre sélection de produits Apple et accessoires high-tech</p>
                <div class="hero-buttons">
                    <a href="/ecommerce-project/articles.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag"></i> Découvrir nos produits
                    </a>
                    <a href="/ecommerce-project/qui-sommes-nous.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-info-circle"></i> En savoir plus
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Avantages -->
<section class="features-section">
    <div class="container">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h3>Livraison rapide</h3>
                <p>Livraison gratuite dès 50€ d'achat</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Paiement sécurisé</h3>
                <p>Vos transactions sont 100% sécurisées</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Support 7j/7</h3>
                <p>Notre équipe à votre écoute</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-undo"></i>
                </div>
                <h3>Retour gratuit</h3>
                <p>30 jours pour changer d'avis</p>
            </div>
        </div>
    </div>
</section>

<!-- Section Nouveautés -->
<section class="products-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Nos nouveautés</h2>
            <p class="section-subtitle">Découvrez nos derniers produits</p>
        </div>
        
        <div class="products-grid">
            <?php if (!empty($produitsNouveautes)): ?>
                <?php foreach ($produitsNouveautes as $produit): ?>
                    <div class="product-card">
                        <!-- Badge nouveauté -->
                        <span class="product-badge badge-new">Nouveau</span>
                        
                        <!-- Image du produit -->
                        <div class="product-image">
                            <a href="/ecommerce-project/produit.php?id=<?php echo $produit['id']; ?>">
                                <img src="/ecommerce-project/uploads/<?php echo escape($produit['image']); ?>" 
                                     alt="<?php echo escape($produit['nom']); ?>"
                                     onerror="this.src='/ecommerce-project/uploads/default.jpg'">
                            </a>
                            
                            <!-- Badge stock faible -->
                            <?php if ($produit['stock_disponible'] > 0 && $produit['stock_disponible'] <= 5): ?>
                                <span class="product-badge badge-stock">Plus que <?php echo $produit['stock_disponible']; ?> !</span>
                            <?php elseif ($produit['stock_disponible'] == 0): ?>
                                <span class="product-badge badge-out">Rupture</span>
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
                                <?php echo escape(substr($produit['description'], 0, 80)) . '...'; ?>
                            </p>
                            
                            <div class="product-footer">
                                <span class="product-price"><?php echo formatPrice($produit['prix']); ?></span>
                                
                                <a href="/ecommerce-project/produit.php?id=<?php echo $produit['id']; ?>" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-products">Aucun produit disponible pour le moment.</p>
            <?php endif; ?>
        </div>
        
        <div class="section-footer">
            <a href="/ecommerce-project/articles.php" class="btn btn-outline">
                Voir tous les produits <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Section Coups de cœur -->
<section class="products-section section-alt">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Nos coups de cœur</h2>
            <p class="section-subtitle">Sélection de nos experts</p>
        </div>
        
        <div class="products-grid products-grid-3">
            <?php if (!empty($produitsCoups)): ?>
                <?php foreach ($produitsCoups as $produit): ?>
                    <div class="product-card product-card-featured">
                        <span class="product-badge badge-favorite">
                            <i class="fas fa-heart"></i> Coup de cœur
                        </span>
                        
                        <div class="product-image">
                            <a href="/ecommerce-project/produit.php?id=<?php echo $produit['id']; ?>">
                                <img src="/ecommerce-project/uploads/<?php echo escape($produit['image']); ?>" 
                                     alt="<?php echo escape($produit['nom']); ?>"
                                     onerror="this.src='/ecommerce-project/uploads/default.jpg'">
                            </a>
                        </div>
                        
                        <div class="product-info">
                            <h3 class="product-name">
                                <a href="/ecommerce-project/produit.php?id=<?php echo $produit['id']; ?>">
                                    <?php echo escape($produit['nom']); ?>
                                </a>
                            </h3>
                            
                            <p class="product-description">
                                <?php echo escape(substr($produit['description'], 0, 100)) . '...'; ?>
                            </p>
                            
                            <div class="product-footer">
                                <span class="product-price"><?php echo formatPrice($produit['prix']); ?></span>
                                
                                <a href="/ecommerce-project/produit.php?id=<?php echo $produit['id']; ?>" 
                                   class="btn btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Découvrir
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Section CTA (Call to Action) -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Prêt à faire vos achats ?</h2>
            <p>Inscrivez-vous maintenant et profitez d'offres exclusives !</p>
            <a href="/ecommerce-project/register.php" class="btn btn-light btn-lg">
                <i class="fas fa-user-plus"></i> Créer un compte
            </a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
