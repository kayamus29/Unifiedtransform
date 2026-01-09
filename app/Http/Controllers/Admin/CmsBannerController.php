<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsBannerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    public function index()
    {
        $banners = CmsBanner::orderBy('order')->get();
        return view('admin.cms.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.cms.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image_path' => 'required|image|max:3072',
            'status' => 'required|boolean',
        ]);

        $data = $request->except('image_path');

        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('cms/banners', 'public');
            $data['image_path'] = $path;
        }

        CmsBanner::create($data);

        return redirect()->route('admin.cms.banners.index')->with('success', 'Banner created successfully.');
    }

    public function edit(CmsBanner $banner)
    {
        return view('admin.cms.banners.edit', compact('banner'));
    }

    public function update(Request $request, CmsBanner $banner)
    {
        $request->validate([
            'image_path' => 'nullable|image|max:3072',
            'status' => 'required|boolean',
        ]);

        $data = $request->except('image_path');

        if ($request->hasFile('image_path')) {
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $path = $request->file('image_path')->store('cms/banners', 'public');
            $data['image_path'] = $path;
        }

        $banner->update($data);

        return redirect()->route('admin.cms.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(CmsBanner $banner)
    {
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }
        $banner->delete();
        return redirect()->route('admin.cms.banners.index')->with('success', 'Banner deleted successfully.');
    }
}
