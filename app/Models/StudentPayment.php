<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'school_session_id',
        'semester_id',
        'amount_paid',
        'transaction_date',
        'reference_no'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function session()
    {
        return $this->belongsTo(SchoolSession::class, 'school_session_id');
    }

    public function semester() // Term
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
