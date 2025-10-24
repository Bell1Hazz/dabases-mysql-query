@extends('admin.layouts.admin')

@section('title', 'Edit Author')

@section('content')
<div class="page-header">
    <h1>Edit Author</h1>
</div>

<div class="form-card">
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label for="name">Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')<span class="error-text">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="password">New Password (Leave empty to keep current)</label>
                <input type="password" id="password" name="password">
                @error('password')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation">
            </div>
        </div>

        <div class="form-group">
            <label for="role">Role *</label>
            <select id="role" name="role" required>
                <option value="author" {{ old('role', $user->role) === 'author' ? 'selected' : '' }}>Author</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
            </select>
            @error('role')<span class="error-text">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="bio">Bio (Optional)</label>
            <textarea id="bio" name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
            @error('bio')<span class="error-text">{{ $message }}</span>@enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <i data-lucide="save"></i>
                Update Author
            </button>
        </div>
    </form>
</div>
@endsection