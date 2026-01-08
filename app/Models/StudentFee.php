<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'fee_head_id',
        'session_id',
        'semester_id',
        'amount',
        'description'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class);
    }

    public function session()
    {
        return $this->belongsTo(SchoolSession::class, 'session_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
