@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">School Announcements</h1>
            <a href="{{ route('admin.cms.announcements.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Post New Announcement
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Title</th>
                                <th>Content Snippet</th>
                                <th>Expires</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($announcements as $ann)
                                <tr class="{{ $ann->is_sticky ? 'table-primary' : '' }}">
                                    <td>
                                        @if($ann->is_sticky)
                                            <span class="badge bg-primary">Sticky</span>
                                        @else
                                            <span class="badge bg-secondary">Normal</span>
                                        @endif
                                    </td>
                                    <td>{{ $ann->title }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($ann->content, 100) }}</td>
                                    <td>{{ $ann->expires_at ? $ann->expires_at->format('M d, Y') : 'Never' }}</td>
                                    <td>
                                        <a href="{{ route('admin.cms.announcements.edit', $ann->id) }}"
                                            class="btn btn-sm btn-info">Edit</a>
                                        <form action="{{ route('admin.cms.announcements.destroy', $ann->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
