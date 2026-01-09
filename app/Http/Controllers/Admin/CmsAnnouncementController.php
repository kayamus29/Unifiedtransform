<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsAnnouncement;
use Illuminate\Http\Request;

class CmsAnnouncementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    public function index()
    {
        $announcements = CmsAnnouncement::latest()->paginate(20);
        return view('admin.cms.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.cms.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'expires_at' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['is_sticky'] = $request->has('is_sticky');

        CmsAnnouncement::create($data);

        return redirect()->route('admin.cms.announcements.index')->with('success', 'Announcement created successfully.');
    }

    public function edit(CmsAnnouncement $announcement)
    {
        return view('admin.cms.announcements.create', ['announcement' => $announcement]);
    }

    public function update(Request $request, CmsAnnouncement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'expires_at' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['is_sticky'] = $request->has('is_sticky');

        $announcement->update($data);

        return redirect()->route('admin.cms.announcements.index')->with('success', 'Announcement updated successfully.');
    }

    public function destroy(CmsAnnouncement $announcement)
    {
        $announcement->delete();
        return redirect()->route('admin.cms.announcements.index')->with('success', 'Announcement deleted successfully.');
    }
}
