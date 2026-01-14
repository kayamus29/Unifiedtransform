@extends('layouts.website')

@section('title', $page->title . ' - ' . config('app.name'))
@section('meta_description', $page->meta_description)

@section('content')
    <div class="bg-primary text-white py-5 mb-5 shadow-sm">
        <div class="container">
            <h1 class="display-4 fw-bold mb-0">{{ $page->title }}</h1>
        </div>
    </div>

    <div class="container min-vh-50 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="cms-content fs-5 leading-relaxed">
                    {!! $page->content !!}
                </div>
            </div>
        </div>
    </div>
@endsection
