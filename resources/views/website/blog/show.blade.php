@extends('layouts.website')

@section('title', $post->title . ' - ' . config('app.name'))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($post->summary), 160))

@section('content')
    <article class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('website.blog') }}">Blog</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ \Illuminate\Support\Str::limit($post->title, 30) }}</li>
                        </ol>
                    </nav>

                    <h1 class="display-4 fw-bold mb-3">{{ $post->title }}</h1>
                    <div class="d-flex align-items-center mb-4 text-muted">
                        <span class="me-3"><i class="bi bi-calendar3 me-1"></i>
                            {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
                        <span><i class="bi bi-tag me-1"></i> {{ $post->category->name ?? 'General' }}</span>
                    </div>

                    @if($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}"
                            class="img-fluid rounded shadow-sm mb-5 w-100" style="max-height: 500px; object-fit: cover;">
                    @endif

                    <div class="post-content fs-5 leading-relaxed">
                        {!! $post->content !!}
                    </div>

                    <hr class="my-5">

                    <div class="d-flex justify-content-between">
                        <h5 class="mb-0">Share this post:</h5>
                        <div class="fs-4">
                            <a href="https://facebook.com/sharer/sharer.php?u={{ urlcurrent() }}" target="_blank"
                                class="text-primary me-3"><i class="bi bi-facebook"></i></a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlcurrent() }}&text={{ urlencode($post->title) }}"
                                target="_blank" class="text-info me-3"><i class="bi bi-twitter"></i></a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlcurrent() }}" target="_blank"
                                class="text-primary"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
@endsection
