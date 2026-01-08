<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentParentInfo;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Traits\SchoolSession;

class ParentPortalController extends Controller
{
    use SchoolSession;

    public function __construct()
    {
        $this->middleware(['auth', 'role:Parent']);
    }

    /**
     * Helper to get all linked children for the logged-in parent.
     */
    private function getLinkedChildren()
    {
        // 1. Get info records linked to this parent
        $infos = StudentParentInfo::where('parent_user_id', Auth::id())
            ->with('student')
            ->get();

        // 2. Return the actual User (Student) objects
        return $infos->map(function ($info) {
            return $info->student;
        })->filter(); // Filter out nulls if student was deleted
    }

    /**
     * Parent Dashboard / Landing.
     * Handles selection logic.
     */
    public function dashboard()
    {
        $children = $this->getLinkedChildren();

        if ($children->isEmpty()) {
            return view('parent.no_children');
        }

        // If multiple children, or user explicitly went to "dashboard" to switch
        // We always show selection if there's more than 1, OR if we want to enforce explicitly.
        // Guidelines say: "If a Parent has more than one linked child: Show a child selection screen"

        if ($children->count() > 1) {
            return view('parent.selection', ['children' => $children]);
        }

        // If only 1 child, strictly redirect to that child's dashboard for convenience
        return redirect()->route('parent.child.dashboard', ['student_id' => $children->first()->id]);
    }

    /**
     * View a specific child's dashboard.
     */
    public function childDashboard($student_id)
    {
        // 1. STRICT SECURITY CHECK
        $linkedChildren = $this->getLinkedChildren();

        if (!$linkedChildren->contains('id', $student_id)) {
            abort(403, 'Unauthorized access to this student.');
        }

        $student = $linkedChildren->where('id', $student_id)->first();
        $current_session_id = $this->getSchoolCurrentSession();

        // 2. Fetch Data (Read Only)
        // Attendance Today / Recent
        $attendance = Attendance::where('student_id', $student_id)
            ->where('school_session_id', $current_session_id)
            ->latest()
            ->take(5)
            ->get();

        // Marks (Just a count or summary for now)
        // This is a placeholder for the full marks view
        $marksCount = Mark::where('student_id', $student_id)
            ->where('school_session_id', $current_session_id)
            ->count();

        return view('parent.child_dashboard', [
            'student' => $student,
            'children' => $linkedChildren, // Passed for the switcher
            'attendance' => $attendance,
            'marksCount' => $marksCount
        ]);
    }
}
