@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Homepage Banners</h1>
            <a href="{{ route('admin.cms.banners.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add New Banner
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title/Subtitle</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($banners as $banner)
                                <tr>
                                    <td>
                                        <img src="{{ asset('storage/' . $banner->image_path) }}" width="100"
                                            class="rounded shadow-sm">
                                    </td>
                                    <td>
                                        <strong>{{ $banner->title }}</strong><br>
                                        <small>{{ $banner->subtitle }}</small>
                                    </td>
                                    <td>{{ $banner->order }}</td>
                                    <td>
                                        <span class="badge {{ $banner->status ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $banner->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.cms.banners.edit', $banner->id) }}"
                                            class="btn btn-sm btn-info">Edit</a>
                                        <form action="{{ route('admin.cms.banners.destroy', $banner->id) }}" method="POST"
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
                </div>
            </div>
        </div>
    </div>
@endsection
