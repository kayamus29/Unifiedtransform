@extends('layouts.website')

@section('content')
    <!-- Carousel / Banners -->
    @if($banners->count() > 0)
        <div id="schoolCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($banners as $index => $banner)
                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                        <div class="hero-banner"
                            style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('{{ asset('storage/' . $banner->image_path) }}');">
                            <div class="container text-center">
                                <h1 class="display-3 fw-bold">{{ $banner->title }}</h1>
                                <p class="lead mb-4">{{ $banner->subtitle }}</p>
                                @if($banner->button_text)
                                    <a href="{{ $banner->button_link }}" class="btn btn-primary btn-lg">{{ $banner->button_text }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($banners->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#schoolCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#schoolCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            @endif
        </div>
    @endif

    <!-- Homepage Content (from CMS Page) -->
    @if($homePage)
        <section class="py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="cms-content">
                            {!! $homePage->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Announcements (Sticky/New) -->
    @if($announcements->count() > 0)
        <section class="py-5 bg-light">
            <div class="container">
                <div class="d-flex align-items-center mb-4">
                    <h2 class="mb-0">Latest Announcements</h2>
                    <div class="ms-auto"><i class="bi bi-megaphone-fill text-primary fs-3"></i></div>
                </div>
                <div class="row g-4">
                    @foreach($announcements as $announcement)
                        <div class="col-md-6">
                            <div class="card h-100 p-3 {{ $announcement->is_sticky ? 'border-primary' : '' }}">
                                <div class="d-flex mb-2">
                                    <h5 class="card-title mb-0">{{ $announcement->title }}</h5>
                                    @if($announcement->is_sticky)
                                        <span class="badge bg-primary ms-auto">Important</span>
                                    @endif
                                </div>
                                <p class="card-text text-muted small mb-0">{{ $announcement->content }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Featured News -->
    <section class="py-5">
        <div class="container">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h2 class="mb-0">Featured News & Activities</h2>
                <a href="{{ route('website.blog') }}" class="btn btn-link text-decoration-none">View All Posts <i
                        class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row g-4">
                @forelse($featuredPosts as $post)
                    <div class="col-md-4">
                        <div class="card h-100 overflow-hidden">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" class="card-img-top featured-image"
                                    alt="{{ $post->title }}">
                            @endif
                            <div class="card-body">
                                <span class="badge bg-light text-primary mb-2">{{ $post->category->name ?? 'General' }}</span>
                                <h5 class="card-title">{{ $post->title }}</h5>
                                <p class="card-text text-muted small">{{ \Illuminate\Support\Str::limit($post->summary, 120) }}
                                </p>
                                <a href="{{ route('website.post', $post->slug) }}"
                                    class="btn btn-outline-primary btn-sm mt-2">Read More</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4">
                        <p class="text-muted">No news items found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Why Choose Us / Quick Stats -->
    <section class="py-5 bg-primary text-white text-center">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3">
                    <h2 class="display-5 fw-bold mb-0">15+</h2>
                    <p class="mb-0">Years Experience</p>
                </div>
                <div class="col-md-3">
                    <h2 class="display-5 fw-bold mb-0">1200+</h2>
                    <p class="mb-0">Current Students</p>
                </div>
                <div class="col-md-3">
                    <h2 class="display-5 fw-bold mb-0">80+</h2>
                    <p class="mb-0">Qualified Teachers</p>
                </div>
                <div class="col-md-3">
                    <h2 class="display-5 fw-bold mb-0">100%</h2>
                    <p class="mb-0">Safe Campus</p>
                </div>
            </div>
        </div>
    </section>

@endsection