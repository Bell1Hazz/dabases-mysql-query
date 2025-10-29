@extends('admin.layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="page-header">
    <h1>Edit Category</h1>
    <a href="{{ route('admin.categories.index') }}" class="btn-secondary">
        <i data-lucide="arrow-left"></i>
        Back
    </a>
</div>

<div class="form-card">
    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="admin-form">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required>
            @error('name')
                <small class="error-text">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" id="slug" name="slug" value="{{ old('slug', $category->slug) }}" required>
            @error('slug')
                <small class="error-text">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Description (optional)</label>
            <textarea id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
        </div>

        <div class="form-group">
            <label for="color">Color</label>
            <div style="display: flex; align-items: center; gap: 10px;">
                <input type="color" id="color" name="color" value="{{ old('color', $category->color) }}">
                <span>{{ old('color', $category->color) }}</span>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i data-lucide="save"></i>
                Update Category
            </button>
        </div>
    </form>
</div>
@endsection
