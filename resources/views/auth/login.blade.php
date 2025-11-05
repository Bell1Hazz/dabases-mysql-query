<x-guest-layout>
    <div class="auth-card">
        {{-- Logo/Header --}}
        <div class="auth-card-header">
            <div class="auth-icon">
                <i data-lucide="lock-keyhole"></i>
            </div>
            <h1>Welcome Back!</h1>
            <p>Login to your Artiqle account</p>
        </div>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                <i data-lucide="check-circle"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf

            {{-- Email Address --}}
            <div class="form-group">
                <label for="email" class="form-label">
                    <i data-lucide="mail"></i>
                    <span>Email Address</span>
                </label>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    autocomplete="username"
                    class="form-input @error('email') error @enderror"
                    placeholder="your.email@example.com"
                >
                @error('email')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label for="password" class="form-label">
                    <i data-lucide="key-round"></i>
                    <span>Password</span>
                </label>
                <div class="password-wrapper">
                    <input 
                        id="password" 
                        type="password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        class="form-input @error('password') error @enderror"
                        placeholder="Enter your password"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i data-lucide="eye" id="passwordIcon"></i>
                    </button>
                </div>
                @error('password')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" id="remember_me" name="remember">
                    <span>Remember me for 30 days</span>
                </label>
            </div>

            {{-- Submit & Forgot Password --}}
            <div class="form-actions">
                <button type="submit" class="btn-login">
                    <i data-lucide="log-in"></i>
                    <span>Sign In</span>
                </button>
            </div>

            @if (Route::has('password.request'))
                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="{{ route('password.request') }}" class="link-secondary">
                        <i data-lucide="help-circle" style="width: 14px; height: 14px;"></i>
                        Forgot your password?
                    </a>
                </div>
            @endif

            {{-- Register Link --}}
            @if (Route::has('register'))
                <div style="text-align: center; margin-top: 1rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                    <span style="color: var(--text-secondary); font-size: 0.875rem;">Don't have an account?</span>
                    <a href="{{ route('register') }}" style="color: var(--primary-color); font-weight: 600; text-decoration: none; margin-left: 0.5rem;">
                        Sign Up
                    </a>
                </div>
            @endif
        </form>
    </div>

    {{-- Demo Credentials Info --}}
    <div class="demo-info">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
            <i data-lucide="info" style="width: 18px; height: 18px;"></i>
            <strong>Demo Credentials:</strong>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.875rem;">
            <div>
                <strong>Admin:</strong><br>
                admin@articlehub.com<br>
                Password: admin123
            </div>
            <div>
                <strong>Author:</strong><br>
                sarah@articlehub.com<br>
                Password: password
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                passwordIcon.setAttribute('data-lucide', 'eye');
            }
            
            lucide.createIcons();
        }
    </script>
    @endpush
</x-guest-layout>