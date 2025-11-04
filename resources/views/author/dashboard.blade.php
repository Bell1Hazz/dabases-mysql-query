@extends('layouts.app')

@section('title', 'My Articles - Author Dashboard')

@section('content')
<section class="articles-section">
    <div class="container" style="max-width: 1000px;">
        <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    My Articles
                </h1>
                <p style="color: var(--text-secondary);">Manage your articles</p>
            </div>
            {{-- FIX: Use public route --}}
            <a href="{{ route('articles.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 2rem; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
                <i data-lucide="plus"></i>
                Create New Article
            </a>
        </div>

        {{-- Stats --}}
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <div class="stat-card" style="background: var(--card-bg); padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <div style="font-size: 2rem; font-weight: 700; color: #3b82f6;">{{ $stats['total_articles'] }}</div>
                <div style="font-size: 0.875rem; color: var(--text-secondary);">Total Articles</div>
            </div>
            <div class="stat-card" style="background: var(--card-bg); padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <div style="font-size: 2rem; font-weight: 700; color: #10b981;">{{ number_format($stats['total_views']) }}</div>
                <div style="font-size: 0.875rem; color: var(--text-secondary);">Total Views</div>
            </div>
            <div class="stat-card" style="background: var(--card-bg); padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <div style="font-size: 2rem; font-weight: 700; color: #8b5cf6;">{{ $stats['total_comments'] }}</div>
                <div style="font-size: 0.875rem; color: var(--text-secondary);">Total Comments</div>
            </div>
        </div>

        {{-- Articles Table --}}
        <div style="background: var(--card-bg); border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: var(--bg-secondary);">
                    <tr>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary);">Title</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary);">Category</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary);">Views</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary);">Date</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                        <tr style="border-top: 1px solid var(--border-color);">
                            <td style="padding: 1rem;"><strong>{{ Str::limit($article->title, 50) }}</strong></td>
                            <td style="padding: 1rem;">
                                <span style="padding: 0.25rem 0.75rem; background: {{ $article->category->color }}; color: white; border-radius: 20px; font-size: 0.875rem; font-weight: 600;">
                                    {{ $article->category->name }}
                                </span>
                            </td>
                            <td style="padding: 1rem;">{{ number_format($article->views) }}</td>
                            <td style="padding: 1rem;">{{ $article->date->format('d M Y') }}</td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="{{ route('articles.show', $article) }}" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); border-radius: 8px; color: var(--text-primary); text-decoration: none; transition: all 0.3s ease;">
                                        <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
                                    </a>
                                    {{-- FIX: Use public route --}}
                                    <a href="{{ route('articles.edit', $article) }}" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); border-radius: 8px; color: var(--text-primary); text-decoration: none; transition: all 0.3s ease;">
                                        <i data-lucide="edit" style="width: 18px; height: 18px;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 3rem; text-align: center; color: var(--text-secondary);">
                                No articles yet. <a href="{{ route('articles.create') }}" style="color: var(--primary-color); font-weight: 600;">Create your first article!</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 2rem;">
            {{ $articles->links() }}
        </div>
    </div>
</section>

@push('scripts')
<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
@endpush
@endsection