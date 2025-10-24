@extends('admin.layouts.admin')

@section('title', 'Manage Articles - Admin')

@section('content')
<div class="page-header">
    <h1>Manage Articles</h1>
    <a href="{{ route('admin.articles.create') }}" class="btn-primary">
        <i data-lucide="plus"></i>
        Add New Article
    </a>
</div>

<!-- Search & Filter -->
<div class="filter-bar">
    <form action="{{ route('admin.articles.index') }}" method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search articles..." value="{{ request('search') }}">
        <select name="category">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary">
            <i data-lucide="search"></i>
            Search
        </button>
    </form>
</div>

<!-- Articles Table -->
<div class="table-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Views</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($articles as $article)
                <tr>
                    <td>
                        <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="table-thumb">
                    </td>
                    <td>
                        <strong>{{ Str::limit($article->title, 40) }}</strong>
                    </td>
                    <td>{{ $article->user->name }}</td>
                    <td>
                        <span class="badge" style="background: {{ $article->category->color }};">
                            {{ $article->category->name }}
                        </span>
                    </td>
                    <td>{{ number_format($article->views) }}</td>
                    <td>{{ $article->date->format('d M Y') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('articles.show', $article) }}" class="btn-icon" title="View">
                                <i data-lucide="eye"></i>
                            </a>
                            <a href="{{ route('admin.articles.edit', $article) }}" class="btn-icon" title="Edit">
                                <i data-lucide="edit"></i>
                            </a>
                            <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this article?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon btn-danger" title="Delete">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                        No articles found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagination-wrapper">
    {{ $articles->links() }}
</div>
@endsection