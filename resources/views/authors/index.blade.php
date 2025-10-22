@extends('layouts.app')

@section('title', 'Our Authors - ArticleHub')

@section('content')
<section class="articles-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Our Authors</h2>
            <p style="text-align: center; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">
                Meet the talented writers behind ArticleHub
            </p>
        </div>

        <div class="articles-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            @foreach($authors as $author)
                <div class="article-card" style="text-align: center;">
                    <div style="padding: 2rem;">
                        <img 
                            src="{{ $author->avatarUrl }}" 
                            alt="{{ $author->name }}"
                            style="width: 100px; height: 100px; border-radius: 50%; margin: 0 auto 1rem; border: 4px solid var(--primary-color);"
                        >
                        <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                            {{ $author->name }}
                        </h3>
                        
                        <div style="display: flex; justify-content: center; gap: 2rem; margin: 1.5rem 0;">
                            <div>
                                <div style="font-size: 2rem; font-weight: 700; color: var(--primary-color);">
                                    {{ $author->articles_count }}
                                </div>
                                <div style="font-size: 0.875rem; color: var(--text-secondary);">
                                    Articles
                                </div>
                            </div>
                            <div>
                                <div style="font-size: 2rem; font-weight: 700; color: var(--accent-color);">
                                    {{ number_format($author->articles->sum('views')) }}
                                </div>
                                <div style="font-size: 0.875rem; color: var(--text-secondary);">
                                    Total Views
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('authors.show', $author) }}" class="read-more-btn" style="display: inline-flex;">
                            <span>View Profile</span>
                            <i data-lucide="arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush