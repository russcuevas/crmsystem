@extends('layouts.junior_high_school')

@section('title', 'GNHS-P - Advisory Class Grades Summary')
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
            background: #4f46e5;
            color: #ffffff;
            border-color: #4f46e5;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
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
            border-top: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .grade-matrix-table th:first-child {
            border-left: 1px solid #e2e8f0;
            border-top-left-radius: 8px;
        }

        .grade-matrix-table th:last-child {
            border-top-right-radius: 8px;
        }

        .grade-matrix-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            vertical-align: middle;
            text-align: center;
            background: #ffffff;
        }

        .grade-matrix-table td:first-child {
            border-left: 1px solid #e2e8f0;
        }

        .grade-matrix-table tr:hover td {
            background: #f1f5f9;
        }

        .grade-badge {
            display: inline-block;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            font-weight: 800;
            font-size: 0.85rem;
            min-width: 48px;
            text-align: center;
        }

        .grade-pass {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .grade-fail {
            background: #ffe4e6;
            color: #9f1239;
            border: 1px solid #fecdd3;
        }

        .grade-na {
            background: #f1f5f9;
            color: #94a3b8;
            border: 1px solid #e2e8f0;
        }

        .teacher-sub-tag {
            display: block;
            font-size: 0.68rem;
            color: #64748b;
            font-weight: 600;
            margin-top: 0.2rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 140px;
        }

        /* Modal styling */
        .custom-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .custom-modal.show {
            display: flex;
        }

        .modal-content {
            background: #ffffff;
            border-radius: 1rem;
            width: 100%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .printable-area,
            .printable-area * {
                visibility: visible;
            }

            .printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Top Navigation Tabs (Subject Teacher View vs Adviser View) -->
    <div class="top-nav-tabs no-print">
        <a href="{{ route('junior_high_school.grades.page') }}" class="top-nav-tab">
            <i class="fa-solid fa-book"></i>
            <span>My Handled Subjects (Subject Teacher)</span>
        </a>
        <a href="{{ route('junior_high_school.grades.advisory.page') }}" class="top-nav-tab active">
            <i class="fa-solid fa-user-shield"></i>
            <span>Advisory Class Grades (Class Adviser)</span>
            <span
                style="background: #f59e0b; color: #1e1b4b; padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 800;">Adviser</span>
        </a>
    </div>

    @if (!$isAdviser)
        <!-- Not an adviser notice -->
        <div
            style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 3rem 2rem; text-align: center; box-shadow: var(--shadow-sm);">
            <div
                style="width: 70px; height: 70px; background: #fef3c7; color: #d97706; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1.25rem;">
                <i class="fa-solid fa-user-xmark"></i>
            </div>
            <h3 style="font-size: 1.35rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;">Not Assigned as Class
                Adviser</h3>
            <p style="color: #64748b; font-size: 0.95rem; max-width: 520px; margin: 0 auto 1.5rem;">
                You are currently not assigned as a Class Adviser for any section in the active school year
                ({{ $activeSchoolYear ? $activeSchoolYear->school_year ?? ($activeSchoolYear->name ?? 'Current') : 'Current' }}).
            </p>
            <a href="{{ route('junior_high_school.grades.page') }}" class="period-tab active"
                style="padding: 0.65rem 1.25rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-arrow-left"></i> Go to Handled Subjects
            </a>
        </div>
    @else
        <!-- Filter Controls: Section Selector & Academic Period Tabs -->
        <div class="no-print"
            style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
            <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
                <!-- Select Advisory Section -->
                <form action="{{ route('junior_high_school.grades.advisory.page') }}" method="GET"
                    style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; margin: 0; width: 100%; max-width: 550px;">
                    <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">
                    <label style="font-weight: 700; font-size: 0.875rem; color: #0f172a; white-space: nowrap;">
                        <i class="fa-solid fa-users-rectangle" style="color: #4f46e5;"></i> Select Advisory Class:
                    </label>
                    <select name="class_section_id" onchange="this.form.submit()"
                        style="padding: 0.55rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 700; font-size: 0.875rem; outline: none; background: #ffffff; flex: 1; min-width: 220px;">
                        @foreach ($advisorySections as $sec)
                            <option value="{{ $sec->id }}"
                                {{ $currentSection && $currentSection->id == $sec->id ? 'selected' : '' }}>
                                {{ $sec->section_name }} ({{ $sec->gradeLevel ? $sec->gradeLevel->name : 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </form>

                <!-- Academic Period Tabs -->
                <div style="display: flex; flex-wrap: wrap; gap: 0.4rem; align-items: center;">
                    @foreach (['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'] as $qtr)
                        <a href="{{ route('junior_high_school.grades.advisory.page', ['class_section_id' => $currentSection ? $currentSection->id : null, 'academic_period' => $qtr]) }}"
                            class="period-tab {{ $selectedPeriod == $qtr ? 'active' : '' }}">
                            {{ $qtr }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        @if ($currentSection)
            <!-- Class Statistics Cards -->
            <div class="no-print" style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
                <div class="stat-card">
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Total
                            Students</span>
                        <h4 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-top: 0.2rem;">
                            {{ $classStats['total_students'] }}</h4>
                    </div>
                    <div class="stat-icon" style="background: #e0e7ff; color: #4338ca;">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div>
                        <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Class
                            Average (GWA)</span>
                        <h4 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-top: 0.2rem;">
                            {{ $classStats['class_average'] > 0 ? number_format($classStats['class_average'], 2) : 'N/A' }}
                        </h4>
                    </div>
                    <div class="stat-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div>
                        <span
                            style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Passed
                            Students</span>
                        <h4 style="font-size: 1.5rem; font-weight: 800; color: #166534; margin-top: 0.2rem;">
                            {{ $classStats['passed_count'] }}</h4>
                    </div>
                    <div class="stat-icon" style="background: #dcfce7; color: #15803d;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div>
                        <span
                            style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Failed
                            Students</span>
                        <h4 style="font-size: 1.5rem; font-weight: 800; color: #9f1239; margin-top: 0.2rem;">
                            {{ $classStats['failed_count'] }}</h4>
                    </div>
                    <div class="stat-icon" style="background: #ffe4e6; color: #be123c;">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div>
                        <span
                            style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Pending
                            Grades</span>
                        <h4 style="font-size: 1.5rem; font-weight: 800; color: #475569; margin-top: 0.2rem;">
                            {{ $classStats['pending_count'] }}</h4>
                    </div>
                    <div class="stat-icon" style="background: #f1f5f9; color: #64748b;">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
            </div>

            <!-- Grade Matrix Table Container -->
            <div class="printable-area"
                style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: var(--shadow-sm);">
                <!-- Header for Printed Document -->
                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
                    <div>
                        <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0;">
                            Section: {{ $currentSection->section_name }}
                            <span style="font-size: 0.9rem; font-weight: 600; color: #64748b;">
                                ({{ $currentSection->gradeLevel ? $currentSection->gradeLevel->name : 'N/A' }})
                            </span>
                        </h3>
                        <p style="font-size: 0.85rem; color: #64748b; margin: 0.2rem 0 0 0;">
                            Class Adviser: <strong>{{ $teacher->first_name }} {{ $teacher->last_name }}</strong> |
                            Academic Period: <strong>{{ $selectedPeriod }}</strong> |
                            S.Y.:
                            <strong>{{ $activeSchoolYear ? $activeSchoolYear->school_year ?? ($activeSchoolYear->name ?? 'Current') : ($currentSection && $currentSection->schoolYear ? $currentSection->schoolYear->school_year : 'N/A') }}</strong>
                        </p>
                    </div>
                    <div class="no-print" style="font-size: 0.8rem; color: #64748b; text-align: right;">
                        <span>Total Subjects: <strong>{{ $sectionSubjects->count() }}</strong></span>
                    </div>
                </div>

                @if ($enrolledStudents->isEmpty())
                    <div style="padding: 3rem; text-align: center; color: #64748b;">
                        <i class="fa-solid fa-users" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 0.75rem;"></i>
                        <p style="font-weight: 700; font-size: 1rem; margin: 0;">No enrolled students found in this section.
                        </p>
                    </div>
                @elseif($sectionSubjects->isEmpty())
                    <div style="padding: 3rem; text-align: center; color: #64748b;">
                        <i class="fa-solid fa-book-open"
                            style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 0.75rem;"></i>
                        <p style="font-weight: 700; font-size: 1rem; margin: 0;">No subjects assigned to this class section
                            yet.</p>
                    </div>
                @else
                    <div style="overflow-x: auto; max-width: 100%;">
                        <table class="grade-matrix-table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th style="text-align: left; min-width: 180px;">Student Name</th>
                                    <th style="min-width: 110px;">LRN</th>
                                    @foreach ($sectionSubjects as $secSub)
                                        <th style="min-width: 120px;">
                                            <div>
                                                {{ $secSub->subject ? $secSub->subject->subject_code ?? $secSub->subject->subject_name : 'Subject' }}
                                            </div>
                                            <span class="teacher-sub-tag"
                                                title="Teacher: {{ $secSub->teacher ? $secSub->teacher->first_name . ' ' . $secSub->teacher->last_name : 'Unassigned' }}">
                                                <i class="fa-solid fa-chalkboard-user"></i>
                                                {{ $secSub->teacher ? $secSub->teacher->last_name : 'No Teacher' }}
                                            </span>
                                        </th>
                                    @endforeach
                                    <th style="min-width: 100px; background: #e0e7ff; color: #1e1b4b;">GWA</th>
                                    <th style="min-width: 100px;">Remarks</th>
                                    <th class="no-print" style="min-width: 80px;">Card</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($enrolledStudents as $index => $student)
                                    @php
                                        $summary = $studentSummaries[$student->id] ?? [
                                            'gwa' => null,
                                            'remarks' => 'Pending',
                                        ];
                                        $gwa = $summary['gwa'];
                                        $remarks = $summary['remarks'];
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td style="text-align: left; font-weight: 700; color: #0f172a;">
                                            {{ trim(($student->last_name ? $student->last_name . ', ' : '') . $student->first_name . ($student->middle_name ? ' ' . $student->middle_name : '') . ($student->extension_name ? ' ' . $student->extension_name : '')) }}
                                        </td>
                                        <td style="font-weight: 600; color: #475569; font-size: 0.8rem;">
                                            {{ $student->lrn ?? ($student->student_number ?? 'N/A') }}
                                        </td>
                                        @foreach ($sectionSubjects as $secSub)
                                            @php
                                                $subjGrade = $gradesMatrix[$student->id][$secSub->id] ?? null;
                                            @endphp
                                            <td>
                                                @if ($subjGrade !== null)
                                                    <span
                                                        class="grade-badge {{ $subjGrade >= 75 ? 'grade-pass' : 'grade-fail' }}">
                                                        {{ number_format($subjGrade, 2) }}
                                                    </span>
                                                @else
                                                    <span class="grade-badge grade-na">--</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <!-- General Weighted Average (GWA) -->
                                        <td style="background: #f5f3ff; font-weight: 800;">
                                            @if ($gwa !== null)
                                                <span
                                                    style="font-size: 0.9rem; color: {{ $gwa >= 75 ? '#15803d' : '#be123c' }}; font-weight: 800;">
                                                    {{ number_format($gwa, 2) }}
                                                </span>
                                            @else
                                                <span style="color: #94a3b8; font-weight: 600;">--</span>
                                            @endif
                                        </td>
                                        <!-- Overall Status / Remarks -->
                                        <td>
                                            @if (str_contains($remarks, 'Passed'))
                                                <span
                                                    style="background: #dcfce7; color: #15803d; font-weight: 800; font-size: 0.75rem; padding: 0.25rem 0.55rem; border-radius: 9999px; display: inline-block;">
                                                    <i class="fa-solid fa-check"></i> {{ $remarks }}
                                                </span>
                                            @elseif ($remarks === 'Failed')
                                                <span
                                                    style="background: #ffe4e6; color: #be123c; font-weight: 800; font-size: 0.75rem; padding: 0.25rem 0.55rem; border-radius: 9999px; display: inline-block;">
                                                    <i class="fa-solid fa-xmark"></i> Failed
                                                </span>
                                            @else
                                                <span
                                                    style="background: #f1f5f9; color: #64748b; font-weight: 700; font-size: 0.75rem; padding: 0.25rem 0.55rem; border-radius: 9999px; display: inline-block;">
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <!-- Action: View Detailed Report Card Modal & Print Form 138 Card -->
                                        <td class="no-print">
                                            <div style="display: flex; gap: 0.35rem; justify-content: center;">
                                                <button type="button"
                                                    onclick="openStudentModal({{ $student->id }}, '{{ addslashes($student->first_name . ' ' . $student->last_name) }}')"
                                                    style="background: #e0e7ff; color: #3730a3; border: none; padding: 0.35rem 0.6rem; border-radius: 6px; font-weight: 700; font-size: 0.75rem; cursor: pointer;"
                                                    title="View Grade Breakdown">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                                <a href="{{ route('junior_high_school.grades.print_card', ['student_id' => $student->id, 'class_section_id' => $currentSection->id]) }}"
                                                    target="_blank"
                                                    style="background: #fef3c7; color: #92400e; text-decoration: none; padding: 0.35rem 0.6rem; border-radius: 6px; font-weight: 700; font-size: 0.75rem; display: inline-flex; align-items: center; justify-content: center;"
                                                    title="Print Form 138 (SF9)">
                                                    <i class="fa-solid fa-print"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    @endif

    <!-- Student Detailed Report Card Modal -->
    <div id="studentReportModal" class="custom-modal no-print">
        <div class="modal-content">
            <div
                style="background: #1e1b4b; color: #ffffff; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                <h4 id="modalStudentName"
                    style="font-size: 1.15rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-graduation-cap" style="color: #f59e0b;"></i> Student Grade Report Card
                </h4>
                <button type="button" onclick="closeStudentModal()"
                    style="background: transparent; border: none; color: #ffffff; font-size: 1.25rem; cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div style="padding: 1.5rem;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #cbd5e1;">
                                <th style="padding: 0.75rem; text-align: left;">Subject</th>
                                <th style="padding: 0.75rem; text-align: left;">Teacher</th>
                                <th style="padding: 0.75rem; text-align: center;">Q1</th>
                                <th style="padding: 0.75rem; text-align: center;">Q2</th>
                                <th style="padding: 0.75rem; text-align: center;">Q3</th>
                                <th style="padding: 0.75rem; text-align: center;">Q4</th>
                                <th style="padding: 0.75rem; text-align: center; background: #e0e7ff; color: #1e1b4b;">
                                    Final</th>
                            </tr>
                        </thead>
                        <tbody id="modalGradesBody">
                            <!-- Populated via Javascript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div
                style="padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
                <a id="modalPrintCardBtn" href="#" target="_blank"
                    style="background: #f59e0b; color: #1e1b4b; text-decoration: none; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 800; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <i class="fa-solid fa-print"></i> Print Official Form 138 (SF9) Card
                </a>
                <button type="button" onclick="closeStudentModal()"
                    style="background: #64748b; color: #ffffff; border: none; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                    Close
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const quarterGradesMatrixData = @json($quarterGradesMatrix ?? []);
        const sectionSubjectsData = @json($sectionSubjects ?? []);

        function openStudentModal(studentId, studentName) {
            document.getElementById('modalStudentName').innerText = studentName + ' - Report Card';
            document.getElementById('modalPrintCardBtn').href = "{{ url('/junior-high-school/grades/print-card') }}/" +
                studentId + "?class_section_id={{ $currentSection ? $currentSection->id : '' }}";
            const tbody = document.getElementById('modalGradesBody');
            tbody.innerHTML = '';

            const studentQuarterGrades = quarterGradesMatrixData[studentId] || {};

            sectionSubjectsData.forEach(sub => {
                const subId = sub.id;
                const subjectName = sub.subject ? (sub.subject.subject_name || sub.subject.subject_code) : 'N/A';
                const teacherName = sub.teacher ? (sub.teacher.first_name + ' ' + sub.teacher.last_name) :
                    'Unassigned';

                const qGrades = studentQuarterGrades[subId] || {};
                const q1 = qGrades['1st Quarter'] !== null && qGrades['1st Quarter'] !== undefined ? parseFloat(
                    qGrades['1st Quarter']).toFixed(2) : '--';
                const q2 = qGrades['2nd Quarter'] !== null && qGrades['2nd Quarter'] !== undefined ? parseFloat(
                    qGrades['2nd Quarter']).toFixed(2) : '--';
                const q3 = qGrades['3rd Quarter'] !== null && qGrades['3rd Quarter'] !== undefined ? parseFloat(
                    qGrades['3rd Quarter']).toFixed(2) : '--';
                const q4 = qGrades['4th Quarter'] !== null && qGrades['4th Quarter'] !== undefined ? parseFloat(
                    qGrades['4th Quarter']).toFixed(2) : '--';

                const validVals = [qGrades['1st Quarter'], qGrades['2nd Quarter'], qGrades['3rd Quarter'], qGrades[
                    '4th Quarter']].filter(v => v !== null && v !== undefined);
                let finalAvg = '--';
                if (validVals.length > 0) {
                    const sum = validVals.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                    finalAvg = (sum / validVals.length).toFixed(2);
                }

                const tr = document.createElement('tr');
                tr.style.borderBottom = '1px solid #e2e8f0';
                tr.innerHTML = `
                    <td style="padding: 0.75rem; font-weight: 700; color: #0f172a;">${subjectName}</td>
                    <td style="padding: 0.75rem; color: #64748b; font-size: 0.8rem;">${teacherName}</td>
                    <td style="padding: 0.75rem; text-align: center; font-weight: 700; color: ${q1 !== '--' ? (parseFloat(q1) >= 75 ? '#15803d' : '#be123c') : '#94a3b8'};">${q1}</td>
                    <td style="padding: 0.75rem; text-align: center; font-weight: 700; color: ${q2 !== '--' ? (parseFloat(q2) >= 75 ? '#15803d' : '#be123c') : '#94a3b8'};">${q2}</td>
                    <td style="padding: 0.75rem; text-align: center; font-weight: 700; color: ${q3 !== '--' ? (parseFloat(q3) >= 75 ? '#15803d' : '#be123c') : '#94a3b8'};">${q3}</td>
                    <td style="padding: 0.75rem; text-align: center; font-weight: 700; color: ${q4 !== '--' ? (parseFloat(q4) >= 75 ? '#15803d' : '#be123c') : '#94a3b8'};">${q4}</td>
                    <td style="padding: 0.75rem; text-align: center; font-weight: 800; background: #f5f3ff; color: ${finalAvg !== '--' ? (parseFloat(finalAvg) >= 75 ? '#15803d' : '#be123c') : '#94a3b8'};">${finalAvg}</td>
                `;
                tbody.appendChild(tr);
            });

            document.getElementById('studentReportModal').classList.add('show');
        }

        function closeStudentModal() {
            document.getElementById('studentReportModal').classList.remove('show');
        }
    </script>
@endpush
