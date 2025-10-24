@extends('layouts.app')

@section('title', $article->title . ' - ArticleHub')

@section('content')
<section class="articles-section">
    <div class="container">
        <!-- Back Button -->
        <div style="margin-bottom: 2rem;">
            <a href="{{ route('articles.index') }}" class="back-btn">
                <i data-lucide="arrow-left"></i>
                <span>Back to Articles</span>
            </a>
        </div>

        <article class="article-detail">
            <!-- Category Badge -->
            <div style="margin-bottom: 1rem;">
                <span style="display: inline-block; padding: 0.5rem 1rem; background: {{ $article->category->color }}; color: white; border-radius: 20px; font-size: 0.875rem; font-weight: 600;">
                    {{ $article->category->name }}
                </span>
            </div>

            <!-- Article Title -->
            <h1 style="font-size: 3rem; font-weight: 800; color: var(--text-primary); margin-bottom: 1.5rem; line-height: 1.2;">
                {{ $article->title }}
            </h1>

            <!-- Article Meta Info -->
            <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 2rem; color: var(--text-secondary); font-size: 1rem;">
                <span style="display: flex; align-items: center; gap: 0.5rem;">
                    <i data-lucide="user"></i>
                    {{ $article->user->name }}
                </span>
                <span style="display: flex; align-items: center; gap: 0.5rem;">
                    <i data-lucide="calendar"></i>
                    {{ $article->date->format('d F Y') }}
                </span>
                <span style="display: flex; align-items: center; gap: 0.5rem;">
                    <i data-lucide="clock"></i>
                    {{ $article->read_time }}
                </span>
                <span style="display: flex; align-items: center; gap: 0.5rem;">
                    <i data-lucide="eye"></i>
                    {{ number_format($article->views) }} views
                </span>
            </div>

            <!-- Featured Image -->
            <div style="margin: 2rem 0; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);">
                <img 
                    src="{{ asset('storage/' . $article->image) }}" 
                    alt="{{ $article->title }}" 
                    style="width: 100%; height: auto; display: block;"
                >
            </div>

            <!-- Article Summary -->
            <div style="padding: 1.5rem; background: var(--bg-secondary); border-left: 4px solid {{ $article->category->color }}; border-radius: 8px; margin: 2rem 0;">
                <p style="font-size: 1.25rem; color: var(--text-primary); font-weight: 500; line-height: 1.8; margin: 0;">
                    {{ $article->summary }}
                </p>
            </div>

            <!-- Article Content -->
            <div style="font-size: 1.125rem; line-height: 1.8; color: var(--text-primary); margin: 2rem 0;">
                {!! nl2br(e($article->content)) !!}
            </div>

            <!-- Tags & Share -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 2rem; border-top: 2px solid var(--border-color); margin-top: 3rem; flex-wrap: wrap; gap: 1.5rem;">
                <!-- Tags -->
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    @foreach($article->tags as $tag)
                        <span style="padding: 0.5rem 1rem; background: var(--bg-secondary); color: var(--text-primary); border-radius: 20px; font-size: 0.875rem; font-weight: 600;">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>

                <!-- Share Buttons -->
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="color: var(--text-secondary); font-weight: 600;">Share:</span>
                    <a href="#" class="share-btn">
                        <i data-lucide="facebook"></i>
                    </a>
                    <a href="#" class="share-btn">
                        <i data-lucide="twitter"></i>
                    </a>
                    <a href="#" class="share-btn">
                        <i data-lucide="linkedin"></i>
                    </a>
                </div>
            </div>

            <!-- Edit Button (Admin Only) -->

            @auth
                @if(auth()->user()->role === 'admin')
                    <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                        <a href="{{ route('admin.articles.edit', $article) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 2rem; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                            <i data-lucide="edit"></i>
                            <span>Edit Article</span>
                        </a>
                    </div>
                @endif
            @endauth


            <!-- Comments Section -->
            <div class="comments-wrapper" style="margin-top: 4rem;">
                <h3 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-lucide="message-circle"></i>
                    Comments ({{ $article->comments->count() }})
                </h3>

                <!-- Comment Form -->
                <div class="comment-form-wrapper" style="background: var(--bg-secondary); padding: 2rem; border-radius: 12px; margin-bottom: 3rem;">
                    <h4 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem;">
                        Leave a Comment
                    </h4>

                    <form action="{{ route('articles.comments.store', $article) }}" method="POST" id="commentForm">
                        @csrf
                        
                        <!-- User Selection -->
                        <div style="margin-bottom: 1.5rem;">
                            <label for="user_id" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                                Post as *
                            </label>
                            <select 
                                id="user_id" 
                                name="user_id" 
                                required
                                style="width: 100%; padding: 0.875rem 1rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 1rem; background: var(--bg-primary); color: var(--text-primary);"
                            >
                                <option value="">Select user...</option>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <span style="display: block; margin-top: 0.5rem; font-size: 0.875rem; color: #ef4444;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Comment Content -->
                        <div style="margin-bottom: 1.5rem;">
                            <label for="content" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">
                                Your Comment *
                            </label>
                            <textarea 
                                id="content" 
                                name="content" 
                                rows="4" 
                                required
                                placeholder="Share your thoughts..."
                                style="width: 100%; padding: 0.875rem 1rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 1rem; font-family: inherit; background: var(--bg-primary); color: var(--text-primary); resize: vertical;"
                            >{{ old('content') }}</textarea>
                            @error('content')
                                <span style="display: block; margin-top: 0.5rem; font-size: 0.875rem; color: #ef4444;">{{ $message }}</span>
                            @enderror
                            <small style="display: block; margin-top: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">
                                <span id="charCount">0</span>/1000 characters
                            </small>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            style="padding: 0.875rem 2rem; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 0.5rem;"
                        >
                            <i data-lucide="send"></i>
                            Post Comment
                        </button>
                    </form>
                </div>

                <!-- Comments List -->
                @if($article->comments->count() > 0)
                    <div class="comments-list">
                        @foreach($article->comments()->whereNull('parent_id')->latest()->get() as $comment)
                            <div class="comment-item" style="background: var(--bg-secondary); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem;">
                                <!-- Comment Header -->
                                <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 1rem;">
                                    <!-- Avatar -->
                                    <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.25rem; flex-shrink: 0;">
                                        {{ substr($comment->user->name, 0, 1) }}
                                    </div>

                                    <!-- Comment Info -->
                                    <div style="flex: 1;">
                                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                            <div>
                                                <strong style="color: var(--text-primary); font-size: 1rem; display: block;">
                                                    {{ $comment->user->name }}
                                                </strong>
                                                <span style="color: var(--text-secondary); font-size: 0.875rem;">
                                                    <i data-lucide="clock" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i>
                                                    {{ $comment->created_at->diffForHumans() }}
                                                </span>
                                            </div>

                                            <!-- Delete Comment Button (FIXED!) -->
                                            <form action="{{ route('articles.comments.destroy', [$article, $comment]) }}" method="POST" onsubmit="return confirm('Delete this comment?')">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit"
                                                    style="background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 0.25rem; transition: color 0.3s ease;"
                                                    onmouseover="this.style.color='#ef4444'"
                                                    onmouseout="this.style.color='var(--text-secondary)'"
                                                >
                                                    <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Comment Content -->
                                        <p style="color: var(--text-primary); line-height: 1.6; margin: 0.75rem 0;">
                                            {{ $comment->content }}
                                        </p>

                                        <!-- Reply Button -->
                                        <button 
                                            onclick="toggleReplyForm({{ $comment->id }})"
                                            style="background: none; border: none; color: var(--primary-color); font-weight: 600; cursor: pointer; font-size: 0.875rem; padding: 0.5rem 0; display: inline-flex; align-items: center; gap: 0.5rem;"
                                        >
                                            <i data-lucide="corner-down-right" style="width: 16px; height: 16px;"></i>
                                            Reply
                                        </button>
                                    </div>
                                </div>

                                <!-- Reply Form (Hidden by default) -->
                                <div id="replyForm{{ $comment->id }}" style="display: none; margin-left: 4rem; margin-top: 1rem; padding: 1.5rem; background: var(--bg-primary); border-radius: 8px; border-left: 3px solid var(--primary-color);">
                                    <form action="{{ route('articles.comments.store', $article) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                        
                                        <!-- User select -->
                                        <div style="margin-bottom: 1rem;">
                                            <label for="reply_user_id_{{ $comment->id }}" style="display: block; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.875rem;">
                                                Reply as
                                            </label>
                                            <select 
                                                id="reply_user_id_{{ $comment->id }}" 
                                                name="user_id" 
                                                required
                                                style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.875rem; background: var(--bg-secondary); color: var(--text-primary);"
                                            >
                                                <option value="">Select user...</option>
                                                @foreach(\App\Models\User::all() as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <textarea 
                                            name="content" 
                                            rows="3" 
                                            required
                                            placeholder="Write your reply..."
                                            style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.875rem; font-family: inherit; background: var(--bg-secondary); color: var(--text-primary); resize: vertical; margin-bottom: 1rem;"
                                        ></textarea>

                                        <div style="display: flex; gap: 0.5rem;">
                                            <button 
                                                type="submit"
                                                style="padding: 0.5rem 1.5rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.5rem;"
                                            >
                                                <i data-lucide="send" style="width: 14px; height: 14px;"></i>
                                                Post Reply
                                            </button>
                                            <button 
                                                type="button"
                                                onclick="toggleReplyForm({{ $comment->id }})"
                                                style="padding: 0.5rem 1.5rem; background: var(--bg-secondary); color: var(--text-primary); border: 2px solid var(--border-color); border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.875rem;"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Replies -->
                                @if($comment->replies->count() > 0)
                                    <div style="margin-left: 4rem; margin-top: 1.5rem; border-left: 3px solid var(--border-color); padding-left: 1.5rem;">
                                        @foreach($comment->replies as $reply)
                                            <div style="padding: 1rem; background: var(--bg-primary); border-radius: 8px; margin-bottom: 1rem;">
                                                <div style="display: flex; align-items: start; gap: 1rem;">
                                                    <!-- Reply Avatar -->
                                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--bg-secondary); display: flex; align-items: center; justify-content: center; color: var(--text-primary); font-weight: 700; font-size: 0.875rem; flex-shrink: 0;">
                                                        {{ substr($reply->user->name, 0, 1) }}
                                                    </div>

                                                    <!-- Reply Content -->
                                                    <div style="flex: 1;">
                                                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                                            <div>
                                                                <strong style="color: var(--text-primary); font-size: 0.9rem; display: block;">
                                                                    {{ $reply->user->name }}
                                                                </strong>
                                                                <span style="color: var(--text-secondary); font-size: 0.8rem;">
                                                                    {{ $reply->created_at->diffForHumans() }}
                                                                </span>
                                                            </div>

                                                            <!-- Delete Reply (FIXED!) -->
                                                            <form action="{{ route('articles.comments.destroy', [$article, $reply]) }}" method="POST" onsubmit="return confirm('Delete this reply?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button 
                                                                    type="submit"
                                                                    style="background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 0.25rem;"
                                                                >
                                                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                                                </button>
                                                            </form>
                                                        </div>

                                                        <p style="color: var(--text-primary); line-height: 1.6; margin: 0; font-size: 0.95rem;">
                                                            {{ $reply->content }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                        <i data-lucide="message-circle" style="width: 48px; height: 48px; margin: 0 auto 1rem; opacity: 0.5;"></i>
                        <p style="font-size: 1.125rem; margin: 0;">No comments yet. Be the first to comment!</p>
                    </div>
                @endif
            </div>
        </article>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Character counter
    const contentTextarea = document.getElementById('content');
    const charCount = document.getElementById('charCount');

    if (contentTextarea && charCount) {
        contentTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            if (length > 1000) {
                charCount.style.color = '#ef4444';
            } else if (length > 800) {
                charCount.style.color = '#f59e0b';
            } else {
                charCount.style.color = '#10b981';
            }
        });
    }

    // Toggle reply form
    function toggleReplyForm(commentId) {
        const form = document.getElementById('replyForm' + commentId);
        if (form.style.display === 'none' || !form.style.display) {
            form.style.display = 'block';
            form.querySelector('textarea').focus();
            lucide.createIcons(); // Re-render icons in form
        } else {
            form.style.display = 'none';
        }
    }

    // Auto-hide alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert-container');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endpush