@extends('layouts.app')

@section('content')
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
                <h1 class="text-white mb-4">My Dashboard</h1>

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-xl-3 col-lg-6">
                        <div class="card card-stats mb-4 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">Present</h5>
                                        <span class="h2 font-weight-bold mb-0">{{ $totalPresent }}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                            <i class="fas fa-check"></i>
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
                                        <h5 class="card-title text-uppercase text-muted mb-0">Absent</h5>
                                        <span class="h2 font-weight-bold mb-0">{{ $totalAbsent }}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                            <i class="fas fa-times"></i>
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
                                        <h5 class="card-title text-uppercase text-muted mb-0">Class</h5>
                                        <span class="h2 font-weight-bold mb-0">
                                            @if($promotion)
                                                {{ $promotion->schoolClass->class_name ?? 'N/A' }}
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                            <i class="fas fa-school"></i>
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
                                <a href="{{ route('student.attendance') }}" class="btn btn-sm btn-primary">See all</a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAttendance as $record)
                                    <tr>
                                        <th scope="row">{{ $record->created_at->format('d M Y') }}</th>
                                        <td>
                                            <span class="badge badge-dot mr-4">
                                                <i class="bg-{{ $record->status == 'Present' ? 'success' : 'warning' }}"></i>
                                                {{ $record->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">No attendance records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">Notices</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @forelse($notices as $notice)
                            <div class="mb-3">
                                <h4 class="h5">{{ $notice->title ?? 'Notice' }}</h4>
                                <p class="text-sm text-muted">{{ Str::limit($notice->notice ?? '', 100) }}</p>
                            </div>
                        @empty
                            <p class="text-muted">No recent notices.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection