<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\SchoolYear;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.superadmin', function ($view) {
            $allSchoolYears = SchoolYear::orderBy('school_year', 'desc')->get();
            $activeSyId = session('active_school_year_id');
            $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();
            if (!$activeSchoolYear) {
                $activeSchoolYear = $allSchoolYears->first();
            }

            $view->with('allSchoolYears', $allSchoolYears);
            $view->with('activeSchoolYear', $activeSchoolYear);
        });
    }
}
