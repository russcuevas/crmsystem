@extends('layouts.senior_high_school')

@section('title', 'GNHS - SHS Teacher Dashboard')
@section('header_title', 'Dashboard Overview')

@section('content')
    <!-- Banner Card -->
    <div
        style="background: linear-gradient(135deg, #450a0a 0%, #7f1d1d 60%, #991b1b 100%); border-radius: 1rem; padding: 2rem 2.5rem; color: #ffffff; margin-bottom: 2rem; box-shadow: var(--shadow-lg); position: relative; overflow: hidden; display: flex; justify-content: space-between; align-items: center;">
        <div style="position: relative; z-index: 2;">
            <span
                style="display: inline-block; background: #f59e0b; color: #450a0a; padding: 0.35rem 0.85rem; border-radius: 0.375rem; font-weight: 800; font-size: 0.775rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">
                Senior High School Level (Grade 11 - 12)
            </span>
            <h2 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 0.5rem;">
                Welcome, {{ $teacher->first_name }} {{ $teacher->last_name }}!
            </h2>
            <p style="color: #fecdd3; font-size: 0.95rem;">
                Manage your handled class sections, strands, subject records, student enrollments, and grades for Senior High School.
            </p>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div
            style="background: #ffffff; border-radius: 0.875rem; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 1.25rem;">
            <div
                style="width: 52px; height: 52px; border-radius: 0.75rem; background: #fee2e2; color: #991b1b; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
            <div>
                <div
                    style="font-size: 0.825rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;">
                    Advisory Sections</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #0f172a; margin-top: 0.15rem;">
                    {{ $advisorySections->count() }}</div>
            </div>
        </div>

        <div
            style="background: #ffffff; border-radius: 0.875rem; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 1.25rem;">
            <div
                style="width: 52px; height: 52px; border-radius: 0.75rem; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <div>
                <div
                    style="font-size: 0.825rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;">
                    Teaching Subjects</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #0f172a; margin-top: 0.15rem;">
                    {{ $assignedSubjects->count() }}</div>
            </div>
        </div>

        <div
            style="background: #ffffff; border-radius: 0.875rem; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 1.25rem;">
            <div
                style="width: 52px; height: 52px; border-radius: 0.75rem; background: #d1fae5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div>
                <div
                    style="font-size: 0.825rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;">
                    Enrolled Students</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #0f172a; margin-top: 0.15rem;">{{ $totalStudents }}
                </div>
            </div>
        </div>

        <div
            style="background: #ffffff; border-radius: 0.875rem; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 1.25rem;">
            <div
                style="width: 52px; height: 52px; border-radius: 0.75rem; background: #f3e8ff; color: #7e22ce; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fa-solid fa-school"></i>
            </div>
            <div>
                <div
                    style="font-size: 0.825rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;">
                    Designated Portal</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-top: 0.15rem;">SHS Level</div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content Sections -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 1.75rem;">
        <!-- Advisory Sections Card -->
        <div
            style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm); overflow: hidden;">
            <div
                style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
                <h3
                    style="font-size: 1.05rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 0.6rem;">
                    <i class="fa-solid fa-users-rectangle" style="color: #7f1d1d;"></i> My Advisory Class Sections
                </h3>
                <span
                    style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.65rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 700;">
                    {{ $advisorySections->count() }} Active
                </span>
            </div>
            <div style="padding: 1.25rem 1.5rem;">
                @if ($advisorySections->isNotEmpty())
                    <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                        @foreach ($advisorySections as $section)
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; padding: 0.95rem 1.15rem; border-radius: 0.625rem; background: #f8fafc; border: 1px solid #f1f5f9;">
                                <div>
                                    <h4 style="font-size: 0.925rem; font-weight: 700; color: #0f172a;">
                                        {{ $section->section_name }}</h4>
                                    <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.15rem;">
                                        <i class="fa-solid fa-graduation-cap"></i>
                                        {{ $section->gradeLevel ? $section->gradeLevel->name : 'N/A' }}
                                        @if ($section->course)
                                            | Strand: <strong>{{ $section->course->course_code }}</strong>
                                        @endif
                                    </p>
                                </div>
                                <span
                                    style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.65rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 700;">
                                    Class Adviser
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 2.5rem 1rem; color: #64748b;">
                        <i class="fa-solid fa-folder-open"
                            style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 0.75rem;"></i>
                        <p style="font-size: 0.875rem;">No advisory class section assigned for this active school year.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Assigned Subjects Card -->
        <div
            style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; box-shadow: var(--shadow-sm); overflow: hidden;">
            <div
                style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
                <h3
                    style="font-size: 1.05rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 0.6rem;">
                    <i class="fa-solid fa-book-bookmark" style="color: #7f1d1d;"></i> My Assigned Teaching Subjects
                </h3>
                <span
                    style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.65rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 700;">
                    {{ $assignedSubjects->count() }} Subjects
                </span>
            </div>
            <div style="padding: 1.25rem 1.5rem;">
                @if ($assignedSubjects->isNotEmpty())
                    <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                        @foreach ($assignedSubjects as $item)
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; padding: 0.95rem 1.15rem; border-radius: 0.625rem; background: #f8fafc; border: 1px solid #f1f5f9;">
                                <div>
                                    <h4 style="font-size: 0.925rem; font-weight: 700; color: #0f172a;">
                                        {{ $item->subject ? $item->subject->subject_name ?? $item->subject->name : 'N/A' }}
                                    </h4>
                                    <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.15rem;">
                                        Section:
                                        <strong>{{ $item->classSection ? $item->classSection->section_name : 'N/A' }}</strong>
                                        ({{ $item->classSection && $item->classSection->gradeLevel ? $item->classSection->gradeLevel->name : 'N/A' }})
                                    </p>
                                </div>
                                <span
                                    style="font-family: monospace; background: #f3f4f6; color: #374151; padding: 0.2rem 0.55rem; border-radius: 0.25rem; font-size: 0.8rem; font-weight: 700;">
                                    {{ $item->subject ? $item->subject->subject_code ?? $item->subject->code : 'SUBJ' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 2.5rem 1rem; color: #64748b;">
                        <i class="fa-solid fa-book" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 0.75rem;"></i>
                        <p style="font-size: 0.875rem;">No subjects assigned to your teaching schedule for this active
                            school year.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
