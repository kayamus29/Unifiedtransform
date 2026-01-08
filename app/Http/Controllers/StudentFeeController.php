<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentFee;
use App\Models\User;
use App\Models\FeeHead;
use App\Models\SchoolSession;
use App\Models\Semester;
use App\Traits\SchoolSession as SchoolSessionTrait;
use App\Interfaces\SchoolSessionInterface;
use Exception;

class StudentFeeController extends Controller
{
    use SchoolSessionTrait;

    protected $schoolSessionRepository;

    public function __construct(SchoolSessionInterface $schoolSessionRepository)
    {
        $this->schoolSessionRepository = $schoolSessionRepository;
    }

    public function index()
    {
        $current_session_id = $this->getSchoolCurrentSession();

        $studentFees = StudentFee::with(['student', 'feeHead', 'session', 'semester'])
            ->where('session_id', $current_session_id)
            ->latest()
            ->get();

        $students = User::where('role', 'student')->get(['id', 'first_name', 'last_name']);
        $feeHeads = FeeHead::all();
        $sessions = SchoolSession::all();
        $semesters = Semester::where('session_id', $current_session_id)->get();

        return view('accounting.fees.student.index', compact('studentFees', 'students', 'feeHeads', 'sessions', 'semesters', 'current_session_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'fee_head_id' => 'required|exists:fee_heads,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'session_id' => 'required|exists:school_sessions,id',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        try {
            StudentFee::create($request->all());
            return redirect()->back()->with('success', 'Fee assigned to student successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error assigning fee: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $fee = StudentFee::findOrFail($id);
            $fee->delete();
            return redirect()->back()->with('success', 'Student fee removed successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error removing fee: ' . $e->getMessage());
        }
    }
}
