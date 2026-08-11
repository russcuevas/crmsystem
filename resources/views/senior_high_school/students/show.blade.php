@extends('layouts.senior_high_school')

@section('title', 'GNHS - Student Profile')
@section('header_title', 'Student Profile View')

@section('content')
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('senior_high_school.students.page') }}"
            style="color: #7f1d1d; text-decoration: none; font-weight: 700; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.4rem;">
            <i class="fa-solid fa-arrow-left"></i> Back to Handled Students List
        </a>
    </div>

    <!-- Main Student Profile Header Card -->
    <div
        style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 2rem; box-shadow: var(--shadow-sm); margin-bottom: 2rem; display: flex; align-items: center; gap: 2rem;">
        <div
            style="width: 84px; height: 84px; border-radius: 50%; background: #7f1d1d; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: 800; border: 4px solid #fecdd3;">
            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
        </div>
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">
                {{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name ?? '' }} {{ $student->extension_name ?? '' }}
            </h2>
            <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem; flex-wrap: wrap;">
                <span style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.65rem; border-radius: 0.375rem; font-size: 0.775rem; font-weight: 800;">
                    Student ID: {{ $student->student_number }}
                </span>
                @if ($student->lrn)
                    <span style="background: #fef3c7; color: #92400e; padding: 0.25rem 0.65rem; border-radius: 0.375rem; font-size: 0.775rem; font-weight: 800;">
                        LRN: {{ $student->lrn }}
                    </span>
                @endif
                <span style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.65rem; border-radius: 0.375rem; font-size: 0.775rem; font-weight: 800;">
                    {{ $student->gradeLevel ? $student->gradeLevel->name : 'N/A' }}
                </span>
                @if ($student->course)
                    <span style="background: #e0e7ff; color: #3730a3; padding: 0.25rem 0.65rem; border-radius: 0.375rem; font-size: 0.775rem; font-weight: 800;">
                        Strand: {{ $student->course->course_code }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Student Details Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem;">
        <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: var(--shadow-sm);">
            <h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
                Personal Details
            </h3>
            <div style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.875rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b; font-weight: 600;">Account Email:</span>
                    <span style="font-weight: 700; color: #0f172a;">{{ $student->user ? $student->user->email : 'N/A' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b; font-weight: 600;">Gender:</span>
                    <span style="font-weight: 700; color: #0f172a;">{{ $student->gender ?? 'Not Specified' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b; font-weight: 600;">Birthday:</span>
                    <span style="font-weight: 700; color: #0f172a;">{{ $student->birthday ? \Carbon\Carbon::parse($student->birthday)->format('F d, Y') : 'N/A' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b; font-weight: 600;">Contact Phone:</span>
                    <span style="font-weight: 700; color: #0f172a;">{{ $student->phone_number ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: var(--shadow-sm);">
            <h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
                Enrollment History
            </h3>
            @if ($student->enrollments->isNotEmpty())
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach ($student->enrollments as $enr)
                        <div style="padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h4 style="font-size: 0.875rem; font-weight: 700; color: #0f172a;">
                                    {{ $enr->classSection ? $enr->classSection->section_name : 'N/A' }}
                                </h4>
                                <span style="font-size: 0.775rem; color: #64748b;">
                                    S.Y. {{ $enr->schoolYear ? $enr->schoolYear->school_year : 'N/A' }}
                                </span>
                            </div>
                            <span style="background: #d1fae5; color: #065f46; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                {{ $enr->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="font-size: 0.85rem; color: #64748b;">No enrollment records found for this student.</p>
            @endif
        </div>
    </div>
@endsection
