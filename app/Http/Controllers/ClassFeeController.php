<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassFee;
use App\Models\SchoolClass;
use App\Models\FeeHead;
use Exception;

class ClassFeeController extends Controller
{
    public function index()
    {
        // Get all classes with their assigned fees
        $classes = SchoolClass::with(['classFees.feeHead'])->get();
        // Also need list of classes and fee heads for the 'Add' modal
        $allClasses = SchoolClass::all();
        $feeHeads = FeeHead::all();

        return view('accounting.fees.class.index', compact('classes', 'allClasses', 'feeHeads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'fee_head_id' => 'required|exists:fee_heads,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255'
        ]);

        try {
            // Check if this fee head is already assigned to this class
            $exists = ClassFee::where('class_id', $request->class_id)
                ->where('fee_head_id', $request->fee_head_id)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'This Fee Head is already assigned to the selected Class.');
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
