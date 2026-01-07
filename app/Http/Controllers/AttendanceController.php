<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\SchoolSession;
use App\Models\Section;
use App\Models\User;
use App\Models\AssignedTeacher; // For scoping
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    // Helper: Scoped Class Query (Returns Builder)
    private function getAccessibleClasses()
    {
        $user = Auth::user();

        // 1. Admin: Return Query Builder for all classes
        if ($user->hasRole('Admin')) {
            return SchoolClass::query();
        }

        // 2. Teacher: Strict Scoping with Session Context
        if ($user->hasRole('Teacher')) {
            // Determine active session dynamically or use latest
            $latest_session = SchoolSession::latest()->first();
            $session_id = $latest_session ? $latest_session->id : 0;

            return SchoolClass::whereIn('id', function ($query) use ($user, $session_id) {
                $query->select('class_id')
                    ->from('assigned_teachers')
                    ->where('teacher_id', $user->id)
                    ->where('session_id', $session_id); // Strict Session Scope
            });
        }

        // 3. Default Deny: Return Query Builder that matches nothing
        return SchoolClass::whereRaw('1 = 0');
    }

    public function index()
    {
        // Permission Check
        if (!Auth::user()->can('take attendance')) {
            abort(403);
        }

        // Execute Query Here
        $classes = $this->getAccessibleClasses()->get();

        return view('attendance.index', compact('classes'));
    }

    public function show(Request $request)
    {
        if (!Auth::user()->can('take attendance')) {
            abort(403);
        }

        // Validate Ownership in Read Request
        $request->validate([
            'class_id' => [
                'required',
                // Admin bypass or Teacher Scoping
                function ($attribute, $value, $fail) {
                    if (Auth::user()->hasRole('Admin'))
                        return;

                    $latest_session = SchoolSession::latest()->first();
                    $session_id = $latest_session ? $latest_session->id : 0;

                    $exists = AssignedTeacher::where('teacher_id', Auth::id())
                        ->where('class_id', $value)
                        ->where('session_id', $session_id) // Strict Session Scope
                        ->exists();

                    if (!$exists) {
                        $fail('You are not assigned to this class for the current session.');
                    }
                },
            ],
            'section_id' => 'required',
            'date' => 'required|date',
        ]);

        // ... existing logic ...
    }

    public function store(Request $request)
    {
        // Permission Check
        if (!Auth::user()->can('take attendance')) {
            abort(403);
        }

        // Strict Validation with Scoping
        $request->validate([
            'class_id' => [
                'required',
                'integer',
                // Scoping Rule
                function ($attribute, $value, $fail) {
                    if (Auth::user()->hasRole('Admin'))
                        return;

                    $latest_session = SchoolSession::latest()->first();
                    $session_id = $latest_session ? $latest_session->id : 0;

                    $exists = AssignedTeacher::where('teacher_id', Auth::id())
                        ->where('class_id', $value)
                        ->where('session_id', $session_id) // Strict Session Scope
                        ->exists();

                    if (!$exists) {
                        $fail('You are not authorized to take attendance for this class in this session.');
                    }
                },
            ],
            'section_id' => 'required|integer',
            'date' => 'required|date',
            'attendances' => 'required|array',
        ]);

        // ... existing store logic ...
    }
}
