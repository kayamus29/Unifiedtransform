@extends('layouts.app')

@section('content')
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
                <h1 class="text-white">My Attendance</h1>
            </div>
        </div>
    </div>

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <h3 class="mb-0">Attendance History</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Class</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendance as $record)
                                    <tr>
                                        <td>{{ $record->created_at->format('d M Y, h:i A') }}</td>
                                        <td>{{ $record->schoolClass->class_name ?? 'N/A' }}</td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $record->status == 'Present' ? 'success' : 'warning' }}">
                                                {{ $record->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No attendance records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer py-4">
                        {{ $attendance->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection