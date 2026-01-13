@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/results-dashboard.css') }}">

    <div class="container-fluid pb-5">
        <div class="row justify-content-start">
            @include('layouts.left-menu')

            <div class="col-xs-11 col-sm-11 col-md-11 col-lg-10 col-xl-10 col-xxl-10">
                <div class="d-sm-flex align-items-center justify-content-between mb-4 pt-3">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800 fw-bold">My Academic Performance</h1>
                        <p class="text-muted small mb-0">{{ $promotion->schoolClass->class_name }} |
                            {{ $promotion->session->session_name }}</p>
                    </div>
                    <div class="no-print">
                        <button onclick="window.print()" class="btn btn-outline-primary shadow-sm" {{ isset($withheld) && $withheld ? 'disabled' : '' }}>
                            <i class="bi bi-download me-1"></i> Download Transcript
                        </button>
                    </div>
                </div>

                @if(isset($withheld) && $withheld)
                <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center py-5">
                    <i class="bi bi-exclamation-octagon-fill me-4 display-4"></i>
                    <div>
                        <h3 class="alert-heading fw-bold">Academic Records Withheld</h3>
                        <p class="mb-0 fs-5 pb-1">Detailed results and performance records are temporarily withheld due to an outstanding financial balance.</p>
                        <hr>
                        <p class="mb-0 text-muted">Please visit the Accountant's office or settle your balance to regain access.</p>
                    </div>
                </div>
                @else
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Course-wise Performance Summary</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-container">
                            <table class="results-grid">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Course Name</th>
                                        @foreach($semesters as $semester)
                                            <th class="text-center">{{ $semester->semester_name }}</th>
                                        @endforeach
                                        <th class="text-center bg-light">Annual Total</th>
                                        <th class="text-center bg-light">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses as $course)
                                        <tr class="results-row">
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark">{{ $course->course_name }}</div>
                                                <div class="text-muted smallest">Code: {{ $course->id }}</div>
                                            </td>

                                            @php $annualTotal = 0;
                                            $termsWithMarks = 0; @endphp

                                            @foreach($semesters as $semester)
                                                @php
                                                    $mark = isset($results[$course->id]) ? $results[$course->id]->where('semester_id', $semester->id)->first() : null;
                                                    $score = $mark ? $mark->final_marks : null;
                                                    if ($score !== null) {
                                                        $annualTotal += $score;
                                                        $termsWithMarks++;
                                                    }
                                                @endphp
                                                <td class="text-center text-center-student">
                                                    @if($score !== null)
                                                        <span class="clickable-mark {{ $score < 50 ? 'mark-fail' : 'mark-pass' }}"
                                                            data-student-id="{{ $student->id }}" data-course-id="{{ $course->id }}"
                                                            data-semester-id="{{ $semester->id }}">
                                                            {{ number_format($score, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted opacity-50">--</span>
                                                    @endif
                                                </td>
                                            @endforeach

                                            <td class="text-center cumulative-total">
                                                {{ $termsWithMarks > 0 ? number_format($annualTotal, 2) : '0.00' }}
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $avg = $termsWithMarks > 0 ? ($annualTotal / $termsWithMarks) : 0;
                                                @endphp
                                                @if($termsWithMarks > 0)
                                                    <span class="badge {{ $avg >= 50 ? 'bg-success' : 'bg-danger' }} grade-badge">
                                                        {{ $avg >= 50 ? 'PASS' : 'FAIL' }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary grade-badge">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-2">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-primary text-white">
                            <div class="card-body">
                                <h6 class="small uppercase fw-bold opacity-75">Average Score</h6>
                                @php
                                    $grandTotal = 0;
                                    $totalEntries = 0;
                                    foreach ($results as $cResults) {
                                        $grandTotal += $cResults->sum('final_marks');
                                        $totalEntries += $cResults->count();
                                    }
                                    $totalAvg = $totalEntries > 0 ? ($grandTotal / $totalEntries) : 0;
                                @endphp
                                <div class="h2 mb-0 fw-bold">{{ number_format($totalAvg, 2) }}%</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="alert alert-info shadow-sm h-100 mb-0 d-flex align-items-center">
                            <div>
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <strong>Usage Tip:</strong> Click on any mark to see your Continuous Assessment (CA) and
                                Exam breakdown. If marks are missing, please contact your course teacher.
                            </div>
                        </div>
                    </div>
                </div>

                @endif

                @include('layouts.footer')
            </div>
        </div>
    </div>

    <!-- Breakdown Modal -->
    <div class="modal fade" id="breakdownModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Performance Breakdown</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4" id="breakdownContent">
                    <!-- AJAX Content -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/results-dashboard.js') }}"></script>
@endsection