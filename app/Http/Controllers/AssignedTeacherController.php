<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\SchoolSession;
use App\Interfaces\SemesterInterface;
use App\Interfaces\SchoolSessionInterface;
use App\Http\Requests\TeacherAssignRequest;
use App\Repositories\AssignedTeacherRepository;

class AssignedTeacherController extends Controller
{
    use SchoolSession;
    protected $schoolSessionRepository;
    protected $semesterRepository;

    /**
     * Create a new Controller instance
     * 
     * @param SchoolSessionInterface $schoolSessionRepository
     * @return void
     */
    public function __construct(
        SchoolSessionInterface $schoolSessionRepository,
        SemesterInterface $semesterRepository
    ) {
        $this->schoolSessionRepository = $schoolSessionRepository;
        $this->semesterRepository = $semesterRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @param  CourseStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function getTeacherCourses(Request $request)
    {
        $teacher_id = $request->query('teacher_id');
        $semester_id = $request->query('semester_id');

        if ($teacher_id == null) {
            abort(404);
        }

        $current_school_session_id = $this->getSchoolCurrentSession();

        $semesters = $this->semesterRepository->getAll($current_school_session_id);

        $assignedTeacherRepository = new AssignedTeacherRepository();

        if ($semester_id == null) {
            $courses = [];
        } else {
            $courses = $assignedTeacherRepository->getTeacherCourses($current_school_session_id, $teacher_id, $semester_id);
        }

        $data = [
            'courses' => $courses,
            'semesters' => $semesters,
            'selected_semester_id' => $semester_id,
        ];

        return view('courses.teacher', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TeacherAssignRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TeacherAssignRequest $request)
    {
        try {
            $assignedTeacherRepository = new AssignedTeacherRepository();
            $assignedTeacherRepository->assign($request->validated());

            return back()->with('status', 'Assigning teacher was successful!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function bulkAssign(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'session_id' => 'required|exists:school_sessions,id',
            'class_teacher_id' => 'nullable|exists:users,id',
            'course_teachers' => 'nullable|array',
            'course_teachers.*' => 'nullable|exists:users,id',
        ]);

        try {
            $current_school_session_id = $this->getSchoolCurrentSession();
            // Default active semester (or passed in request if needed, assuming current session active semester)
            // For simplicity and matching existing logic, we might need a semester.
            // But Class Teacher might be per Session.
            // Existing schema has semester_id as NOT NULL? No, it wasn't made nullable.
            // So we MUST have a semester.
            // Let's assume generic "First Semester" or active one if not provided?
            // Existing logic in Repository fetches first semester if 0.

            $semester_id = $this->semesterRepository->getAll($current_school_session_id)->first()->id ?? 1; // Fallback or strict

            // 1. Assign Class Teacher (Course NULL, Section NULL)
            if ($request->has('class_teacher_id')) {
                \App\Models\AssignedTeacher::updateOrCreate(
                    [
                        'class_id' => $request->class_id,
                        'session_id' => $request->session_id,
                        'course_id' => null,
                        'section_id' => null,
                        'semester_id' => $semester_id
                    ],
                    [
                        'teacher_id' => $request->class_teacher_id
                    ]
                );
            }

            // 2. Assign Course Teachers
            if ($request->has('course_teachers')) {
                foreach ($request->course_teachers as $course_id => $teacher_id) {
                    if ($teacher_id) {
                        \App\Models\AssignedTeacher::updateOrCreate(
                            [
                                'class_id' => $request->class_id,
                                'session_id' => $request->session_id,
                                'course_id' => $course_id,
                                'section_id' => null, // Assuming general course assignment for class
                                'semester_id' => $semester_id
                            ],
                            [
                                'teacher_id' => $teacher_id
                            ]
                        );
                    }
                }
            }

            return back()->with('status', 'Teachers assigned successfully!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }
}
