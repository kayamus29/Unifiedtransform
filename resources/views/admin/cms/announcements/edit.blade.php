@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ isset($announcement) ? 'Edit' : 'Post' }} Announcement</h1>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form
                            action="{{ isset($announcement) ? route('admin.cms.announcements.update', $announcement->id) : route('admin.cms.announcements.store') }}"
                            method="POST">
                            @csrf
                            @if(isset($announcement)) @method('PUT') @endif

                            <div class="form-group mb-3">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ $announcement->title ?? old('title') }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label>Content</label>
                                <textarea name="content" class="form-control" rows="5"
                                    required>{{ $announcement->content ?? old('content') }}</textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label>Expiration Date (Optional)</label>
                                <input type="date" name="expires_at" class="form-control"
                                    value="{{ isset($announcement) && $announcement->expires_at ? $announcement->expires_at->format('Y-m-d') : '' }}">
                                <small class="text-muted">Leave empty for no expiration.</small>
                            </div>

                            <div class="form-group mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="is_sticky" class="custom-control-input" id="isSticky" {{ (isset($announcement) && $announcement->is_sticky) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="isSticky">Pin to top (Sticky)</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Announcement</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
