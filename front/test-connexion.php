<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=youlla_books;charset=utf8mb4", "root", "root");
    echo "✅ Connexion réussie à la base de données.";
} catch (PDOException $e) {
    echo "❌ Erreur de connexion : " . $e->getMessage();
}
?>
