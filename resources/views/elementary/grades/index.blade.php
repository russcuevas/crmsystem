@extends('layouts.elementary')

@section('title', 'GNHS-P BED - Class Record & Grading')
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
            border-color: #059669;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.2);
            background: #ecfdf5;
        }

        .score-saved {
            border-color: #10b981 !important;
            background: #d1fae5 !important;
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
            background: #064e3b;
            color: #ffffff;
            border-color: #064e3b;
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
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem;">
        <a href="{{ route('elementary.grades.page') }}"
            style="padding: 0.65rem 1.25rem; border-radius: 0.625rem; font-weight: 700; font-size: 0.875rem; text-decoration: none; color: #ffffff; background: #064e3b; box-shadow: 0 4px 12px rgba(6, 78, 59, 0.25); display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-book"></i>
            <span>My Handled Subjects (Subject Teacher)</span>
        </a>
        <a href="{{ route('elementary.grades.advisory.page') }}"
            style="padding: 0.65rem 1.25rem; border-radius: 0.625rem; font-weight: 700; font-size: 0.875rem; text-decoration: none; color: #64748b; background: transparent; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-user-shield"></i>
            <span>Advisory Class Grades (Class Adviser)</span>
        </a>
    </div>

    <!-- Header Controls: Subject Selector & Academic Quarter Tabs -->
    <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
            <!-- Select Handled Subject -->
            <form action="{{ route('elementary.grades.page') }}" method="GET" style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; margin: 0; width: 100%; max-width: 580px;">
                <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">
                <label style="font-weight: 700; font-size: 0.875rem; color: #0f172a; white-space: nowrap;">
                    <i class="fa-solid fa-book-open" style="color: #059669;"></i> Select Handled Subject:
                </label>
                <select name="class_section_subject_id" onchange="this.form.submit()" style="padding: 0.55rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 700; font-size: 0.875rem; outline: none; background: #ffffff; flex: 1; min-width: 220px; max-width: 100%;">
                    @if ($sectionSubjects->isEmpty())
                        <option value="">No handled subjects assigned</option>
                    @else
                        @foreach ($sectionSubjects as $ss)
                            <option value="{{ $ss->id }}" {{ $targetSecSubId == $ss->id ? 'selected' : '' }}>
                                {{ $ss->subject ? ($ss->subject->subject_name ?? $ss->subject->name) : 'N/A' }} -
                                {{ $ss->classSection ? $ss->classSection->section_name : 'N/A' }}
                                ({{ $ss->classSection && $ss->classSection->gradeLevel ? $ss->classSection->gradeLevel->name : 'N/A' }})
                            </option>
                        @endforeach
                    @endif
                </select>
            </form>

            <!-- Academic Period Tabs (1st Qtr to 4th Qtr) -->
            <div style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
                @foreach (['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'] as $qtr)
                    <a href="{{ route('elementary.grades.page', ['class_section_subject_id' => $targetSecSubId, 'academic_period' => $qtr]) }}"
                        class="period-tab {{ $selectedPeriod == $qtr ? 'active' : '' }}">
                        {{ $qtr }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @if ($targetSectionSubject)
        <!-- Action Toolbar for Grading Categories, Tasks, Attendance & Computations -->
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">
                    {{ $targetSectionSubject->subject ? ($targetSectionSubject->subject->subject_name ?? $targetSectionSubject->subject->name) : 'Subject' }}
                    <span style="color: #64748b; font-weight: 600; font-size: 0.95rem; margin-left: 0.35rem;">
                        - {{ $targetSectionSubject->classSection ? $targetSectionSubject->classSection->section_name : 'N/A' }}
                    </span>
                </h3>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                <button type="button" onclick="openCategoryModal()" style="background: #e0e7ff; color: #3730a3; padding: 0.5rem 0.85rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.8rem; border: none; cursor: pointer;">
                    <i class="fa-solid fa-plus"></i> Add Category
                </button>
                <button type="button" onclick="openTaskModal()" style="background: #fef3c7; color: #92400e; padding: 0.5rem 0.85rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.8rem; border: none; cursor: pointer;">
                    <i class="fa-solid fa-file-circle-plus"></i> Add Task
                </button>
                <button type="button" onclick="openAttendanceModal()" style="background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; padding: 0.5rem 0.85rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.8rem; cursor: pointer;">
                    <i class="fa-solid fa-calendar-plus"></i> Add Attendance Date
                </button>
                <form action="{{ route('elementary.grades.compute') }}" method="POST" style="margin: 0;">
                    @csrf
                    <input type="hidden" name="class_section_subject_id" value="{{ $targetSecSubId }}">
                    <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">
                    <button type="submit" style="background: #10b981; color: #ffffff; padding: 0.5rem 0.9rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.8rem; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="fa-solid fa-calculator"></i> Compute & Publish Final Grades
                    </button>
                </form>
            </div>
        </div>

        <!-- Attendance Codes Legend Bar -->
        <div style="display: flex; gap: 0.6rem; flex-wrap: wrap; margin-bottom: 1rem; padding: 0.65rem 1rem; background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 0.78rem; font-weight: 700; align-items: center; box-shadow: var(--shadow-sm);">
            <span style="color: #475569;"><i class="fa-solid fa-clipboard-user" style="color: #059669;"></i> Attendance Codes:</span>
            <span style="background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; padding: 0.2rem 0.6rem; border-radius: 6px;">P - Present</span>
            <span style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; padding: 0.2rem 0.6rem; border-radius: 6px;">L - Late</span>
            <span style="background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; padding: 0.2rem 0.6rem; border-radius: 6px;">A - Absent</span>
            <span style="background: #f3e8ff; color: #6b21a8; border: 1px solid #d8b4fe; padding: 0.2rem 0.6rem; border-radius: 6px;">AEL - Excuse Letter</span>
            <span style="background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; padding: 0.2rem 0.6rem; border-radius: 6px;">E - Excuse</span>
            <span style="background: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3; padding: 0.2rem 0.6rem; border-radius: 6px;">C - Cutting Class</span>
        </div>

        <!-- Class Record Spreadsheet Table -->
        <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: var(--shadow-sm);" class="table-responsive">
            @if ($enrolledStudents->isNotEmpty())
                <table class="grading-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="min-width: 200px;">Student Name</th>
                            @foreach ($categories as $cat)
                                <th colspan="{{ $cat->gradingTasks->isNotEmpty() ? $cat->gradingTasks->count() + 1 : 1 }}" style="text-align: center; background: #eef2ff; padding: 0.5rem; border-right: 2px solid #cbd5e1;">
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
                                        <span style="font-weight: 800; color: #3730a3;">{{ $cat->category_name ?? $cat->name }} ({{ number_format($cat->weight, 2) }}%)</span>
                                        <button type="button" onclick='openEditCategoryModal(@json($cat))' style="background: none; border: none; color: #4f46e5; cursor: pointer; font-size: 0.75rem; padding: 0;" title="Edit Category">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('elementary.grades.category.destroy', $cat->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete category {{ addslashes($cat->category_name ?? $cat->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 0.85rem; padding: 0;" title="Delete Category">&times;</button>
                                        </form>
                                    </div>
                                </th>
                            @endforeach
                            <th colspan="{{ $attendanceDates->count() ?: 1 }}" style="text-align: center; background: #fff7ed; color: #9a3412;">
                                <i class="fa-solid fa-calendar-check"></i> Attendance Record
                            </th>
                            <th rowspan="2" style="text-align: center; background: #ecfdf5; min-width: 110px;">Final Qtr Grade</th>
                            <th rowspan="2" style="text-align: center; background: #ecfdf5; min-width: 90px;">Remarks</th>
                        </tr>
                        <tr>
                            @foreach ($categories as $cat)
                                @if ($cat->gradingTasks->isNotEmpty())
                                    @foreach ($cat->gradingTasks as $t)
                                        <th style="text-align: center; font-size: 0.775rem;">
                                            <div style="display: flex; align-items: center; justify-content: center; gap: 0.35rem;">
                                                <div>
                                                    <strong>{{ $t->task_name }}</strong><br>
                                                    <span style="color: #64748b; font-weight: normal;">(Max: {{ floatval($t->max_score) }})</span>
                                                </div>
                                                <button type="button" onclick='openEditTaskModal(@json($t))' style="background: none; border: none; color: #d97706; cursor: pointer; font-size: 0.75rem; padding: 0;" title="Edit Task">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <form action="{{ route('elementary.grades.task.destroy', $t->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete task {{ addslashes($t->task_name) }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 0.75rem; padding: 0;" title="Delete Task">&times;</button>
                                                </form>
                                            </div>
                                        </th>
                                    @endforeach
                                    <th style="text-align: center; font-size: 0.775rem; background: #e0e7ff; color: #3730a3; min-width: 80px; border-right: 2px solid #cbd5e1;">
                                        <strong>Total Score</strong><br>
                                        <span style="color: #4338ca; font-weight: normal;">(Max: {{ floatval($cat->gradingTasks->sum('max_score')) }})</span>
                                    </th>
                                @else
                                    <th style="text-align: center; font-size: 0.75rem; color: #94a3b8; font-style: italic; border-right: 2px solid #cbd5e1;">No Tasks</th>
                                @endif
                            @endforeach

                            @if ($attendanceDates->isNotEmpty())
                                @foreach ($attendanceDates as $adate)
                                    <th style="text-align: center; font-size: 0.775rem; background: #fff7ed;">
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.3rem;">
                                            <span>{{ \Carbon\Carbon::parse($adate)->format('M d') }}</span>
                                            <form action="{{ route('elementary.grades.attendance.date.destroy') }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete attendance session for {{ $adate }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="class_section_subject_id" value="{{ $targetSecSubId }}">
                                                <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">
                                                <input type="hidden" name="attendance_date" value="{{ $adate }}">
                                                <button type="submit" style="background: #fee2e2; border: 1px solid #fca5a5; color: #dc2626; cursor: pointer; font-size: 0.85rem; font-weight: 800; padding: 1px 5px; border-radius: 4px; line-height: 1;" title="Delete Date Session">&times;</button>
                                            </form>
                                        </div>
                                    </th>
                                @endforeach
                            @else
                                <th style="text-align: center; font-size: 0.75rem; color: #94a3b8; font-style: italic; background: #fff7ed;">No Attendance Dates</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($enrolledStudents as $stu)
                            <tr>
                                <td style="font-weight: 700; color: #0f172a;">
                                    {{ trim(($stu->last_name ? $stu->last_name . ', ' : '') . $stu->first_name . ($stu->middle_name ? ' ' . $stu->middle_name : '') . ($stu->extension_name ? ' ' . $stu->extension_name : '')) }}
                                </td>

                                @foreach ($categories as $cat)
                                    @if ($cat->gradingTasks->isNotEmpty())
                                        @php $catStudentTotal = 0; @endphp
                                        @foreach ($cat->gradingTasks as $t)
                                            @php
                                                $key = $stu->id . '_' . $t->id;
                                                $scoreVal = isset($scores[$key]) ? $scores[$key]->score : '';
                                                if ($scoreVal !== '' && $scoreVal !== null) {
                                                    $catStudentTotal += floatval($scoreVal);
                                                }
                                            @endphp
                                            <td style="text-align: center;">
                                                <input type="number" step="0.01" max="{{ $t->max_score }}" min="0" value="{{ $scoreVal }}" data-student-id="{{ $stu->id }}" data-category-id="{{ $cat->id }}" data-task-id="{{ $t->id }}" oninput="recalculateRow(this)" onchange="saveScore({{ $stu->id }}, {{ $t->id }}, this.value, this)" class="score-cell" placeholder="-">
                                            </td>
                                        @endforeach
                                        <td id="cat-total-{{ $stu->id }}-{{ $cat->id }}" style="text-align: center; font-weight: 800; font-size: 0.9rem; background: #f5f3ff; color: #4338ca; border-right: 2px solid #e2e8f0;">
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
                                            $rawStatus = isset($attendances[$attKey]) ? $attendances[$attKey]->status : 'P';
                                            $statusMap = [
                                                'present' => 'P',
                                                'late' => 'L',
                                                'absent' => 'A',
                                                'excused_letter' => 'AEL',
                                                'excused' => 'E',
                                                'cutting' => 'C',
                                            ];
                                            $attStatus = $statusMap[strtolower($rawStatus)] ?? strtoupper($rawStatus);
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
                                            <select onchange="saveAttendance({{ $stu->id }}, {{ $targetSecSubId }}, '{{ $formattedDate }}', this.value, this)"
                                                style="padding: 0.25rem 0.35rem; border-radius: 6px; font-size: 0.775rem; font-weight: 700; outline: none; border: 1px solid #cbd5e1; width: 62px; text-align: center; background: {{ $bgMap[$attStatus] }}; color: {{ $colorMap[$attStatus] }};">
                                                <option value="P" {{ $attStatus == 'P' ? 'selected' : '' }}>P</option>
                                                <option value="L" {{ $attStatus == 'L' ? 'selected' : '' }}>L</option>
                                                <option value="A" {{ $attStatus == 'A' ? 'selected' : '' }}>A</option>
                                                <option value="AEL" {{ $attStatus == 'AEL' ? 'selected' : '' }}>AEL</option>
                                                <option value="E" {{ $attStatus == 'E' ? 'selected' : '' }}>E</option>
                                                <option value="C" {{ $attStatus == 'C' ? 'selected' : '' }}>C</option>
                                            </select>
                                        </td>
                                    @endforeach
                                @else
                                    <td style="text-align: center; color: #cbd5e1;">-</td>
                                @endif

                                @php
                                    $savedGrade = isset($computedGrades[$stu->id]) ? $computedGrades[$stu->id] : null;
                                    $finalGradeVal = $savedGrade && !is_null($savedGrade->final_grade) ? number_format($savedGrade->final_grade, 2) : 'N/A';
                                    $computedRemarks = $savedGrade ? $savedGrade->remarks : 'Incomplete';
                                @endphp

                                <td id="final-grade-{{ $stu->id }}" style="text-align: center; font-weight: 800; font-size: 0.95rem; color: #0f172a; background: #f0fdf4;">
                                    {{ $finalGradeVal }}
                                </td>
                                <td id="remarks-{{ $stu->id }}" style="text-align: center; font-weight: 700; background: #f0fdf4;">
                                    @if (strtoupper($computedRemarks) == 'PASSED' || $computedRemarks == 'Passed')
                                        <span style="color: #059669;">Passed</span>
                                    @elseif (strtoupper($computedRemarks) == 'FAILED' || $computedRemarks == 'Failed')
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
                <div style="text-align: center; padding: 3rem; color: #94a3b8;">
                    <i class="fa-solid fa-users-slash" style="font-size: 2.5rem; margin-bottom: 0.75rem;"></i>
                    <p style="font-size: 0.9rem;">No students enrolled in this section for S.Y. {{ $activeSchoolYear ? $activeSchoolYear->school_year : '' }}.</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Modals -->
    <!-- Add Category Modal -->
    <div id="addCatModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 200; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 1rem; width: 100%; max-width: 420px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Add Grading Category</h3>
            <form action="{{ route('elementary.grades.category.store') }}" method="POST">
                @csrf
                <input type="hidden" name="class_section_subject_id" value="{{ $targetSecSubId }}">
                <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">Category Name</label>
                    <input type="text" name="name" required placeholder="e.g. Written Work, Performance Task" style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">Weight (%)</label>
                    <input type="number" step="0.01" name="weight" required placeholder="e.g. 40" style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="$('#addCatModal').css('display', 'none')" style="background: #f1f5f9; color: #475569; border: none; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Cancel</button>
                    <button type="submit" style="background: #059669; color: #ffffff; border: none; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Save Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCatModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 200; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 1rem; width: 100%; max-width: 420px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Edit Category</h3>
            <form id="editCatForm" method="POST">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">Category Name</label>
                    <input type="text" id="edit_cat_name" name="name" required style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">Weight (%)</label>
                    <input type="number" step="0.01" id="edit_cat_weight" name="weight" required style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="$('#editCatModal').css('display', 'none')" style="background: #f1f5f9; color: #475569; border: none; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Cancel</button>
                    <button type="submit" style="background: #4f46e5; color: #ffffff; border: none; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Update Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div id="addTaskModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 200; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 1rem; width: 100%; max-width: 420px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Add Grading Task</h3>
            <form action="{{ route('elementary.grades.task.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">Category</label>
                    <select name="grading_category_id" required style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->category_name ?? $cat->name }} ({{ number_format($cat->weight, 2) }}%)</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">Task Name</label>
                    <input type="text" name="task_name" required placeholder="e.g. Quiz 1, Project 1" style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">Max Score</label>
                    <input type="number" step="0.01" name="max_score" required placeholder="e.g. 50" style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="$('#addTaskModal').css('display', 'none')" style="background: #f1f5f9; color: #475569; border: none; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Cancel</button>
                    <button type="submit" style="background: #059669; color: #ffffff; border: none; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Save Task</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div id="editTaskModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 200; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 1rem; width: 100%; max-width: 420px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Edit Task</h3>
            <form id="editTaskForm" method="POST">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">Task Name</label>
                    <input type="text" id="edit_task_name" name="task_name" required style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">Max Score</label>
                    <input type="number" step="0.01" id="edit_max_score" name="max_score" required style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="$('#editTaskModal').css('display', 'none')" style="background: #f1f5f9; color: #475569; border: none; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Cancel</button>
                    <button type="submit" style="background: #d97706; color: #ffffff; border: none; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Update Task</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Attendance Modal -->
    <div id="addAttendanceModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 200; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 1rem; width: 100%; max-width: 420px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1rem;">Add Attendance Session</h3>
            <form action="{{ route('elementary.grades.attendance.date.store') }}" method="POST">
                @csrf
                <input type="hidden" name="class_section_subject_id" value="{{ $targetSecSubId }}">
                <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">Attendance Date</label>
                    <input type="date" name="attendance_date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="$('#addAttendanceModal').css('display', 'none')" style="background: #f1f5f9; color: #475569; border: none; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Cancel</button>
                    <button type="submit" style="background: #059669; color: #ffffff; border: none; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Add Session</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openCategoryModal() {
            $('#addCatModal').css('display', 'flex');
        }

        function openTaskModal() {
            $('#addTaskModal').css('display', 'flex');
        }

        function openAttendanceModal() {
            $('#addAttendanceModal').css('display', 'flex');
        }

        function openEditCategoryModal(cat) {
            $('#editCatForm').attr('action', "/elementary/grades/category/update/" + cat.id);
            $('#edit_cat_name').val(cat.category_name || cat.name);
            $('#edit_cat_weight').val(cat.weight);
            $('#editCatModal').css('display', 'flex');
        }

        function openEditTaskModal(task) {
            $('#editTaskForm').attr('action', "/elementary/grades/task/update/" + task.id);
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
                        catMaxScore += parseFloat(task.max_score) || 0;
                        const scoreInput = tr.find(`input[data-task-id="${task.id}"]`);
                        if (scoreInput.length && scoreInput.val() !== '' && scoreInput.val() !== null) {
                            catStudentScore += parseFloat(scoreInput.val()) || 0;
                            hasAnyScore = true;
                        }
                    });

                    if (catMaxScore > 0) {
                        let catPct = (catStudentScore / catMaxScore) * 100;
                        totalQuarterGrade += (catPct * parseFloat(cat.weight)) / 100;
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
                url: "{{ route('elementary.grades.update_score') }}",
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
                    if (typeof showToast === 'function') {
                        showToast('success', 'Score updated successfully!');
                    }
                },
                error: function(xhr) {
                    if (typeof showToast === 'function') {
                        showToast('error', 'Error updating score.');
                    }
                }
            });
        }

        function saveAttendance(studentId, classSecSubId, dateStr, status, selectElem) {
            $.ajax({
                url: "{{ route('elementary.grades.attendance.status.update') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    student_id: studentId,
                    class_section_subject_id: classSecSubId,
                    academic_period: "{{ $selectedPeriod }}",
                    attendance_date: dateStr,
                    status: status
                },
                success: function(response) {
                    const styles = {
                        'P': { bg: '#d1fae5', color: '#065f46' },
                        'L': { bg: '#fef3c7', color: '#92400e' },
                        'A': { bg: '#fee2e2', color: '#991b1b' },
                        'AEL': { bg: '#f3e8ff', color: '#6b21a8' },
                        'E': { bg: '#e0e7ff', color: '#3730a3' },
                        'C': { bg: '#ffe4e6', color: '#9f1239' },
                    };
                    let st = styles[status] || { bg: '#f1f5f9', color: '#475569' };
                    $(selectElem).css({
                        'background': st.bg,
                        'color': st.color
                    });
                    if (typeof showToast === 'function') {
                        showToast('success', 'Attendance updated to ' + status);
                    }
                },
                error: function(xhr) {
                    if (typeof showToast === 'function') {
                        showToast('error', 'Failed to update attendance status.');
                    }
                }
            });
        }
    </script>
@endpush
