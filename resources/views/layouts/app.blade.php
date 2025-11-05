<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    {{-- Performance Hints --}}
    <link rel="preconnect" href="{{ url('/') }}">
    <link rel="dns-prefetch" href="https://unpkg.com">

    <title>@yield('title', 'Artiqle - Latest Articles')</title>

    {{-- Defer Lucide Icons --}}
    <script defer src="https://unpkg.com/lucide@latest"></script>
    
    {{-- Styles --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    @stack('styles')
</head>
<body>
    <header class="header">
        <div class="container">
            {{-- Logo --}}
            <div class="logo">
                <a href="{{ route('articles.index') }}" class="logo-link">
                    <img src="{{ asset('images/logo-articlehub.png') }}" alt="ArticleHub Logo" class="logo-img">
                    <span class="logo-text">Artiqle</span>
                </a>
            </div>
            
            {{-- Navigation Menu --}}
            <nav class="nav-menu" id="navMenu">
                <ul class="nav-list">
                    {{-- Home --}}
                    <li class="nav-item">
                        <a href="{{ route('articles.index') }}" class="nav-link {{ request()->routeIs('articles.index') ? 'active' : '' }}">
                            <i data-lucide="home" class="nav-icon"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    
                    {{-- Articles --}}
                    <li class="nav-item">
                        <a href="{{ route('articles.index') }}#articles" class="nav-link">
                            <i data-lucide="newspaper" class="nav-icon"></i>
                            <span>Articles</span>
                        </a>
                    </li>
                    
                    {{-- Authenticated User Links --}}
                    @auth
                        @php
                            $userRole = auth()->user()->role;
                        @endphp
                        
                        {{-- Admin Panel --}}
                        @if($userRole === 'admin')
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                                    <i data-lucide="layout-dashboard" class="nav-icon"></i>
                                    <span>Admin Panel</span>
                                </a>
                            </li>
                        @endif
                        
                        {{-- Author Dashboard --}}
                        @if($userRole === 'author')
                            <li class="nav-item">
                                <a href="{{ route('author.dashboard') }}" class="nav-link {{ request()->routeIs('author.*') ? 'active' : '' }}">
                                    <i data-lucide="edit" class="nav-icon"></i>
                                    <span>My Articles</span>
                                </a>
                            </li>
                        @endif
                    @endauth
                    
                    {{-- About --}}
                    <li class="nav-item">
                        <a href="{{ route('articles.index') }}#about" class="nav-link">
                            <i data-lucide="info" class="nav-icon"></i>
                            <span>About Us</span>
                        </a>
                    </li>
                </ul>
            </nav>

            {{-- Nav Actions --}}
            <div class="nav-actions">
                {{-- User Info / Login --}}
                @auth
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: var(--bg-secondary); border-radius: 20px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.875rem;">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">
                            {{ auth()->user()->name }}
                        </span>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline; margin: 0;">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 0.25rem; display: flex; align-items: center; transition: color 0.3s ease;" title="Logout">
                                <i data-lucide="log-out" style="width: 18px; height: 18px;"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1.5rem; background: var(--primary-color); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.875rem; transition: all 0.3s ease;">
                        <i data-lucide="log-in" style="width: 18px; height: 18px;"></i>
                        Login
                    </a>
                @endauth
                
                {{-- Theme Toggle --}}
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    {{-- Icon replaced by JavaScript --}}
                </button>
                
                {{-- Search Toggle --}}
                <button class="search-toggle" id="searchToggle" aria-label="Toggle search">
                    <i data-lucide="search"></i>
                </button>
            </div>
        </div>

        {{-- Search Bar --}}
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
        {{-- Success Alert --}}
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

        {{-- Error Alert --}}
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
                {{-- Footer Logo & Social --}}
                <div class="footer-section">
                    <div class="footer-logo">
                        <img src="{{ asset('images/logo-articlehub.png') }}" alt="ArticleHub Logo" class="footer-logo-img">
                        <span class="footer-logo-text">Artiqle</span>
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
                
                {{-- Quick Links --}}
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('articles.index') }}">Home</a></li>
                        <li><a href="{{ route('articles.index') }}#articles">Articles</a></li>
                        @auth
                            @if(auth()->user()->role === 'admin')
                                <li><a href="{{ route('admin.dashboard') }}">Admin Panel</a></li>
                            @elseif(auth()->user()->role === 'author')
                                <li><a href="{{ route('author.dashboard') }}">My Articles</a></li>
                            @endif
                        @endauth
                        <li><a href="{{ route('articles.index') }}#about">About Us</a></li>
                    </ul>
                </div>
                
                {{-- Categories --}}
                <div class="footer-section">
                    <h3>Categories</h3>
                    <ul class="footer-links">
                        @foreach($footerCategories ?? [] as $category)
                            <li>
                                <a href="{{ route('articles.index', ['category' => $category->slug]) }}">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                
                {{-- Newsletter --}}
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
                <p>© 2025 Artiqle. All rights reserved.</p>
                <div class="footer-bottom-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Scripts --}}
    <script defer src="{{ asset('js/app.js') }}"></script>
    
    {{-- Initialize Lucide Icons --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>