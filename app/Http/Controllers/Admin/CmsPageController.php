<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsPageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    public function index()
    {
        $pages = CmsPage::latest()->paginate(20);
        return view('admin.cms.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.cms.pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,published',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);

        if ($request->is_home) {
            CmsPage::where('is_home', true)->update(['is_home' => false]);
        }

        CmsPage::create($data);

        return redirect()->route('admin.cms.pages.index')->with('success', 'Page created successfully.');
    }

    public function edit(CmsPage $page)
    {
        return view('admin.cms.pages.edit', compact('page'));
    }

    public function update(Request $request, CmsPage $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,published',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['is_home'] = $request->has('is_home');

        if ($data['is_home']) {
            CmsPage::where('id', '!=', $page->id)->where('is_home', true)->update(['is_home' => false]);
        }

        $page->update($data);

        return redirect()->route('admin.cms.pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(CmsPage $page)
    {
        $page->delete();
        return redirect()->route('admin.cms.pages.index')->with('success', 'Page deleted successfully.');
    }
}
