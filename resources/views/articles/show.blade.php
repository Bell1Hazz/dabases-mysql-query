@extends('layouts.app')

@section('title', $article->title . ' - ArticleHub')

@section('content')
<section class="articles-section" style="padding-top: 100px;">
    <div class="container">
        <!-- Back Button -->
        <div style="margin-bottom: 2rem;">
            <a href="{{ route('articles.index') }}" class="back-btn" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 8px; text-decoration: none; color: var(--text-primary); font-weight: 600; transition: all 0.3s ease;">
                <span>←</span> Back to Articles
            </a>
        </div>

        <article class="article-detail" style="max-width: 900px; margin: 0 auto;">
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
                <div class="detail-info">
                    <span class="detail-author">
                        <i data-lucide="user"></i>
                        {{ $article->user->name }}
                    </span>
                    <span class="detail-date">
                        <i data-lucide="calendar"></i>
                        {{ $article->date->format('d F Y') }}
                    </span>
                    <span class="detail-time">
                        <i data-lucide="clock"></i>
                        {{ $article->read_time }}
                    </span>
                    <span class="detail-views">
                        <i data-lucide="eye"></i>
                        {{ number_format($article->views) }} views
                    </span>
                </div>


            <!-- Featured Image -->
        <div style="margin: 2rem 0; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);">
             <img 
        src="{{ asset('storage/' . $article->image) }}" 
        alt="{{ $article->title }}" 
        style="width: 100%; height: auto; display: block;" >
        </div>

            <!-- Article Summary (Highlighted) -->
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
                <div class="detail-share">
                    <span>Share this article:</span>
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

            <!-- Edit Button (for admin) -->
            <a href="{{ route('articles.edit', $article) }}" style="...">
                <i data-lucide="edit"></i>
                <span>Edit Article</span>
            </a>

            <!-- Comments Section -->
            @if($article->comments->count() > 0)
            <div style="margin-top: 4rem;">
                <h3 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 2rem;">
                    💬 Comments ({{ $article->comments->count() }})
                </h3>
                
                @foreach($article->comments()->whereNull('parent_id')->get() as $comment)
                    <div style="padding: 1.5rem; background: var(--bg-secondary); border-radius: 12px; margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                                {{ substr($comment->user->name, 0, 1) }}
                            </div>
                            <div>
                                <strong style="color: var(--text-primary); font-size: 1rem;">{{ $comment->user->name }}</strong>
                                <span style="display: block; color: var(--text-secondary); font-size: 0.875rem;">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        <p style="color: var(--text-primary); line-height: 1.6; margin: 0;">{{ $comment->content }}</p>
                        
                        @if($comment->replies->count() > 0)
                            <div style="margin-left: 3rem; margin-top: 1rem; border-left: 3px solid var(--border-color); padding-left: 1.5rem;">
                                @foreach($comment->replies as $reply)
                                    <div style="padding: 1rem; background: var(--bg-primary); border-radius: 8px; margin-bottom: 0.75rem;">
                                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                                            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--bg-secondary); display: flex; align-items: center; justify-content: center; color: var(--text-primary); font-weight: 700; font-size: 0.875rem;">
                                                {{ substr($reply->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <strong style="color: var(--text-primary); font-size: 0.9rem;">{{ $reply->user->name }}</strong>
                                                <span style="display: block; color: var(--text-secondary); font-size: 0.8rem;">
                                                    {{ $reply->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                        <p style="color: var(--text-primary); line-height: 1.6; margin: 0; font-size: 0.95rem;">{{ $reply->content }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            @endif
        </article>
    </div>
</section>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush

@push('styles')
<style>
/* Responsive untuk mobile */
@media (max-width: 768px) {
    h1 {
        font-size: 2rem !important;
    }
    
    .article-detail {
        padding: 0 1rem;
    }
}

/* Hover effect untuk share buttons */
a[href="#"]:hover {
    background: var(--primary-color) !important;
    transform: translateY(-3px);
}

/* Back button hover */
.back-btn:hover {
    background: var(--bg-primary) !important;
    border-color: var(--primary-color) !important;
    transform: translateX(-5px);
}
</style>
@endpush