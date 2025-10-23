@extends('layouts.app')

@section('title', 'Edit Article - ArticleHub')

@section('content')
<section class="form-section">
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h1>✏️ Edit Article</h1>
                <p>Update your article information</p>
            </div>

            <form action="{{ route('articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="article-form">
                @csrf
                @method('PUT')
                
                <div class="form-grid">
                    <!-- Title -->
                    <div class="form-group full-width">
                        <label for="title" class="form-label">Article Title *</label>
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            class="form-input @error('title') error @enderror" 
                            placeholder="Enter an engaging title..."
                            value="{{ old('title', $article->title) }}"
                            required
                        >
                        @error('title')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Author -->
                    <div class="form-group">
                        <label for="user_id" class="form-label">Author *</label>
                        <select 
                            id="user_id" 
                            name="user_id" 
                            class="form-select @error('user_id') error @enderror"
                            required
                        >
                            @foreach(\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $article->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div class="form-group">
                        <label for="date" class="form-label">Publication Date *</label>
                        <input 
                            type="date" 
                            id="date" 
                            name="date" 
                            class="form-input @error('date') error @enderror" 
                            value="{{ old('date', $article->date->format('Y-m-d')) }}"
                            required
                        >
                        @error('date')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="form-group">
                        <label for="category_id" class="form-label">Category *</label>
                        <select 
                            id="category_id" 
                            name="category_id" 
                            class="form-select @error('category_id') error @enderror"
                            required
                        >
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Read Time -->
                    <div class="form-group">
                        <label for="read_time" class="form-label">Read Time *</label>
                        <input 
                            type="text" 
                            id="read_time" 
                            name="read_time" 
                            class="form-input @error('read_time') error @enderror" 
                            placeholder="e.g., 5 min read"
                            value="{{ old('read_time', $article->read_time) }}"
                            required
                        >
                        @error('read_time')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- 🖼️ IMAGE UPLOAD (with current image preview) -->
                    <div class="form-group full-width">
                        <label for="image" class="form-label">Article Image</label>
                        
                        <!-- Current Image Preview -->
                        @if($article->image)
                        <div class="current-image-preview" style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Current Image:</label>
                            <img 
                                src="{{ asset('storage/' . $article->image) }}" 
                                alt="Current article image"
                                style="max-width: 300px; border-radius: 8px; border: 2px solid var(--border-color);"
                            >
                        </div>
                        @endif
                        
                        <div class="image-upload-wrapper">
                            <input 
                                type="file" 
                                id="image" 
                                name="image" 
                                class="form-input-file @error('image') error @enderror" 
                                accept="image/jpeg,image/png,image/jpg,image/webp"
                                onchange="previewImage(event)"
                            >
                            <label for="image" class="file-upload-label">
                                <span class="file-upload-icon">📁</span>
                                <span class="file-upload-text" id="fileNameDisplay">Upload new image...</span>
                                <span class="file-upload-btn">Browse</span>
                            </label>
                            
                            <!-- New Image Preview -->
                            <div id="imagePreview" class="image-preview" style="display: none;">
                                <img id="previewImg" src="" alt="Preview">
                                <button type="button" onclick="removeImage()" class="remove-preview-btn">✕ Remove</button>
                            </div>
                            
                            <small class="form-hint">
                                Leave empty to keep current image | Supported: JPG, PNG, WEBP | Max: 2MB
                            </small>
                        </div>
                        @error('image')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Tags (Checkbox Style) -->
                    <div class="form-group full-width">
                        <label class="form-label">Tags (Optional)</label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 0.75rem; padding: 1rem; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 8px;">
                            @foreach($tags as $tag)
                                <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background: var(--bg-primary); border-radius: 6px; cursor: pointer; transition: all 0.3s ease; border: 2px solid transparent;">
                                    <input 
                                        type="checkbox" 
                                        name="tags[]" 
                                        value="{{ $tag->id }}"
                                        {{ in_array($tag->id, old('tags', $article->tags->pluck('id')->toArray())) ? 'checked' : '' }}
                                        style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary-color);"
                                    >
                                    <span style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">
                                        {{ $tag->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <small class="form-hint">Select multiple tags by clicking the checkboxes</small>
                        @error('tags')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- Summary -->
                    <div class="form-group full-width">
                        <label for="summary" class="form-label">Summary *</label>
                        <textarea 
                            id="summary" 
                            name="summary" 
                            rows="3" 
                            class="form-textarea @error('summary') error @enderror" 
                            placeholder="Write a brief summary of your article..."
                            required
                        >{{ old('summary', $article->summary) }}</textarea>
                        @error('summary')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div class="form-group full-width">
                        <label for="content" class="form-label">Article Content *</label>
                        <textarea 
                            id="content" 
                            name="content" 
                            rows="12" 
                            class="form-textarea @error('content') error @enderror" 
                            placeholder="Write your full article content here..."
                            required
                        >{{ old('content', $article->content) }}</textarea>
                        @error('content')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('articles.show', $article) }}" class="btn-secondary">
                        <span>←</span> Cancel
                    </a>
                    <button type="submit" class="btn-primary">
                        <span>💾</span> Update Article
                    </button>
                </div>
            </form>

            <!-- Delete Section -->
            <div class="danger-zone">
                <h3>🗑️ Danger Zone</h3>
                <p>Once you delete this article, there is no going back. Please be certain.</p>
                <form action="{{ route('articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this article? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">
                        Delete Article
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
// Image Preview Function
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            fileNameDisplay.textContent = file.name;
        }
        
        reader.readAsDataURL(file);
    }
}

// Remove Image Function
function removeImage() {
    const fileInput = document.getElementById('image');
    const preview = document.getElementById('imagePreview');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    
    fileInput.value = '';
    preview.style.display = 'none';
    fileNameDisplay.textContent = 'Upload new image...';
}
</script>
@endpush