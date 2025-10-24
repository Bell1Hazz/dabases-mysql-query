@extends('admin.layouts.admin')

@section('title', 'Edit Article')

@section('content')
<div class="page-header">
    <h1>Edit Article</h1>
</div>

<div class="form-card">
    <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" value="{{ old('title', $article->title) }}" required>
                @error('title')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="user_id">Author *</label>
                <select id="user_id" name="user_id" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $article->user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')<span class="error-text">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')<span class="error-text">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="date">Date *</label>
                <input type="date" id="date" name="date" value="{{ old('date', $article->date->format('Y-m-d')) }}" required>
                @error('date')<span class="error-text">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-group">
            <label for="read_time">Read Time *</label>
            <input type="text" id="read_time" name="read_time" value="{{ old('read_time', $article->read_time) }}" required>
            @error('read_time')<span class="error-text">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="image">Image (Leave empty to keep current)</label>
            @if($article->image)
                <div style="margin-bottom: 1rem;">
                    <img src="{{ asset('storage/' . $article->image) }}" style="max-width: 300px; border-radius: 8px;">
                </div>
            @endif
            <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
            @error('image')<span class="error-text">{{ $message }}</span>@enderror
            <div id="imagePreview" style="display: none; margin-top: 1rem;">
                <img id="preview" style="max-width: 300px; border-radius: 8px;">
            </div>
        </div>

        <div class="form-group">
            <label>Tags (Optional)</label>
            <div class="tag-checkboxes">
                @foreach($tags as $tag)
                    <label class="tag-checkbox">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $article->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
                        <span>{{ $tag->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <label for="summary">Summary *</label>
            <textarea id="summary" name="summary" rows="3" required>{{ old('summary', $article->summary) }}</textarea>
            @error('summary')<span class="error-text">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="content">Content *</label>
            <textarea id="content" name="content" rows="12" required>{{ old('content', $article->content) }}</textarea>
            @error('content')<span class="error-text">{{ $message }}</span>@enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.articles.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <i data-lucide="save"></i>
                Update Article
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function previewImage(event) {
    const preview = document.getElementById('imagePreview');
    const img = document.getElementById('preview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection