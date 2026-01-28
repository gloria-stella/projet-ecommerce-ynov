    </main>
    
    <!-- Footer -->
    <footer class="main-footer" role="contentinfo">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <span class="logo-icon" aria-hidden="true">📚</span>
                        <span class="logo-text">Youlla Books</span>
                    </div>
                    <p class="footer-description">
                        Votre librairie en ligne depuis 2024. Nous croyons que les livres ont le pouvoir de transformer des vies. Notre mission : rendre la lecture accessible a tous.
                    </p>
                </div>
                
                <div class="footer-col">
                    <h4>Navigation</h4>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_URL ?>/front/index.php">Accueil</a></li>
                        <li><a href="<?= BASE_URL ?>/front/products.php">Nos Livres</a></li>
                        <li><a href="<?= BASE_URL ?>/front/about.php">Qui sommes-nous</a></li>
                        <li><a href="<?= BASE_URL ?>/front/cart.php">Mon Panier</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Categories</h4>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_URL ?>/front/products.php?category=Litterature">Litterature</a></li>
                        <li><a href="<?= BASE_URL ?>/front/products.php?category=Science-Fiction">Science-Fiction</a></li>
                        <li><a href="<?= BASE_URL ?>/front/products.php?category=Fantasy">Fantasy</a></li>
                        <li><a href="<?= BASE_URL ?>/front/products.php?category=Jeunesse">Jeunesse</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Contact</h4>
                    <address class="footer-contact">
                        <p><span aria-hidden="true">📧</span> contact@youlla-books.com</p>
                        <p><span aria-hidden="true">📞</span> 01 23 45 67 89</p>
                        <p><span aria-hidden="true">📍</span> 123 Rue des Livres, 75001 Paris</p>
                    </address>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Youlla Books. Tous droits reserves.</p>
                <p class="footer-accessibility">
                    <span aria-hidden="true">♿</span> Site accessible a tous - Navigation clavier supportee
                </p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>