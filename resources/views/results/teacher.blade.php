@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/results-dashboard.css') }}">

<div class="container-fluid pb-5">
    <div class="row justify-content-start">
        @include('layouts.left-menu')
        
        <div class="col-xs-11 col-sm-11 col-md-11 col-lg-10 col-xl-10 col-xxl-10">
            <div class="d-sm-flex align-items-center justify-content-between mb-4 pt-3">
                <div>
                    <h1 class="h3 mb-1 text-gray-800 fw-bold">Results Dashboard</h1>
                    <p class="text-muted small mb-0">High-precision academic performance tracking and term-wise auditing.</p>
                </div>
                <div class="no-print">
                    <button onclick="window.print()" class="btn btn-outline-secondary shadow-sm">
                        <i class="bi bi-printer me-1"></i> Print Report
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="card shadow-sm border-0 mb-4 no-print">
                <div class="card-body">
                    <form action="{{ route('results.teacher') }}" method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Course Selection</label>
                            <select name="course_class" class="form-select border-light bg-light" onchange="this.form.submit()">
                                <option value="">-- Select Course/Class --</option>
                                @foreach($assignments as $a)
                                    @php $val = $a->course_id . '|' . $a->class_id . '|' . $a->section_id; @endphp
                                    <option value="{{ $val }}" {{ (request('course_class') == $val) ? 'selected' : '' }}>
                                        {{ $a->course->course_name ?? 'Unknown Course' }} - {{ $a->schoolClass->class_name ?? 'Unknown Class' }} ({{ $a->section->section_name ?? '??' }})
                                    </option>
                                @endforeach
                            </select>
                            {{-- Hidden inputs to maintain state if selection is parsed --}}
                            @if(request('course_class'))
                                @php 
                                    $parts = explode('|', request('course_class'));
                                    echo '<input type="hidden" name="course_id" value="'.($parts[0] ?? '').'">';
                                    echo '<input type="hidden" name="class_id" value="'.($parts[1] ?? '').'">';
                                    echo '<input type="hidden" name="section_id" value="'.($parts[2] ?? '').'">';
                                @endphp
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            @if(count($students) > 0)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Academic Performance Grid</h6>
                        <div class="small text-muted">Pass Mark: <span class="badge bg-light text-dark">50.00</span></div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-container">
                            <table class="results-grid">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Student Name</th>
                                        @foreach($semesters as $semester)
                                            <th class="text-center">{{ $semester->semester_name }}</th>
                                        @endforeach
                                        <th class="text-center bg-light">Annual Total</th>
                                        <th class="text-center bg-light">Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                        <tr class="results-row">
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark">{{ $student->first_name }} {{ $student->last_name }}</div>
                                                <div class="text-muted smallest">ID: {{ $student->id }}</div>
                                            </td>
                                            
                                            @php $annualTotal = 0; $termsWithMarks = 0; @endphp
                                            
                                            @foreach($semesters as $semester)
                                                @php
                                                    $mark = isset($results[$student->id]) ? $results[$student->id]->where('semester_id', $semester->id)->first() : null;
                                                    $score = $mark ? $mark->final_marks : null;
                                                    if($score !== null) {
                                                        $annualTotal += $score;
                                                        $termsWithMarks++;
                                                    }
                                                @endphp
                                                <td class="text-center">
                                                    @if($score !== null)
                                                        <span class="clickable-mark {{ $score < 50 ? 'mark-fail' : 'mark-pass' }}"
                                                              data-student-id="{{ $student->id }}"
                                                              data-course-id="{{ $course_id }}"
                                                              data-semester-id="{{ $semester->id }}"
                                                              title="Click for breakdown">
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
                                                    $grade = 'N/A';
                                                    if($avg >= 70) $grade = 'A';
                                                    elseif($avg >= 60) $grade = 'B';
                                                    elseif($avg >= 50) $grade = 'C';
                                                    elseif($avg >= 45) $grade = 'D';
                                                    elseif($avg > 0) $grade = 'F';
                                                @endphp
                                                <span class="badge {{ $avg >= 50 ? 'bg-success' : 'bg-danger' }} grade-badge">
                                                    {{ $grade }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5 bg-white rounded shadow-sm">
                    <i class="bi bi-journal-x fs-1 text-muted opacity-25"></i>
                    <h5 class="mt-3 text-muted">Select a course to view student performance.</h5>
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
            <div class="modal-header border-0 pb-0 shadow-sm">
                <h5 class="modal-title fw-bold"><i class="bi bi-info-square me-2 text-primary"></i>Assessment Breakdown</h5>
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
