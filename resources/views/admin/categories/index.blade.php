@extends('admin.layouts.admin')

@section('title', 'Manage Categories')

@section('content')
<div class="page-header">
    <h1>Manage Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn-primary">
        <i data-lucide="plus"></i>
        Add Category
    </a>
</div>

<div class="table-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Color</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Articles</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>
                        <div style="width: 30px; height: 30px; border-radius: 50%; background: {{ $category->color }};"></div>
                    </td>
                    <td><strong>{{ $category->name }}</strong></td>
                    <td>{{ $category->slug }}</td>
                    <td>{{ $category->articles_count }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn-icon">
                                <i data-lucide="edit"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon btn-danger">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection