<?php

namespace App\Http\Controllers\elementary;

use App\Http\Controllers\Controller;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ElementaryEnrollmentController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $teacher = $user->teacher;

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();

        // Elementary Advisory Sections
        $advisorySections = ClassSection::where('class_adviser_id', $teacher->id)
            ->whereHas('gradeLevel.educationLevel', fn($q) => $q->whereIn('code', ['BED', 'ELEM', 'ELEMENTARY']))
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->with('gradeLevel')
            ->get();

        $advisorySectionIds = $advisorySections->pluck('id');

        $enrollments = Enrollment::whereIn('class_section_id', $advisorySectionIds)
            ->when($activeSchoolYear, fn($q) => $q->where('school_year_id', $activeSchoolYear->id))
            ->with(['student.user', 'student.gradeLevel', 'classSection.gradeLevel', 'schoolYear'])
            ->latest()
            ->get();

        // Students available for enrollment in BED
        $studentsList = Student::whereHas('educationLevel', fn($q) => $q->whereIn('code', ['BED', 'ELEM', 'ELEMENTARY']))
            ->with('user')
            ->get();

        return view('elementary.enrollment.index', compact('enrollments', 'advisorySections', 'studentsList', 'activeSchoolYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_section_id' => 'required|exists:class_sections,id',
        ]);

        $activeSyId = session('active_school_year_id');
        $activeSchoolYear = $activeSyId ? SchoolYear::find($activeSyId) : SchoolYear::where('is_active', true)->first();

        if (!$activeSchoolYear) {
            return redirect()->back()->with('error', 'No active school year set.');
        }

        // Check if student already enrolled in section for SY
        $existing = Enrollment::where('student_id', $validated['student_id'])
            ->where('school_year_id', $activeSchoolYear->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Student is already enrolled for the selected school year.');
        }

        Enrollment::create([
            'student_id' => $validated['student_id'],
            'class_section_id' => $validated['class_section_id'],
            'school_year_id' => $activeSchoolYear->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Student enrolled successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:active,enrolled,dropped,transferred',
        ]);

        $enrollment->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Enrollment status updated successfully!');
    }

    public function destroy($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        return redirect()->back()->with('success', 'Enrollment record deleted successfully!');
    }
}
