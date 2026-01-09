@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Contact Inquiries</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Sender</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $sub)
                                <tr class="{{ $sub->status == 'new' ? 'font-weight-bold table-light' : '' }}">
                                    <td>
                                        <span
                                            class="badge {{ $sub->status == 'new' ? 'bg-primary' : ($sub->status == 'read' ? 'bg-info' : 'bg-secondary') }}">
                                            {{ ucfirst($sub->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $sub->name }}<br>
                                        <small>{{ $sub->email }}</small>
                                    </td>
                                    <td>{{ $sub->subject ?? 'No Subject' }}</td>
                                    <td>{{ $sub->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.cms.inquiries.show', $sub->id) }}"
                                            class="btn btn-sm btn-info">View</a>
                                        <form action="{{ route('admin.cms.inquiries.destroy', $sub->id) }}" method="POST"
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
                    {{ $submissions->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection