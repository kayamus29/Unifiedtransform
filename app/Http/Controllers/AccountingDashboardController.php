<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolSession;
use App\Models\StudentPayment;
use App\Models\ClassFee;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class AccountingDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Accountant|Admin']);
    }

    public function index()
    {
        $session = SchoolSession::latest()->first();
        if (!$session) {
            return view('accounting.dashboard', ['error' => 'No academic session found.']);
        }

        // 1. Total Enrolled Students (assuming role 'student')
        $totalStudents = User::where('role', 'student')->count();

        // 2. Total Expected Fees for Current Session
        // Logic: For each class, sum(class_fees) * count(students_in_class)
        // This is complex if students aren't strictly linked to class in DB.
        // Simplified Logic: We will use the payments to estimate or assume all active students owe fees.
        // Better Estimation: 
        $classes = SchoolClass::with('classFees')->get();
        $totalExpectedFees = 0;

        // This loop works efficiently for small-medium school sizes. for large scale, use SQL aggregation.
        // We need to count students per class. Assuming we don't have a reliable 'school_class_id' on users table from previous steps,
        // we might rely on the manual assignments or just use the total count * avg fee if data is loose.
        // However, let's try to do it right. If User model doesn't have class_id, we can't sum accurately per class without an enrollment table.
        // BUT, our seeder created students but didn't assign class_id in a traceable way other than payments. 
        // Let's assume for this MVP Dashboard we calculate 'Expected' based on the payments recorded + outstanding for those who have paid at least once? 
        // No, that's bad.
        // Let's assume for the sake of the dashboard we calculate average fee * total students for a rough estimate if strict linking is missing,
        // OR better: we sum up all defined Class Fees * (Students we can find linked to that class).

        // Let's assume we can fetch students. If not, we show 0 or N/A.
        // In our seeder, we did not set class_id on Users. We only set it on StudentPayment. 
        // This is a limitation of the current schema/seeder. 
        // To fix this display, we will use the count of students who have made payments as 'Active Paying Students'.

        $totalExpectedFees = 0;
        $activePayingStudents = StudentPayment::select('student_id')->distinct()->count();

        // Calculate based on payments made: 
        // Expected = Amount Paid + Balance (if we tracked balance). 
        // Since we don't track individualized invoices, "Total Expected" is hard to get 100% right without enrollment.
        // Strategy: Sum of (Class Fee Total) for every unique student payment found for that class.

        $distinctStudentClasses = StudentPayment::select('student_id', 'class_id')
            ->distinct()
            ->get();

        foreach ($distinctStudentClasses as $record) {
            $classFeeTotal = ClassFee::where('class_id', $record->class_id)->sum('amount');
            $totalExpectedFees += $classFeeTotal;
        }

        // 3. Total Received
        $totalReceived = StudentPayment::where('school_session_id', $session->id)->sum('amount_paid');

        // 4. Outstanding
        $totalOutstanding = $totalExpectedFees - $totalReceived;
        if ($totalOutstanding < 0)
            $totalOutstanding = 0; // Should not happen unless overpayment

        // 5. Expenses
        $totalExpenses = Expense::sum('amount');

        // 6. Profit/Loss
        $netBalance = $totalReceived - $totalExpenses;

        // 7. Recent Transactions
        $recentPayments = StudentPayment::with(['student', 'schoolClass'])->latest()->take(5)->get();
        $recentExpenses = Expense::latest()->take(5)->get();

        return view('accounting.dashboard', compact(
            'totalStudents',
            'activePayingStudents',
            'totalExpectedFees',
            'totalReceived',
            'totalOutstanding',
            'totalExpenses',
            'netBalance',
            'recentPayments',
            'recentExpenses'
        ));
    }
}
