<?php
require_once '../config/database.php';

if (!isAdmin()) {
    redirect('login.php');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT i.*, s.quantity as stock FROM items i LEFT JOIN stock s ON i.id = s.item_id WHERE i.id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    setFlashMessage('error', 'Livre non trouve.');
    redirect('products.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $author = sanitize($_POST['author'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $longDescription = sanitize($_POST['long_description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $category = sanitize($_POST['category'] ?? '');
    $isbn = sanitize($_POST['isbn'] ?? '');
    $publisher = sanitize($_POST['publisher'] ?? '');
    $publicationYear = (int)($_POST['publication_year'] ?? 0) ?: null;
    $pages = (int)($_POST['pages'] ?? 0) ?: null;
    $stock = (int)($_POST['stock'] ?? 0);
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $image = $book['image'];
    
    if (empty($title) || empty($author) || $price <= 0) {
        $error = 'Veuillez remplir les champs obligatoires.';
    } else {
        // Upload nouvelle image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/img/books/';
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $image = uniqid('book_') . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
            }
        }
        
        try {
            $stmt = $pdo->prepare("
                UPDATE items SET title = ?, author = ?, description = ?, long_description = ?, price = ?, category = ?, isbn = ?, publisher = ?, publication_year = ?, pages = ?, image = ?, is_featured = ?
                WHERE id = ?
            ");
            $stmt->execute([$title, $author, $description, $longDescription, $price, $category, $isbn, $publisher, $publicationYear, $pages, $image, $isFeatured, $id]);
            
            $stmt = $pdo->prepare("UPDATE stock SET quantity = ? WHERE item_id = ?");
            $stmt->execute([$stock, $id]);
            
            setFlashMessage('success', 'Livre modifie avec succes !');
            redirect('products.php');
        } catch (Exception $e) {
            $error = 'Erreur lors de la modification.';
        }
    }
}

$categories = $pdo->query("SELECT DISTINCT category FROM items WHERE category IS NOT NULL ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un livre - Admin</title>
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
            <div class="form-container" style="max-width: 700px;">
                <h1>Modifier : <?= sanitize($book['title']) ?></h1>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Titre <span class="required">*</span></label>
                            <input type="text" name="title" id="title" required value="<?= sanitize($book['title']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="author">Auteur <span class="required">*</span></label>
                            <input type="text" name="author" id="author" required value="<?= sanitize($book['author']) ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description courte</label>
                        <textarea name="description" id="description" rows="3"><?= sanitize($book['description']) ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="long_description">Description complete</label>
                        <textarea name="long_description" id="long_description" rows="5"><?= sanitize($book['long_description']) ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Prix (EUR) <span class="required">*</span></label>
                            <input type="number" name="price" id="price" step="0.01" min="0" required value="<?= $book['price'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="stock">Stock</label>
                            <input type="number" name="stock" id="stock" min="0" value="<?= $book['stock'] ?? 0 ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="category">Categorie</label>
                            <input type="text" name="category" id="category" list="categories-list" value="<?= sanitize($book['category']) ?>">
                            <datalist id="categories-list">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= sanitize($cat) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label for="isbn">ISBN</label>
                            <input type="text" name="isbn" id="isbn" value="<?= sanitize($book['isbn']) ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="publisher">Editeur</label>
                            <input type="text" name="publisher" id="publisher" value="<?= sanitize($book['publisher']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="publication_year">Annee de publication</label>
                            <input type="number" name="publication_year" id="publication_year" value="<?= $book['publication_year'] ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="pages">Nombre de pages</label>
                        <input type="number" name="pages" id="pages" value="<?= $book['pages'] ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Image actuelle</label>
                        <img src="<?= getBookImage($book['image']) ?>" alt="" style="max-width: 150px; display: block; margin: 10px 0; border-radius: var(--radius);">
                        
                        <label for="image">Nouvelle image (optionnel)</label>
                        <input type="file" name="image" id="image" accept="image/*">
                    </div>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="is_featured" value="1" <?= $book['is_featured'] ? 'checked' : '' ?>>
                            Mettre en vedette (coup de coeur)
                        </label>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <a href="products.php" class="btn btn-outline">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
