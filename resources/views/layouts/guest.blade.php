<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'ArticleHub') }} - @yield('title', 'Authentication')</title>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    {{-- Header (Minimal for Auth Pages) --}}
    <header class="auth-header">
        <div class="container">
            <a href="{{ route('articles.index') }}" class="logo-link">
                <img src="{{ asset('images/logo-articlehub.png') }}" alt="ArticleHub" class="logo-img">
                <span class="logo-text">ArticleHub</span>
            </a>
            
            <div style="display: flex; align-items: center; gap: 1rem;">
                <a href="{{ route('articles.index') }}" style="color: var(--text-secondary); text-decoration: none; font-weight: 500; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                    Back to Home
                </a>
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme"></button>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="auth-main">
        <div class="auth-container">
            {{ $slot }}
        </div>
    </main>

    {{-- Footer (Minimal) --}}
    <footer class="auth-footer">
        <p>© 2025 ArticleHub. All rights reserved.</p>
    </footer>

    <script defer src="{{ asset('js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>