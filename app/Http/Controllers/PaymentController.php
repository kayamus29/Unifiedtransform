<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentPayment;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\SchoolSession;
use App\Models\Semester;
use Exception;
use App\Services\FinancialService;

class PaymentController extends Controller
{
    protected $financialService;

    public function __construct(FinancialService $financialService)
    {
        $this->middleware(['auth', 'role:Accountant|Admin']);
        $this->financialService = $financialService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = StudentPayment::with(['student', 'schoolClass', 'session', 'semester']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })
                    ->orWhereHas('student.promotions', function ($pq) use ($search) {
                        $pq->where('id_card_number', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->latest('transaction_date')
            ->paginate(20)
            ->appends(['search' => $search]);

        // Calculate balances for the current view using centralized logic
        foreach ($payments as $payment) {
            if ($payment->student) {
                $payment->outstanding_balance = $payment->student->getTotalOutstandingBalance();
                $payment->total_fees = $payment->student->getTotalFees();
            } else {
                $payment->outstanding_balance = 0;
                $payment->total_fees = 0;
            }
        }

        return view('accounting.payments.index', compact('payments'));
    }

    public function create()
    {
        $students = User::where('role', 'student')->get(['id', 'first_name', 'last_name']);
        $classes = SchoolClass::all();
        $sessions = SchoolSession::all();
        $semesters = Semester::all();

        $student_id = request('student_id');
        $fees = [];
        if ($student_id) {
            $fees = StudentFee::where('student_id', $student_id)->where('balance', '>', 0)->get();
        }

        return view('accounting.payments.create', compact('students', 'classes', 'sessions', 'semesters', 'fees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:school_classes,id',
            'amount_paid' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
        ]);

        try {
            // Auto-detect session/semester if not provided? 
            // For now, let's make them required or fallback to latest.
            $session_id = $request->school_session_id ?? SchoolSession::latest()->first()->id;
            $semester_id = $request->semester_id ?? Semester::where('session_id', $session_id)->first()->id; // Fallback risky

            // Better to force user selection to be accurate
            $request->validate([
                'school_session_id' => 'required|exists:school_sessions,id',
                'semester_id' => 'required|exists:semesters,id',
            ]);

            $ref = 'PAY-' . strtoupper(uniqid());

            $payment = StudentPayment::create([
                'student_id' => $request->student_id,
                'student_fee_id' => $request->student_fee_id, // Link to specific fee
                'class_id' => $request->class_id,
                'session_id' => $request->school_session_id,
                'semester_id' => $request->semester_id,
                'amount_paid' => $request->amount_paid,
                'payment_method' => $request->payment_method,
                'transaction_date' => $request->transaction_date,
                'reference_no' => $request->reference_no ?? $ref,
                'received_by' => auth()->id(),
            ]);

            // Process the payment through the financial service
            $this->financialService->recordPayment($payment);

            return redirect()->route('accounting.payments.index')->with('success', 'Payment recorded successfully. Ref: ' . $payment->reference_no);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error recording payment: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $payment = StudentPayment::with(['student', 'schoolClass', 'session', 'semester'])->findOrFail($id);
        return view('accounting.payments.show', compact('payment'));
    }
}
