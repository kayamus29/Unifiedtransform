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
        $totalExpectedFees = StudentFee::where('session_id', $session->id)->sum('amount');

        // 3. Total Received
        $totalReceived = StudentPayment::where('school_session_id', $session->id)->sum('amount_paid');

        // 4. Outstanding
        $totalOutstanding = StudentFee::where('session_id', $session->id)->sum('balance');

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
