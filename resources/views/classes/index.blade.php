@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-start">
        @include('layouts.left-menu')
        <div class="col-xs-11 col-sm-11 col-md-11 col-lg-10 col-xl-10 col-xxl-10">
            <div class="row pt-2">
                <div class="col ps-4">
                    <h1 class="display-6 mb-3"><i class="bi bi-diagram-3"></i> Classes</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Classes</li>
                        </ol>
                    </nav>
                    <div class="row">
                        @isset($school_classes)
                            @foreach ($school_classes as $school_class)
                            @php
                                $total_sections = 0;
                            @endphp
                                <div class="col-12">
                                    <div class="card my-3">
                                        <div class="card-header bg-transparent">
                                            <ul class="nav nav-tabs card-header-tabs">
                                                <li class="nav-item">
                                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#class{{$school_class->id}}" role="tab" aria-current="true"><i class="bi bi-diagram-3"></i> {{$school_class->class_name}}</button>
                                                </li>
                                                <li class="nav-item">
                                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#class{{$school_class->id}}-syllabus" role="tab" aria-current="false"><i class="bi bi-journal-text"></i> Syllabus</button>
                                                </li>
                                                <li class="nav-item">
                                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#class{{$school_class->id}}-courses" role="tab" aria-current="false"><i class="bi bi-journal-medical"></i> Courses</button>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card-body text-dark">
                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="class{{$school_class->id}}" role="tabpanel">
                                                    <div class="accordion" id="accordionClass{{$school_class->id}}">
                                                        @isset($school_sections)
                                                            @foreach ($school_sections as $school_section)
                                                                @if ($school_section->class_id == $school_class->id)
                                                                    @php
                                                                        $total_sections++;
                                                                    @endphp
                                                                    <div class="accordion-item">
                                                                        <h2 class="accordion-header" id="headingClass{{$school_class->id}}Section{{$school_section->id}}">
                                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionClass{{$school_class->id}}Section{{$school_section->id}}" aria-expanded="false" aria-controls="accordionClass{{$school_class->id}}Section{{$school_section->id}}">
                                                                            {{$school_section->section_name}}
                                                                        </button>
                                                                        </h2>
                                                                        <div id="accordionClass{{$school_class->id}}Section{{$school_section->id}}" class="accordion-collapse collapse" aria-labelledby="headingClass{{$school_class->id}}Section{{$school_section->id}}" data-bs-parent="#accordionClass{{$school_class->id}}">
                                                                            <div class="accordion-body">
                                                                                <p class="lead d-flex justify-content-between">
                                                                                    <span>Room No: {{$school_section->room_no}}</span>
                                                                                    @can('edit sections')
                                                                                    <span><a href="{{route('section.edit', ['id' => $school_section->id])}}" role="button" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a></span>
                                                                                    @endcan
                                                                                </p>
                                                                                <div class="list-group">
                                                                                    <a href="{{route('student.list.show', ['class_id' => $school_class->id, 'section_id' => $school_section->id, 'section_name' => $school_section->section_name])}}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                                                        View Students
                                                                                    </a>
                                                                                    <a href="{{route('section.routine.show', ['class_id' => $school_class->id, 'section_id' => $school_section->id])}}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                                                        View Routine
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        @endisset
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="class{{$school_class->id}}-syllabus" role="tabpanel">
                                                    @isset($school_class->syllabi)
                                                    <table class="table table-borderless">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col">Syllabus Name</th>
                                                            <th scope="col">Actions</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach ($school_class->syllabi as $syllabus)
                                                            <tr>
                                                            <td>{{$syllabus->syllabus_name}}</td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <a href="{{asset('storage/'.$syllabus->syllabus_file_path)}}" role="button" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i> Download</a>
                                                                </div>
                                                            </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                    @endisset
                                                </div>
                                                <div class="tab-pane fade" id="class{{$school_class->id}}-courses" role="tabpanel">
                                                    @isset($school_class->courses)
                                                        <table class="table">
                                                            <thead>
                                                            <tr>
                                                                <th scope="col">Course Name</th>
                                                                <th scope="col">Type</th>
                                                                <th scope="col">Actions</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($school_class->courses as $course)
                                                            <tr>
                                                                <td>{{$course->course_name}}</td>
                                                                <td>{{$course->course_type}}</td>
                                                                <td>
                                                                    @can('edit courses')
                                                                    <a href="{{route('course.edit', ['id' => $course->id])}}" class="btn btn-sm btn-outline-primary" role="button"><i class="bi bi-pencil"></i> Edit</a>
                                                                    @endcan
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    @endisset
                                                </div>
                                            </div>
                                        </div>
                                            <div class="mt-2 mb-2">
                                                <strong>Class Teacher:</strong>
                                                @php
                                                    $classTeacher = $school_class->assignedTeachers->whereNull('course_id')->first();
                                                @endphp
                                                <span class="text-primary">{{ $classTeacher ? $classTeacher->teacher->first_name . ' ' . $classTeacher->teacher->last_name : 'Not Assigned' }}</span>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent d-flex justify-content-between">
                                            @isset($total_sections)
                                                <span>Total Sections: {{$total_sections}}</span>
                                            @endisset
                                            <div>
                                                @can('assign teachers') 
                                                    {{-- Assuming a permission exists, or use 'edit classes' --}}
                                                    <button type="button" class="btn btn-sm btn-outline-dark me-2" data-bs-toggle="modal" data-bs-target="#assignTeacherModal{{$school_class->id}}">
                                                        <i class="bi bi-person-badge"></i> Assign Teachers
                                                    </button>
                                                @endcan
                                                @can('edit classes')
                                                <a href="{{route('class.edit', ['id' => $school_class->id])}}" class="btn btn-sm btn-outline-primary" role="button"><i class="bi bi-pencil"></i> Edit Class</a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Assign Teacher Modal -->
                                <div class="modal fade" id="assignTeacherModal{{$school_class->id}}" tabindex="-1" aria-labelledby="assignTeacherModalLabel{{$school_class->id}}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="assignTeacherModalLabel{{$school_class->id}}">Assign Teachers - {{$school_class->class_name}}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{route('school.teacher.assign.bulk')}}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <input type="hidden" name="class_id" value="{{$school_class->id}}">
                                                    <input type="hidden" name="session_id" value="{{$school_class->session_id}}"> 
                                                    
                                                    <div class="mb-4">
                                                        <h6 class="border-bottom pb-2">Class Teacher</h6>
                                                        <div class="mb-3">
                                                            <label class="form-label small">Select Class Teacher</label>
                                                            <select class="form-select form-select-sm" name="class_teacher_id">
                                                                <option value="">-- Select Teacher --</option>
                                                                @foreach($teachers as $teacher)
                                                                    <option value="{{$teacher->id}}" 
                                                                        @if($classTeacher && $classTeacher->teacher_id == $teacher->id) selected @endif>
                                                                        {{$teacher->first_name}} {{$teacher->last_name}}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="mb-4">
                                                        <h6 class="border-bottom pb-2">Course Teachers</h6>
                                                        @if($school_class->courses->count() > 0)
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Course</th>
                                                                            <th>Current Teacher</th>
                                                                            <th>Assign New</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($school_class->courses as $course)
                                                                            @php
                                                                                // Find existing assignment for this course
                                                                                $courseAssignment = $school_class->assignedTeachers->where('course_id', $course->id)->first();
                                                                            @endphp
                                                                            <tr>
                                                                                <td>{{$course->course_name}} <small class="text-muted">({{$course->course_type}})</small></td>
                                                                                <td>
                                                                                    @if($courseAssignment)
                                                                                        <span class="badge bg-success">{{$courseAssignment->teacher->first_name}} {{$courseAssignment->teacher->last_name}}</span>
                                                                                    @else
                                                                                        <span class="badge bg-secondary">Unassigned</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    <select class="form-select form-select-sm" name="course_teachers[{{$course->id}}]">
                                                                                        <option value="">-- Select --</option>
                                                                                        @foreach($teachers as $teacher)
                                                                                            <option value="{{$teacher->id}}"
                                                                                                @if($courseAssignment && $courseAssignment->teacher_id == $teacher->id) selected @endif>
                                                                                                {{$teacher->first_name}} {{$teacher->last_name}}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @else
                                                            <p class="text-muted">No courses found for this class.</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endisset
                    </div>
                </div>
            </div>
            @include('layouts.footer')
        </div>
    </div>
</div>
@endsection
