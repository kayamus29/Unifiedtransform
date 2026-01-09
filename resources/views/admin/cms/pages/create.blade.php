@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ isset($page) ? 'Edit' : 'Create' }} Website Page</h1>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form
                            action="{{ isset($page) ? route('admin.cms.pages.update', $page->id) : route('admin.cms.pages.store') }}"
                            method="POST">
                            @csrf
                            @if(isset($page)) @method('PUT') @endif

                            <div class="form-group mb-3">
                                <label>Page Title</label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ $page->title ?? old('title') }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label>Content</label>
                                <textarea name="content"
                                    class="form-control editor">{{ $page->content ?? old('content') }}</textarea>
                            </div>

                            <div class="form-group mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="is_home" class="custom-control-input" id="isHome" {{ (isset($page) && $page->is_home) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="isHome">Set as Homepage</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="draft" {{ (isset($page) && $page->status == 'draft') ? 'selected' : '' }}>
                                        Draft</option>
                                    <option value="published" {{ (isset($page) && $page->status == 'published') ? 'selected' : '' }}>Published</option>
                                </select>
                            </div>

                            <hr>
                            <h5>SEO Settings</h5>
                            <div class="form-group mb-3">
                                <label>Meta Title</label>
                                <input type="text" name="meta_title" class="form-control"
                                    value="{{ $page->meta_title ?? old('meta_title') }}">
                            </div>
                            <div class="form-group mb-3">
                                <label>Meta Description</label>
                                <textarea name="meta_description"
                                    class="form-control">{{ $page->meta_description ?? old('meta_description') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Page</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.cms.editor-script')
@endsection