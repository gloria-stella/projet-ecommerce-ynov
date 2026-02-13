<?php
/**
 * YOULLA BOOKS - Configuration Base de Donnees
 * Connexion PDO securisee avec gestion d'erreurs
 */

// Configuration de la base de donnees
define('DB_HOST', 'localhost');
define('DB_NAME', 'youlla_books');
define('DB_USER', 'root');
define('DB_PASS', 'root'); // Vide par defaut sur MAMP/XAMPP

// Chemin de base du projet
define('BASE_URL', '/YOULLA-BOOKS');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // En production, ne pas afficher les details de l'erreur
    die("Erreur de connexion a la base de donnees. Veuillez reessayer plus tard.");
}

// Demarrer la session si pas deja fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// FONCTIONS UTILITAIRES
// ============================================

/**
 * Verifie si un utilisateur est connecte
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Verifie si l'utilisateur est admin
 */
function isAdmin(): bool {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Redirige vers une URL
 */
function redirect(string $url): void {
    header("Location: $url");
    exit();
}

/**
 * Nettoie les donnees pour l'affichage HTML (protection XSS)
 */
function sanitize(?string $data): string {
    if ($data === null) return '';
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Genere un token CSRF
 */
function generateCSRFToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifie le token CSRF
 */
function verifyCSRFToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Affiche un message flash
 */
function setFlashMessage(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Recupere et supprime le message flash
 */
function getFlashMessage(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Formate un prix
 */
function formatPrice(float $price): string {
    return number_format($price, 2, ',', ' ') . ' €';
}

/**
 * Compte les articles dans le panier
 */
function getCartCount(): int {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }
    return array_sum($_SESSION['cart']);
}

/**
 * Calcule le total du panier
 */
function getCartTotal($pdo): float {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }
    
    $total = 0;
    foreach ($_SESSION['cart'] as $itemId => $quantity) {
        $stmt = $pdo->prepare("SELECT price FROM items WHERE id = ?");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch();
        if ($item) {
            $total += $item['price'] * $quantity;
        }
    }
    return $total;
}
/**
 * Retourne l'URL de l'image d'un livre
 * Gère les URLs externes et les images locales
 */
function getBookImage(string $image): string {
    // Si c'est déjà une URL externe
    if (strpos($image, 'http://') === 0 || strpos($image, 'https://') === 0) {
        return $image;
    }

    // Sinon, c'est une image locale
    return BASE_URL . '/assets/img/books/' . $image;
}

?>