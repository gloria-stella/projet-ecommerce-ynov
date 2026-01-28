<?php
$pageTitle = 'Qui sommes-nous';
include '../includes/header.php';
?>

<div class="container">
    <div class="about-content">
        <div class="about-hero">
            <h1>Qui sommes-nous ?</h1>
            <p class="section-subtitle">Une passion pour les livres, un engagement pour l'accessibilite</p>
        </div>
        
        <section class="about-section">
            <h2>Notre Histoire</h2>
            <p>
                <strong>Youlla Books</strong> est nee d'une passion simple mais profonde : celle des livres et de leur pouvoir de transformation. 
                Fondee en 2024 par une equipe de passionnes de lecture, notre librairie en ligne s'est donnee pour mission de rendre 
                la litterature accessible a tous, sans exception.
            </p>
            <p>
                Le nom "Youlla" vient d'une expression qui signifie "allons-y" - car nous croyons que chaque livre est une invitation 
                au voyage, a la decouverte, a l'aventure. Allons-y ensemble, decouvrons de nouveaux mondes.
            </p>
        </section>
        
        <section class="about-section">
            <h2>Nos Valeurs</h2>
            <div class="values-grid">
                <div class="value-card">
                    <span class="value-icon" aria-hidden="true">♿</span>
                    <h3>Accessibilite</h3>
                    <p>Notre site est concu pour etre accessible a tous : navigation au clavier, tailles de texte ajustables, contraste eleve, et compatible avec les lecteurs d'ecran.</p>
                </div>
                <div class="value-card">
                    <span class="value-icon" aria-hidden="true">📚</span>
                    <h3>Diversite</h3>
                    <p>Nous proposons une selection variee de livres : classiques, nouveautes, jeunesse, science-fiction, et bien plus encore, pour satisfaire tous les gouts.</p>
                </div>
                <div class="value-card">
                    <span class="value-icon" aria-hidden="true">💚</span>
                    <h3>Qualite</h3>
                    <p>Chaque livre de notre catalogue est soigneusement selectionne. Nous privilegions la qualite a la quantite pour vous offrir le meilleur.</p>
                </div>
                <div class="value-card">
                    <span class="value-icon" aria-hidden="true">🤝</span>
                    <h3>Service</h3>
                    <p>Votre satisfaction est notre priorite. Notre equipe est a votre ecoute pour repondre a vos questions et vous conseiller dans vos choix.</p>
                </div>
            </div>
        </section>
        
        <section class="about-section">
            <h2>Notre Engagement Accessibilite</h2>
            <p>
                Chez Youlla Books, nous croyons que la lecture doit etre un plaisir accessible a tous. C'est pourquoi notre site integre :
            </p>
            <ul style="margin-left: 2rem; margin-top: 1rem; line-height: 2;">
                <li><strong>Tailles de texte ajustables</strong> pour une lecture confortable</li>
                <li><strong>Mode contraste eleve</strong> pour les personnes malvoyantes</li>
                <li><strong>Police adaptee a la dyslexie</strong> sur demande</li>
                <li><strong>Navigation complete au clavier</strong> (raccourcis : Alt+H, Alt+P, Alt+C)</li>
                <li><strong>Compatibilite avec les lecteurs d'ecran</strong> (ARIA)</li>
                <li><strong>Descriptions alternatives</strong> pour toutes les images</li>
            </ul>
        </section>
        
        <section class="about-section">
            <h2>Contactez-nous</h2>
            <p>
                Vous avez des questions, des suggestions ou simplement envie de partager votre amour des livres ? 
                N'hesitez pas a nous contacter !
            </p>
            <div class="values-grid" style="margin-top: 2rem;">
                <div class="value-card">
                    <span class="value-icon" aria-hidden="true">📧</span>
                    <h3>Email</h3>
                    <p>contact@youlla-books.com</p>
                </div>
                <div class="value-card">
                    <span class="value-icon" aria-hidden="true">📞</span>
                    <h3>Telephone</h3>
                    <p>01 23 45 67 89</p>
                </div>
                <div class="value-card">
                    <span class="value-icon" aria-hidden="true">📍</span>
                    <h3>Adresse</h3>
                    <p>123 Rue des Livres<br>75001 Paris</p>
                </div>
            </div>
        </section>
        
        <div style="text-align: center; margin-top: 3rem;">
            <a href="products.php" class="btn btn-primary btn-large">Decouvrir nos livres</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>