<?php

namespace App\Repositories;

use App\Models\Exam;
use App\Models\Semester;
use App\Models\SchoolClass;
use App\Interfaces\ExamInterface;

class ExamRepository implements ExamInterface {
    public function create($request) {
        try {
            Exam::create($request);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create exam. '.$e->getMessage());
        }
    }

    public function delete($id) {
        try {
            Exam::destroy($id);
        } catch (\Exception $e) {
            throw new \Exception('Failed to delete exam. '.$e->getMessage());
        }
    }

    public function getAll($session_id, $semester_id, $class_id)
    {
        if($semester_id == 0 || $class_id == 0) {
            $semester = Semester::where('session_id', $session_id)->first();
            $class = SchoolClass::where('session_id', $session_id)->first();

            if (!$semester || !$class) {
                return collect(); // Return an empty collection if no semester or class is found
            }

            $semester_id = $semester->id;
            $class_id = $class->id;
        }
        return Exam::with('course')->where('session_id', $session_id)
                    ->where('semester_id', $semester_id)
                    ->where('class_id', $class_id)
                    ->get();
    }
}