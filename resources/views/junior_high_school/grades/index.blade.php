@extends('layouts.junior_high_school')

@section('title', 'GNHS-P - JHS Class Record & Grading')
@section('header_title', 'Class Record & Grading Sheet')

@push('styles')
    <style>
        .score-cell {
            width: 65px;
            padding: 0.35rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            text-align: center;
            font-weight: 700;
            font-size: 0.85rem;
            outline: none;
            transition: all 0.15s ease;
        }

        .score-cell:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
            background: #eef2ff;
        }

        .score-saved {
            border-color: #10b981 !important;
            background: #ecfdf5 !important;
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
        }

        .period-tab.active {
            background: #1e1b4b;
            color: #ffffff;
            border-color: #1e1b4b;
        }

        .table-responsive {
            overflow-x: auto;
            max-width: 100%;
        }

        .grading-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .grading-table th,
        .grading-table td {
            border: 1px solid #e2e8f0;
            padding: 0.65rem 0.75rem;
            white-space: nowrap;
        }

        .grading-table th {
            background: #f8fafc;
            color: #0f172a;
            font-weight: 700;
        }
    </style>
@endpush

@section('content')
    <!-- Top Navigation Tabs (Subject Teacher View vs Adviser View) -->
    <div
        style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem;">
        <a href="{{ route('junior_high_school.grades.page') }}"
            style="padding: 0.65rem 1.25rem; border-radius: 0.625rem; font-weight: 700; font-size: 0.875rem; text-decoration: none; color: #ffffff; background: #1e1b4b; box-shadow: 0 4px 12px rgba(30, 27, 75, 0.25); display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-book"></i>
            <span>My Handled Subjects (Subject Teacher)</span>
        </a>
        <a href="{{ route('junior_high_school.grades.advisory.page') }}"
            style="padding: 0.65rem 1.25rem; border-radius: 0.625rem; font-weight: 700; font-size: 0.875rem; text-decoration: none; color: #64748b; background: transparent; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-user-shield"></i>
            <span>Advisory Class Grades (Class Adviser)</span>
            @if (!empty($isAdviser))
                <span
                    style="background: #f59e0b; color: #1e1b4b; padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 800;">Adviser</span>
            @endif
        </a>
    </div>

    <!-- Header Controls: Subject Selector & Academic Quarter Tabs -->
    <div
        style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
            <!-- Select Handled Subject -->
            <form action="{{ route('junior_high_school.grades.page') }}" method="GET"
                style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; margin: 0; width: 100%; max-width: 580px;">
                <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">
                <label style="font-weight: 700; font-size: 0.875rem; color: #0f172a; white-space: nowrap;">
                    <i class="fa-solid fa-book-open" style="color: #4f46e5;"></i> Select Handled Subject:
                </label>
                <select name="section_subject_id" onchange="this.form.submit()"
                    style="padding: 0.55rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 700; font-size: 0.875rem; outline: none; background: #ffffff; flex: 1; min-width: 220px; max-width: 100%;">
                    @if ($handledSubjects->isEmpty())
                        <option value="">No handled subjects assigned</option>
                    @else
                        @foreach ($handledSubjects as $hs)
                            <option value="{{ $hs->id }}"
                                {{ $currentSectionSubject && $currentSectionSubject->id == $hs->id ? 'selected' : '' }}>
                                {{ $hs->subject ? $hs->subject->subject_name ?? $hs->subject->subject_code : 'N/A' }} -
                                {{ $hs->classSection ? $hs->classSection->section_name : 'N/A' }}
                                ({{ $hs->classSection && $hs->classSection->gradeLevel ? $hs->classSection->gradeLevel->name : 'N/A' }})
                            </option>
                        @endforeach
                    @endif
                </select>
            </form>

            <!-- Academic Period Tabs (1st Qtr to 4th Qtr) -->
            <div style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
                @foreach (['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'] as $qtr)
                    <a href="{{ route('junior_high_school.grades.page', ['section_subject_id' => $currentSectionSubject ? $currentSectionSubject->id : null, 'academic_period' => $qtr]) }}"
                        class="period-tab {{ $selectedPeriod == $qtr ? 'active' : '' }}">
                        {{ $qtr }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @if (isset($isParentSubject) && $isParentSubject)
        <!-- MAPEH Subcomponents Tab Bar -->
        <div
            style="background: #ffffff; padding: 0.85rem 1.25rem; border-radius: 1rem; border: 1px solid #e2e8f0; margin-bottom: 1.25rem; box-shadow: var(--shadow-sm);">
            <div
                style="font-size: 0.78rem; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 0.6rem; letter-spacing: 0.5px; display: flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-cubes" style="color: #4f46e5;"></i>
                {{ $currentSectionSubject->subject->subject_name }} Component Tabs & Attendance:
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
                @foreach ($subSectionSubjects as $subSec)
                    @php
                        $isSubActive =
                            is_object($activeSubSectionSubject) && $activeSubSectionSubject->id == $subSec->id;
                        $sName = $subSec->subject->subject_name ?? '';
                        $iconClass = 'fa-book';
                        if (str_contains(strtolower($sName), 'music')) {
                            $iconClass = 'fa-music';
                        } elseif (str_contains(strtolower($sName), 'art')) {
                            $iconClass = 'fa-palette';
                        } elseif (
                            str_contains(strtolower($sName), 'pe') ||
                            str_contains(strtolower($sName), 'physical')
                        ) {
                            $iconClass = 'fa-futbol';
                        } elseif (str_contains(strtolower($sName), 'health')) {
                            $iconClass = 'fa-heart-pulse';
                        }
                    @endphp
                    <a href="{{ route('junior_high_school.grades.page', ['section_subject_id' => $currentSectionSubject->id, 'academic_period' => $selectedPeriod, 'sub_subject_id' => $subSec->id]) }}"
                        style="padding: 0.55rem 1.1rem; border-radius: 10px; font-weight: 800; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.45rem; transition: all 0.2s; {{ $isSubActive ? 'background: #4f46e5; color: #ffffff; box-shadow: 0 3px 8px rgba(79, 70, 229, 0.3);' : 'background: #f8fafc; color: #334155; border: 1.5px solid #cbd5e1;' }}">
                        <i class="fa-solid {{ $iconClass }}"></i> {{ $sName }}
                    </a>
                @endforeach

                <a href="{{ route('junior_high_school.grades.page', ['section_subject_id' => $currentSectionSubject->id, 'academic_period' => $selectedPeriod, 'sub_subject_id' => 'summary']) }}"
                    style="padding: 0.55rem 1.1rem; border-radius: 10px; font-weight: 800; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.45rem; transition: all 0.2s; {{ $activeSubSectionSubject === 'summary' ? 'background: #059669; color: #ffffff; box-shadow: 0 3px 8px rgba(5, 150, 105, 0.3);' : 'background: #f8fafc; color: #334155; border: 1.5px solid #cbd5e1;' }}">
                    <i class="fa-solid fa-chart-line"></i> {{ $currentSectionSubject->subject->subject_name }} Summary
                </a>
            </div>
        </div>

        @if ($activeSubSectionSubject === 'summary')
            <!-- MAPEH Overall Summary Table Card -->
            <div
                style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 2rem; box-shadow: var(--shadow-sm);">
                <div
                    style="background: #ecfdf5; padding: 1rem 1.25rem; border-bottom: 1.5px solid #a7f3d0; color: #065f46; font-weight: 800; font-size: 1rem;">
                    <i class="fa-solid fa-chart-pie" style="color: #059669; margin-right: 6px;"></i>
                    {{ $currentSectionSubject->subject->subject_name }} {{ $selectedPeriod }} Overall Component Summary
                </div>
                <div style="padding: 1.25rem; overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 0.75rem; text-align: left;">#</th>
                                <th style="padding: 0.75rem; text-align: left;">LRN</th>
                                <th style="padding: 0.75rem; text-align: left;">Student Name</th>
                                @foreach ($subSectionSubjects as $subSec)
                                    <th style="padding: 0.75rem; text-align: center;">{{ $subSec->subject->subject_name }}
                                    </th>
                                @endforeach
                                <th
                                    style="padding: 0.75rem; text-align: center; background: #dcfce7; color: #15803d; font-weight: 800;">
                                    {{ $currentSectionSubject->subject->subject_name }} Grade</th>
                                <th style="padding: 0.75rem; text-align: center;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($enrolledStudents as $idx => $studentItem)
                                @php
                                    $student =
                                        is_object($studentItem) && isset($studentItem->student) && $studentItem->student
                                            ? $studentItem->student
                                            : $studentItem;
                                    $studentEnrollmentIds =
                                        is_object($student) &&
                                        method_exists($student, 'enrollments') &&
                                        $student->enrollments
                                            ? $student->enrollments->pluck('id')->toArray()
                                            : [$studentItem->id ?? 0];

                                    $subGradesList = [];
                                    foreach ($subSectionSubjects as $subSec) {
                                        $g = $mapehSummaryGrades
                                            ->filter(function ($item) use ($studentEnrollmentIds, $student, $subSec) {
                                                return $item->class_section_subject_id == $subSec->id &&
                                                    (($item->enrollment &&
                                                        $item->enrollment->student_id == $student->id) ||
                                                        in_array($item->enrollment_id, $studentEnrollmentIds));
                                            })
                                            ->first();
                                        $subGradesList[$subSec->id] = $g ? $g->final_grade : null;
                                    }

                                    $parentG = $mapehSummaryGrades
                                        ->filter(function ($item) use (
                                            $studentEnrollmentIds,
                                            $student,
                                            $currentSectionSubject,
                                        ) {
                                            return $item->class_section_subject_id == $currentSectionSubject->id &&
                                                (($item->enrollment && $item->enrollment->student_id == $student->id) ||
                                                    in_array($item->enrollment_id, $studentEnrollmentIds));
                                        })
                                        ->first();

                                    $parentGradeVal = $parentG ? $parentG->final_grade : null;
                                    $parentRemarks = $parentG ? $parentG->remarks : 'Pending';
                                @endphp
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 0.75rem;">{{ $idx + 1 }}</td>
                                    <td style="padding: 0.75rem;">
                                        <strong>{{ $student->lrn ?? ($student->student_number ?? 'N/A') }}</strong></td>
                                    <td style="padding: 0.75rem;"><strong>{{ $student->last_name ?? '' }},
                                            {{ $student->first_name ?? '' }} {{ $student->middle_name ?? '' }}</strong>
                                    </td>
                                    @foreach ($subSectionSubjects as $subSec)
                                        <td style="padding: 0.75rem; text-align: center;">
                                            @if ($subGradesList[$subSec->id] !== null)
                                                <span
                                                    style="font-weight: 800; font-size: 0.9rem; color: #0f172a;">{{ number_format($subGradesList[$subSec->id], 0) }}</span>
                                            @else
                                                <span style="color: #94a3b8; font-style: italic;">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td style="padding: 0.75rem; text-align: center; background: #f0fdf4;">
                                        @if ($parentGradeVal !== null)
                                            <span
                                                style="font-weight: 800; font-size: 0.95rem; color: #047857;">{{ number_format($parentGradeVal, 0) }}</span>
                                        @else
                                            <span style="color: #94a3b8; font-style: italic;">-</span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem; text-align: center;">
                                        @if ($parentRemarks == 'Passed')
                                            <span
                                                style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 0.2rem 0.6rem; border-radius: 6px; font-weight: 700; font-size: 0.75rem;">Passed</span>
                                        @elseif($parentRemarks == 'Failed')
                                            <span
                                                style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; padding: 0.2rem 0.6rem; border-radius: 6px; font-weight: 700; font-size: 0.75rem;">Failed</span>
                                        @else
                                            <span
                                                style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 0.2rem 0.6rem; border-radius: 6px; font-weight: 700; font-size: 0.75rem;">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

    @if ($currentSectionSubject)
        @php
            $targetSecSubId =
                isset($isParentSubject) && $isParentSubject && is_object($activeSubSectionSubject)
                    ? $activeSubSectionSubject->id
                    : $currentSectionSubject->id;
            $displaySubjName =
                isset($isParentSubject) && $isParentSubject && is_object($activeSubSectionSubject)
                    ? $activeSubSectionSubject->subject->subject_name
                    : ($currentSectionSubject->subject
                        ? $currentSectionSubject->subject->subject_name
                        : 'N/A');
        @endphp
        @if ($activeSubSectionSubject !== 'summary')
            <!-- Action Toolbar for Grading Categories, Tasks, Attendance & Computations -->
            <div
                style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.25rem;">
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a;">
                        {{ $displaySubjName }}
                        <br>
                        <span style="color: #64748b; font-weight: 600; font-size: 0.95rem; margin-left: 0.35rem;">
                            {{ $currentSectionSubject->classSection ? $currentSectionSubject->classSection->section_name : 'N/A' }}
                        </span>
                    </h3>
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    <button type="button" onclick="openCategoryModal()"
                        style="background: #e0e7ff; color: #3730a3; padding: 0.5rem 0.85rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.8rem; border: none; cursor: pointer;">
                        <i class="fa-solid fa-plus"></i> Add Category
                    </button>
                    <button type="button" onclick="openTaskModal()"
                        style="background: #fef3c7; color: #92400e; padding: 0.5rem 0.85rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.8rem; border: none; cursor: pointer;">
                        <i class="fa-solid fa-file-circle-plus"></i> Add Task
                    </button>
                    <button type="button" onclick="openAttendanceModal()"
                        style="background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; padding: 0.5rem 0.85rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.8rem; cursor: pointer;">
                        <i class="fa-solid fa-calendar-plus"></i> Add Attendance Date
                    </button>
                    <form action="{{ route('junior_high_school.grades.compute.total') }}" method="POST"
                        style="margin: 0;">
                        @csrf
                        <input type="hidden" name="class_section_subject_id" value="{{ $targetSecSubId }}">
                        <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">
                        <button type="submit"
                            style="background: #10b981; color: #ffffff; padding: 0.5rem 0.9rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.8rem; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="fa-solid fa-calculator"></i> Compute & Publish Final Grades
                        </button>
                    </form>
                </div>
            </div>

            <!-- Attendance Codes Legend Bar -->
            <div
                style="display: flex; gap: 0.6rem; flex-wrap: wrap; margin-bottom: 1rem; padding: 0.65rem 1rem; background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 0.78rem; font-weight: 700; align-items: center; box-shadow: var(--shadow-sm);">
                <span style="color: #475569;"><i class="fa-solid fa-clipboard-user" style="color: #4f46e5;"></i> Attendance
                    Codes:</span>
                <span
                    style="background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; padding: 0.2rem 0.6rem; border-radius: 6px;">P
                    - Present</span>
                <span
                    style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; padding: 0.2rem 0.6rem; border-radius: 6px;">L
                    - Late</span>
                <span
                    style="background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; padding: 0.2rem 0.6rem; border-radius: 6px;">A
                    - Absent</span>
                <span
                    style="background: #f3e8ff; color: #6b21a8; border: 1px solid #d8b4fe; padding: 0.2rem 0.6rem; border-radius: 6px;">AEL
                    - Excuse Letter</span>
                <span
                    style="background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; padding: 0.2rem 0.6rem; border-radius: 6px;">E
                    - Excuse</span>
                <span
                    style="background: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3; padding: 0.2rem 0.6rem; border-radius: 6px;">C
                    - Cutting Class</span>
            </div>

            <!-- Class Record Spreadsheet Table -->
            <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: var(--shadow-sm);"
                class="table-responsive">
                @if ($enrolledStudents->isNotEmpty())
                    <table class="grading-table">
                        <thead>
                            <tr>
                                <th rowspan="2" style="min-width: 200px;">Student Name</th>
                                @foreach ($categories as $cat)
                                    <th colspan="{{ $cat->tasks->isNotEmpty() ? $cat->tasks->count() + 1 : 1 }}"
                                        style="text-align: center; background: #eef2ff; padding: 0.5rem; border-right: 2px solid #cbd5e1;">
                                        <div
                                            style="display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
                                            <span style="font-weight: 800; color: #3730a3;">{{ $cat->name }}
                                                ({{ number_format($cat->weight, 2) }}%)
                                            </span>
                                            <button type="button"
                                                onclick='openEditCategoryModal(@json($cat))'
                                                style="background: none; border: none; color: #4f46e5; cursor: pointer; font-size: 0.75rem; padding: 0;"
                                                title="Edit Category">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <form
                                                action="{{ route('junior_high_school.grades.category.destroy', $cat->id) }}"
                                                method="POST" style="display: inline;"
                                                onsubmit="return confirm('Are you sure you want to delete category \'{{ $cat->name }}\' and all its tasks?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 0.85rem; padding: 0;"
                                                    title="Delete Category">&times;</button>
                                            </form>
                                        </div>
                                    </th>
                                @endforeach
                                <th colspan="{{ $attendanceDates->count() ?: 1 }}"
                                    style="text-align: center; background: #fff7ed; color: #9a3412;">
                                    <i class="fa-solid fa-calendar-check"></i> Attendance Record
                                </th>
                                <th rowspan="2" style="text-align: center; background: #ecfdf5; min-width: 110px;">
                                    Final Qtr
                                    Grade</th>
                                <th rowspan="2" style="text-align: center; background: #ecfdf5; min-width: 90px;">
                                    Remarks
                                </th>
                            </tr>
                            <tr>
                                @foreach ($categories as $cat)
                                    @if ($cat->tasks->isNotEmpty())
                                        @foreach ($cat->tasks as $t)
                                            <th style="text-align: center; font-size: 0.775rem;">
                                                <div
                                                    style="display: flex; align-items: center; justify-content: center; gap: 0.35rem;">
                                                    <div>
                                                        <strong>{{ $t->task_name }}</strong><br>
                                                        <span style="color: #64748b; font-weight: normal;">(Max:
                                                            {{ $t->max_score }})</span>
                                                    </div>
                                                    <button type="button"
                                                        onclick='openEditTaskModal(@json($t))'
                                                        style="background: none; border: none; color: #d97706; cursor: pointer; font-size: 0.75rem; padding: 0;"
                                                        title="Edit Task">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                    <form
                                                        action="{{ route('junior_high_school.grades.task.destroy', $t->id) }}"
                                                        method="POST" style="display: inline;"
                                                        onsubmit="return confirm('Are you sure you want to delete task \'{{ $t->task_name }}\'?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 0.75rem; padding: 0;"
                                                            title="Delete Task">&times;</button>
                                                    </form>
                                                </div>
                                            </th>
                                        @endforeach
                                        <th style="text-align: center; font-size: 0.775rem; background: #e0e7ff; color: #3730a3; min-width: 80px; border-right: 2px solid #cbd5e1;">
                                            <strong>Total Score</strong><br>
                                            <span style="color: #4338ca; font-weight: normal;">(Max: {{ floatval($cat->tasks->sum('max_score')) }})</span>
                                        </th>
                                    @else
                                        <th
                                            style="text-align: center; font-size: 0.75rem; color: #94a3b8; font-style: italic; border-right: 2px solid #cbd5e1;">
                                            No Tasks</th>
                                    @endif
                                @endforeach

                                @if ($attendanceDates->isNotEmpty())
                                    @foreach ($attendanceDates as $adate)
                                        <th style="text-align: center; font-size: 0.775rem; background: #fff7ed;">
                                            <div
                                                style="display: flex; align-items: center; justify-content: center; gap: 0.3rem;">
                                                <span>{{ \Carbon\Carbon::parse($adate)->format('M d') }}</span>
                                                <form
                                                    action="{{ route('junior_high_school.grades.attendance.date.destroy') }}"
                                                    method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Delete attendance session for {{ $adate }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="class_section_subject_id"
                                                        value="{{ $targetSecSubId }}">
                                                    <input type="hidden" name="academic_period"
                                                        value="{{ $selectedPeriod }}">
                                                    <input type="hidden" name="attendance_date"
                                                        value="{{ $adate }}">
                                                    <button type="submit"
                                                        style="background: #fee2e2; border: 1px solid #fca5a5; color: #dc2626; cursor: pointer; font-size: 0.85rem; font-weight: 800; padding: 1px 5px; border-radius: 4px; line-height: 1; display: inline-flex; align-items: center; justify-content: center; margin-left: 3px;"
                                                        title="Delete Date Session">&times;</button>
                                                </form>
                                            </div>
                                        </th>
                                    @endforeach
                                @else
                                    <th
                                        style="text-align: center; font-size: 0.75rem; color: #94a3b8; font-style: italic; background: #fff7ed;">
                                        No Attendance Dates</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($enrolledStudents as $stu)
                                <tr>
                                    <td style="font-weight: 700; color: #0f172a;">
                                        {{ trim(($stu->last_name ? $stu->last_name . ', ' : '') . $stu->first_name . ' ' . $stu->middle_name . ' ' . $stu->extension_name) }}
                                    </td>

                                    @foreach ($categories as $cat)
                                        @if ($cat->tasks->isNotEmpty())
                                            @php
                                                $catStudentTotal = 0;
                                            @endphp
                                            @foreach ($cat->tasks as $t)
                                                @php
                                                    $key = $stu->id . '_' . $t->id;
                                                    $scoreVal = isset($scores[$key]) ? $scores[$key]->score : '';
                                                    if ($scoreVal !== '' && $scoreVal !== null) {
                                                        $catStudentTotal += floatval($scoreVal);
                                                    }
                                                @endphp
                                                <td style="text-align: center;">
                                                    <input type="number" step="0.01" max="{{ $t->max_score }}"
                                                        min="0" value="{{ $scoreVal }}"
                                                        data-student-id="{{ $stu->id }}"
                                                        data-task-id="{{ $t->id }}" oninput="recalculateRow(this)"
                                                        onchange="saveScore({{ $stu->id }}, {{ $t->id }}, this.value, this)"
                                                        class="score-cell" placeholder="-">
                                                </td>
                                            @endforeach
                                            <td id="cat-total-{{ $stu->id }}-{{ $cat->id }}"
                                                style="text-align: center; font-weight: 800; font-size: 0.9rem; background: #f5f3ff; color: #4338ca; border-right: 2px solid #e2e8f0;">
                                                {{ number_format($catStudentTotal, 2) }}
                                            </td>
                                        @else
                                            <td style="text-align: center; color: #cbd5e1; border-right: 2px solid #e2e8f0;">-</td>
                                        @endif
                                    @endforeach

                                    @if ($attendanceDates->isNotEmpty())
                                        @foreach ($attendanceDates as $adate)
                                            @php
                                                $formattedDate = \Carbon\Carbon::parse($adate)->format('Y-m-d');
                                                $attKey = $stu->id . '_' . $formattedDate;
                                                $rawStatus = isset($attendances[$attKey])
                                                    ? $attendances[$attKey]->status
                                                    : 'P';
                                                $statusMap = [
                                                    'present' => 'P',
                                                    'late' => 'L',
                                                    'absent' => 'A',
                                                    'excused_letter' => 'AEL',
                                                    'excused' => 'E',
                                                    'cutting' => 'C',
                                                ];
                                                $attStatus =
                                                    $statusMap[strtolower($rawStatus)] ?? strtoupper($rawStatus);
                                                if (!in_array($attStatus, ['P', 'L', 'A', 'AEL', 'E', 'C'])) {
                                                    $attStatus = 'P';
                                                }

                                                $bgMap = [
                                                    'P' => '#d1fae5',
                                                    'L' => '#fef3c7',
                                                    'A' => '#fee2e2',
                                                    'AEL' => '#f3e8ff',
                                                    'E' => '#e0e7ff',
                                                    'C' => '#ffe4e6',
                                                ];
                                                $colorMap = [
                                                    'P' => '#065f46',
                                                    'L' => '#92400e',
                                                    'A' => '#991b1b',
                                                    'AEL' => '#6b21a8',
                                                    'E' => '#3730a3',
                                                    'C' => '#9f1239',
                                                ];
                                            @endphp
                                            <td style="text-align: center; background: #fffdfb;">
                                                <select
                                                    onchange="saveAttendance({{ $stu->id }}, {{ $currentSectionSubject->id }}, '{{ $formattedDate }}', this.value, this)"
                                                    style="padding: 0.25rem 0.35rem; border-radius: 6px; font-size: 0.775rem; font-weight: 700; outline: none; border: 1px solid #cbd5e1; width: 62px; text-align: center; background: {{ $bgMap[$attStatus] }}; color: {{ $colorMap[$attStatus] }};">
                                                    <option value="P" {{ $attStatus == 'P' ? 'selected' : '' }}>P
                                                    </option>
                                                    <option value="L" {{ $attStatus == 'L' ? 'selected' : '' }}>L
                                                    </option>
                                                    <option value="A" {{ $attStatus == 'A' ? 'selected' : '' }}>A
                                                    </option>
                                                    <option value="AEL" {{ $attStatus == 'AEL' ? 'selected' : '' }}>AEL
                                                    </option>
                                                    <option value="E" {{ $attStatus == 'E' ? 'selected' : '' }}>E
                                                    </option>
                                                    <option value="C" {{ $attStatus == 'C' ? 'selected' : '' }}>C
                                                    </option>
                                                </select>
                                            </td>
                                        @endforeach
                                    @else
                                        <td style="text-align: center; color: #cbd5e1;">-</td>
                                    @endif

                                    @php
                                        $finalGradeVal = null;
                                        $computedRemarks = 'Incomplete';

                                        // Dynamic live computation based on task scores & category weights
                                        $liveQuarterGrade = 0;
                                        $hasAnyTaskScores = false;

                                        foreach ($categories as $cat) {
                                            $catTasks = $cat->tasks;
                                            $catMaxScore = $catTasks ? $catTasks->sum('max_score') : 0;
                                            $catStudentScore = 0;

                                            if ($catMaxScore > 0 && $catTasks->isNotEmpty()) {
                                                foreach ($catTasks as $ctask) {
                                                    $ckey = $stu->id . '_' . $ctask->id;
                                                    if (isset($scores[$ckey]) && $scores[$ckey]->score !== null) {
                                                        $catStudentScore += floatval($scores[$ckey]->score);
                                                        $hasAnyTaskScores = true;
                                                    }
                                                }
                                                $catPct = ($catStudentScore / $catMaxScore) * 100;
                                                $liveQuarterGrade += ($catPct * $cat->weight) / 100;
                                            }
                                        }

                                        if ($hasAnyTaskScores) {
                                            $finalGradeVal = number_format(round($liveQuarterGrade, 2), 2);
                                            $computedRemarks = round($liveQuarterGrade, 2) >= 75 ? 'Passed' : 'Failed';
                                        } elseif (isset($computedGrades[$stu->id])) {
                                            $finalGradeVal = number_format($computedGrades[$stu->id]->quarter_grade, 2);
                                            $computedRemarks = $computedGrades[$stu->id]->remarks;
                                        }
                                    @endphp
                                    <td id="final-grade-{{ $stu->id }}"
                                        style="text-align: center; font-weight: 800; font-size: 0.95rem; color: #0f172a; background: #f0fdf4;">
                                        {{ $finalGradeVal ?? 'N/A' }}
                                    </td>
                                    <td id="remarks-{{ $stu->id }}"
                                        style="text-align: center; font-weight: 700; background: #f0fdf4;">
                                        @if ($computedRemarks == 'Passed')
                                            <span style="color: #059669;">Passed</span>
                                        @elseif ($computedRemarks == 'Failed')
                                            <span style="color: #dc2626;">Failed</span>
                                        @else
                                            <span style="color: #d97706;">Incomplete</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align: center; padding: 3rem 1rem; color: #64748b;">
                        <i class="fa-solid fa-users-slash"
                            style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 0.75rem;"></i>
                        <p style="font-size: 0.9rem;">No students enrolled in this section for S.Y.
                            {{ $activeSchoolYear ? $activeSchoolYear->school_year : '' }}.</p>
                    </div>
                @endif
            </div>
        @endif
    @else
        <div
            style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 3rem; text-align: center; color: #64748b;">
            <i class="fa-solid fa-chalkboard" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
            <p style="font-size: 1rem; font-weight: 700;">No handled teaching subject selected.</p>
        </div>
    @endif

    <!-- Category Modal -->
    <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 9999;"
        id="catModal">
        <div
            style="background: #ffffff; border-radius: 1rem; width: 100%; max-width: 480px; padding: 1.5rem; box-shadow: var(--shadow-lg);">
            <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.5rem;">Add Grading Category</h3>
            <div
                style="background: #e0e7ff; color: #3730a3; padding: 0.4rem 0.75rem; border-radius: 6px; font-weight: 700; font-size: 0.8rem; margin-bottom: 1rem;">
                📌 Component: <strong>{{ $displaySubjName ?? 'Subject' }}</strong>
            </div>
            <form action="{{ route('junior_high_school.grades.category.store') }}" method="POST">
                @csrf
                <input type="hidden" name="class_section_subject_id" value="{{ $targetSecSubId ?? '' }}">
                <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">
                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Category Name (e.g., Written Work,
                        Performance Task)</label>
                    <input type="text" name="name" required
                        style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
                <div style="margin-bottom: 1.25rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Weight Percentage (%)</label>
                    <input type="number" step="0.1" name="weight" required min="1" max="100"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" onclick="$('#catModal').hide()"
                        style="padding: 0.4rem 0.8rem; border: 1px solid #cbd5e1; background: #fff; border-radius: 6px; cursor: pointer;">Cancel</button>
                    <button type="submit"
                        style="padding: 0.4rem 1rem; background: #1e1b4b; color: #fff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Task Modal -->
    <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 9999;"
        id="taskModal">
        <div
            style="background: #ffffff; border-radius: 1rem; width: 100%; max-width: 480px; padding: 1.5rem; box-shadow: var(--shadow-lg);">
            <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.5rem;">Add Assessment Task</h3>
            <div
                style="background: #fef3c7; color: #92400e; padding: 0.4rem 0.75rem; border-radius: 6px; font-weight: 700; font-size: 0.8rem; margin-bottom: 1rem;">
                📝 Component: <strong>{{ $displaySubjName ?? 'Subject' }}</strong>
            </div>
            <form action="{{ route('junior_high_school.grades.task.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Grading Category</label>
                    <select name="grading_category_id" required
                        style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->weight }}%)</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Task Name (e.g., Quiz 1, Project
                        1)</label>
                    <input type="text" name="task_name" required
                        style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
                <div style="margin-bottom: 1.25rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Max Score</label>
                    <input type="number" name="max_score" required min="1"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" onclick="$('#taskModal').hide()"
                        style="padding: 0.4rem 0.8rem; border: 1px solid #cbd5e1; background: #fff; border-radius: 6px; cursor: pointer;">Cancel</button>
                    <button type="submit"
                        style="padding: 0.4rem 1rem; background: #1e1b4b; color: #fff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Save
                        Task</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Date Modal -->
    <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 9999;"
        id="attendanceModal">
        <div
            style="background: #ffffff; border-radius: 1rem; width: 100%; max-width: 440px; padding: 1.5rem; box-shadow: var(--shadow-lg);">
            <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.5rem;">Add Attendance Date Session</h3>
            <div
                style="background: #fff7ed; color: #c2410c; padding: 0.4rem 0.75rem; border-radius: 6px; font-weight: 700; font-size: 0.8rem; margin-bottom: 1rem;">
                📅 Component: <strong>{{ $displaySubjName ?? 'Subject' }}</strong>
            </div>
            <form action="{{ route('junior_high_school.grades.attendance.date.store') }}" method="POST">
                @csrf
                <input type="hidden" name="class_section_subject_id" value="{{ $targetSecSubId ?? '' }}">
                <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">
                <div style="margin-bottom: 1.25rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Select Attendance Date</label>
                    <input type="date" name="attendance_date" required value="{{ date('Y-m-d') }}"
                        style="width: 100%; padding: 0.55rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" onclick="$('#attendanceModal').hide()"
                        style="padding: 0.4rem 0.8rem; border: 1px solid #cbd5e1; background: #fff; border-radius: 6px; cursor: pointer;">Cancel</button>
                    <button type="submit"
                        style="padding: 0.4rem 1rem; background: #1e1b4b; color: #fff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Add
                        Session</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 9999;"
        id="editCatModal">
        <div
            style="background: #ffffff; border-radius: 1rem; width: 100%; max-width: 480px; padding: 1.5rem; box-shadow: var(--shadow-lg);">
            <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Edit Grading Category</h3>
            <form id="editCatForm" method="POST">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Category Name</label>
                    <input type="text" id="edit_cat_name" name="name" required
                        style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
                <div style="margin-bottom: 1.25rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Weight Percentage (%)</label>
                    <input type="number" step="0.1" id="edit_cat_weight" name="weight" required min="1"
                        max="100"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" onclick="$('#editCatModal').hide()"
                        style="padding: 0.4rem 0.8rem; border: 1px solid #cbd5e1; background: #fff; border-radius: 6px; cursor: pointer;">Cancel</button>
                    <button type="submit"
                        style="padding: 0.4rem 1rem; background: #1e1b4b; color: #fff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Update
                        Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 9999;"
        id="editTaskModal">
        <div
            style="background: #ffffff; border-radius: 1rem; width: 100%; max-width: 480px; padding: 1.5rem; box-shadow: var(--shadow-lg);">
            <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Edit Assessment Task</h3>
            <form id="editTaskForm" method="POST">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Task Name</label>
                    <input type="text" id="edit_task_name" name="task_name" required
                        style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
                <div style="margin-bottom: 1.25rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Max Score</label>
                    <input type="number" id="edit_max_score" name="max_score" required min="1"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" onclick="$('#editTaskModal').hide()"
                        style="padding: 0.4rem 0.8rem; border: 1px solid #cbd5e1; background: #fff; border-radius: 6px; cursor: pointer;">Cancel</button>
                    <button type="submit"
                        style="padding: 0.4rem 1rem; background: #1e1b4b; color: #fff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Update
                        Task</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Close any modal when clicking outside its card content
            $(document).on('click', '#catModal, #taskModal, #attendanceModal, #editCatModal, #editTaskModal',
                function(e) {
                    if (e.target === this) {
                        $(this).hide();
                    }
                });
        });

        function openCategoryModal() {
            $('#catModal').css('display', 'flex');
        }

        function openTaskModal() {
            $('#taskModal').css('display', 'flex');
        }

        function openAttendanceModal() {
            $('#attendanceModal').css('display', 'flex');
        }

        function openEditCategoryModal(cat) {
            $('#editCatForm').attr('action', "/junior-high-school/grades/category/update/" + cat.id);
            $('#edit_cat_name').val(cat.name);
            $('#edit_cat_weight').val(cat.weight);
            $('#editCatModal').css('display', 'flex');
        }

        function openEditTaskModal(task) {
            $('#editTaskForm').attr('action', "/junior-high-school/grades/task/update/" + task.id);
            $('#edit_task_name').val(task.task_name);
            $('#edit_max_score').val(task.max_score);
            $('#editTaskModal').css('display', 'flex');
        }

        const categoriesData = @json($categoriesData);

        function recalculateRow(inputElem) {
            const tr = $(inputElem).closest('tr');
            const studentId = $(inputElem).data('student-id');

            let totalQuarterGrade = 0;
            let hasAnyScore = false;

            categoriesData.forEach(cat => {
                let catMaxScore = 0;
                let catStudentScore = 0;

                if (cat.tasks && cat.tasks.length > 0) {
                    cat.tasks.forEach(task => {
                        catMaxScore += task.max_score;
                        const scoreInput = tr.find(`input[data-task-id="${task.id}"]`);
                        if (scoreInput.length && scoreInput.val() !== '' && scoreInput.val() !== null) {
                            catStudentScore += parseFloat(scoreInput.val()) || 0;
                            hasAnyScore = true;
                        }
                    });

                    if (catMaxScore > 0) {
                        let catPct = (catStudentScore / catMaxScore) * 100;
                        totalQuarterGrade += (catPct * cat.weight) / 100;
                    }

                    const catTotalTd = $(`#cat-total-${studentId}-${cat.id}`);
                    if (catTotalTd.length) {
                        catTotalTd.text(catStudentScore.toFixed(2));
                    }
                }
            });

            const finalGradeTd = $(`#final-grade-${studentId}`);
            const remarksTd = $(`#remarks-${studentId}`);

            if (hasAnyScore) {
                let finalGrade = Math.round(totalQuarterGrade * 100) / 100;
                finalGradeTd.text(finalGrade.toFixed(2));
                if (finalGrade >= 75) {
                    remarksTd.html('<span style="color: #059669;">Passed</span>');
                } else {
                    remarksTd.html('<span style="color: #dc2626;">Failed</span>');
                }
            } else {
                finalGradeTd.text('N/A');
                remarksTd.html('<span style="color: #d97706;">Incomplete</span>');
            }
        }

        function saveScore(studentId, taskId, score, inputElem) {
            $.ajax({
                url: "{{ route('junior_high_school.grades.update_score') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    student_id: studentId,
                    grading_task_id: taskId,
                    score: score
                },
                success: function(response) {
                    $(inputElem).addClass('score-saved');
                    setTimeout(() => $(inputElem).removeClass('score-saved'), 1500);
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error updating score.';
                    if (typeof showToast === 'function') {
                        showToast('error', msg);
                    } else {
                        alert(msg);
                    }
                }
            });
        }

        function saveAttendance(studentId, sectionSubjId, date, status, selectElem) {
            $.ajax({
                url: "{{ route('junior_high_school.grades.attendance.update_status') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    student_id: studentId,
                    class_section_subject_id: sectionSubjId,
                    academic_period: "{{ $selectedPeriod }}",
                    attendance_date: date,
                    status: status
                },
                success: function(response) {
                    const styles = {
                        'P': {
                            bg: '#d1fae5',
                            color: '#065f46'
                        },
                        'L': {
                            bg: '#fef3c7',
                            color: '#92400e'
                        },
                        'A': {
                            bg: '#fee2e2',
                            color: '#991b1b'
                        },
                        'AEL': {
                            bg: '#f3e8ff',
                            color: '#6b21a8'
                        },
                        'E': {
                            bg: '#e0e7ff',
                            color: '#3730a3'
                        },
                        'C': {
                            bg: '#ffe4e6',
                            color: '#9f1239'
                        },
                    };
                    let st = styles[status] || {
                        bg: '#f1f5f9',
                        color: '#475569'
                    };
                    $(selectElem).css({
                        'background': st.bg,
                        'color': st.color
                    });
                    if (typeof showToast === 'function') {
                        showToast('success', 'Attendance updated to ' + status);
                    }
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error updating attendance status.';
                    if (typeof showToast === 'function') {
                        showToast('error', msg);
                    } else {
                        alert(msg);
                    }
                }
            });
        }
    </script>
@endpush
