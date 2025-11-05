<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Artiqle') }} - @yield('title', 'Authentication')</title>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    {{-- Header (Minimal for Auth Pages) --}}
<header class="auth-header">
    <div class="container" style="display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; padding: 0 2rem;">
        {{-- LEFT: Back Button --}}
        <div style="justify-self: start;">
            <a href="{{ route('articles.index') }}" class="back-link">
                <i data-lucide="arrow-left"></i>
                <span>Back to Home</span>
            </a>
        </div>
        
        {{-- CENTER: Logo --}}
        <div style="justify-self: center;">
            <a href="{{ route('articles.index') }}" class="logo-link">
                <img src="{{ asset('images/logo-articlehub.png') }}" alt="Artiqle" class="logo-img">
                <span class="logo-text">Artiqle</span>
            </a>
        </div>
        
        {{-- RIGHT: Theme Toggle --}}
        <div style="justify-self: end;">
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
        <p>© 2025 Artiqle. All rights reserved.</p>
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