<?php
require_once '../config/database.php';

if (isLoggedIn() && isAdmin()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        redirect('dashboard.php');
    } else {
        $error = 'Identifiants invalides ou acces non autorise.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Youlla Books</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+3:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body style="background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="form-container">
        <h1 style="text-align: center;">Administration</h1>
        <p style="text-align: center; color: var(--text-light); margin-bottom: 2rem;">Youlla Books - Espace reserve</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= sanitize($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email administrateur</label>
                <input type="email" name="email" id="email" required autocomplete="email">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Connexion</button>
        </form>
        
        <p class="form-footer">
            <a href="<?= BASE_URL ?>/front/index.php">Retour au site</a>
        </p>
    </div>
</body>
</html>