@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/results-dashboard.css') }}">

<div class="container-fluid pb-5">
    <div class="row justify-content-start">
        @include('layouts.left-menu')
        
        <div class="col-xs-11 col-sm-11 col-md-11 col-lg-10 col-xl-10 col-xxl-10">
            <div class="d-sm-flex align-items-center justify-content-between mb-4 pt-3">
                <h1 class="h3 mb-0 text-gray-800 fw-bold">Section Performance Audit</h1>
            </div>

            <div class="row g-4">
                <!-- Selectors Sidebar -->
                <div class="col-lg-4 no-print">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white fw-bold">1. Select Section</div>
                        <div class="card-body">
                            <form action="{{ route('results.section') }}" method="GET" id="sectionForm">
                                <select name="section_id" class="form-select border-light bg-light" onchange="this.form.submit()">
                                    <option value="">-- Choose Section --</option>
                                    @foreach($sections as $s)
                                        <option value="{{ $s->section_id }}" {{ $section_id == $s->section_id ? 'selected' : '' }}>
                                            {{ $s->schoolClass->class_name ?? '??' }} ({{ $s->section->section_name ?? '??' }})
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>

                    @if($section_id && count($students) > 0)
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white fw-bold d-flex justify-content-between">
                                <span>2. Select Student</span>
                                <span class="badge bg-primary rounded-pill">{{ count($students) }}</span>
                            </div>
                            <div class="card-body p-0" style="max-height: 500px; overflow-y: auto;">
                                <div class="list-group list-group-flush">
                                    @foreach($students as $s)
                                        <a href="{{ route('results.section', ['section_id' => $section_id, 'student_id' => $s->id]) }}" 
                                           class="list-group-item list-group-item-action border-0 py-3 {{ $student_id == $s->id ? 'active' : '' }}">
                                            <div class="fw-bold">{{ $s->first_name }} {{ $s->last_name }}</div>
                                            <small class="{{ $student_id == $s->id ? 'text-white-50' : 'text-muted' }}">ID: {{ $s->id }}</small>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @elseif($section_id)
                        <div class="alert alert-info border-0 shadow-sm">No students found in this section.</div>
                    @endif
                </div>

                <!-- Results Display -->
                <div class="col-lg-8">
                    @if($selectedStudent)
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Consolidated Results: {{ $selectedStudent->first_name }} {{ $selectedStudent->last_name }}</h6>
                                <button onclick="window.print()" class="btn btn-sm btn-outline-secondary no-print">
                                    <i class="bi bi-printer"></i> Print
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-container">
                                    <table class="results-grid">
                                        <thead>
                                            <tr>
                                                <th class="ps-4">Course</th>
                                                @foreach($semesters as $semester)
                                                    <th class="text-center">{{ $semester->semester_name }}</th>
                                                @endforeach
                                                <th class="text-center bg-light">Annual</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($courses as $course)
                                                <tr class="results-row">
                                                    <td class="ps-4 fw-bold text-dark">{{ $course->course_name }}</td>
                                                    @php $annual = 0; $count = 0; @endphp
                                                    @foreach($semesters as $semester)
                                                        @php
                                                            $mark = isset($results[$course->id]) ? $results[$course->id]->where('semester_id', $semester->id)->first() : null;
                                                            if($mark) { $annual += $mark->final_marks; $count++; }
                                                        @endphp
                                                        <td class="text-center">
                                                            @if($mark)
                                                                <span class="clickable-mark {{ $mark->final_marks < 50 ? 'text-danger' : 'text-success' }}"
                                                                    data-student-id="{{ $selectedStudent->id }}"
                                                                    data-course-id="{{ $course->id }}"
                                                                    data-semester-id="{{ $semester->id }}">
                                                                    {{ number_format($mark->final_marks, 2) }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted opacity-25">--</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                    <td class="text-center cumulative-total">{{ $count > 0 ? number_format($annual, 2) : '0.00' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5 bg-white rounded shadow-sm border border-dashed h-100 d-flex flex-column justify-content-center">
                            <i class="bi bi-person-bounding-box fs-1 text-muted opacity-25"></i>
                            <h5 class="mt-3 text-muted">Select a section and student to view detailed results.</h5>
                        </div>
                    @endif
                </div>
            </div>

            @include('layouts.footer')
        </div>
    </div>
</div>

<!-- Breakdown Modal -->
<div class="modal fade" id="breakdownModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered shadow-lg">
        <div class="modal-content border-0">
            <div class="modal-header border-0 pb-0 shadow-sm">
                <h5 class="modal-title fw-bold">Assessment Breakdown</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="breakdownContent">
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('js/results-dashboard.js') }}"></script>
@endsection
