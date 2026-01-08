@extends('layouts.app')

@section('content')
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
                <h1 class="text-white">My Marks & Grades</h1>
            </div>
        </div>
    </div>

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <h3 class="mb-0">Academic Performance</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Course</th>
                                    <th scope="col">Exam</th>
                                    <th scope="col">Marks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($marks as $mark)
                                    <tr>
                                        <td>{{ $mark->course->course_name ?? 'N/A' }}</td>
                                        <td>{{ $mark->exam->exam_name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-primary">
                                                {{ $mark->marks ?? 'N/A' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No marks available yet.</td>
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