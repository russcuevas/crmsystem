@extends('layouts.college')

@section('title', 'College Faculty Dashboard')
@section('header_title', 'College Faculty Dashboard')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Welcome Banner Card -->
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 12px; padding: 1.75rem 2rem; color: #ffffff; box-shadow: var(--shadow-md); display: flex; align-items: center; justify-content: space-between;">
        <div>
            <span style="background: rgba(56, 189, 248, 0.2); color: #38bdf8; font-size: 0.75rem; font-weight: 800; padding: 0.25rem 0.75rem; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em;">
                Higher Education / College
            </span>
            <h2 style="font-size: 1.6rem; font-weight: 800; margin-top: 0.5rem; margin-bottom: 0.25rem;">
                Welcome Back, Prof. {{ $teacher->first_name }} {{ $teacher->last_name }}!
            </h2>
            <p style="color: #94a3b8; font-size: 0.9rem;">
                Academic Year: <strong>S.Y. {{ $activeSchoolYear->school_year ?? 'N/A' }}</strong>
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('college.grades.page') }}" style="background: #38bdf8; color: #020617; text-decoration: none; font-weight: 800; padding: 0.75rem 1.25rem; border-radius: 8px; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 10px rgba(56, 189, 248, 0.3);">
                <i class="fa-solid fa-pen-to-square"></i> Open Class Record
            </a>
            <a href="{{ route('college.enrollment.page') }}" style="background: rgba(255, 255, 255, 0.1); color: #ffffff; text-decoration: none; font-weight: 700; padding: 0.75rem 1.25rem; border-radius: 8px; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; border: 1px solid rgba(255, 255, 255, 0.2);">
                <i class="fa-solid fa-user-plus"></i> Enroll Student
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
        <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 52px; height: 52px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fa-solid fa-book-bookmark"></i>
            </div>
            <div>
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Handled Subjects</span>
                <h3 style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-top: 0.1rem;">{{ $handledSubjects->count() }}</h3>
            </div>
        </div>

        <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 52px; height: 52px; border-radius: 12px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div>
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Enrolled Students</span>
                <h3 style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-top: 0.1rem;">{{ $enrolledStudentsCount }}</h3>
            </div>
        </div>

        <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 52px; height: 52px; border-radius: 12px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Academic Term</span>
                <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin-top: 0.2rem;">Semester System</h3>
            </div>
        </div>
    </div>

    <!-- Handled Subjects Table -->
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-sm);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-main);">
                <i class="fa-solid fa-layer-group" style="color: #0284c7; margin-right: 0.5rem;"></i> My Handled College Classes
            </h3>
            <a href="{{ route('college.grades.page') }}" style="color: #0284c7; font-size: 0.875rem; font-weight: 700; text-decoration: none;">View All &rarr;</a>
        </div>

        @if($handledSubjects->isEmpty())
            <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                <i class="fa-solid fa-folder-open" style="font-size: 2.5rem; margin-bottom: 0.75rem; color: #cbd5e1;"></i>
                <p style="font-weight: 600;">No College class section subjects assigned to you for S.Y. {{ $activeSchoolYear->school_year ?? '' }}.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid var(--border-color); color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">
                            <th style="padding: 0.85rem 1rem;">Code</th>
                            <th style="padding: 0.85rem 1rem;">Subject Title</th>
                            <th style="padding: 0.85rem 1rem;">Section</th>
                            <th style="padding: 0.85rem 1rem;">Course / Program</th>
                            <th style="padding: 0.85rem 1rem;">Semester</th>
                            <th style="padding: 0.85rem 1rem; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($handledSubjects as $ss)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.85rem 1rem; font-weight: 800; color: #0284c7;">{{ $ss->subject->subject_code ?? 'N/A' }}</td>
                                <td style="padding: 0.85rem 1rem; font-weight: 700; color: var(--text-main);">{{ $ss->subject->subject_name ?? 'N/A' }}</td>
                                <td style="padding: 0.85rem 1rem;">
                                    <span style="background: #f1f5f9; color: #334155; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.8rem;">
                                        {{ $ss->classSection->section_name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td style="padding: 0.85rem 1rem; color: var(--text-muted);">
                                    {{ $ss->classSection->course->course_code ?? 'General College' }}
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <span style="background: #e0f2fe; color: #0369a1; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">
                                        {{ $ss->subject->semester ?? '1st Semester' }}
                                    </span>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: right;">
                                    <a href="{{ route('college.grades.page', ['section_subject_id' => $ss->id]) }}" style="background: #0f172a; color: #ffffff; text-decoration: none; padding: 0.4rem 0.85rem; border-radius: 6px; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <i class="fa-solid fa-pen-to-square"></i> Open Record
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
