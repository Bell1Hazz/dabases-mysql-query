@extends('layouts.app')

@section('title', 'ArticleHub - Latest Articles')

@section('content')
<!-- Hero Section -->
<section class="hero" id="home">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Discover Amazing Articles</h1>
            <p class="hero-description">
                Temukan artikel-artikel menarik dan informatif dari berbagai topik yang sedang trending
            </p>
            <button class="cta-btn" onclick="document.getElementById('articles').scrollIntoView({behavior: 'smooth'})">
                <span>Jelajahi Artikel</span>
                <span>⬇</span>
            </button>
        </div>
    </div>
</section>

<!-- Articles Section -->
<section class="articles-section" id="articles">
    <div class="container">
       <div class="section-header">
    <h2 class="section-title">Latest Articles</h2>
    <div class="article-filters">
        <a href="{{ route('articles.index') }}" class="filter-btn {{ !request('category') ? 'active' : '' }}">
            All
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('articles.index', ['category' => $cat->slug]) }}" 
               class="filter-btn {{ request('category') == $cat->slug ? 'active' : '' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>
</div>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

<div class="articles-grid" id="articlesGrid">
    @forelse($articles as $article)
<div class="article-card fade-in">
    <div class="article-image">
<img 
    src="{{ asset('storage/' . $article->image) }}" 
    alt="{{ $article->title }}"
    width="400"
    height="250"
    loading="lazy"
    decoding="async"
>
    </div>
    <div class="article-content">
        <div class="article-meta">
            <span class="article-category">{{ $article->category->name }}</span>
            <span class="article-author">{{ $article->user->name }}</span>
            <span class="article-date">{{ $article->date->format('d F Y') }}</span>
        </div>
        <h3 class="article-title">{{ Str::limit($article->title, 60) }}</h3>
        <p class="article-summary">{{ Str::limit($article->summary, 150) }}</p>
        
        <div class="article-footer">
            <a href="{{ route('articles.show', $article) }}" class="read-more-btn">
                <span>Read More</span>
                <span>→</span>
            </a>
            <span class="read-time">
                <span>🕒</span>
                {{ $article->read_time }}
            </span>
        </div>
    </div>
</div>
        <div class="detail-info">
    <span class="detail-author">👤 {{ $article->user->name }}</span>
    <span class="detail-date">📅 {{ $article->date->format('d F Y') }}</span>
    <span class="detail-time">🕒 {{ $article->read_time }}</span>
    <span class="detail-views">👁️ {{ number_format($article->views) }} views</span>
</div>

{{-- Display tags --}}
<div class="detail-tags">
    @foreach($article->tags as $tag)
        <span class="tag">{{ $tag->name }}</span>
    @endforeach
</div>

{{-- Display comments --}}
<div class="comments-section">
    <h3>Comments ({{ $article->comments->count() }})</h3>
    @foreach($article->comments()->parent()->get() as $comment)
        <div class="comment">
            <strong>{{ $comment->user->name }}</strong>
            <p>{{ $comment->content }}</p>
            
            @foreach($comment->replies as $reply)
                <div class="comment-reply">
                    <strong>{{ $reply->user->name }}</strong>
                    <p>{{ $reply->content }}</p>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
    @empty
        <div class="no-results">
            <div style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.5;">🔍</div>
            <h3>No Articles Found</h3>
            <p>Try adjusting your search terms or filters</p>
        </div>
    @endforelse
</div>

        <!-- Pagination -->
        <div class="load-more-container">
            {{ $articles->links() }}
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section" id="about">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <h2>About ArticleHub</h2>
                <p>
                    ArticleHub adalah platform yang menyediakan artikel-artikel kekinian yang selalu update  
                    dari dunia Internet. Kami berkomitmen untuk memberikan konten yang informatif, 
                    menarik, dan mudah dipahami untuk semua pembaca.
                </p>
                <div class="about-stats">
                    <div class="stat-item">
                        <div class="stat-number">{{ \App\Models\Article::count() }}+</div>
                        <div class="stat-label">Articles</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">10K+</div>
                        <div class="stat-label">Readers</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Writers</div>
                    </div>
                </div>
            </div>
            <div class="about-image">
                <img src="{{ asset('images/logo-articlehub.png') }}" alt="About ArticleHub showing modern office workspace with people reading articles on digital devices">
            </div>
        </div>
    </div>
    <img 
    src="{{ asset('storage/' . $article->image) }}" 
    alt="{{ $article->title }}"
    width="400"
    height="250"
    loading="lazy"
    decoding="async"
    class="lazy-image"
    data-src="{{ asset('storage/' . $article->image) }}"
>
</section>
@endsection