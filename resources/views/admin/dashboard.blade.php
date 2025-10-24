@extends('admin.layouts.admin')

@section('title', 'Dashboard - Admin Panel')

@section('content')
<div class="dashboard-header">
    <h1>Dashboard</h1>
    <p>Welcome back, {{ auth()->user()->name }}!</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #3b82f6;">
            <i data-lucide="newspaper"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['total_articles'] }}</div>
            <div class="stat-label">Total Articles</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #10b981;">
            <i data-lucide="users"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['total_authors'] }}</div>
            <div class="stat-label">Total Authors</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #f59e0b;">
            <i data-lucide="eye"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ number_format($stats['total_views']) }}</div>
            <div class="stat-label">Total Views</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #8b5cf6;">
            <i data-lucide="message-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['total_comments'] }}</div>
            <div class="stat-label">Total Comments</div>
        </div>
    </div>
</div>

<!-- Dashboard Grid -->
<div class="dashboard-grid">
    <!-- Recent Articles -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3>Recent Articles</h3>
            <a href="{{ route('admin.articles.index') }}" class="btn-link">View All</a>
        </div>
        <div class="article-list">
            @foreach($recentArticles as $article)
                <div class="article-item">
                    <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="article-thumb">
                    <div class="article-info">
                        <h4>{{ Str::limit($article->title, 50) }}</h4>
                        <div class="article-meta">
                            <span><i data-lucide="user"></i> {{ $article->user->name }}</span>
                            <span><i data-lucide="eye"></i> {{ number_format($article->views) }}</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.articles.edit', $article) }}" class="btn-icon">
                        <i data-lucide="edit"></i>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Top Authors -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3>Top Authors</h3>
            <a href="{{ route('admin.users.index') }}" class="btn-link">View All</a>
        </div>
        <div class="author-list">
            @foreach($topAuthors as $author)
                <div class="author-item">
                    <div class="author-avatar">{{ substr($author->name, 0, 1) }}</div>
                    <div class="author-info">
                        <h4>{{ $author->name }}</h4>
                        <span>{{ $author->articles_count }} articles</span>
                    </div>
                    <a href="{{ route('admin.users.edit', $author) }}" class="btn-icon">
                        <i data-lucide="edit"></i>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
