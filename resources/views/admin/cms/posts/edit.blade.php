@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ isset($post) ? 'Edit' : 'Create' }} Blog Post</h1>
        </div>

        <form action="{{ isset($post) ? route('admin.cms.posts.update', $post->id) : route('admin.cms.posts.store') }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($post)) @method('PUT') @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label>Post Title</label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ $post->title ?? old('title') }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label>Summary</label>
                                <textarea name="summary" class="form-control"
                                    rows="3">{{ $post->summary ?? old('summary') }}</textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label>Content</label>
                                <textarea name="content"
                                    class="form-control editor">{{ $post->content ?? old('content') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Post Settings</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="draft" {{ (isset($post) && $post->status == 'draft') ? 'selected' : '' }}>
                                        Draft</option>
                                    <option value="published" {{ (isset($post) && $post->status == 'published') ? 'selected' : '' }}>Published</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label>Category</label>
                                <select name="category_id" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (isset($post) && $post->category_id == $category->id) ? 'selected' : '' }}>{{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label>Featured Image</label>
                                @if(isset($post) && $post->featured_image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $post->featured_image) }}" class="img-fluid rounded">
                                    </div>
                                @endif
                                <input type="file" name="featured_image" class="form-control-file">
                            </div>

                            <button type="submit" class="btn btn-primary btn-block mt-3">Save Post</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @include('admin.cms.editor-script')
@endsection
