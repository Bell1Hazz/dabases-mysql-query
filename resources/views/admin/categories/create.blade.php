@extends('admin.layouts.admin')

@section('title', 'Create Category')

@section('content')
<div class="page-header">
    <h1>Create New Category</h1>
</div>

<div class="form-card" style="max-width: 600px;">
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')<span class="error-text">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="color">Color *</label>
            <input type="color" id="color" name="color" value="{{ old('color', '#3b82f6') }}" required>
            @error('color')<span class="error-text">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3">{{ old('description') }}</textarea>
            @error('description')<span class="error-text">{{ $message }}</span>@enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <i data-lucide="save"></i>
                Create Category
            </button>
        </div>
    </form>
</div>
@endsection