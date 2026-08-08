<?php

namespace App\Http\Controllers\superadmins;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use Illuminate\Http\Request;

class SuperAdminSchoolYearController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        SchoolYear::query()->update(['is_active' => false]);
        SchoolYear::where('id', $request->school_year_id)->update(['is_active' => true]);

        session(['active_school_year_id' => $request->school_year_id]);

        return back()->with('success', 'Active school year updated successfully.');
    }
}
