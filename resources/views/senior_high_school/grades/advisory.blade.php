@extends('layouts.senior_high_school')

@section('title', 'GNHS-P - Advisory Class Grades Summary (SHS)')
@section('header_title', 'Advisory Class Grades Summary')

@push('styles')
    <style>
        .top-nav-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.5rem;
        }

        .top-nav-tab {
            padding: 0.65rem 1.25rem;
            border-radius: 0.625rem;
            font-weight: 700;
            font-size: 0.875rem;
            text-decoration: none;
            color: #64748b;
            background: transparent;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .top-nav-tab:hover {
            color: #1e1b4b;
            background: #f1f5f9;
        }

        .top-nav-tab.active {
            color: #ffffff;
            background: #1e1b4b;
            box-shadow: 0 4px 12px rgba(30, 27, 75, 0.25);
        }

        .period-tab {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.825rem;
            font-weight: 700;
            text-decoration: none;
            color: #64748b;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            transition: all 0.15s ease;
        }

        .period-tab:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .period-tab.active {
            background: #1e1b4b;
            color: #ffffff;
            border-color: #1e1b4b;
            box-shadow: 0 2px 8px rgba(30, 27, 75, 0.3);
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.875rem;
            padding: 1.1rem 1.25rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex: 1;
            min-width: 180px;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .grade-matrix-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.85rem;
        }

        .grade-matrix-table th {
            background: #f8fafc;
            color: #1e293b;
            font-weight: 700;
            padding: 0.85rem 0.75rem;
            border-bottom: 2px solid #cbd5e1;
            white-space: nowrap;
        }

        .grade-matrix-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #f1f5f9;
            white-space: nowrap;
        }

        .table-responsive {
            overflow-x: auto;
            max-width: 100%;
        }
    </style>
@endpush

@section('content')
    <!-- Navigation Tabs -->
    <div class="top-nav-tabs">
        <a href="{{ route('senior_high_school.grades.page') }}" class="top-nav-tab">
            <i class="fa-solid fa-book"></i>
            <span>My Handled Subjects (Subject Teacher)</span>
        </a>
        <a href="{{ route('senior_high_school.grades.advisory.page') }}" class="top-nav-tab active">
            <i class="fa-solid fa-user-shield"></i>
            <span>Advisory Class Grades (Class Adviser)</span>
            <span
                style="background: #f59e0b; color: #450a0a; padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 800;">Adviser</span>
        </a>
    </div>

    @if ($isAdviser)
        <!-- Controls Bar -->
        <div
            style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
            <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">

                <form action="{{ route('senior_high_school.grades.advisory.page') }}" method="GET"
                    style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; margin: 0; width: 100%; max-width: 680px;">
                    <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">

                    <!-- Semester Filter -->
                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                        <label style="font-weight: 700; font-size: 0.875rem; color: #0f172a; white-space: nowrap;">
                            <i class="fa-solid fa-calendar-days" style="color: #7f1d1d;"></i> Semester:
                        </label>
                        <select name="semester" onchange="this.form.submit()"
                            style="padding: 0.6rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 700; color: #7f1d1d; background: #fff1f2; outline: none; cursor: pointer;">
                            <option value="1st Semester" {{ $selectedSemester == '1st Semester' ? 'selected' : '' }}>1st
                                Semester</option>
                            <option value="2nd Semester" {{ $selectedSemester == '2nd Semester' ? 'selected' : '' }}>2nd
                                Semester</option>
                        </select>
                    </div>

                    <!-- Advisory Section Selector -->
                    <div style="display: flex; align-items: center; gap: 0.4rem; flex: 1;">
                        <label style="font-weight: 700; font-size: 0.875rem; color: #0f172a; white-space: nowrap;">
                            <i class="fa-solid fa-users-rectangle" style="color: #7f1d1d;"></i> Section:
                        </label>
                        <select name="class_section_id" onchange="this.form.submit()"
                            style="width: 100%; padding: 0.6rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; outline: none; background: #ffffff;">
                            @foreach ($advisorySections as $sec)
                                <option value="{{ $sec->id }}"
                                    {{ $currentSection && $currentSection->id == $sec->id ? 'selected' : '' }}>
                                    {{ $sec->section_name }} ({{ $sec->gradeLevel ? $sec->gradeLevel->name : '' }}
                                    {{ $sec->course ? '- Strand: ' . $sec->course->course_code : '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <!-- Period Tabs (Prelim, Midterm, Finals) -->
                <div style="display: flex; gap: 0.4rem;">
                    @foreach ($periods as $period)
                        <a href="{{ route('senior_high_school.grades.advisory.page', ['semester' => $selectedSemester, 'class_section_id' => $currentSection ? $currentSection->id : '', 'academic_period' => $period]) }}"
                            class="period-tab {{ $selectedPeriod == $period ? 'active' : '' }}">
                            {{ $period }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        @if ($currentSection)
            <!-- Section Stats Cards -->
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
                <div class="stat-card">
                    <div>
                        <span
                            style="font-size: 0.775rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Total
                            Students</span>
                        <h4 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-top: 0.15rem;">
                            {{ $classStats['total_students'] }}</h4>
                    </div>
                    <div class="stat-icon" style="background: #fee2e2; color: #991b1b;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div>
                        <span
                            style="font-size: 0.775rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Class
                            Average</span>
                        <h4 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-top: 0.15rem;">
                            {{ $classStats['class_average'] ? $classStats['class_average'] . '%' : 'N/A' }}</h4>
                    </div>
                    <div class="stat-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div>
                        <span
                            style="font-size: 0.775rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Passed</span>
                        <h4 style="font-size: 1.5rem; font-weight: 800; color: #059669; margin-top: 0.15rem;">
                            {{ $classStats['passed_count'] }}</h4>
                    </div>
                    <div class="stat-icon" style="background: #d1fae5; color: #059669;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div>
                        <span
                            style="font-size: 0.775rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Failed</span>
                        <h4 style="font-size: 1.5rem; font-weight: 800; color: #dc2626; margin-top: 0.15rem;">
                            {{ $classStats['failed_count'] }}</h4>
                    </div>
                    <div class="stat-icon" style="background: #fee2e2; color: #dc2626;">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                </div>
            </div>

            <!-- Grades Matrix Card -->
            <div
                style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: var(--shadow-sm);">
                <div class="table-responsive">
                    <table class="grade-matrix-table">
                        <thead>
                            <tr>
                                <th style="text-align: left; position: sticky; left: 0; background: #f8fafc; z-index: 5;">
                                    Student Name</th>
                                @foreach ($sectionSubjects as $css)
                                    <th style="text-align: center;">
                                        {{ $css->subject ? $css->subject->subject_code ?? $css->subject->code : 'SUBJ' }}
                                    </th>
                                @endforeach
                                <th style="text-align: center; background: #e0e7ff; color: #3730a3;">General Average</th>
                                <th style="text-align: center; background: #e0e7ff; color: #3730a3;">Remarks</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($enrolledStudents as $student)
                                @php
                                    $summary = $studentSummaries[$student->id] ?? [
                                        'general_average' => null,
                                        'remarks' => 'PENDING',
                                    ];
                                @endphp
                                <tr>
                                    <td
                                        style="position: sticky; left: 0; background: #ffffff; z-index: 5; font-weight: 700; color: #0f172a;">
                                        {{ trim(($student->last_name ? $student->last_name . ', ' : '') . $student->first_name . ($student->middle_name ? ' ' . $student->middle_name : '') . ($student->extension_name ? ' ' . $student->extension_name : '')) }}
                                    </td>
                                    @foreach ($sectionSubjects as $css)
                                        @php
                                            $gVal = $gradesMatrix[$student->id][$css->id] ?? null;
                                        @endphp
                                        <td style="text-align: center; font-weight: 700;">
                                            @if ($gVal)
                                                <span
                                                    style="{{ $gVal < 75 ? 'color: #dc2626;' : 'color: #0f172a;' }}">{{ number_format($gVal, 2) }}</span>
                                            @else
                                                <span style="color: #cbd5e1;">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td
                                        style="text-align: center; font-weight: 800; font-size: 0.9rem; color: #1e1b4b; background: #f8fafc;">
                                        {{ $summary['general_average'] ? number_format($summary['general_average'], 2) : '-' }}
                                    </td>
                                    <td style="text-align: center; background: #f8fafc;">
                                        @if ($summary['remarks'] === 'PASSED')
                                            <span
                                                style="background: #d1fae5; color: #065f46; padding: 0.2rem 0.55rem; border-radius: 4px; font-weight: 800; font-size: 0.75rem;">PASSED</span>
                                        @elseif ($summary['remarks'] === 'FAILED')
                                            <span
                                                style="background: #fee2e2; color: #991b1b; padding: 0.2rem 0.55rem; border-radius: 4px; font-weight: 800; font-size: 0.75rem;">FAILED</span>
                                        @else
                                            <span style="color: #94a3b8; font-size: 0.75rem;">PENDING</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('senior_high_school.grades.print_card', $student->id) }}"
                                            target="_blank"
                                            style="padding: 0.35rem 0.65rem; background: #fee2e2; color: #991b1b; border-radius: 0.375rem; font-size: 0.775rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem;">
                                            <i class="fa-solid fa-print"></i> Report Card
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" style="text-align: center; padding: 2rem; color: #64748b;">
                                        No enrolled students in this advisory class section.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @else
        <div
            style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 3rem; text-align: center; color: #64748b;">
            <i class="fa-solid fa-user-shield" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
            <h3>No Advisory Section Assigned</h3>
            <p style="font-size: 0.875rem; margin-top: 0.25rem;">You are not currently assigned as class adviser for any
                section in Senior High School for this active school year.</p>
        </div>
    @endif
@endsection
