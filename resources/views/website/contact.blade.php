@extends('layouts.website')

@section('title', 'Contact Us - ' . config('app.name'))

@section('content')
    <div class="bg-primary text-white py-5 mb-5 shadow-sm">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Get In Touch</h1>
            <p class="lead">Have questions? We're here to help.</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row g-5">
            <div class="col-lg-5">
                <div class="card bg-light p-4 h-100">
                    <h3 class="mb-4">Contact Information</h3>
                    <div class="d-flex mb-4">
                        <div class="fs-3 text-primary me-3"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <h5>Our Location</h5>
                            <p class="text-muted mb-0">123 Education Way, Academic City, State 45678</p>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="fs-3 text-primary me-3"><i class="bi bi-envelope"></i></div>
                        <div>
                            <h5>Email Address</h5>
                            <p class="text-muted mb-0">info@school.edu<br>admissions@school.edu</p>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="fs-3 text-primary me-3"><i class="bi bi-telephone"></i></div>
                        <div>
                            <h5>Phone Number</h5>
                            <p class="text-muted mb-0">+1 (234) 567-890<br>+1 (234) 567-891</p>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <h5>Social Media</h5>
                        <div class="d-flex gap-3 fs-3 mt-2">
                            <a href="#" class="text-dark"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="text-dark"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="text-dark"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card p-4">
                    <h3 class="mb-4">Send Us a Message</h3>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('website.contact.submit') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Your Name</label>
                                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="john@example.com"
                                    required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Subject</label>
                                <input type="text" name="subject" class="form-control" placeholder="How can we help?">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message</label>
                                <textarea name="message" class="form-control" rows="5"
                                    placeholder="Type your message here..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg">Send Message <i
                                        class="bi bi-send ms-2"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Placeholder -->
    <div class="container-fluid px-0 pt-5">
        <div class="ratio ratio-21x9 bg-light d-flex align-items-center justify-content-center border-top">
            <div class="text-center text-muted">
                <i class="bi bi-map-fill display-1 mb-3"></i>
                <h3>Google Maps Integration Placeholder</h3>
                <p>Add your school's Google Maps embed code here.</p>
            </div>
        </div>
    </div>
@endsection