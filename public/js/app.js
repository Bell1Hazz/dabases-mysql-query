// ===== THEME TOGGLE =====
const ThemeManager = {
    init() {
        this.themeToggle = document.getElementById('themeToggle');
        this.themeIcon = document.getElementById('themeIcon');
        this.loadSavedTheme();
        this.attachEventListener();
    },

    attachEventListener() {
        if (this.themeToggle) {
            this.themeToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleTheme();
            });
        }
    },

    loadSavedTheme() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        this.applyTheme(savedTheme);
    },

    toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        this.applyTheme(newTheme);
    },

    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        
        // Update icon
        if (this.themeIcon) {
            this.themeIcon.setAttribute('data-lucide', theme === 'dark' ? 'sun' : 'moon');
            lucide.createIcons(); // Re-render icons
        }
    }
};

// ===== MOBILE NAVIGATION =====
const MobileNav = {
    init() {
        this.navToggle = document.getElementById('navToggle');
        this.navToggleIcon = document.getElementById('navToggleIcon');
        this.navMenu = document.getElementById('navMenu');
        this.attachEventListeners();
    },

    attachEventListeners() {
        if (this.navToggle) {
            this.navToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleNav();
            });
        }

        if (this.navMenu) {
            this.navMenu.addEventListener('click', (e) => {
                if (e.target.tagName === 'A') {
                    this.closeNav();
                }
            });
        }
    },

    toggleNav() {
        if (this.navMenu) {
            this.navMenu.classList.toggle('active');
            this.updateToggleIcon();
        }
    },

    closeNav() {
        if (this.navMenu) {
            this.navMenu.classList.remove('active');
            this.updateToggleIcon();
        }
    },

    updateToggleIcon() {
        if (this.navToggleIcon) {
            const isActive = this.navMenu.classList.contains('active');
            this.navToggleIcon.setAttribute('data-lucide', isActive ? 'x' : 'menu');
            lucide.createIcons(); // Re-render icons
        }
    }
};

// ===== SEARCH FUNCTIONALITY =====
const SearchManager = {
    init() {
        this.searchToggle = document.getElementById('searchToggle');
        this.searchBar = document.getElementById('searchBar');
        this.searchInput = document.getElementById('searchInput');
        this.attachEventListeners();
    },

    attachEventListeners() {
        if (this.searchToggle) {
            this.searchToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSearch();
            });
        }
    },

    toggleSearch() {
        if (this.searchBar) {
            this.searchBar.classList.toggle('active');
            
            if (this.searchBar.classList.contains('active')) {
                setTimeout(() => this.searchInput.focus(), 300);
            }
        }
    }
};

// ===== NEWSLETTER SUBSCRIPTION =====
const Newsletter = {
    init() {
        this.subscribeBtn = document.getElementById('subscribeBtn');
        this.emailInput = document.getElementById('newsletterEmail');
        this.attachEventListener();
    },

    attachEventListener() {
        if (this.subscribeBtn) {
            this.subscribeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleSubscribe();
            });
        }

        if (this.emailInput) {
            this.emailInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.handleSubscribe();
                }
            });
        }
    },

    handleSubscribe() {
        const email = this.emailInput.value.trim();

        if (!this.validateEmail(email)) {
            alert('Please enter a valid email address');
            return;
        }

        alert('Thank you for subscribing to our newsletter!');
        this.emailInput.value = '';
    },

    validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
};

// ===== SMOOTH SCROLLING =====
const SmoothScroll = {
    init() {
        this.attachEventListeners();
    },

    attachEventListeners() {
        document.addEventListener('click', (e) => {
            const anchor = e.target.closest('a[href^="#"]');
            if (anchor) {
                const targetId = anchor.getAttribute('href');
                if (targetId !== '#') {
                    const target = document.querySelector(targetId);
                    if (target) {
                        e.preventDefault();
                        this.scrollToElement(target);
                    }
                }
            }
        });
    },

    scrollToElement(element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
};

// ===== INITIALIZE ALL MANAGERS =====
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide Icons
    lucide.createIcons();
    
    // Initialize modules
    ThemeManager.init();
    SearchManager.init();
    MobileNav.init();
    Newsletter.init();
    SmoothScroll.init();

    console.log('✅ ArticleHub initialized with Lucide Icons');
});