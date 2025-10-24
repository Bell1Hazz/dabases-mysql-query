@extends('admin.layouts.admin')

@section('title', 'Create Tag')

@section('content')
<div class="page-header">
    <h1>Create New Tag</h1>
</div>

<div class="form-card" style="max-width: 600px;">
    <form action="{{ route('admin.tags.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Tag Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')<span class="error-text">{{ $message }}</span>@enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.tags.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <i data-lucide="save"></i>
                Create Tag
            </button>
        </div>
    </form>
</div>
@endsection