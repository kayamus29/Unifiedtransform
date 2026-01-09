<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StudentFee;
use App\Models\StudentPayment;

class StudentFinancialProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Accountant|Admin']);
    }

    public function show($id)
    {
        $student = User::with(['promotions.schoolClass', 'promotions.session'])->findOrFail($id);

        $fees = StudentFee::with(['feeHead', 'session', 'semester'])
            ->where('student_id', $id)
            ->latest()
            ->get();

        $payments = StudentPayment::with(['schoolClass', 'session', 'semester', 'receiver', 'studentFee.feeHead'])
            ->where('student_id', $id)
            ->latest()
            ->get();

        return view('accounting.students.financial_profile', compact('student', 'fees', 'payments'));
    }
}
