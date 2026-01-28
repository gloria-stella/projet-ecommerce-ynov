<?php
require_once '../config/database.php';

if (!isAdmin()) {
    redirect('login.php');
}

// Supprimer un utilisateur
if (isset($_GET['delete']) && isset($_GET['csrf'])) {
    if (verifyCSRFToken($_GET['csrf'])) {
        $id = (int)$_GET['delete'];
        // Ne pas supprimer les admins
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'client'");
        $stmt->execute([$id]);
        setFlashMessage('success', 'Utilisateur supprime.');
    }
    redirect('users.php');
}

$users = $pdo->query("
    SELECT u.*, 
           (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
           (SELECT COALESCE(SUM(total), 0) FROM invoice WHERE user_id = u.id) as total_spent
    FROM users u
    ORDER BY u.created_at DESC
")->fetchAll();

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+3:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <h1>📚 Youlla Books - Administration</h1>
            <nav class="admin-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="products.php">Livres</a>
                <a href="users.php" class="active">Utilisateurs</a>
                <a href="<?= BASE_URL ?>/front/index.php">Voir le site</a>
                <a href="<?= BASE_URL ?>/front/logout.php">Deconnexion</a>
            </nav>
        </div>
    </header>
    
    <div class="admin-content">
        <div class="container">
            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>"><?= sanitize($flash['message']) ?></div>
            <?php endif; ?>
            
            <h2 style="margin-bottom: 2rem;">Gestion des utilisateurs</h2>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Commandes</th>
                        <th>Total depense</th>
                        <th>Inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><strong><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></strong></td>
                            <td><?= sanitize($user['email']) ?></td>
                            <td>
                                <span style="padding: 4px 10px; background: <?= $user['role'] === 'admin' ? 'var(--primary)' : 'var(--secondary)' ?>; color: <?= $user['role'] === 'admin' ? 'white' : 'var(--text-dark)' ?>; border-radius: 20px; font-size: 0.8rem;">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td><?= $user['order_count'] ?></td>
                            <td><?= formatPrice($user['total_spent']) ?></td>
                            <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <?php if ($user['role'] !== 'admin'): ?>
                                    <a href="users.php?delete=<?= $user['id'] ?>&csrf=<?= generateCSRFToken() ?>" 
                                       class="btn btn-danger btn-small"
                                       data-confirm="Supprimer cet utilisateur ? Ses commandes seront aussi supprimees.">
                                        Supprimer
                                    </a>
                                <?php else: ?>
                                    <span style="color: var(--text-light); font-size: 0.85rem;">Admin protege</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>