@extends('layouts.elementary')

@section('title', 'GNHS-P BED - Elementary Teacher Dashboard')
@section('header_title', 'Elementary Teacher Dashboard')

@section('content')
    <!-- Welcome Header -->
    <div style="background: linear-gradient(135deg, #064e3b 0%, #047857 100%); color: #ffffff; border-radius: 1rem; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 4px 20px rgba(4, 120, 87, 0.2);">
        <h2 style="font-size: 1.6rem; font-weight: 800; margin-bottom: 0.5rem;">
            Welcome back, Teacher {{ $teacher->first_name }}! 👋
        </h2>
        <p style="color: #a7f3d0; font-size: 0.9rem; max-width: 600px; font-weight: 500;">
            Basic Education Department (BED / Elementary) - School Year {{ $activeSchoolYear ? $activeSchoolYear->school_year : '2026-2027' }}.
        </p>
    </div>

    <!-- Quick Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.25rem; box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Handled Students</span>
                <h3 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-top: 0.25rem;">{{ $studentsCount }}</h3>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #d1fae5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.25rem; box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Advisory Classes</span>
                <h3 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-top: 0.25rem;">{{ $advisorySections->count() }}</h3>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #e0e7ff; color: #3730a3; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.25rem; box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Assigned Subjects</span>
                <h3 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-top: 0.25rem;">{{ $assignedSectionSubjects->count() }}</h3>
            </div>
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                <i class="fa-solid fa-book-open"></i>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
        <!-- Advisory Sections -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; box-shadow: var(--shadow-sm);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a;">My Advisory Classes</h3>
                <a href="{{ route('elementary.grades.advisory.page') }}" style="color: #059669; font-weight: 700; font-size: 0.825rem; text-decoration: none;">View Matrix &rarr;</a>
            </div>

            @forelse ($advisorySections as $sec)
                <div style="padding: 0.85rem; border: 1px solid #e2e8f0; border-radius: 0.75rem; margin-bottom: 0.75rem; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h4 style="font-size: 0.95rem; font-weight: 800; color: #0f172a;">{{ $sec->section_name }}</h4>
                        <span style="font-size: 0.775rem; color: #64748b; font-weight: 600;">{{ $sec->gradeLevel ? $sec->gradeLevel->name : 'N/A' }}</span>
                    </div>
                    <a href="{{ route('elementary.grades.advisory.page', ['class_section_id' => $sec->id]) }}" style="background: #d1fae5; color: #065f46; padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 700; font-size: 0.775rem; text-decoration: none;">
                        Open Grades
                    </a>
                </div>
            @empty
                <p style="color: #94a3b8; font-size: 0.85rem; text-align: center; padding: 1.5rem;">No advisory classes assigned for BED level.</p>
            @endforelse
        </div>

        <!-- Handled Subjects -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; box-shadow: var(--shadow-sm);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a;">Handled Subject Loads</h3>
                <a href="{{ route('elementary.grades.page') }}" style="color: #059669; font-weight: 700; font-size: 0.825rem; text-decoration: none;">Class Record &rarr;</a>
            </div>

            @forelse ($assignedSectionSubjects as $ss)
                <div style="padding: 0.85rem; border: 1px solid #e2e8f0; border-radius: 0.75rem; margin-bottom: 0.75rem; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h4 style="font-size: 0.95rem; font-weight: 800; color: #0f172a;">{{ $ss->subject ? ($ss->subject->subject_name ?? $ss->subject->name) : 'Subject' }}</h4>
                        <span style="font-size: 0.775rem; color: #64748b; font-weight: 600;">{{ $ss->classSection ? $ss->classSection->section_name : 'N/A' }} ({{ $ss->classSection && $ss->classSection->gradeLevel ? $ss->classSection->gradeLevel->name : '' }})</span>
                    </div>
                    <a href="{{ route('elementary.grades.page', ['class_section_subject_id' => $ss->id]) }}" style="background: #e0e7ff; color: #3730a3; padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 700; font-size: 0.775rem; text-decoration: none;">
                        Record Scores
                    </a>
                </div>
            @empty
                <p style="color: #94a3b8; font-size: 0.85rem; text-align: center; padding: 1.5rem;">No subject loads assigned for BED level.</p>
            @endforelse
        </div>
    </div>
@endsection
