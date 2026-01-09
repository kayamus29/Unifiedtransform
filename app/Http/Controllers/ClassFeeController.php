<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassFee;
use App\Models\SchoolClass;
use App\Models\FeeHead;
use App\Models\SchoolSession;
use App\Models\Semester;
use App\Traits\SchoolSession as SchoolSessionTrait;
use App\Interfaces\SchoolSessionInterface;
use Exception;

class ClassFeeController extends Controller
{
    use SchoolSessionTrait;

    protected $schoolSessionRepository;

    public function __construct(SchoolSessionInterface $schoolSessionRepository)
    {
        $this->middleware(['auth', 'role:Accountant|Admin']);
        $this->schoolSessionRepository = $schoolSessionRepository;
    }

    public function index()
    {
        // Get all classes with their assigned fees
        $classes = SchoolClass::with(['classFees.feeHead', 'classFees.session', 'classFees.semester'])->get();
        // Also need list of classes and fee heads for the 'Add' modal
        $allClasses = SchoolClass::all();
        $feeHeads = FeeHead::all();

        $sessions = SchoolSession::all();
        $current_session_id = $this->getSchoolCurrentSession();
        $semesters = Semester::where('session_id', $current_session_id)->get();

        return view('accounting.fees.class.index', compact('classes', 'allClasses', 'feeHeads', 'sessions', 'semesters', 'current_session_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'fee_head_id' => 'required|exists:fee_heads,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'session_id' => 'required|exists:school_sessions,id',
            'semester_id' => 'required|exists:semesters,id'
        ]);

        try {
            // Check if this fee head is already assigned to this class in this session/semester
            $exists = ClassFee::where('class_id', $request->class_id)
                ->where('fee_head_id', $request->fee_head_id)
                ->where('session_id', $request->session_id)
                ->where('semester_id', $request->semester_id)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'This Fee Head is already assigned to the selected Class for this Term.');
            }

            ClassFee::create($request->all());
            return redirect()->back()->with('success', 'Fee assigned to class successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error assigning fee: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $classFee = ClassFee::findOrFail($id);
            $classFee->delete();
            return redirect()->back()->with('success', 'Fee assignment removed successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error removing fee assignment: ' . $e->getMessage());
        }
    }
}
