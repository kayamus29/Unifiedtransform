<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    public function index()
    {
        $submissions = ContactSubmission::latest()->paginate(20);
        return view('admin.cms.inquiries.index', compact('submissions'));
    }

    public function show(ContactSubmission $submission)
    {
        if ($submission->status == 'new') {
            $submission->update(['status' => 'read']);
        }
        return view('admin.cms.inquiries.show', compact('submission'));
    }

    public function destroy(ContactSubmission $submission)
    {
        $submission->delete();
        return redirect()->route('admin.cms.inquiries.index')->with('success', 'Inquiry deleted successfully.');
    }
}
