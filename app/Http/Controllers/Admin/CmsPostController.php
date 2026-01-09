<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPost;
use App\Models\CmsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CmsPostController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    public function index()
    {
        $posts = CmsPost::with('category')->latest()->paginate(20);
        return view('admin.cms.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = CmsCategory::all();
        return view('admin.cms.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('featured_image');
        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('cms/posts', 'public');
            $data['featured_image'] = $path;
        }

        if ($data['status'] == 'published') {
            $data['published_at'] = now();
        }

        CmsPost::create($data);

        return redirect()->route('admin.cms.posts.index')->with('success', 'Post created successfully.');
    }

    public function edit(CmsPost $post)
    {
        $categories = CmsCategory::all();
        return view('admin.cms.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, CmsPost $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('featured_image');
        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $path = $request->file('featured_image')->store('cms/posts', 'public');
            $data['featured_image'] = $path;
        }

        if ($data['status'] == 'published' && !$post->published_at) {
            $data['published_at'] = now();
        }

        $post->update($data);

        return redirect()->route('admin.cms.posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(CmsPost $post)
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->delete();
        return redirect()->route('admin.cms.posts.index')->with('success', 'Post deleted successfully.');
    }
}
