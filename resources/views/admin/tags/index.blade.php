@extends('admin.layouts.admin')

@section('title', 'Manage Tags')

@section('content')
<div class="page-header">
    <h1>Manage Tags</h1>
    <a href="{{ route('admin.tags.create') }}" class="btn-primary">
        <i data-lucide="plus"></i>
        Add Tag
    </a>
</div>

<div class="tags-grid">
    @foreach($tags as $tag)
        <div class="tag-card">
            <div>
                <h4>{{ $tag->name }}</h4>
                <span>{{ $tag->articles_count }} articles</span>
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.tags.edit', $tag) }}" class="btn-icon">
                    <i data-lucide="edit"></i>
                </a>
                <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-icon btn-danger">
                        <i data-lucide="trash-2"></i>
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endsection