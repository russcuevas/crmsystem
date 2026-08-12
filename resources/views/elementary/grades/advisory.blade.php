@extends('layouts.elementary')

@section('title', 'GNHS-P BED - Advisory Class Grades Summary')
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
            color: #064e3b;
            background: #f1f5f9;
        }

        .top-nav-tab.active {
            color: #ffffff;
            background: #064e3b;
            box-shadow: 0 4px 12px rgba(6, 78, 59, 0.25);
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
            background: #059669;
            color: #ffffff;
            border-color: #059669;
            box-shadow: 0 2px 8px rgba(5, 150, 105, 0.3);
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
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .table-responsive {
            overflow-x: auto;
            max-width: 100%;
        }
    </style>
@endpush

@section('content')
    <!-- Top Navigation Tabs (Subject Teacher View vs Adviser View) -->
    <div class="top-nav-tabs">
        <a href="{{ route('elementary.grades.page') }}" class="top-nav-tab">
            <i class="fa-solid fa-book"></i>
            <span>My Handled Subjects (Subject Teacher)</span>
        </a>
        <a href="{{ route('elementary.grades.advisory.page') }}" class="top-nav-tab active">
            <i class="fa-solid fa-user-shield"></i>
            <span>Advisory Class Grades (Class Adviser)</span>
            @if (!empty($isAdviser))
                <span style="background: #f59e0b; color: #064e3b; padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 800;">Adviser</span>
            @endif
        </a>
    </div>

    <!-- Header Controls: Advisory Section Selector & Academic Quarter Tabs -->
    <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
            <!-- Select Advisory Section -->
            <form action="{{ route('elementary.grades.advisory.page') }}" method="GET" style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; margin: 0; width: 100%; max-width: 580px;">
                <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">
                <label style="font-weight: 700; font-size: 0.875rem; color: #0f172a; white-space: nowrap;">
                    <i class="fa-solid fa-users" style="color: #059669;"></i> Select Advisory Class Section:
                </label>
                <select name="class_section_id" onchange="this.form.submit()" style="padding: 0.55rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 700; font-size: 0.875rem; outline: none; background: #ffffff; flex: 1; min-width: 220px; max-width: 100%;">
                    @if ($advisorySections->isEmpty())
                        <option value="">No advisory classes assigned</option>
                    @else
                        @foreach ($advisorySections as $sec)
                            <option value="{{ $sec->id }}" {{ $currentSection && $currentSection->id == $sec->id ? 'selected' : '' }}>
                                {{ $sec->section_name }} ({{ $sec->gradeLevel ? $sec->gradeLevel->name : 'N/A' }})
                            </option>
                        @endforeach
                    @endif
                </select>
            </form>

            <!-- Academic Period Tabs (1st Qtr to 4th Qtr) -->
            <div style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
                @foreach (['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'] as $qtr)
                    <a href="{{ route('elementary.grades.advisory.page', ['class_section_id' => $currentSection ? $currentSection->id : null, 'academic_period' => $qtr]) }}"
                        class="period-tab {{ $selectedPeriod == $qtr ? 'active' : '' }}">
                        {{ $qtr }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @if ($currentSection)
        <!-- Stat Cards Grid -->
        <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
            <div class="stat-card">
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Students</span>
                    <h3 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-top: 0.2rem;">{{ $classStats['total_students'] }}</h3>
                </div>
                <div class="stat-icon" style="background: #e0e7ff; color: #3730a3;">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>

            <div class="stat-card">
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Class Average</span>
                    <h3 style="font-size: 1.5rem; font-weight: 800; color: #059669; margin-top: 0.2rem;">{{ $classStats['class_average'] ? number_format($classStats['class_average'], 2) : '0.00' }}</h3>
                </div>
                <div class="stat-icon" style="background: #d1fae5; color: #059669;">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>

            <div class="stat-card">
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Passed Students</span>
                    <h3 style="font-size: 1.5rem; font-weight: 800; color: #059669; margin-top: 0.2rem;">{{ $classStats['passed_count'] }}</h3>
                </div>
                <div class="stat-icon" style="background: #d1fae5; color: #065f46;">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>

            <div class="stat-card">
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Failed Students</span>
                    <h3 style="font-size: 1.5rem; font-weight: 800; color: #dc2626; margin-top: 0.2rem;">{{ $classStats['failed_count'] }}</h3>
                </div>
                <div class="stat-icon" style="background: #fee2e2; color: #dc2626;">
                    <i class="fa-solid fa-user-xmark"></i>
                </div>
            </div>
        </div>

        <!-- Grade Summary Matrix Table -->
        <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: var(--shadow-sm);" class="table-responsive">
            <table class="grade-matrix-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="position: sticky; left: 0; background: #f8fafc; z-index: 5; border-right: 2px solid #cbd5e1; min-width: 200px;">Student Name</th>
                        @foreach ($sectionSubjects as $css)
                            <th colspan="4" style="text-align: center; background: #eef2ff; color: #3730a3; border-right: 2px solid #cbd5e1;">
                                {{ $css->subject ? ($css->subject->subject_name ?? $css->subject->name) : 'Subject' }}
                            </th>
                        @endforeach
                        <th rowspan="2" style="text-align: center; background: #ecfdf5; color: #065f46; min-width: 110px;">General Average</th>
                        <th rowspan="2" style="text-align: center; background: #ecfdf5; color: #065f46; min-width: 90px;">Remarks</th>
                        <th rowspan="2" style="text-align: center; background: #f8fafc; min-width: 110px;">Action</th>
                    </tr>
                    <tr>
                        @foreach ($sectionSubjects as $css)
                            <th style="text-align: center; font-size: 0.725rem; background: #e0e7ff; width: 35px;">1st</th>
                            <th style="text-align: center; font-size: 0.725rem; background: #e0e7ff; width: 35px;">2nd</th>
                            <th style="text-align: center; font-size: 0.725rem; background: #e0e7ff; width: 35px;">3rd</th>
                            <th style="text-align: center; font-size: 0.725rem; background: #e0e7ff; width: 35px; border-right: 2px solid #cbd5e1;">4th</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($enrolledStudents as $stu)
                        <tr>
                            <td style="font-weight: 700; color: #0f172a; position: sticky; left: 0; background: #ffffff; z-index: 5; border-right: 2px solid #cbd5e1;">
                                {{ trim(($stu->last_name ? $stu->last_name . ', ' : '') . $stu->first_name . ($stu->middle_name ? ' ' . $stu->middle_name : '') . ($stu->extension_name ? ' ' . $stu->extension_name : '')) }}
                            </td>

                            @foreach ($sectionSubjects as $css)
                                @php
                                    $q1 = $quarterGradesMatrix[$stu->id][$css->id]['1st Quarter'] ?? null;
                                    $q2 = $quarterGradesMatrix[$stu->id][$css->id]['2nd Quarter'] ?? null;
                                    $q3 = $quarterGradesMatrix[$stu->id][$css->id]['3rd Quarter'] ?? null;
                                    $q4 = $quarterGradesMatrix[$stu->id][$css->id]['4th Quarter'] ?? null;
                                @endphp
                                <td style="text-align: center; font-size: 0.8rem; background: #fafafa;">{{ $q1 !== null ? round($q1) : '-' }}</td>
                                <td style="text-align: center; font-size: 0.8rem; background: #fafafa;">{{ $q2 !== null ? round($q2) : '-' }}</td>
                                <td style="text-align: center; font-size: 0.8rem; background: #fafafa;">{{ $q3 !== null ? round($q3) : '-' }}</td>
                                <td style="text-align: center; font-size: 0.8rem; background: #fafafa; border-right: 2px solid #cbd5e1;">{{ $q4 !== null ? round($q4) : '-' }}</td>
                            @endforeach

                            @php
                                $summary = $studentSummaries[$stu->id] ?? ['general_average' => null, 'remarks' => 'Pending'];
                                $genAvgVal = $summary['general_average'] ? round($summary['general_average']) : null;
                            @endphp

                            <td style="text-align: center; font-weight: 800; font-size: 0.95rem; color: #0f172a; background: #f0fdf4;">
                                {{ $genAvgVal ?? 'N/A' }}
                            </td>

                            <td style="text-align: center; font-weight: 700; background: #f0fdf4;">
                                @if ($summary['remarks'] == 'Passed')
                                    <span style="color: #059669;">Passed</span>
                                @elseif ($summary['remarks'] == 'Failed')
                                    <span style="color: #dc2626;">Failed</span>
                                @else
                                    <span style="color: #d97706;">Pending</span>
                                @endif
                            </td>

                            <td style="text-align: center; padding: 0.4rem;">
                                <a href="{{ route('elementary.grades.print_card', ['student_id' => $stu->id, 'class_section_id' => $currentSection->id]) }}" target="_blank" style="background: #059669; color: #ffffff; text-decoration: none; padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 700; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.3rem;">
                                    <i class="fa-solid fa-print"></i> Report Card
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%" style="text-align: center; padding: 3rem; color: #94a3b8;">
                                <i class="fa-solid fa-users-slash" style="font-size: 2.5rem; margin-bottom: 0.75rem;"></i>
                                <p>No enrolled students found in this advisory section.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
@endsection
