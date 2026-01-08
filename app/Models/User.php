<?php

namespace App\Models;

use App\Models\Mark;
use App\Models\Promotion;
use App\Models\ClassFee;
use App\Models\StudentFee;
use App\Models\StudentPayment;
use App\Models\StudentParentInfo;
use App\Models\StudentAcademicInfo;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasRoles, HasFactory, Notifiable, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'gender',
        'nationality',
        'phone',
        'address',
        'address2',
        'city',
        'zip',
        'photo',
        'birthday',
        'religion',
        'blood_type',
        'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the parent_info.
     */
    public function parent_info()
    {
        return $this->hasOne(StudentParentInfo::class, 'student_id', 'id');
    }

    /**
     * Get the academic_info.
     */
    public function academic_info()
    {
        return $this->hasOne(StudentAcademicInfo::class, 'student_id', 'id');
    }

    /**
     * Get the marks.
     */
    public function marks()
    {
        return $this->hasMany(Mark::class, 'student_id', 'id');
    }

    public function getTotalOutstandingBalance()
    {
        // 1. Get all promotions to find all classes/sessions the student was in
        $promotions = Promotion::where('student_id', $this->id)->get();
        $totalExpected = 0;

        foreach ($promotions as $promo) {
            // Get class fees for this class. 
            // If session_id is set on ClassFee, filter by it. 
            // If nullable, it applies to all sessions (legacy or universal fees).
            $totalExpected += ClassFee::where('class_id', $promo->class_id)
                ->where(function ($q) use ($promo) {
                    $q->whereNull('session_id')->orWhere('session_id', $promo->session_id);
                })
                ->sum('amount');
        }

        // 2. Add student-specific fees
        $totalExpected += StudentFee::where('student_id', $this->id)->sum('amount');

        // 3. Subtract all payments made by the student
        $totalPaid = StudentPayment::where('student_id', $this->id)->sum('amount_paid');

        return $totalExpected - $totalPaid;
    }

    public function getTotalFees()
    {
        $promotions = Promotion::where('student_id', $this->id)->get();
        $totalExpected = 0;

        foreach ($promotions as $promo) {
            $totalExpected += ClassFee::where('class_id', $promo->class_id)
                ->where(function ($q) use ($promo) {
                    $q->whereNull('session_id')->orWhere('session_id', $promo->session_id);
                })
                ->sum('amount');
        }

        $totalExpected += StudentFee::where('student_id', $this->id)->sum('amount');

        return $totalExpected;
    }
}
