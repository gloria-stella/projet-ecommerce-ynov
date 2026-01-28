<?php
require_once '../config/database.php';

if (!isAdmin()) {
    redirect('login.php');
}

// Supprimer un livre
if (isset($_GET['delete']) && isset($_GET['csrf'])) {
    if (verifyCSRFToken($_GET['csrf'])) {
        $id = (int)$_GET['delete'];
        $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
        $stmt->execute([$id]);
        setFlashMessage('success', 'Livre supprime avec succes.');
    }
    redirect('products.php');
}

$books = $pdo->query("
    SELECT i.*, s.quantity as stock 
    FROM items i 
    LEFT JOIN stock s ON i.id = s.item_id 
    ORDER BY i.created_at DESC
")->fetchAll();

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des livres - Admin</title>
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
                <a href="products.php" class="active">Livres</a>
                <a href="users.php">Utilisateurs</a>
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
            
            <div class="admin-title">
                <h2>Gestion des livres</h2>
                <a href="products_add.php" class="btn btn-primary">+ Ajouter un livre</a>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Categorie</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td>
                                <img src="<?= BASE_URL ?>/assets/img/books/<?= sanitize($book['image']) ?>" alt="">
                            </td>
                            <td><strong><?= sanitize($book['title']) ?></strong></td>
                            <td><?= sanitize($book['author']) ?></td>
                            <td><?= sanitize($book['category']) ?></td>
                            <td><?= formatPrice($book['price']) ?></td>
                            <td>
                                <span style="color: <?= ($book['stock'] ?? 0) <= 5 ? 'var(--error)' : 'var(--success)' ?>;">
                                    <?= $book['stock'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="table-actions">
                                <a href="product_edit.php?id=<?= $book['id'] ?>" class="btn btn-outline btn-small">Modifier</a>
                                <a href="products.php?delete=<?= $book['id'] ?>&csrf=<?= generateCSRFToken() ?>" 
                                   class="btn btn-danger btn-small" 
                                   data-confirm="Supprimer ce livre ?">Supprimer</a>
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