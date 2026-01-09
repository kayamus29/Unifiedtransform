@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-start">
            @include('layouts.left-menu')
            <div class="col-xs-11 col-sm-11 col-md-11 col-lg-10 col-xl-10 col-xxl-10">
                <div class="row pt-3">
                    <div class="col ps-4">
                        <h1 class="display-6 mb-3"><i class="bi bi-journal-check"></i> My Marks & Grades</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Marks</li>
                            </ol>
                        </nav>

                        <div class="card shadow-sm border-0 mt-4">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-4">Course</th>
                                                <th>Exam</th>
                                                <th>Marks</th>
                                                <th>Grade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($marks as $mark)
                                                <tr>
                                                    <td class="ps-4 fw-bold">{{ $mark->course->course_name ?? 'N/A' }}</td>
                                                    <td>{{ $mark->exam->exam_name ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="h5 mb-0">{{ $mark->marks }}</span>
                                                    </td>
                                                    <td>
                                                        {{-- Grade logic would normally go here based on school rules --}}
                                                        <span class="badge bg-secondary">N/A</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-5 text-muted">No marks records found
                                                        yet for this session.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>
@endsection