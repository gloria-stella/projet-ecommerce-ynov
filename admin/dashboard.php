<?php
require_once '../config/database.php';

if (!isAdmin()) {
    redirect('login.php');
}

// Statistiques
$totalBooks = $pdo->query("SELECT COUNT(*) FROM items")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'client'")->fetchColumn();
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM invoice WHERE payment_status != 'refunded'")->fetchColumn();

// Commandes recentes
$recentOrders = $pdo->query("
    SELECT o.*, u.first_name, u.last_name, i.total
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN invoice i ON o.id = i.order_id
    ORDER BY o.created_at DESC
    LIMIT 5
")->fetchAll();

// Livres a faible stock
$lowStock = $pdo->query("
    SELECT i.title, s.quantity
    FROM items i
    JOIN stock s ON i.id = s.item_id
    WHERE s.quantity <= s.low_stock_alert
    ORDER BY s.quantity ASC
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Youlla Books</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+3:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <h1>📚 Youlla Books - Administration</h1>
            <nav class="admin-nav">
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="products.php">Livres</a>
                <a href="users.php">Utilisateurs</a>
                <a href="<?= BASE_URL ?>/front/index.php">Voir le site</a>
                <a href="<?= BASE_URL ?>/front/logout.php">Deconnexion</a>
            </nav>
        </div>
    </header>
    
    <div class="admin-content">
        <div class="container">
            <h2 style="margin-bottom: 2rem;">Tableau de bord</h2>
            
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Livres</h3>
                    <div class="stat-value"><?= $totalBooks ?></div>
                </div>
                <div class="stat-card">
                    <h3>Commandes</h3>
                    <div class="stat-value"><?= $totalOrders ?></div>
                </div>
                <div class="stat-card">
                    <h3>Clients</h3>
                    <div class="stat-value"><?= $totalUsers ?></div>
                </div>
                <div class="stat-card">
                    <h3>Chiffre d'affaires</h3>
                    <div class="stat-value"><?= formatPrice($totalRevenue) ?></div>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-top: 2rem;">
                <!-- Commandes recentes -->
                <div>
                    <h3 style="margin-bottom: 1rem;">Dernieres commandes</h3>
                    <?php if (empty($recentOrders)): ?>
                        <p>Aucune commande pour le moment.</p>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td><?= $order['id'] ?></td>
                                        <td><?= sanitize($order['first_name'] . ' ' . $order['last_name']) ?></td>
                                        <td><?= formatPrice($order['total'] ?? 0) ?></td>
                                        <td>
                                            <span style="padding: 4px 8px; background: var(--secondary); border-radius: 4px; font-size: 0.85rem;">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                
                <!-- Alertes stock -->
                <div>
                    <h3 style="margin-bottom: 1rem;">Alertes stock faible</h3>
                    <?php if (empty($lowStock)): ?>
                        <p style="color: var(--success);">Tous les stocks sont bons !</p>
                    <?php else: ?>
                        <div style="background: #FFEBEE; padding: 1rem; border-radius: var(--radius);">
                            <?php foreach ($lowStock as $item): ?>
                                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                    <span><?= sanitize($item['title']) ?></span>
                                    <strong style="color: var(--error);"><?= $item['quantity'] ?></strong>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>