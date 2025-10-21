/**
 * ===== PERFORMANCE OPTIMIZED APP.JS =====
 */

// ===== UTILITY FUNCTIONS =====
function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function (...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

function toggleClass(element, className) {
    if (element) {
        element.classList.toggle(className);
    }
}

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
        this.applyTheme(savedTheme, false); // false = skip animation on load
    },

    toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        this.applyTheme(newTheme, true); // true = with animation
    },

    applyTheme(theme, animate = false) {
        // Apply theme to document
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        
        // Update icon IMMEDIATELY
        if (this.themeIcon) {
            const iconName = theme === 'dark' ? 'sun' : 'moon';
            this.themeIcon.setAttribute('data-lucide', iconName);
            
            // Re-render icon with Lucide
            lucide.createIcons();
        }

        // Add animation class if needed
        if (animate && this.themeToggle) {
            this.themeToggle.style.transform = 'rotate(360deg)';
            setTimeout(() => {
                this.themeToggle.style.transform = 'rotate(0deg)';
            }, 300);
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

        if (this.searchInput) {
            this.searchInput.addEventListener('input', debounce(() => {
                // Autocomplete bisa ditambahkan di sini
            }, 300));
        }
    },

    toggleSearch() {
        if (this.searchBar) {
            toggleClass(this.searchBar, 'active');
            
            if (this.searchBar.classList.contains('active')) {
                setTimeout(() => this.searchInput.focus(), 300);
            }
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
            toggleClass(this.navMenu, 'active');
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
            const iconName = isActive ? 'x' : 'menu';
            this.navToggleIcon.setAttribute('data-lucide', iconName);
            
            // Re-render icon
            lucide.createIcons();
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
                    e.preventDefault();
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

// ===== LAZY LOADING IMAGES =====
// Lazy Loading Images
const LazyImages = {
    init() {
        const images = document.querySelectorAll('img[loading="lazy"]');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src || img.src;
                        imageObserver.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px' // Load 50px before visible
            });

            images.forEach(img => imageObserver.observe(img));
        }
    }
};

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    LazyImages.init();
    // ... other inits
});

// ===== PERFORMANCE MONITORING =====
const PerformanceMonitor = {
    init() {
        this.logPerformanceMetrics();
    },

    logPerformanceMetrics() {
        window.addEventListener('load', () => {
            const perfData = window.performance.timing;
            const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
            const connectTime = perfData.responseEnd - perfData.requestStart;
            const renderTime = perfData.domComplete - perfData.domLoading;

            console.group('📊 Performance Metrics');
            console.log(`Total Load Time: ${pageLoadTime}ms`);
            console.log(`Connect Time: ${connectTime}ms`);
            console.log(`Render Time: ${renderTime}ms`);
            console.groupEnd();
        });
    }
};

// ===== INITIALIZE ALL MANAGERS =====
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide Icons first
    lucide.createIcons();
    
    // Initialize modules
    ThemeManager.init();
    SearchManager.init();
    MobileNav.init();
    Newsletter.init();
    SmoothScroll.init();
    LazyImages.init();
    PerformanceMonitor.init();

    console.log('✅ ArticleHub initialized with Lucide Icons');
});

// Handle visibility change
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        console.log('Page hidden');
    } else {
        console.log('Page visible');
    }
});
