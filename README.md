# projet-ecommerce-ynov-Paris-Nanterre

Projet e-commerce en PHP/MySQL réalisé  
par:
Njundom Gloria.S B2/Cybersécurité
Larabi Youmna. B2/Informatique

# Youlla Books – Site E‑commerce en PHP

Youlla Books est un site e‑commerce dynamique développé en PHP avec une base de données MySQL.  
Le projet a été réalisé dans le cadre d’un travail scolaire visant à créer une boutique en ligne complète : catalogue, panier, gestion des utilisateurs et interface d’administration.

## Fonctionnalités principales

### Front‑office (côté utilisateur)

- **Accueil** : présentation du site, mise en avant de produits.
- **Qui sommes‑nous ?** : page statique expliquant le concept.
- **Catalogue des articles** : affichage de tous les produits (image, titre, prix, description).
- **Fiche produit** : détails complets d’un article.
- **Inscription / Connexion** :
  - Validation des données
  - Hachage des mots de passe (`password_hash`)
- **Panier** :
  - Ajouter un article
  - Modifier les quantités
  - Supprimer un article
  - Calcul automatique du total

## Back‑office (administration)

- Connexion sécurisée réservée aux administrateurs
- **CRUD (Create, Read, Update, Delete) complet sur les produits** :
  - Ajouter un article (image, description, prix, stock…)
  - Modifier un article
  - Supprimer un article
  - Voir la liste complète
- **Gestion des utilisateurs** :
  - Voir les comptes créés
  - Supprimer un utilisateur si nécessaire

## Base de données (MySQL)

Le projet utilise au minimum **5 tables** :

- `users` : gestion des utilisateurs
- `items` : gestion des produits
- `stock` : quantités disponibles
- `orders` : commandes
- `invoice` : factures
- `orders_item` : détails commandes

Les relations suivent un modèle relationnel cohérent (clé primaire / clé étrangère).

## Technologies utilisées

- **PHP** (back‑end)
- **HTML / CSS / JS**
- **Bootstrap** (mise en page)
- **MySQL** (base de données)
- **phpMyAdmin** (gestion de la base)
- **Serveur local** : MAMP

---

## Installation et lancement du projet

### Cloner le projet

git clone https://github.com/votre-nom/votre-repo.git

### Placer le projet dans votre serveur local

Sous MAMP → dossier htdocs

### Importer la base de données

Ouvrir phpMyAdmin

### Créer une base (ex : youlla_books)

Importer le fichier .sql fourni (tables + données)

### Configurer la connexion à la base

Dans config/database.php (ou équivalent), modifier :
$host = "localhost";
$dbname = "youlla_books";
$username = "root";
$password = "";
(Selon votre environnement local)

### Lancer le site

Ouvrir dans votre navigateur : http://localhost/YOULLA-BOOKS/front/index.php
