<?php
$pageTitle = 'Inscription';
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
        $firstName = sanitize($_POST['first_name'] ?? '');
        $lastName = sanitize($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            $error = 'Veuillez remplir tous les champs obligatoires.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Adresse email invalide.';
        } elseif (strlen($password) < 6) {
            $error = 'Le mot de passe doit contenir au moins 6 caracteres.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Les mots de passe ne correspondent pas.';
        } else {
            // Verifier si l'email existe
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = 'Cette adresse email est deja utilisee.';
            } else {
                // Creer le compte
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
                $stmt->execute([$firstName, $lastName, $email, $hashedPassword]);
                
                setFlashMessage('success', 'Compte cree avec succes ! Vous pouvez maintenant vous connecter.');
                redirect('login.php' . ($redirect !== 'index.php' ? '?redirect=' . urlencode($redirect) : ''));
            }
        }
    }
}
?>

<div class="container">
    <div class="form-container">
        <h1>Creer un compte</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error" role="alert"><?= sanitize($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" data-validate>
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">Prenom <span class="required">*</span></label>
                    <input type="text" 
                           name="first_name" 
                           id="first_name" 
                           required 
                           autocomplete="given-name"
                           placeholder="Jean"
                           value="<?= isset($_POST['first_name']) ? sanitize($_POST['first_name']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="last_name">Nom <span class="required">*</span></label>
                    <input type="text" 
                           name="last_name" 
                           id="last_name" 
                           required 
                           autocomplete="family-name"
                           placeholder="Dupont"
                           value="<?= isset($_POST['last_name']) ? sanitize($_POST['last_name']) : '' ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Adresse email <span class="required">*</span></label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       required 
                       autocomplete="email"
                       placeholder="jean.dupont@email.com"
                       value="<?= isset($_POST['email']) ? sanitize($_POST['email']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe <span class="required">*</span></label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       required 
                       autocomplete="new-password"
                       placeholder="Minimum 6 caracteres"
                       minlength="6">
                <p class="form-help">Minimum 6 caracteres</p>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe <span class="required">*</span></label>
                <input type="password" 
                       name="confirm_password" 
                       id="confirm_password" 
                       required 
                       autocomplete="new-password"
                       placeholder="Repetez votre mot de passe">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-large">
                Creer mon compte
            </button>
        </form>
        
        <p class="form-footer">
            Deja inscrit ? <a href="login.php<?= $redirect !== 'index.php' ? '?redirect=' . urlencode($redirect) : '' ?>">Se connecter</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>