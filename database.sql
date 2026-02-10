-- ============================================
-- Base de données E-commerce - Partie A (Produits)
-- ============================================

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS ecommerce_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce_db;

-- ============================================
-- Table des utilisateurs (géré par Personne B, mais nécessaire pour les relations)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table des produits (items) - VOTRE RESPONSABILITÉ
-- ============================================
CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    prix DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default.jpg',
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
    INDEX idx_statut (statut),
    INDEX idx_prix (prix)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table de gestion du stock - VOTRE RESPONSABILITÉ
-- ============================================
CREATE TABLE IF NOT EXISTS stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_item INT NOT NULL,
    quantite_stock INT NOT NULL DEFAULT 0,
    FOREIGN KEY (id_item) REFERENCES items(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_id_item (id_item)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table des commandes (géré par Personne B, mais créée pour référence)
-- ============================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_item INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_item) REFERENCES items(id) ON DELETE CASCADE,
    INDEX idx_user (id_user),
    INDEX idx_item (id_item)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table des factures (géré par Personne B, mais créée pour référence)
-- ============================================
CREATE TABLE IF NOT EXISTS invoice (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    date_transaction DATETIME DEFAULT CURRENT_TIMESTAMP,
    montant DECIMAL(10, 2) NOT NULL,
    adresse_facturation VARCHAR(255) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    code_postal VARCHAR(10) NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insertion de données de test pour les produits
-- ============================================

-- Insertion de produits de test
INSERT INTO items (nom, description, prix, image) VALUES
('MacBook Pro M3', 'Ordinateur portable haute performance avec puce M3, 16GB RAM, 512GB SSD. Parfait pour les professionnels et créatifs.', 2499.99, 'macbook-pro.jpg'),
('iPhone 15 Pro', 'Smartphone premium avec puce A17 Pro, appareil photo 48MP, écran Super Retina XDR 6.1 pouces.', 1299.99, 'iphone-15-pro.jpg'),
('AirPods Pro', 'Écouteurs sans fil avec réduction active du bruit, son spatial personnalisé et résistance à l\'eau.', 279.99, 'airpods-pro.jpg'),
('iPad Air', 'Tablette polyvalente avec puce M1, écran Liquid Retina 10.9 pouces, compatible Apple Pencil.', 699.99, 'ipad-air.jpg'),
('Apple Watch Series 9', 'Montre connectée avec GPS, moniteur de santé avancé, écran Always-On Retina.', 449.99, 'apple-watch.jpg'),
('Magic Keyboard', 'Clavier sans fil rechargeable avec disposition française, design élégant et compact.', 129.99, 'magic-keyboard.jpg'),
('Magic Mouse', 'Souris sans fil Multi-Touch rechargeable avec surface Multi-Touch optimisée.', 89.99, 'magic-mouse.jpg'),
('HomePod mini', 'Enceinte intelligente compacte avec son à 360°, Siri intégré et contrôle de la maison.', 109.99, 'homepod-mini.jpg'),
('AirTag Pack de 4', 'Traceurs Bluetooth pour retrouver vos objets facilement via l\'app Localiser.', 119.99, 'airtag.jpg'),
('USB-C vers Lightning', 'Câble de charge rapide 1m certifié Apple, compatible iPhone et iPad.', 25.99, 'cable-usbc.jpg');

-- Insertion du stock correspondant
INSERT INTO stock (id_item, quantite_stock) VALUES
(1, 15),
(2, 50),
(3, 100),
(4, 30),
(5, 45),
(6, 80),
(7, 60),
(8, 25),
(9, 200),
(10, 150);

-- Insertion d'un compte administrateur de test
-- Mot de passe : admin123 (hashé avec password_hash)
INSERT INTO users (nom, prenom, email, mot_de_passe, role) VALUES
('Admin', 'Système', 'admin@ecommerce.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ============================================
-- Vues utiles pour les requêtes fréquentes
-- ============================================

-- Vue combinant produits et stock
CREATE OR REPLACE VIEW vue_produits_stock AS
SELECT 
    i.id,
    i.nom,
    i.description,
    i.prix,
    i.image,
    i.date_publication,
    i.statut,
    COALESCE(s.quantite_stock, 0) AS stock_disponible
FROM items i
LEFT JOIN stock s ON i.id = s.id_item;
