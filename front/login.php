<?php
$pageTitle = 'Connexion';
include '../includes/header.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$redirect = isset($_GET['redirect']) ? sanitize($_GET['redirect']) : 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifier CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Erreur de securite. Veuillez reessayer.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = 'Veuillez remplir tous les champs.';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Connexion reussie
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                setFlashMessage('success', 'Bienvenue, ' . $user['first_name'] . ' !');
                redirect($redirect);
            } else {
                $error = 'Email ou mot de passe incorrect.';
            }
        }
    }
}
?>

<div class="container">
    <div class="form-container">
        <h1>Connexion</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error" role="alert"><?= sanitize($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" data-validate>
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="form-group">
                <label for="email">Adresse email <span class="required">*</span></label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       required 
                       autocomplete="email"
                       placeholder="votre@email.com"
                       value="<?= isset($_POST['email']) ? sanitize($_POST['email']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe <span class="required">*</span></label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       required 
                       autocomplete="current-password"
                       placeholder="Votre mot de passe">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-large">
                Se connecter
            </button>
        </form>
        
        <p class="form-footer">
            Pas encore de compte ? <a href="register.php<?= $redirect !== 'index.php' ? '?redirect=' . urlencode($redirect) : '' ?>">Creer un compte</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>