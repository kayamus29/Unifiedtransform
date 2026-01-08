<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentPayment;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\SchoolSession;
use App\Models\Semester;
use Exception;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = StudentPayment::with(['student', 'schoolClass', 'session', 'semester']);

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('id_card_number', 'like', "%{$search}%"); // ID card number might be in users or promotion table?
                // Actually, id_card_number is in many places. Let's check User model/table.
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

        return view('accounting.payments.create', compact('students', 'classes', 'sessions', 'semesters'));
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
            $semester_id = $request->semester_id ?? Semester::where('school_session_id', $session_id)->first()->id; // Fallback risky

            // Better to force user selection to be accurate
            $request->validate([
                'school_session_id' => 'required|exists:school_sessions,id',
                'semester_id' => 'required|exists:semesters,id',
            ]);

            $ref = 'PAY-' . strtoupper(uniqid());

            $payment = StudentPayment::create([
                'student_id' => $request->student_id,
                'class_id' => $request->class_id,
                'school_session_id' => $request->school_session_id,
                'semester_id' => $request->semester_id,
                'amount_paid' => $request->amount_paid,
                'transaction_date' => $request->transaction_date,
                'reference_no' => $request->reference_no ?? $ref,
            ]);

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
