@extends('layouts.app')

@section('content')
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
                <!-- Header & Switcher -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="text-white">
                        Dashboard: {{ $student->first_name }} {{ $student->last_name }}
                    </h1>

                    @if($children->count() > 1)
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="childSwitcher"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Switch Child
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="childSwitcher">
                                @foreach($children as $child)
                                    <a class="dropdown-item {{ $child->id === $student->id ? 'active' : '' }}"
                                        href="{{ route('parent.child.dashboard', $child->id) }}">
                                        {{ $child->first_name }} {{ $child->last_name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-xl-3 col-lg-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">Attendance</h5>
                                        <span class="h2 font-weight-bold mb-0">Check List</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                            <i class="fas fa-chart-bar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">Academic Info</h5>
                                        <span class="h2 font-weight-bold mb-0">{{ $marksCount }} Marks</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                            <i class="fas fa-chart-pie"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt--7">
        <div class="row mt-5">
            <div class="col-xl-8 mb-5 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">Recent Attendance</h3>
                            </div>
                            <div class="col text-right">
                                <a href="#!" class="btn btn-sm btn-primary">See all</a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <!-- Projects table -->
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendance as $record)
                                    <tr>
                                        <th scope="row">
                                            {{ $record->created_at->format('d M Y') }}
                                        </th>
                                        <td>
                                            <span class="badge badge-dot mr-4">
                                                <i class="bg-{{ $record->status == 'Present' ? 'success' : 'warning' }}"></i>
                                                {{ $record->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">No recent attendance records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection