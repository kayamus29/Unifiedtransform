<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mark;
use App\Models\FinalMark;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Course;
use App\Models\Semester;
use App\Models\AssignedTeacher;
use App\Models\Promotion;
use App\Models\User;
use App\Traits\SchoolSession;
use App\Interfaces\SchoolSessionInterface;
use Illuminate\Support\Facades\Auth;
use Exception;

class ResultsDashboardController extends Controller
{
    use SchoolSession;

    protected $schoolSessionRepository;

    public function __construct(SchoolSessionInterface $schoolSessionRepository)
    {
        $this->middleware(['auth']);
        $this->schoolSessionRepository = $schoolSessionRepository;
    }

    /**
     * Teacher / Class Teacher View
     */
    /**
     * Subject Teacher View
     */
    public function teacherView(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('Teacher') && !$user->hasRole('Admin')) {
            abort(403);
        }

        $session_id = $this->getSchoolCurrentSession();

        // Handle combined course_class parameter
        $course_class = $request->query('course_class');
        $class_id = $request->query('class_id');
        $section_id = $request->query('section_id');
        $course_id = $request->query('course_id');

        if ($course_class) {
            $parts = explode('|', $course_class);
            if (count($parts) === 3) {
                $course_id = $parts[0] !== '' ? $parts[0] : null;
                $class_id = $parts[1];
                $section_id = $parts[2];
            }
        }

        // Fetch subject assignments only (course_id NOT NULL)
        $assignments = AssignedTeacher::with(['schoolClass', 'section', 'course'])
            ->where('teacher_id', $user->id)
            ->where('session_id', $session_id)
            ->whereNotNull('course_id')
            ->get();

        $semesters = Semester::where('session_id', $session_id)->orderBy('id')->get();

        $students = [];
        $results = [];

        if ($class_id && $course_id) {
            // Validate ownership
            if ($user->hasRole('Teacher')) {
                $isAssigned = $assignments->where('class_id', $class_id)->where('course_id', $course_id)->first();
                if (!$isAssigned && !$user->hasRole('Admin')) {
                    abort(403, 'Unauthorized access to this course/class.');
                }
            }

            // Get students in this section
            $students = Promotion::with('student')
                ->where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->where('session_id', $session_id)
                ->get()
                ->pluck('student');

            // Fetch all final marks for these students in this course across all semesters
            $results = FinalMark::where('course_id', $course_id)
                ->where('session_id', $session_id)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->groupBy('student_id');
        }

        return view('results.teacher', compact('assignments', 'semesters', 'students', 'results', 'class_id', 'section_id', 'course_id'));
    }

    /**
     * Class/Section Teacher View
     */
    public function sectionView(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('Teacher') && !$user->hasRole('Admin')) {
            abort(403);
        }

        $session_id = $this->getSchoolCurrentSession();
        $section_id = $request->query('section_id');
        $student_id = $request->query('student_id');

        // Fetch section assignments only (course_id NULL)
        $sections = AssignedTeacher::with(['schoolClass', 'section'])
            ->where('teacher_id', $user->id)
            ->where('session_id', $session_id)
            ->whereNull('course_id')
            ->get();

        $students = [];
        $selectedStudent = null;
        $results = [];
        $courses = [];
        $semesters = Semester::where('session_id', $session_id)->orderBy('id')->get();

        if ($section_id) {
            // Validate ownership
            if ($user->hasRole('Teacher')) {
                $isAssigned = $sections->where('section_id', $section_id)->first();
                if (!$isAssigned && !$user->hasRole('Admin')) {
                    abort(403, 'Unauthorized access to this section.');
                }
            }

            // Fetch students in this section
            $students = Promotion::with('student')
                ->where('section_id', $section_id)
                ->where('session_id', $session_id)
                ->get()
                ->pluck('student');

            if ($student_id) {
                $selectedStudent = $students->where('id', $student_id)->first();
                if ($selectedStudent) {
                    $promotion = Promotion::where('student_id', $student_id)
                        ->where('session_id', $session_id)
                        ->with(['schoolClass.courses'])
                        ->first();

                    if ($promotion) {
                        $courses = $promotion->schoolClass->courses;
                        $results = FinalMark::where('student_id', $student_id)
                            ->where('session_id', $session_id)
                            ->get()
                            ->groupBy('course_id');
                    }
                }
            }
        }

        return view('results.section', compact('sections', 'students', 'selectedStudent', 'results', 'courses', 'semesters', 'section_id', 'student_id'));
    }

    /**
     * Student View
     */
    public function studentView()
    {
        $student = Auth::user();
        if (!$student->hasRole('Student')) {
            abort(403);
        }

        $session_id = $this->getSchoolCurrentSession();
        $semesters = Semester::where('session_id', $session_id)->orderBy('id')->get();

        // Apply Financial Withholding Gate
        if (!\App\Classes\AcademicGate::canViewResults($student)) {
            $withheld = true;
            return view('results.student', compact('student', 'session_id', 'semesters', 'withheld'));
        }

        // Get all courses student is registered in (via Promotion -> Class -> Courses)
        $promotion = Promotion::where('student_id', $student->id)
            ->where('session_id', $session_id)
            ->with(['schoolClass.courses'])
            ->first();

        if (!$promotion) {
            return view('results.student', ['error' => 'No active enrollment found for current session.']);
        }

        $courses = $promotion->schoolClass->courses;

        // Fetch all final marks for this student
        $results = FinalMark::where('student_id', $student->id)
            ->where('session_id', $session_id)
            ->get()
            ->groupBy('course_id');

        return view('results.student', compact('student', 'semesters', 'courses', 'results', 'promotion'));
    }

    /**
     * Admin View - Student Search
     */
    public function adminView(Request $request)
    {
        if (!Auth::user()->hasRole('Admin')) {
            abort(403);
        }

        $session_id = $this->getSchoolCurrentSession();
        $student_id = $request->query('student_id');
        $student = null;
        $results = [];
        $courses = [];
        $semesters = Semester::where('session_id', $session_id)->orderBy('id')->get();

        if ($student_id) {
            $student = User::find($student_id);
            $promotion = Promotion::where('student_id', $student_id)
                ->where('session_id', $session_id)
                ->with(['schoolClass.courses'])
                ->first();

            if ($promotion) {
                $courses = $promotion->schoolClass->courses;
                $results = FinalMark::where('student_id', $student_id)
                    ->where('session_id', $session_id)
                    ->get()
                    ->groupBy('course_id');
            }
        }

        $allStudents = User::role('Student')->get();

        return view('results.admin', compact('student', 'results', 'courses', 'semesters', 'allStudents'));
    }

    /**
     * AJAX Breakdown for Modal
     */
    public function getBreakdownAjax(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id'
        ]);

        $session_id = $this->getSchoolCurrentSession();

        $marks = Mark::with('exam')
            ->where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->where('session_id', $session_id)
            ->whereHas('exam', function ($q) use ($request) {
                $q->where('semester_id', $request->semester_id);
            })
            ->get();

        $finalMark = FinalMark::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->where('semester_id', $request->semester_id)
            ->where('session_id', $session_id)
            ->first();

        return response()->json([
            'success' => true,
            'assessments' => $marks,
            'summary' => $finalMark
        ]);
    }
}
