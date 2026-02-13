<?php
$pageTitle = 'Mon Panier';
include '../includes/header.php';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifier CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Erreur de securite.');
        redirect('cart.php');
    }
    
    // Mise a jour quantites
    if (isset($_POST['update_cart']) && isset($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $itemId => $qty) {
            $qty = max(0, (int)$qty);
            if ($qty > 0) {
                $_SESSION['cart'][(int)$itemId] = $qty;
            } else {
                unset($_SESSION['cart'][(int)$itemId]);
            }
        }
        setFlashMessage('success', 'Panier mis a jour.');
        redirect('cart.php');
    }
    
    // Supprimer un article
    if (isset($_POST['remove_item'])) {
        $itemId = (int)$_POST['remove_item'];
        unset($_SESSION['cart'][$itemId]);
        setFlashMessage('success', 'Article supprime du panier.');
        redirect('cart.php');
    }
    
    // Vider le panier
    if (isset($_POST['clear_cart'])) {
        unset($_SESSION['cart']);
        setFlashMessage('success', 'Panier vide.');
        redirect('cart.php');
    }
    
    // Passer commande
    if (isset($_POST['checkout'])) {
        if (!isLoggedIn()) {
            setFlashMessage('warning', 'Veuillez vous connecter pour passer commande.');
            redirect('login.php?redirect=cart.php');
        }
        
        $address = sanitize($_POST['shipping_address'] ?? '');
        $city = sanitize($_POST['shipping_city'] ?? '');
        $postalCode = sanitize($_POST['shipping_postal_code'] ?? '');
        
        if (empty($address) || empty($city) || empty($postalCode)) {
            setFlashMessage('error', 'Veuillez remplir tous les champs de livraison.');
        } else {
            try {
                $pdo->beginTransaction();
                
                // Calculer le total
                $subtotal = 0;
                $cartItems = [];
                
                foreach ($_SESSION['cart'] as $itemId => $quantity) {
                    $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
                    $stmt->execute([$itemId]);
                    $item = $stmt->fetch();
                    
                    if ($item) {
                        $cartItems[] = ['item' => $item, 'quantity' => $quantity];
                        $subtotal += $item['price'] * $quantity;
                    }
                }
                
                // Frais de livraison
                $shipping = $subtotal >= 35 ? 0 : 4.90;
                $total = $subtotal + $shipping;
                
                // Creer la commande
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, status) VALUES (?, 'pending')");
                $stmt->execute([$_SESSION['user_id']]);
                $orderId = $pdo->lastInsertId();
                
                // Ajouter les articles
                foreach ($cartItems as $cartItem) {
                    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, item_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        $orderId,
                        $cartItem['item']['id'],
                        $cartItem['quantity'],
                        $cartItem['item']['price']
                    ]);
                    
                    // Mettre a jour le stock
                    $stmt = $pdo->prepare("UPDATE stock SET quantity = quantity - ? WHERE item_id = ?");
                    $stmt->execute([$cartItem['quantity'], $cartItem['item']['id']]);
                }
                
                // Creer la facture
                $invoiceNumber = 'YB-' . date('Ymd') . '-' . str_pad($orderId, 5, '0', STR_PAD_LEFT);
                $stmt = $pdo->prepare("
                    INSERT INTO invoice (order_id, user_id, invoice_number, subtotal, shipping_cost, total, billing_address, billing_city, billing_postal_code, shipping_address, shipping_city, shipping_postal_code, payment_status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
                ");
                $stmt->execute([
                    $orderId,
                    $_SESSION['user_id'],
                    $invoiceNumber,
                    $subtotal,
                    $shipping,
                    $total,
                    $address,
                    $city,
                    $postalCode,
                    $address,
                    $city,
                    $postalCode
                ]);
                
                $pdo->commit();
                
                unset($_SESSION['cart']);
                
                setFlashMessage('success', 'Commande #' . $orderId . ' passee avec succes ! Numero de facture : ' . $invoiceNumber);
                redirect('cart.php');
                
            } catch (Exception $e) {
                $pdo->rollBack();
                setFlashMessage('error', 'Erreur lors de la commande. Veuillez reessayer.');
            }
        }
    }
}

// Recuperer les articles du panier
$cartItems = [];
$subtotal = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $itemId => $quantity) {
        $stmt = $pdo->prepare("SELECT i.*, s.quantity as stock FROM items i LEFT JOIN stock s ON i.id = s.item_id WHERE i.id = ?");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch();
        
        if ($item) {
            $cartItems[] = ['item' => $item, 'quantity' => $quantity];
            $subtotal += $item['price'] * $quantity;
        }
    }
}

$shipping = $subtotal >= 35 ? 0 : 4.90;
$total = $subtotal + $shipping;
?>

<div class="container">
    <h1 class="section-title" style="text-align: left; margin-bottom: 2rem;">Mon Panier</h1>
    
    <?php if (empty($cartItems)): ?>
        <div class="empty-state">
            <div class="empty-state-icon" aria-hidden="true">🛒</div>
            <h2>Votre panier est vide</h2>
            <p>Decouvrez notre collection de livres et ajoutez vos favoris a votre panier.</p>
            <a href="products.php" class="btn btn-primary btn-large">Parcourir les livres</a>
        </div>
    <?php else: ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="cart-container">
                <div class="cart-header">
                    <h2><?= count($cartItems) ?> article<?= count($cartItems) > 1 ? 's' : '' ?> dans votre panier</h2>
                </div>
                
                <div class="cart-items">
                    <?php foreach ($cartItems as $cartItem): ?>
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <img src="<?= getBookImage($cartItem['item']['image']) ?>" 
                                     alt="<?= sanitize($cartItem['item']['title']) ?>">
                            </div>
                            
                            <div class="cart-item-info">
                                <h4>
                                    <a href="product.php?id=<?= $cartItem['item']['id'] ?>">
                                        <?= sanitize($cartItem['item']['title']) ?>
                                    </a>
                                </h4>
                                <p class="author"><?= sanitize($cartItem['item']['author']) ?></p>
                                <p style="color: var(--primary);"><?= formatPrice($cartItem['item']['price']) ?> / unite</p>
                            </div>
                            
                            <div class="cart-item-quantity">
                                <label for="qty-<?= $cartItem['item']['id'] ?>" class="sr-only">Quantite</label>
                                <input type="number" 
                                       name="quantities[<?= $cartItem['item']['id'] ?>]" 
                                       id="qty-<?= $cartItem['item']['id'] ?>"
                                       value="<?= $cartItem['quantity'] ?>" 
                                       min="0" 
                                       max="<?= $cartItem['item']['stock'] ?>"
                                       aria-label="Quantite pour <?= sanitize($cartItem['item']['title']) ?>">
                            </div>
                            
                            <div class="cart-item-price">
                                <?= formatPrice($cartItem['item']['price'] * $cartItem['quantity']) ?>
                            </div>
                            
                            <button type="submit" 
                                    name="remove_item" 
                                    value="<?= $cartItem['item']['id'] ?>" 
                                    class="btn btn-danger btn-small"
                                    aria-label="Supprimer <?= sanitize($cartItem['item']['title']) ?>">
                                Supprimer
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <div class="cart-summary-row">
                        <span>Sous-total</span>
                        <span><?= formatPrice($subtotal) ?></span>
                    </div>
                    <div class="cart-summary-row">
                        <span>Livraison</span>
                        <span>
                            <?php if ($shipping > 0): ?>
                                <?= formatPrice($shipping) ?>
                                <small style="display: block; color: var(--text-light);">Gratuite des 35 EUR</small>
                            <?php else: ?>
                                <span style="color: var(--success);">Gratuite</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="cart-summary-row cart-total">
                        <span>Total</span>
                        <span><?= formatPrice($total) ?></span>
                    </div>
                    
                    <div class="cart-actions">
                        <button type="submit" name="update_cart" class="btn btn-outline">
                            Mettre a jour
                        </button>
                        <button type="submit" name="clear_cart" class="btn btn-secondary">
                            Vider le panier
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Formulaire de livraison -->
            <div class="form-container" style="max-width: 100%; margin-top: 2rem;">
                <h2 style="margin-bottom: 1.5rem;">Finaliser la commande</h2>
                
                <?php if (!isLoggedIn()): ?>
                    <div class="alert alert-warning" style="margin-bottom: 1.5rem;">
                        <p>Veuillez <a href="login.php?redirect=cart.php"><strong>vous connecter</strong></a> ou <a href="register.php?redirect=cart.php"><strong>creer un compte</strong></a> pour passer commande.</p>
                    </div>
                <?php else: ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="shipping_address">Adresse de livraison <span class="required">*</span></label>
                            <input type="text" name="shipping_address" id="shipping_address" required 
                                   placeholder="123 rue des Livres">
                        </div>
                        <div class="form-group">
                            <label for="shipping_city">Ville <span class="required">*</span></label>
                            <input type="text" name="shipping_city" id="shipping_city" required 
                                   placeholder="Paris">
                        </div>
                    </div>
                    <div class="form-group" style="max-width: 200px;">
                        <label for="shipping_postal_code">Code postal <span class="required">*</span></label>
                        <input type="text" name="shipping_postal_code" id="shipping_postal_code" required 
                               placeholder="75001" pattern="[0-9]{5}">
                    </div>
                    
                    <button type="submit" name="checkout" class="btn btn-primary btn-large btn-block">
                        Passer la commande (<?= formatPrice($total) ?>)
                    </button>
                <?php endif; ?>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
