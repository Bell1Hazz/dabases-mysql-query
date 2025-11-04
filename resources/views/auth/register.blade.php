<x-guest-layout>
    <div class="auth-card">
        <div class="auth-card-header">
            <div class="auth-icon">
                <i data-lucide="user-plus"></i>
            </div>
            <h1>Create Account</h1>
            <p>Join ArticleHub community today</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="auth-form">
            @csrf

            {{-- Name --}}
            <div class="form-group">
                <label for="name" class="form-label">
                    <i data-lucide="user"></i>
                    <span>Full Name</span>
                </label>
                <input 
                    id="name" 
                    type="text" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required 
                    autofocus
                    class="form-input @error('name') error @enderror"
                    placeholder="John Doe"
                >
                @error('name')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            {{-- Email --}}
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
                        class="form-input @error('password') error @enderror"
                        placeholder="Minimum 8 characters"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <i data-lucide="eye" id="passwordIcon"></i>
                    </button>
                </div>
                @error('password')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="form-group">
                <label for="password_confirmation" class="form-label">
                    <i data-lucide="shield-check"></i>
                    <span>Confirm Password</span>
                </label>
                <div class="password-wrapper">
                    <input 
                        id="password_confirmation" 
                        type="password" 
                        name="password_confirmation" 
                        required
                        class="form-input"
                        placeholder="Re-enter your password"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                        <i data-lucide="eye" id="confirmIcon"></i>
                    </button>
                </div>
            </div>

            {{-- Submit --}}
            <div class="form-actions">
                <button type="submit" class="btn-login">
                    <i data-lucide="user-plus"></i>
                    <span>Create Account</span>
                </button>
            </div>

            {{-- Login Link --}}
            <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                <span style="color: var(--text-secondary); font-size: 0.875rem;">Already have an account?</span>
                <a href="{{ route('login') }}" style="color: var(--primary-color); font-weight: 600; text-decoration: none; margin-left: 0.5rem;">
                    Sign In
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId === 'password' ? 'passwordIcon' : 'confirmIcon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            
            lucide.createIcons();
        }
    </script>
    @endpush
</x-guest-layout>