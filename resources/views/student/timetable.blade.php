@extends('layouts.app')

@section('content')
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
                <h1 class="text-white">My Timetable</h1>
            </div>
        </div>
    </div>

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <h3 class="mb-0">Class Schedule</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Day</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Course</th>
                                    <th scope="col">Room</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($routines as $routine)
                                    <tr>
                                        <td>{{ $routine->weekday }}</td>
                                        <td>{{ $routine->start_time }} - {{ $routine->end_time }}</td>
                                        <td>{{ $routine->course->course_name ?? 'N/A' }}</td>
                                        <td>{{ $routine->room_no ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No timetable available.</td>
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