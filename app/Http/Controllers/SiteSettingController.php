<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth'); // Already applied in web.php group
        // Add permission check if needed, e.g., $this->middleware('can:manage site settings');
    }

    public function edit()
    {
        $setting = SiteSetting::first();
        if (!$setting) {
            $setting = SiteSetting::create([
                'school_name' => 'Unifiedtransform',
                'primary_color' => '#3490dc',
            ]);
        }
        return view('settings.site', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'login_background' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'primary_color' => 'required|string|size:7', // Hex code
            'secondary_color' => 'nullable|string|size:7',
            'office_lat' => 'nullable|numeric|between:-90,90',
            'office_long' => 'nullable|numeric|between:-180,180',
            'geo_range' => 'nullable|integer|min:1',
            'late_time' => 'nullable|date_format:H:i',
        ]);

        $setting = SiteSetting::first();
        if (!$setting) {
            $setting = new SiteSetting();
        }

        $setting->school_name = $request->school_name;
        $setting->primary_color = $request->primary_color;
        $setting->secondary_color = $request->secondary_color;
        $setting->office_lat = $request->office_lat;
        $setting->office_long = $request->office_long;
        $setting->geo_range = $request->geo_range;
        $setting->late_time = $request->late_time;

        if ($request->hasFile('school_logo')) {
            $path = $request->file('school_logo')->store('public/uploads/logos');
            $setting->school_logo_path = Storage::url($path);
        }

        if ($request->hasFile('login_background')) {
            $path = $request->file('login_background')->store('public/uploads/backgrounds');
            $setting->login_background_path = Storage::url($path);
        }

        $setting->save();

        return redirect()->route('settings.site.edit')->with('success', 'Site settings updated successfully.');
    }
}
