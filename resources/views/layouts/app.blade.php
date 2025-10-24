<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <link rel="preconnect" href="{{ url('/') }}">
    <link rel="dns-prefetch" href="{{ url('/') }}">
    
    <title>@yield('title', 'ArticleHub - Latest Articles')</title>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    @stack('styles')
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="{{ route('articles.index') }}" class="logo-link">
                    <img src="{{ asset('images/logo-articlehub.png') }}" alt="ArticleHub Logo" class="logo-img">
                    <span class="logo-text">ArticleHub</span>
                </a>
            </div>
            
            <nav class="nav-menu" id="navMenu">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('articles.index') }}" class="nav-link {{ request()->routeIs('articles.index') ? 'active' : '' }}">
                            <i data-lucide="home" class="nav-icon"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('articles.index') }}#articles" class="nav-link">
                            <i data-lucide="newspaper" class="nav-icon"></i>
                            <span>Articles</span>
                        </a>
                    </li>
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                                    <i data-lucide="layout-dashboard" class="nav-icon"></i>
                                    <span>Admin Panel</span>
                                </a>
                            </li>
                        @endif
                    @endauth
                    <li class="nav-item">
                        <a href="{{ route('articles.index') }}#about" class="nav-link">
                            <i data-lucide="info" class="nav-icon"></i>
                            <span>About Us</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="nav-actions">
                @auth
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: var(--bg-secondary); border-radius: 20px;">
                        <span style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 0.25rem;" title="Logout">
                                <i data-lucide="log-out" style="width: 18px; height: 18px;"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: var(--primary-color); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.875rem;">
                        <i data-lucide="log-in" style="width: 18px; height: 18px;"></i>
                        Login
                    </a>
                @endauth
                
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    <i data-lucide="moon" id="themeIcon"></i>
                </button>
                <button class="search-toggle" id="searchToggle" aria-label="Toggle search">
                    <i data-lucide="search"></i>
                </button>
                <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                    <i data-lucide="menu" id="navToggleIcon"></i>
                </button>
            </div>
        </div>

        <div class="search-bar" id="searchBar">
            <div class="container">
                <form action="{{ route('articles.index') }}" method="GET" class="search-input-wrapper">
                    <i data-lucide="search" class="search-icon"></i>
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Cari artikel..." 
                        id="searchInput" 
                        value="{{ request('search') }}"
                        aria-label="Search articles"
                    >
                    <button type="submit" class="search-btn" aria-label="Submit search">
                        <i data-lucide="arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="main">
        @if(session('success'))
            <div class="alert-container">
                <div class="container">
                    <div class="alert alert-success" role="alert">
                        <i data-lucide="check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-container">
                <div class="container">
                    <div class="alert alert-error" role="alert">
                        <i data-lucide="x-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <img src="{{ asset('images/logo-articlehub.png') }}" alt="ArticleHub Logo" class="footer-logo-img">
                        <span class="footer-logo-text">ArticleHub</span>
                    </div>
                    <p>Platform artikel terbaik untuk semua topik menarik dan informatif.</p>
                    <div class="social-links">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <i data-lucide="facebook"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <i data-lucide="twitter"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <i data-lucide="instagram"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Linkedin">
                            <i data-lucide="linkedin"></i>
                        </a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('articles.index') }}">Home</a></li>
                        <li><a href="{{ route('articles.index') }}#articles">Articles</a></li>
                        @auth
                            @if(auth()->user()->role === 'admin')
                                <li><a href="{{ route('admin.dashboard') }}">Admin Panel</a></li>
                            @endif
                        @endauth
                        <li><a href="{{ route('articles.index') }}#about">About Us</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Categories</h3>
                    <ul class="footer-links">
                        @foreach(\App\Models\Category::limit(5)->get() as $category)
                            <li>
                                <a href="{{ route('articles.index', ['category' => $category->slug]) }}">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Newsletter</h3>
                    <p>Berlangganan untuk mendapat artikel terbaru</p>
                    <div class="newsletter-form">
                        <input 
                            type="email" 
                            placeholder="Email anda..." 
                            id="newsletterEmail"
                            aria-label="Newsletter email"
                        >
                        <button type="submit" id="subscribeBtn" aria-label="Subscribe">
                            <i data-lucide="send"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>© 2025 ArticleHub. All rights reserved.</p>
                <div class="footer-bottom-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script defer src="{{ asset('js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>