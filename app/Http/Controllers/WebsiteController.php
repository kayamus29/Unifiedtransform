<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\CmsPost;
use App\Models\CmsBanner;
use App\Models\CmsAnnouncement;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function index()
    {
        $banners = CmsBanner::where('status', true)->orderBy('order')->get();
        $featuredPosts = CmsPost::where('status', 'published')->latest()->take(3)->get();
        $announcements = CmsAnnouncement::where(function ($query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })->latest()->get();

        $homePage = CmsPage::where('is_home', true)->where('status', 'published')->first();

        return view('website.index', compact('banners', 'featuredPosts', 'announcements', 'homePage'));
    }

    public function blog()
    {
        $posts = CmsPost::where('status', 'published')->latest()->paginate(9);
        return view('website.blog.index', compact('posts'));
    }

    public function post($slug)
    {
        $post = CmsPost::where('slug', $slug)->where('status', 'published')->firstOrFail();
        return view('website.blog.show', compact('post'));
    }

    public function page($slug)
    {
        $page = CmsPage::where('slug', $slug)->where('status', 'published')->firstOrFail();
        return view('website.page', compact('page'));
    }

    public function contact()
    {
        return view('website.contact');
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        ContactSubmission::create($request->all());

        return redirect()->back()->with('success', 'Your message has been sent successfully. We will get back to you soon.');
    }
}
