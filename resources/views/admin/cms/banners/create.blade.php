@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ isset($banner) ? 'Edit' : 'Create' }} Banner</h1>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form
                            action="{{ isset($banner) ? route('admin.cms.banners.update', $banner->id) : route('admin.cms.banners.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if(isset($banner)) @method('PUT') @endif

                            <div class="form-group mb-3">
                                <label>Banner Image (Recommended size: 1920x600)</label>
                                @if(isset($banner) && $banner->image_path)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $banner->image_path) }}" class="img-fluid rounded">
                                    </div>
                                @endif
                                <input type="file" name="image_path" class="form-control-file" {{ isset($banner) ? '' : 'required' }}>
                            </div>

                            <div class="form-group mb-3">
                                <label>Title (Optional)</label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ $banner->title ?? old('title') }}">
                            </div>

                            <div class="form-group mb-3">
                                <label>Subtitle (Optional)</label>
                                <input type="text" name="subtitle" class="form-control"
                                    value="{{ $banner->subtitle ?? old('subtitle') }}">
                            </div>

                            <div class="form-group mb-3">
                                <label>Button Text</label>
                                <input type="text" name="button_text" class="form-control"
                                    value="{{ $banner->button_text ?? old('button_text') }}" placeholder="e.g. Learn More">
                            </div>

                            <div class="form-group mb-3">
                                <label>Button Link</label>
                                <input type="text" name="button_link" class="form-control"
                                    value="{{ $banner->button_link ?? old('button_link') }}" placeholder="https://...">
                            </div>

                            <div class="form-group mb-3">
                                <label>Display Order</label>
                                <input type="number" name="order" class="form-control" value="{{ $banner->order ?? 0 }}">
                            </div>

                            <div class="form-group mb-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ (isset($banner) && $banner->status == 1) ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="0" {{ (isset($banner) && $banner->status == 0) ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Banner</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection