@extends('layouts.website')

@section('title', 'News & Blog - ' . config('app.name'))

@section('content')
    <div class="bg-light py-5 mb-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Latest News & Blog</h1>
            <p class="lead text-muted">Stay updated with our school activities and announcements.</p>
        </div>
    </div>

    <div class="container">
        <div class="row g-4">
            @forelse($posts as $post)
                <div class="col-md-4">
                    <div class="card h-100 overflow-hidden">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" class="card-img-top featured-image"
                                alt="{{ $post->title }}">
                        @endif
                        <div class="card-body">
                            <div class="d-flex mb-2">
                                <span class="badge bg-light text-primary">{{ $post->category->name ?? 'General' }}</span>
                                <small
                                    class="ms-auto text-muted">{{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</small>
                            </div>
                            <h5 class="card-title">{{ $post->title }}</h5>
                            <p class="card-text text-muted small">{{ \Illuminate\Support\Str::limit($post->summary, 150) }}</p>
                            <a href="{{ route('website.post', $post->slug) }}" class="btn btn-outline-primary btn-sm mt-2">Read
                                More</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted fs-5">No news items found currently. Check back later!</p>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $posts->links() }}
        </div>
    </div>
@endsection
