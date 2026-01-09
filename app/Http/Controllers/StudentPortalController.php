<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Mark;
use App\Models\Routine;
use App\Models\Notice;
use App\Models\Promotion;
use App\Traits\SchoolSession;
use App\Interfaces\SchoolSessionInterface;

class StudentPortalController extends Controller
{
    use SchoolSession;
    protected $schoolSessionRepository;

    public function __construct(SchoolSessionInterface $schoolSessionRepository)
    {
        $this->middleware(['auth', 'role:Student']);
        $this->schoolSessionRepository = $schoolSessionRepository;
    }

    /**
     * Student Dashboard - Overview
     */
    public function dashboard()
    {
        $student = Auth::user();
        $current_session_id = $this->getSchoolCurrentSession();

        // Get student's current class/section
        $promotion = Promotion::where('student_id', $student->id)
            ->where('session_id', $current_session_id)
            ->with(['schoolClass', 'section'])
            ->first();

        // Recent attendance
        $recentAttendance = Attendance::where('student_id', $student->id)
            ->where('school_session_id', $current_session_id)
            ->latest()
            ->take(5)
            ->get();

        // Attendance summary
        $totalPresent = Attendance::where('student_id', $student->id)
            ->where('school_session_id', $current_session_id)
            ->where('status', 'Present')
            ->count();

        $totalAbsent = Attendance::where('student_id', $student->id)
            ->where('school_session_id', $current_session_id)
            ->where('status', 'Absent')
            ->count();

        // Recent notices
        $notices = Notice::where('session_id', $current_session_id)
            ->latest()
            ->take(3)
            ->get();

        return view('student.dashboard', compact(
            'student',
            'promotion',
            'recentAttendance',
            'totalPresent',
            'totalAbsent',
            'notices'
        ));
    }

    /**
     * View full attendance history
     */
    public function attendance()
    {
        $student = Auth::user();
        $current_session_id = $this->getSchoolCurrentSession();

        $attendance = Attendance::where('student_id', $student->id)
            ->where('school_session_id', $current_session_id)
            ->with('schoolClass')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('student.attendance', compact('attendance', 'student'));
    }

    /**
     * View marks/grades
     */
    public function marks()
    {
        $student = Auth::user();
        $current_session_id = $this->getSchoolCurrentSession();

        $marks = Mark::where('student_id', $student->id)
            ->where('school_session_id', $current_session_id)
            ->with(['course', 'exam'])
            ->get();

        return view('student.marks', compact('marks', 'student'));
    }

    /**
     * View timetable/routine
     */
    public function timetable()
    {
        $student = Auth::user();
        $current_session_id = $this->getSchoolCurrentSession();

        // Get student's section
        $promotion = Promotion::where('student_id', $student->id)
            ->where('session_id', $current_session_id)
            ->first();

        $routines = [];
        if ($promotion) {
            $routines = Routine::where('section_id', $promotion->section_id)
                ->where('session_id', $current_session_id)
                ->orderBy('weekday')
                ->orderBy('start_time')
                ->get();
        }

        return view('student.timetable', compact('routines', 'student'));
    }
}
