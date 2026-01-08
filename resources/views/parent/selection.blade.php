@extends('layouts.app')

@section('content')
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
                <h1 class="text-white text-center mb-4">Select Child</h1>
                <p class="text-white text-center mb-5">Please select the child you wish to view.</p>
            </div>
        </div>
    </div>

    <div class="container-fluid mt--7">
        <div class="row justify-content-center">
            @foreach($children as $child)
                <div class="col-md-4 mb-4">
                    <div class="card card-stats shadow h-100 hover-elevate">
                        <div class="card-body text-center">
                            <div class="avatar avatar-xl rounded-circle mb-3">
                                <img src="{{ asset('assets/img/theme/team-4-800x800.jpg') }}" class="rounded-circle"
                                    alt="{{ $child->first_name }}">
                                <!-- Use placeholder if no photo, not implemented yet -->
                            </div>
                            <h3 class="h2 font-weight-bold mb-0">{{ $child->first_name }} {{ $child->last_name }}</h3>
                            <p class="text-muted text-sm">{{ $child->email }}</p>

                            <a href="{{ route('parent.child.dashboard', $child->id) }}" class="btn btn-primary mt-3">
                                View Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection