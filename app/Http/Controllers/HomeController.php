<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\SchoolSession;
use App\Interfaces\UserInterface;
use App\Repositories\NoticeRepository;
use App\Interfaces\SchoolClassInterface;
use App\Interfaces\SchoolSessionInterface;
use App\Repositories\PromotionRepository;

class HomeController extends Controller
{
    use SchoolSession;
    protected $schoolSessionRepository;
    protected $schoolClassRepository;
    protected $userRepository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        UserInterface $userRepository,
        SchoolSessionInterface $schoolSessionRepository,
        SchoolClassInterface $schoolClassRepository
    ) {
        // $this->middleware('auth');
        $this->userRepository = $userRepository;
        $this->schoolSessionRepository = $schoolSessionRepository;
        $this->schoolClassRepository = $schoolClassRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $current_school_session_id = $this->getSchoolCurrentSession();

        $classCount = $this->schoolClassRepository->getAllBySession($current_school_session_id)->count();

        $studentCount = $this->userRepository->getAllStudentsBySessionCount($current_school_session_id);

        $promotionRepository = new PromotionRepository();

        $maleStudentsBySession = $promotionRepository->getMaleStudentsBySessionCount($current_school_session_id);

        $teacherCount = $this->userRepository->getAllTeachers()->count();

        $noticeRepository = new NoticeRepository();
        $notices = $noticeRepository->getAll($current_school_session_id);

        // Absences Today
        $absentStaff = [];
        $absentStudents = [];
        $isSchoolDay = \Carbon\Carbon::now()->isWeekday();

        if ($isSchoolDay && auth()->user()->role == 'admin') {
            // Staff Absence: All staff who haven't checked in today
            $today = \Carbon\Carbon::today();
            $staffIdsWithAttendance = \App\Models\StaffAttendance::where('date', $today->toDateString())
                ->pluck('user_id')
                ->toArray();

            $absentStaff = \App\Models\User::whereIn('role', ['staff', 'librarian'])
                ->whereNotIn('id', $staffIdsWithAttendance)
                ->get();

            // Student Absence: Students marked 'Absent' in the attendances table for today
            $absentStudents = \App\Models\Attendance::with('student', 'schoolClass')
                ->whereDate('created_at', $today)
                ->where('status', 'Absent')
                ->get();
        }

        $data = [
            'classCount' => $classCount,
            'studentCount' => $studentCount,
            'teacherCount' => $teacherCount,
            'notices' => $notices,
            'maleStudentsBySession' => $maleStudentsBySession,
            'absentStaff' => $absentStaff,
            'absentStudents' => $absentStudents,
            'isSchoolDay' => $isSchoolDay,
        ];

        return view('home', $data);
    }
}
