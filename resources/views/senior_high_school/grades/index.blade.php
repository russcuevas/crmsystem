@extends('layouts.senior_high_school')

@section('title', 'GNHS - SHS Class Record & Grading')
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
            border-color: #7f1d1d;
            box-shadow: 0 0 0 3px rgba(127, 29, 29, 0.2);
            background: #fff1f2;
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
            background: #7f1d1d;
            color: #ffffff;
            border-color: #7f1d1d;
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

        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: all 0.25s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-box {
            background: #ffffff;
            border-radius: 1rem;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            background: #7f1d1d;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-body {
            padding: 1.5rem;
        }
    </style>
@endpush

@section('content')
    <!-- Navigation Tabs -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem;">
        <a href="{{ route('senior_high_school.grades.page') }}"
            style="padding: 0.65rem 1.25rem; border-radius: 0.625rem; font-weight: 700; font-size: 0.875rem; text-decoration: none; color: #ffffff; background: #7f1d1d; box-shadow: 0 4px 12px rgba(127, 29, 29, 0.25); display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-book"></i>
            <span>My Handled Subjects (Subject Teacher)</span>
        </a>
        <a href="{{ route('senior_high_school.grades.advisory.page') }}"
            style="padding: 0.65rem 1.25rem; border-radius: 0.625rem; font-weight: 700; font-size: 0.875rem; text-decoration: none; color: #64748b; background: transparent; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-user-shield"></i>
            <span>Advisory Class Grades (Class Adviser)</span>
            @if ($advisorySections->isNotEmpty())
                <span style="background: #f59e0b; color: #450a0a; padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 800;">Adviser</span>
            @endif
        </a>
    </div>

    <!-- Controls: Semester & Subject Selector & Academic Periods (Prelim, Midterm, Finals) -->
    <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.25rem; box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
            
            <form action="{{ route('senior_high_school.grades.page') }}" method="GET" style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; margin: 0; width: 100%; max-width: 680px;">
                <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">
                
                <!-- Semester Filter -->
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                    <label style="font-weight: 700; font-size: 0.875rem; color: #0f172a; white-space: nowrap;">
                        <i class="fa-solid fa-calendar-days" style="color: #7f1d1d;"></i> Semester:
                    </label>
                    <select name="semester" onchange="this.form.submit()" style="padding: 0.6rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 700; color: #7f1d1d; background: #fff1f2; outline: none; cursor: pointer;">
                        <option value="1st Semester" {{ $selectedSemester == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                        <option value="2nd Semester" {{ $selectedSemester == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                    </select>
                </div>

                <!-- Subject Selector -->
                <div style="display: flex; align-items: center; gap: 0.4rem; flex: 1;">
                    <label style="font-weight: 700; font-size: 0.875rem; color: #0f172a; white-space: nowrap;">
                        <i class="fa-solid fa-book-bookmark" style="color: #7f1d1d;"></i> Subject:
                    </label>
                    <select name="section_subject_id" onchange="this.form.submit()" style="width: 100%; padding: 0.6rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; outline: none; background: #ffffff;">
                        @forelse ($handledSubjects as $hs)
                            <option value="{{ $hs->id }}" {{ $currentSectionSubject && $currentSectionSubject->id == $hs->id ? 'selected' : '' }}>
                                {{ $hs->subject ? ($hs->subject->subject_name ?? $hs->subject->name) : 'N/A' }} - Section: {{ $hs->classSection ? $hs->classSection->section_name : 'N/A' }} ({{ $hs->classSection && $hs->classSection->gradeLevel ? $hs->classSection->gradeLevel->name : '' }} {{ $hs->classSection && $hs->classSection->course ? '['.$hs->classSection->course->course_code.']' : '' }})
                            </option>
                        @empty
                            <option value="">No handled subjects for {{ $selectedSemester }}</option>
                        @endforelse
                    </select>
                </div>
            </form>

            <!-- Academic Period Tabs (Prelim, Midterm, Finals) -->
            <div style="display: flex; gap: 0.4rem;">
                @foreach ($availablePeriods as $period)
                    <a href="{{ route('senior_high_school.grades.page', ['semester' => $selectedSemester, 'section_subject_id' => $currentSectionSubject ? $currentSectionSubject->id : '', 'academic_period' => $period]) }}"
                        class="period-tab {{ $selectedPeriod == $period ? 'active' : '' }}">
                        {{ $period }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @if ($currentSectionSubject)
        <!-- Subject Overview Header -->
        <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-sm); margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a;">
                    {{ $currentSectionSubject->subject ? ($currentSectionSubject->subject->subject_name ?? $currentSectionSubject->subject->name) : 'N/A' }}
                </h3>
                <p style="font-size: 0.825rem; color: #64748b; margin-top: 0.2rem;">
                    Section: <strong>{{ $currentSectionSubject->classSection ? $currentSectionSubject->classSection->section_name : 'N/A' }}</strong>
                    | Grade: <strong>{{ $currentSectionSubject->classSection && $currentSectionSubject->classSection->gradeLevel ? $currentSectionSubject->classSection->gradeLevel->name : 'N/A' }}</strong>
                    @if ($currentSectionSubject->classSection && $currentSectionSubject->classSection->course)
                        | Strand: <strong>{{ $currentSectionSubject->classSection->course->course_code }}</strong>
                    @endif
                    | Semester: <strong style="color: #7f1d1d;">{{ $selectedSemester }}</strong>
                    | Period: <strong style="color: #f59e0b;">{{ $selectedPeriod }}</strong>
                    | Active S.Y. <strong>{{ $activeSchoolYear ? $activeSchoolYear->school_year : 'N/A' }}</strong>
                </p>
            </div>
            <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
                <button type="button" onclick="openCategoryModal()" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #0f172a; padding: 0.5rem 0.85rem; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 700; cursor: pointer;">
                    <i class="fa-solid fa-folder-plus" style="color: #7f1d1d;"></i> Add Category
                </button>
                <button type="button" onclick="openTaskModal()" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #0f172a; padding: 0.5rem 0.85rem; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 700; cursor: pointer;">
                    <i class="fa-solid fa-file-circle-plus" style="color: #7f1d1d;"></i> Add Task Column
                </button>
                <button type="button" onclick="openAttendanceModal()" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #0f172a; padding: 0.5rem 0.85rem; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 700; cursor: pointer;">
                    <i class="fa-solid fa-calendar-plus" style="color: #7f1d1d;"></i> Add Attendance Date
                </button>
            </div>
        </div>

        <!-- Class Record Table Card -->
        <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: var(--shadow-sm);">
            <form action="{{ route('senior_high_school.grades.compute.total') }}" method="POST">
                @csrf
                <input type="hidden" name="class_section_subject_id" value="{{ $currentSectionSubject->id }}">
                <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">

                <div class="table-responsive">
                    <table class="grading-table">
                        <thead>
                            <tr>
                                <th style="width: 220px; text-align: left; position: sticky; left: 0; background: #f8fafc; z-index: 5;">Student Name</th>
                                @foreach ($categories as $cat)
                                    @php $cTasks = $cat->gradingTasks; @endphp
                                    <th colspan="{{ $cTasks->count() > 0 ? $cTasks->count() : 1 }}" style="text-align: center; background: #fee2e2; color: #991b1b; border-bottom: 2px solid #fca5a5;">
                                        {{ $cat->category_name }} ({{ floatval($cat->weight) }}%)
                                    </th>
                                @endforeach
                                <th style="text-align: center; background: #e0e7ff; color: #3730a3;">{{ $selectedPeriod }} Grade</th>
                                <th style="text-align: center; background: #e0e7ff; color: #3730a3;">Remarks</th>
                            </tr>
                            <tr>
                                <th style="position: sticky; left: 0; background: #f8fafc; z-index: 5;">Max Score &rarr;</th>
                                @foreach ($categories as $cat)
                                    @forelse ($cat->gradingTasks as $t)
                                        <th style="text-align: center; font-size: 0.775rem;">
                                            <div>{{ $t->task_name }}</div>
                                            <span style="color: #64748b;">({{ floatval($t->max_score) }} pts)</span>
                                        </th>
                                    @empty
                                        <th style="text-align: center; color: #94a3b8; font-size: 0.75rem;">No Tasks</th>
                                    @endforelse
                                @endforeach
                                <th style="text-align: center;">Final</th>
                                <th style="text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($enrolledStudents as $student)
                                @php
                                    $computed = $computedGrades->get($student->id);
                                    $finalVal = $computed ? $computed->final_quarter_grade : '';
                                    $remarksVal = $computed ? $computed->remarks : '';
                                @endphp
                                <tr>
                                    <td style="position: sticky; left: 0; background: #ffffff; z-index: 5; font-weight: 700;">
                                        {{ $student->last_name }}, {{ $student->first_name }}
                                    </td>
                                    @foreach ($categories as $cat)
                                        @forelse ($cat->gradingTasks as $t)
                                            @php
                                                $key = $student->id . '_' . $t->id;
                                                $stScore = $scores->get($key);
                                                $scoreVal = $stScore ? $stScore->score : '';
                                            @endphp
                                            <td style="text-align: center;">
                                                <input type="number" step="0.01" min="0" max="{{ $t->max_score }}"
                                                    class="score-cell"
                                                    data-student-id="{{ $student->id }}"
                                                    data-task-id="{{ $t->id }}"
                                                    value="{{ $scoreVal }}"
                                                    onchange="saveScore(this)">
                                            </td>
                                        @empty
                                            <td style="text-align: center; color: #cbd5e1;">-</td>
                                        @endforelse
                                    @endforeach
                                    <td style="text-align: center;">
                                        <input type="number" step="0.01" name="grades[{{ $student->id }}]" value="{{ $finalVal }}"
                                            style="width: 75px; text-align: center; font-weight: 800; padding: 0.35rem; border: 1.5px solid #cbd5e1; border-radius: 6px;">
                                    </td>
                                    <td style="text-align: center;">
                                        @if ($remarksVal === 'PASSED')
                                            <span style="background: #d1fae5; color: #065f46; padding: 0.2rem 0.55rem; border-radius: 4px; font-weight: 800; font-size: 0.75rem;">PASSED</span>
                                        @elseif ($remarksVal === 'FAILED')
                                            <span style="background: #fee2e2; color: #991b1b; padding: 0.2rem 0.55rem; border-radius: 4px; font-weight: 800; font-size: 0.75rem;">FAILED</span>
                                        @else
                                            <span style="color: #94a3b8; font-size: 0.75rem;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" style="text-align: center; padding: 2rem; color: #64748b;">
                                        No enrolled students in this section for the active school year.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                    <button type="submit" style="background: #7f1d1d; color: #ffffff; padding: 0.65rem 1.5rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.875rem; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 10px rgba(127, 29, 29, 0.25);">
                        <i class="fa-solid fa-floppy-disk"></i> Save {{ $selectedPeriod }} Grades
                    </button>
                </div>
            </form>
        </div>

        <!-- Modal: Category Creation -->
        <div id="categoryModal" class="modal-overlay">
            <div class="modal-box">
                <div class="modal-header">
                    <h3 style="font-size: 1.05rem; font-weight: 800;">Add Grading Category ({{ $selectedPeriod }})</h3>
                    <button type="button" onclick="closeModal('categoryModal')" style="background: transparent; border: none; color: #ffffff; font-size: 1.2rem; cursor: pointer;">&times;</button>
                </div>
                <form action="{{ route('senior_high_school.grades.category.store') }}" method="POST" class="modal-body">
                    @csrf
                    <input type="hidden" name="class_section_subject_id" value="{{ $currentSectionSubject->id }}">
                    <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">
                    <div style="margin-bottom: 1rem;">
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Category Name *</label>
                        <input type="text" name="category_name" placeholder="e.g. Written Work, Performance Task, Exam" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Weight (%) *</label>
                        <input type="number" step="0.01" name="weight" placeholder="e.g. 40" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button type="button" onclick="closeModal('categoryModal')" style="padding: 0.5rem 1rem; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 6px; cursor: pointer;">Cancel</button>
                        <button type="submit" style="padding: 0.5rem 1.25rem; background: #7f1d1d; color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Add Category</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Task Creation -->
        <div id="taskModal" class="modal-overlay">
            <div class="modal-box">
                <div class="modal-header">
                    <h3 style="font-size: 1.05rem; font-weight: 800;">Add Grading Task Column ({{ $selectedPeriod }})</h3>
                    <button type="button" onclick="closeModal('taskModal')" style="background: transparent; border: none; color: #ffffff; font-size: 1.2rem; cursor: pointer;">&times;</button>
                </div>
                <form action="{{ route('senior_high_school.grades.task.store') }}" method="POST" class="modal-body">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Select Category *</label>
                        <select name="grading_category_id" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->category_name }} ({{ floatval($cat->weight) }}%)</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Task Name *</label>
                        <input type="text" name="task_name" placeholder="e.g. Quiz 1, Prelim Exam" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Max Score (Points) *</label>
                        <input type="number" step="0.01" name="max_score" placeholder="e.g. 50" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button type="button" onclick="closeModal('taskModal')" style="padding: 0.5rem 1rem; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 6px; cursor: pointer;">Cancel</button>
                        <button type="submit" style="padding: 0.5rem 1.25rem; background: #7f1d1d; color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Add Task Column</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Attendance Date Creation -->
        <div id="attendanceModal" class="modal-overlay">
            <div class="modal-box">
                <div class="modal-header">
                    <h3 style="font-size: 1.05rem; font-weight: 800;">Add Attendance Date Column</h3>
                    <button type="button" onclick="closeModal('attendanceModal')" style="background: transparent; border: none; color: #ffffff; font-size: 1.2rem; cursor: pointer;">&times;</button>
                </div>
                <form action="{{ route('senior_high_school.grades.attendance.date.store') }}" method="POST" class="modal-body">
                    @csrf
                    <input type="hidden" name="class_section_subject_id" value="{{ $currentSectionSubject->id }}">
                    <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">
                    <div style="margin-bottom: 1.5rem;">
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Attendance Date *</label>
                        <input type="date" name="attendance_date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button type="button" onclick="closeModal('attendanceModal')" style="padding: 0.5rem 1rem; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 6px; cursor: pointer;">Cancel</button>
                        <button type="submit" style="padding: 0.5rem 1.25rem; background: #7f1d1d; color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Add Date</button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 3rem; text-align: center; color: #64748b;">
            <i class="fa-solid fa-folder-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
            <h3>No Handled Subjects Found</h3>
            <p style="font-size: 0.875rem; margin-top: 0.25rem;">No subjects found for {{ $selectedSemester }} in Senior High School.</p>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        function openCategoryModal() { document.getElementById('categoryModal').classList.add('active'); }
        function openTaskModal() { document.getElementById('taskModal').classList.add('active'); }
        function openAttendanceModal() { document.getElementById('attendanceModal').classList.add('active'); }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }

        function saveScore(inputElem) {
            const studentId = inputElem.dataset.studentId;
            const taskId = inputElem.dataset.taskId;
            const scoreVal = inputElem.value;

            fetch("{{ route('senior_high_school.grades.update_score') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    student_id: studentId,
                    grading_task_id: taskId,
                    score: scoreVal
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    inputElem.classList.add('score-saved');
                    setTimeout(() => inputElem.classList.remove('score-saved'), 1500);
                } else {
                    Swal.fire({ icon: 'error', title: 'Invalid Score', text: data.message });
                }
            })
            .catch(err => {
                console.error(err);
            });
        }
    </script>
@endpush
