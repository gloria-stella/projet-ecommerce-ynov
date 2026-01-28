/**
 * YOULLA BOOKS - JavaScript principal
 * Accessibilite et interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ================================================
    // ACCESSIBILITE - Taille de police
    // ================================================
    const baseFontSize = 16;
    let currentFontSize = parseInt(localStorage.getItem('fontSize')) || baseFontSize;
    
    function updateFontSize(size) {
        document.documentElement.style.setProperty('--font-size-base', size + 'px');
        localStorage.setItem('fontSize', size);
        currentFontSize = size;
    }
    
    // Appliquer la taille sauvegardee
    updateFontSize(currentFontSize);
    
    document.getElementById('decrease-font')?.addEventListener('click', function() {
        if (currentFontSize > 12) {
            updateFontSize(currentFontSize - 2);
        }
    });
    
    document.getElementById('reset-font')?.addEventListener('click', function() {
        updateFontSize(baseFontSize);
    });
    
    document.getElementById('increase-font')?.addEventListener('click', function() {
        if (currentFontSize < 24) {
            updateFontSize(currentFontSize + 2);
        }
    });
    
    // ================================================
    // ACCESSIBILITE - Mode contraste eleve
    // ================================================
    const contrastBtn = document.getElementById('toggle-contrast');
    let highContrast = localStorage.getItem('highContrast') === 'true';
    
    function toggleContrast() {
        highContrast = !highContrast;
        document.body.classList.toggle('high-contrast', highContrast);
        contrastBtn?.classList.toggle('active', highContrast);
        localStorage.setItem('highContrast', highContrast);
    }
    
    // Appliquer le mode sauvegarde
    if (highContrast) {
        document.body.classList.add('high-contrast');
        contrastBtn?.classList.add('active');
    }
    
    contrastBtn?.addEventListener('click', toggleContrast);
    
    // ================================================
    // ACCESSIBILITE - Police dyslexie
    // ================================================
    const dyslexiaBtn = document.getElementById('toggle-dyslexia');
    let dyslexiaFont = localStorage.getItem('dyslexiaFont') === 'true';
    
    function toggleDyslexia() {
        dyslexiaFont = !dyslexiaFont;
        document.body.classList.toggle('dyslexia-font', dyslexiaFont);
        dyslexiaBtn?.classList.toggle('active', dyslexiaFont);
        localStorage.setItem('dyslexiaFont', dyslexiaFont);
    }
    
    // Appliquer le mode sauvegarde
    if (dyslexiaFont) {
        document.body.classList.add('dyslexia-font');
        dyslexiaBtn?.classList.add('active');
    }
    
    dyslexiaBtn?.addEventListener('click', toggleDyslexia);
    
    // ================================================
    // MENU MOBILE
    // ================================================
    const menuToggle = document.getElementById('menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    
    menuToggle?.addEventListener('click', function() {
        const isExpanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', !isExpanded);
        mainNav?.classList.toggle('active');
    });
    
    // ================================================
    // FERMER LES ALERTES
    // ================================================
    document.querySelectorAll('.alert-close').forEach(function(btn) {
        btn.addEventListener('click', function() {
            this.closest('.alert').remove();
        });
    });
    
    // ================================================
    // CONFIRMATION SUPPRESSION
    // ================================================
    document.querySelectorAll('[data-confirm]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm || 'Etes-vous sur ?')) {
                e.preventDefault();
            }
        });
    });
    
    // ================================================
    // MISE A JOUR PANIER EN TEMPS REEL
    // ================================================
    document.querySelectorAll('.cart-item-quantity input').forEach(function(input) {
        input.addEventListener('change', function() {
            // Optionnel : soumettre automatiquement
            // this.closest('form').submit();
        });
    });
    
    // ================================================
    // VALIDATION FORMULAIRES
    // ================================================
    document.querySelectorAll('form[data-validate]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Verification des champs requis
            form.querySelectorAll('[required]').forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });
            
            // Verification email
            form.querySelectorAll('input[type="email"]').forEach(function(field) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (field.value && !emailRegex.test(field.value)) {
                    isValid = false;
                    field.classList.add('error');
                }
            });
            
            // Verification mot de passe
            const password = form.querySelector('input[name="password"]');
            const confirmPassword = form.querySelector('input[name="confirm_password"]');
            
            if (password && confirmPassword) {
                if (password.value !== confirmPassword.value) {
                    isValid = false;
                    confirmPassword.classList.add('error');
                    alert('Les mots de passe ne correspondent pas.');
                }
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
    
    // ================================================
    // ANIMATION AU SCROLL
    // ================================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.product-card, .stat-card, .value-card').forEach(function(el) {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
    });
    
    // ================================================
    // RACCOURCIS CLAVIER
    // ================================================
    document.addEventListener('keydown', function(e) {
        // Alt + H : Aller a l'accueil
        if (e.altKey && e.key === 'h') {
            window.location.href = '/YOULLA-BOOKS/front/index.php';
        }
        // Alt + P : Aller aux produits
        if (e.altKey && e.key === 'p') {
            window.location.href = '/YOULLA-BOOKS/front/products.php';
        }
        // Alt + C : Aller au panier
        if (e.altKey && e.key === 'c') {
            window.location.href = '/YOULLA-BOOKS/front/cart.php';
        }
    });
    
    console.log('Youlla Books - Site charge avec succes');
    console.log('Raccourcis clavier : Alt+H (Accueil), Alt+P (Produits), Alt+C (Panier)');
});