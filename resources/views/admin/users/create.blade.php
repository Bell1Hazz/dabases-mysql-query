@extends('admin.layouts.admin')

@section('title', 'Add New Author - Admin')

@section('content')
<div class="page-header">
    <h1>Add New Author</h1>
</div>

<div class="form-card">
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label for="name">Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')<span class="error-text">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required>
                @error('password')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
        </div>

        <div class="form-group">
            <label for="role">Role *</label>
            <select id="role" name="role" required>
                <option value="author" {{ old('role') === 'author' ? 'selected' : '' }}>Author</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
            </select>
            @error('role')<span class="error-text">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="bio">Bio (Optional)</label>
            <textarea id="bio" name="bio" rows="4">{{ old('bio') }}</textarea>
            @error('bio')<span class="error-text">{{ $message }}</span>@enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Create Author</button>
        </div>
    </form>
</div>
@endsection