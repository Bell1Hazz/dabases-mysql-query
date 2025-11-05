@extends('layouts.app')

@section('title', 'Create New Article - Artiqle')

@section('content')
<section class="form-section">
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h1>✍️ Create New Article</h1>
                <p>Share your knowledge with the world</p>
            </div>

            {{-- Validation Summary --}}
            @if ($errors->any())
                <div class="validation-summary">
                    <h4>❌ Terdapat {{ $errors->count() }} kesalahan pada form:</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data" class="article-form" id="articleForm">
                @csrf
                
                <div class="form-grid">
                    {{-- Show Current Author (Read-only) --}}
                    <div class="form-group full-width">
                        <label class="form-label">Author</label>
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 8px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div>
                                <strong style="color: var(--text-primary); display: block;">{{ auth()->user()->name }}</strong>
                                <span style="font-size: 0.875rem; color: var(--text-secondary);">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                        <small class="form-hint">Article will be published under your name</small>
                    </div>

                    {{-- Title --}}
                    <div class="form-group full-width">
                        <label for="title" class="form-label">Article Title *</label>
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            class="form-input @error('title') error @enderror" 
                            placeholder="Enter an engaging title..."
                            value="{{ old('title') }}"
                            required
                        >
                        @error('title')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div class="form-group">
                        <label for="category_id" class="form-label">Category *</label>
                        <select 
                            id="category_id" 
                            name="category_id" 
                            class="form-select @error('category_id') error @enderror"
                            required
                        >
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Date --}}
                    <div class="form-group">
                        <label for="date" class="form-label">Publication Date *</label>
                        <input 
                            type="date" 
                            id="date" 
                            name="date" 
                            class="form-input @error('date') error @enderror" 
                            value="{{ old('date', date('Y-m-d')) }}"
                            required
                        >
                        @error('date')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Read Time --}}
                    <div class="form-group full-width">
                        <label for="read_time" class="form-label">Read Time *</label>
                        <input 
                            type="text" 
                            id="read_time" 
                            name="read_time" 
                            class="form-input @error('read_time') error @enderror" 
                            placeholder="e.g., 5 min read"
                            value="{{ old('read_time') }}"
                            required
                        >
                        @error('read_time')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Image Upload --}}
                    <div class="form-group full-width">
                        <label for="image" class="form-label">Article Image *</label>
                        <div class="image-upload-wrapper">
                            <input 
                                type="file" 
                                id="image" 
                                name="image" 
                                class="form-input-file @error('image') error @enderror" 
                                accept="image/jpeg,image/png,image/jpg,image/webp"
                                onchange="previewImage(event)"
                                required
                            >
                            <label for="image" class="file-upload-label">
                                <span class="file-upload-icon">📁</span>
                                <span class="file-upload-text" id="fileNameDisplay">Choose an image...</span>
                                <span class="file-upload-btn">Browse</span>
                            </label>
                            
                            {{-- Image Preview --}}
                            <div id="imagePreview" class="image-preview" style="display: none;">
                                <img id="previewImg" src="" alt="Preview">
                                <button type="button" onclick="removeImage()" class="remove-preview-btn">✕ Remove</button>
                            </div>
                            
                            <small class="form-hint">
                                Supported: JPG, PNG, WEBP | Max size: 2MB | Recommended: 1200x600px
                            </small>
                        </div>
                        @error('image')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tags (Checkbox) --}}
                    <div class="form-group full-width">
                        <label class="form-label">Tags (Optional)</label>
                        <div class="tag-checkboxes">
                            @foreach($tags as $tag)
                                <label class="tag-checkbox">
                                    <input 
                                        type="checkbox" 
                                        name="tags[]" 
                                        value="{{ $tag->id }}"
                                        {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
                                    >
                                    <span>{{ $tag->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <small class="form-hint">Select multiple tags by clicking the checkboxes</small>
                        @error('tags')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Summary --}}
                    <div class="form-group full-width">
                        <label for="summary" class="form-label">Summary *</label>
                        <textarea 
                            id="summary" 
                            name="summary" 
                            rows="3" 
                            class="form-textarea @error('summary') error @enderror" 
                            placeholder="Write a brief summary of your article..."
                            required
                        >{{ old('summary') }}</textarea>
                        @error('summary')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Content --}}
                    <div class="form-group full-width">
                        <label for="content" class="form-label">Article Content *</label>
                        <textarea 
                            id="content" 
                            name="content" 
                            rows="12" 
                            class="form-textarea @error('content') error @enderror" 
                            placeholder="Write your full article content here..."
                            required
                        >{{ old('content') }}</textarea>
                        @error('content')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions">
                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.articles.index') : route('author.dashboard') }}" class="btn-secondary">
                        <i data-lucide="arrow-left"></i>
                        <span>Cancel</span>
                    </a>
                    <button type="submit" class="btn-primary">
                        <i data-lucide="send"></i>
                        <span>Publish Article</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
/* Tag Checkbox Styling */
.tag-checkboxes {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 0.75rem;
    padding: 1rem;
    background: var(--bg-secondary);
    border: 2px solid var(--border-color);
    border-radius: 8px;
}

.tag-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: var(--bg-primary);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.tag-checkbox:hover {
    background: var(--hover-bg);
    border-color: var(--primary-color);
}

.tag-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--primary-color);
}

.tag-checkbox:has(input:checked) {
    background: rgba(37, 99, 235, 0.1);
    border-color: var(--primary-color);
}

.tag-checkbox span {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary);
}

.tag-checkbox:has(input:checked) span {
    color: var(--primary-color);
    font-weight: 600;
}
</style>
@endpush

@push('scripts')
<script>
// Image Preview Function
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    
    if (file) {
        // Check file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            event.target.value = '';
            return;
        }

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
    fileNameDisplay.textContent = 'Choose an image...';
}

// Initialize Lucide Icons
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endpush