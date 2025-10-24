@extends('admin.layouts.admin')

@section('title', 'Manage Authors - Admin')

@section('content')
<div class="page-header">
    <h1>Manage Authors</h1>
    <a href="{{ route('admin.users.create') }}" class="btn-primary">
        <i data-lucide="user-plus"></i>
        Add New Author
    </a>
</div>

<!-- Users Table -->
<div class="table-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Avatar</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Articles</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>
                        <div class="table-avatar">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    </td>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge {{ $user->role === 'admin' ? 'badge-primary' : 'badge-secondary' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>{{ $user->articles_count }}</td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn-icon">
                                <i data-lucide="edit"></i>
                            </a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon btn-danger">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ $users->links() }}
@endsection