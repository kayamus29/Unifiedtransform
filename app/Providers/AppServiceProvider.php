<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('site_settings')) {
            $site_setting = \App\Models\SiteSetting::first();
            if (!$site_setting) {
                // Ensure there is always a setting object to avoid null checks in views
                $site_setting = new \App\Models\SiteSetting([
                    'school_name' => config('app.name'),
                    'primary_color' => '#3490dc',
                ]);
            }
            \Illuminate\Support\Facades\View::share('site_setting', $site_setting);
        }
    }
}
