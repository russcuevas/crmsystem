@extends('layouts.college')

@section('title', 'College Class Record & Grades')
@section('header_title', 'College Class Record & Grading System')

@push('styles')
<style>
    .class-record-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
        margin-bottom: 1.5rem;
    }
    .period-pill {
        padding: 0.5rem 1.25rem;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.85rem;
        text-decoration: none;
        color: var(--text-muted);
        background: #f1f5f9;
        transition: all 0.2s ease;
    }
    .period-pill.active {
        background: #0f172a;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.25);
    }
    .score-input {
        width: 60px;
        padding: 0.35rem 0.5rem;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        text-align: center;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    .score-input:focus {
        border-color: #38bdf8;
        outline: none;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
    }
    .score-saved {
        background-color: #dcfce7 !important;
        border-color: #22c55e !important;
    }
    .badge-status {
        padding: 0.2rem 0.55rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 800;
    }
    .badge-p { background: #dcfce7; color: #15803d; }
    .badge-l { background: #fef3c7; color: #b45309; }
    .badge-a { background: #fee2e2; color: #b91c1c; }
    .badge-ael { background: #e0e7ff; color: #4338ca; }
    .badge-e { background: #f3e8ff; color: #6b21a8; }
    .badge-c { background: #ffedd5; color: #c2410c; }

    /* Enhanced Animated Modal Styles */
    .crm-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.25s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .crm-modal-overlay.active {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }
    .crm-modal-card {
        background: #ffffff;
        width: 100%;
        max-width: 460px;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
        transform: scale(0.92) translateY(16px);
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .crm-modal-overlay.active .crm-modal-card {
        transform: scale(1) translateY(0);
    }

    .category-card-item {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 0.85rem 1rem;
        font-size: 0.85rem;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        min-width: 290px;
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
    }
    .category-card-item:hover {
        border-color: #94a3b8;
        box-shadow: var(--shadow-md);
    }
</style>
@endpush

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Subject Selection Bar -->
    <div class="class-record-card" style="padding: 1.25rem 1.5rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span style="font-size: 0.75rem; font-weight: 800; color: #0284c7; text-transform: uppercase; letter-spacing: 0.05em;">Handled College Class Subject</span>
                <form id="selectSubjectForm" method="GET" action="{{ route('college.grades.page') }}" style="margin-top: 0.35rem; display: flex; align-items: center; gap: 0.75rem;">
                    <select name="section_subject_id" onchange="this.form.submit()" style="padding: 0.6rem 1rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.9rem; font-weight: 800; color: var(--text-main); min-width: 320px; background: #ffffff;">
                        @forelse($classSectionSubjects as $ss)
                            <option value="{{ $ss->id }}" {{ ($currentSectionSubject && $currentSectionSubject->id == $ss->id) ? 'selected' : '' }}>
                                {{ $ss->subject->subject_code ?? '' }} - {{ $ss->subject->subject_name ?? '' }} ({{ $ss->classSection->section_name ?? '' }})
                            </option>
                        @empty
                            <option value="">-- No Handled College Subjects --</option>
                        @endforelse
                    </select>

                    <select name="semester" onchange="this.form.submit()" style="padding: 0.6rem 1rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; font-weight: 700; color: var(--text-main);">
                        <option value="1st Semester" {{ $selectedSemester == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                        <option value="2nd Semester" {{ $selectedSemester == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                    </select>
                </form>
            </div>

            @if($currentSectionSubject)
                <div style="display: flex; gap: 0.75rem;">
                    <button type="button" onclick="computeTotalGradesAjax()" style="background: #0f172a; color: #ffffff; border: none; font-weight: 800; padding: 0.65rem 1.15rem; border-radius: 8px; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; box-shadow: var(--shadow-sm);">
                        <i class="fa-solid fa-calculator"></i> Compute & Save Total Grades
                    </button>
                    <button type="button" onclick="resetTotalGradesAjax()" style="background: #fee2e2; color: #991b1b; border: 1px solid #f87171; font-weight: 800; padding: 0.65rem 1rem; border-radius: 8px; font-size: 0.85rem; cursor: pointer;">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </button>
                </div>
            @endif
        </div>
    </div>

    @if($currentSectionSubject)
        <!-- Term Selector Pills -->
        <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
            @foreach($availablePeriods as $p)
                <a href="{{ route('college.grades.page', ['section_subject_id' => $currentSectionSubject->id, 'academic_period' => $p, 'semester' => $selectedSemester]) }}"
                   class="period-pill {{ $selectedPeriod == $p ? 'active' : '' }}">
                    {{ $p }}
                </a>
            @endforeach
        </div>

        <!-- Category & Task Management Toolbar -->
        <div class="class-record-card" style="padding: 1.25rem 1.5rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-main);">
                        Grading Categories & Weight Breakdown ({{ $selectedPeriod }})
                    </h3>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <button type="button" onclick="openAddCategoryModal()" style="background: #0f172a; color: #ffffff; border: none; font-size: 0.8rem; font-weight: 800; padding: 0.5rem 0.9rem; border-radius: 6px; cursor: pointer;">
                        + Add Category
                    </button>
                    @if($categories->isNotEmpty())
                        <button type="button" onclick="openAddTaskModal()" style="background: #38bdf8; color: #020617; border: none; font-size: 0.8rem; font-weight: 800; padding: 0.5rem 0.9rem; border-radius: 6px; cursor: pointer;">
                            + Add Task / Quiz
                        </button>
                    @endif
                    <button type="button" onclick="openAddAttendanceModal()" style="background: #f1f5f9; color: #0f172a; border: 1px solid var(--border-color); font-size: 0.8rem; font-weight: 800; padding: 0.5rem 0.9rem; border-radius: 6px; cursor: pointer;">
                        + Add Attendance Date
                    </button>
                </div>
            </div>

            <!-- Category Cards with Task Dropdown -->
            <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
                @forelse($categories as $cat)
                    <div class="category-card-item">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 30px; height: 30px; border-radius: 6px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                                    <i class="fa-solid fa-folder-closed"></i>
                                </div>
                                <div>
                                    <strong style="color: var(--text-main); font-size: 0.875rem;">{{ $cat->category_name }}</strong>
                                    <span style="background: #0284c7; color: #ffffff; padding: 0.12rem 0.45rem; border-radius: 9999px; font-size: 0.68rem; font-weight: 800; margin-left: 0.25rem;">{{ $cat->weight_percentage }}%</span>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.35rem;">
                                <button type="button" onclick='openEditCategoryModal(@json($cat))' style="background: #f1f5f9; border: 1px solid var(--border-color); color: #0284c7; border-radius: 6px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.75rem;" title="Edit Category">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('college.grades.category.destroy', $cat->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete category \'{{ addslashes($cat->category_name) }}\' and all its tasks?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: #fee2e2; border: 1px solid #fecaca; color: #dc2626; border-radius: 6px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.75rem;" title="Delete Category">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Dropdown Trigger & Quick Add -->
                        <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 0.4rem; border-top: 1px solid #f1f5f9;">
                            <button type="button" onclick="toggleCategoryTasks({{ $cat->id }})" id="cat_toggle_btn_{{ $cat->id }}" style="background: none; border: none; padding: 0; font-size: 0.78rem; font-weight: 700; color: #0284c7; cursor: pointer; display: flex; align-items: center; gap: 0.35rem;">
                                <span>{{ $cat->tasks->count() }} Tasks</span>
                                <i class="fa-solid fa-chevron-down" id="cat_chevron_{{ $cat->id }}" style="font-size: 0.7rem; transition: transform 0.2s ease;"></i>
                            </button>
                            <button type="button" onclick="openAddTaskModal({{ $cat->id }})" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; font-size: 0.72rem; font-weight: 800; padding: 0.2rem 0.5rem; border-radius: 5px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                <i class="fa-solid fa-plus"></i> Add Task
                            </button>
                        </div>

                        <!-- Tasks Collapsible Dropdown List -->
                        <div id="cat_tasks_list_{{ $cat->id }}" style="display: none; flex-direction: column; gap: 0.4rem; padding-top: 0.5rem; border-top: 1px dashed #e2e8f0;">
                            @forelse($cat->tasks as $t)
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.45rem 0.65rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                                    <div style="display: flex; flex-direction: column;">
                                        <span style="font-weight: 700; color: var(--text-main); font-size: 0.78rem;">{{ $t->task_name }}</span>
                                        <span style="font-size: 0.7rem; color: #0284c7; font-weight: 800;">Max: {{ floatval($t->max_score) }} pts</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.25rem;">
                                        <button type="button" onclick='openEditTaskModal(@json($t))' style="background: #fef3c7; border: 1px solid #fde68a; color: #b45309; border-radius: 4px; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.7rem;" title="Edit Task">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('college.grades.task.destroy', $t->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete task \'{{ addslashes($t->task_name) }}\'?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: #fee2e2; border: 1px solid #fecaca; color: #dc2626; border-radius: 4px; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.7rem;" title="Delete Task">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-style: italic; padding: 0.25rem 0;">No tasks in this category yet. Click "+ Add Task" to create one.</div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">No grading categories set up for {{ $selectedPeriod }} yet. Click "+ Add Category" to start.</div>
                @endforelse
            </div>
        </div>

        <!-- Attendance & Absent Guide Legend -->
        <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 0.75rem 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; box-shadow: var(--shadow-sm);">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-circle-info" style="color: #0284c7; font-size: 0.95rem;"></i>
                <span style="font-size: 0.8rem; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.05em;">Attendance & Absent Guide:</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap;">
                <span style="font-size: 0.78rem; display: inline-flex; align-items: center; gap: 0.35rem; color: var(--text-main);">
                    <span class="badge-status badge-p">P</span> <strong>Present</strong>
                </span>
                <span style="font-size: 0.78rem; display: inline-flex; align-items: center; gap: 0.35rem; color: var(--text-main);">
                    <span class="badge-status badge-l">L</span> <strong>Late / Tardy</strong>
                </span>
                <span style="font-size: 0.78rem; display: inline-flex; align-items: center; gap: 0.35rem; color: var(--text-main);">
                    <span class="badge-status badge-a">A</span> <strong>Absent (Unexcused)</strong>
                </span>
                <span style="font-size: 0.78rem; display: inline-flex; align-items: center; gap: 0.35rem; color: var(--text-main);">
                    <span class="badge-status badge-ael">AEL</span> <strong>Absent Excused w/ Letter</strong>
                </span>
                <span style="font-size: 0.78rem; display: inline-flex; align-items: center; gap: 0.35rem; color: var(--text-main);">
                    <span class="badge-status badge-e">E</span> <strong>Excused</strong>
                </span>
                <span style="font-size: 0.78rem; display: inline-flex; align-items: center; gap: 0.35rem; color: var(--text-main);">
                    <span class="badge-status badge-c">C</span> <strong>Cutting Classes</strong>
                </span>
            </div>
        </div>

        <!-- Grade Sheet Record Table -->
        <div class="class-record-card" style="padding: 1.5rem;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead>
                        <!-- Category / Group Headers (Top Tier) -->
                        <tr style="background: #f8fafc; color: var(--text-muted); font-size: 0.75rem;">
                            <th rowspan="2" style="padding: 0.85rem 1rem; text-align: left; vertical-align: middle; border-bottom: 2px solid var(--border-color); background: #f8fafc; color: var(--text-main); font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; min-width: 220px;">
                                Student Information
                            </th>

                            @foreach($categories as $cat)
                                <th colspan="{{ $cat->tasks->isNotEmpty() ? ($cat->tasks->count() + 2) : 1 }}" style="padding: 0.65rem 0.75rem; text-align: center; border-left: 2px solid #cbd5e1; border-bottom: 1px solid #e2e8f0; background: #f1f5f9; color: #0f172a; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 0.35rem; flex-wrap: wrap;">
                                        <span><i class="fa-solid fa-folder-closed" style="color: #0284c7; margin-right: 0.25rem;"></i>{{ $cat->category_name }}</span>
                                        <span style="background: #0284c7; color: #ffffff; padding: 0.15rem 0.45rem; border-radius: 9999px; font-size: 0.68rem;">{{ $cat->weight_percentage }}%</span>
                                        <button type="button" onclick='openEditCategoryModal(@json($cat))' style="background: none; border: none; color: #0284c7; cursor: pointer; font-size: 0.8rem; padding: 0 0.2rem;" title="Edit Category">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('college.grades.category.destroy', $cat->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete category \'{{ addslashes($cat->category_name) }}\' and all its tasks?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 0.8rem; padding: 0 0.2rem;" title="Delete Category">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </th>
                            @endforeach

                            <!-- Term Attendance Sessions Header -->
                            @if($attendanceDates->isNotEmpty())
                                <th colspan="{{ $attendanceDates->count() + 1 }}" style="padding: 0.65rem 0.75rem; text-align: center; border-left: 2px solid #cbd5e1; border-bottom: 1px solid #e2e8f0; background: #ecfdf5; color: #065f46; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    <i class="fa-solid fa-calendar-check" style="color: #059669; margin-right: 0.35rem;"></i>Attendance
                                </th>
                            @endif

                            <th rowspan="2" style="padding: 0.85rem 1rem; text-align: center; vertical-align: middle; border-left: 2px solid #38bdf8; border-bottom: 2px solid var(--border-color); background: #f0f9ff; color: #0369a1; font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; min-width: 110px;">
                                Term Grade
                            </th>
                        </tr>

                        <!-- Sub-headers: Specific Task / Date Columns (Second Tier) -->
                        <tr style="background: #ffffff; color: var(--text-muted); font-size: 0.75rem;">
                            @foreach($categories as $cat)
                                @if($cat->tasks->isNotEmpty())
                                    @foreach($cat->tasks as $tIdx => $t)
                                        <th style="padding: 0.6rem 0.5rem; text-align: center; {{ $tIdx === 0 ? 'border-left: 2px solid #cbd5e1;' : 'border-left: 1px solid var(--border-color);' }} border-bottom: 2px solid var(--border-color); background: #ffffff;">
                                            <div style="display: flex; align-items: center; justify-content: center; gap: 0.35rem;">
                                                <span style="font-weight: 700; color: var(--text-main); font-size: 0.78rem;">{{ $t->task_name }}</span>
                                                <button type="button" onclick='openEditTaskModal(@json($t))' style="background: none; border: none; color: #d97706; cursor: pointer; font-size: 0.75rem; padding: 0;" title="Edit Task">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <form action="{{ route('college.grades.task.destroy', $t->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete task \'{{ addslashes($t->task_name) }}\'?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 0.75rem; padding: 0;" title="Delete Task">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div style="color: #0284c7; font-weight: 800; font-size: 0.7rem; margin-top: 0.15rem;">Max: {{ floatval($t->max_score) }}</div>
                                        </th>
                                    @endforeach
                                    <th style="padding: 0.6rem 0.5rem; text-align: center; border-left: 1px solid #cbd5e1; border-bottom: 2px solid var(--border-color); background: #f1f5f9; min-width: 60px;">
                                        <div style="font-weight: 800; color: #0284c7; font-size: 0.75rem;">TOTAL</div>
                                        <div style="color: #0f172a; font-weight: 800; font-size: 0.7rem; margin-top: 0.15rem;">Max: {{ $cat->tasks->sum('max_score') }}</div>
                                    </th>
                                    <th style="padding: 0.6rem 0.5rem; text-align: center; border-left: 1px solid #cbd5e1; border-bottom: 2px solid var(--border-color); background: #ecfdf5; min-width: 65px;">
                                        <div style="font-weight: 800; color: #15803d; font-size: 0.75rem;">WS ({{ $cat->weight_percentage }}%)</div>
                                        <div style="color: #166534; font-weight: 800; font-size: 0.7rem; margin-top: 0.15rem;">Equiv.</div>
                                    </th>
                                @else
                                    <th style="padding: 0.6rem 0.5rem; text-align: center; border-left: 2px solid #cbd5e1; border-bottom: 2px solid var(--border-color); background: #ffffff; color: var(--text-muted); font-size: 0.75rem; font-style: italic;">
                                        No Tasks
                                    </th>
                                @endif
                            @endforeach

                            @if($attendanceDates->isNotEmpty())
                                @foreach($attendanceDates as $aIdx => $adate)
                                    <th style="padding: 0.6rem 0.5rem; text-align: center; {{ $aIdx === 0 ? 'border-left: 2px solid #cbd5e1;' : 'border-left: 1px solid var(--border-color);' }} border-bottom: 2px solid var(--border-color); background: #fcfdfd;">
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.35rem;">
                                            <span style="font-weight: 700; color: #065f46; font-size: 0.75rem;">{{ \Carbon\Carbon::parse($adate)->format('m/d') }}</span>
                                            <form action="{{ route('college.grades.attendance.date.destroy') }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete attendance session on {{ \Carbon\Carbon::parse($adate)->format('M d, Y') }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="class_section_subject_id" value="{{ $currentSectionSubject->id }}">
                                                <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">
                                                <input type="hidden" name="attendance_date" value="{{ $adate }}">
                                                <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 0.75rem; padding: 0;" title="Delete Attendance Session">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </th>
                                @endforeach
                                <th style="padding: 0.6rem 0.5rem; text-align: center; border-left: 1px solid #cbd5e1; border-bottom: 2px solid var(--border-color); background: #ecfdf5; min-width: 60px;">
                                    <div style="font-weight: 800; color: #065f46; font-size: 0.75rem;">TOTAL</div>
                                    <div style="color: #059669; font-weight: 800; font-size: 0.7rem; margin-top: 0.15rem;">Present</div>
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrolledStudents as $e)
                            @php
                                $stu = $e->student;
                                $sg = $savedGrades->get($e->id);
                            @endphp
                            <tr data-breakdown-row="{{ $e->id }}" style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.85rem 1rem; font-weight: 700; color: var(--text-main);">
                                    {{ $stu->last_name ?? '' }}, {{ $stu->first_name ?? '' }} {{ $stu->middle_name ?? '' }} {{ $stu->extension_name ?? '' }}
                                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">{{ $stu->student_number ?? '' }}</div>
                                </td>

                                <!-- Task Scores & Category Total & WS -->
                                @foreach($categories as $cat)
                                    @if($cat->tasks->isNotEmpty())
                                        @php
                                            $catScoreSum = 0;
                                            $catMaxSum = (float)$cat->tasks->sum('max_score');
                                            $catWeight = (float)$cat->weight_percentage;
                                        @endphp
                                        @foreach($cat->tasks as $tIdx => $t)
                                            @php
                                                $scoreObj = $t->scores->firstWhere('enrollment_id', $e->id);
                                                $scoreVal = $scoreObj ? $scoreObj->score : '';
                                                if ($scoreVal !== '' && is_numeric($scoreVal)) {
                                                    $catScoreSum += (float)$scoreVal;
                                                }
                                            @endphp
                                            <td style="padding: 0.5rem; text-align: center; {{ $tIdx === 0 ? 'border-left: 2px solid #cbd5e1;' : 'border-left: 1px solid var(--border-color);' }}">
                                                <input type="number" step="0.01" min="0" max="{{ $t->max_score }}"
                                                       value="{{ $scoreVal }}" class="score-input"
                                                       data-category-id="{{ $cat->id }}"
                                                       data-enrollment-id="{{ $e->id }}"
                                                       onchange="saveTaskScore({{ $t->id }}, {{ $e->id }}, this.value, this, {{ $cat->id }})">
                                            </td>
                                        @endforeach
                                        @php
                                            $catWS = $catMaxSum > 0 ? ($catScoreSum / $catMaxSum) * $catWeight : 0;
                                        @endphp
                                        <td style="padding: 0.5rem; text-align: center; font-weight: 800; background: #f8fafc; border-left: 1px solid #cbd5e1; color: #0284c7;">
                                            <span class="cat-total-{{ $cat->id }}-{{ $e->id }}">{{ round($catScoreSum, 2) }}</span>
                                        </td>
                                        <td style="padding: 0.5rem; text-align: center; font-weight: 800; background: #f0fdf4; border-left: 1px solid #cbd5e1; color: #15803d;">
                                            <span class="cat-ws-{{ $cat->id }}-{{ $e->id }}" data-weight="{{ $catWeight }}" data-max="{{ $catMaxSum }}">
                                                {{ number_format($catWS, 2) }}%
                                            </span>
                                        </td>
                                    @else
                                        <td style="padding: 0.5rem; text-align: center; border-left: 2px solid #cbd5e1; color: var(--text-muted); font-size: 0.75rem;">-</td>
                                    @endif
                                @endforeach

                                <!-- Attendance Code Dropdowns & Attendance Total -->
                                @if($attendanceDates->isNotEmpty())
                                    @php
                                        $presentCount = 0;
                                    @endphp
                                    @foreach($attendanceDates as $aIdx => $adate)
                                        @php
                                            $attObj = $attendances->where('enrollment_id', $e->id)->where('attendance_date', $adate)->first();
                                            $attCode = $attObj ? $attObj->status : 'P';
                                            if ($attCode === 'P') {
                                                $presentCount++;
                                            }
                                        @endphp
                                        <td style="padding: 0.5rem; text-align: center; {{ $aIdx === 0 ? 'border-left: 2px solid #cbd5e1;' : 'border-left: 1px solid var(--border-color);' }}">
                                            <select data-attendance-enrollment="{{ $e->id }}"
                                                    onchange="saveAttendanceStatus({{ $currentSectionSubject->id }}, {{ $e->id }}, '{{ $adate }}', this.value, '{{ $selectedPeriod }}', this)"
                                                    style="padding: 0.25rem; border-radius: 6px; font-weight: 800; font-size: 0.75rem; border: 1px solid var(--border-color);" class="badge-{{ strtolower($attCode) }}">
                                                <option value="P" title="P - Present" {{ $attCode == 'P' ? 'selected' : '' }}>P</option>
                                                <option value="L" title="L - Late / Tardy" {{ $attCode == 'L' ? 'selected' : '' }}>L</option>
                                                <option value="A" title="A - Absent (Unexcused)" {{ $attCode == 'A' ? 'selected' : '' }}>A</option>
                                                <option value="AEL" title="AEL - Absent Excused w/ Letter" {{ $attCode == 'AEL' ? 'selected' : '' }}>AEL</option>
                                                <option value="E" title="E - Excused" {{ $attCode == 'E' ? 'selected' : '' }}>E</option>
                                                <option value="C" title="C - Cutting Classes" {{ $attCode == 'C' ? 'selected' : '' }}>C</option>
                                            </select>
                                        </td>
                                    @endforeach
                                    <td style="padding: 0.5rem; text-align: center; font-weight: 800; background: #f0fdf4; border-left: 1px solid #cbd5e1; color: #15803d;">
                                        <span class="att-total-{{ $e->id }}">{{ $presentCount }}</span>
                                    </td>
                                @endif

                                <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 800; color: #0284c7; border-left: 2px solid #38bdf8; background: #f0f9ff;">
                                    <span class="sg-val">{{ $sg && $sg->final_grade !== null ? number_format($sg->final_grade, 2) : '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="30" style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                                    No students enrolled in this college section yet. Use "Enroll Students" to add students.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<!-- Modal: Add Category -->
<div id="addCategoryModal" class="crm-modal-overlay">
    <div class="crm-modal-card">
        <div style="background: #0f172a; padding: 1.25rem 1.5rem; color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0;">Add Grading Category</h3>
            <button type="button" onclick="closeAddCategoryModal()" style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        <form action="{{ route('college.grades.category.store') }}" method="POST" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.15rem;">
            @csrf
            <input type="hidden" name="class_section_subject_id" value="{{ $currentSectionSubject->id ?? '' }}">
            <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">

            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">Category Name *</label>
                <input type="text" name="category_name" placeholder="e.g. Quizzes, Exams, Major Output" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">Weight Percentage (%) *</label>
                <input type="number" step="0.01" min="1" max="100" name="weight_percentage" placeholder="e.g. 40" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                <button type="button" onclick="closeAddCategoryModal()" style="padding: 0.6rem 1.15rem; border: 1px solid var(--border-color); background: #ffffff; border-radius: 6px; font-weight: 700; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 0.6rem 1.15rem; background: #0f172a; color: #ffffff; border: none; border-radius: 6px; font-weight: 800; cursor: pointer;">Save Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Category -->
<div id="editCategoryModal" class="crm-modal-overlay">
    <div class="crm-modal-card">
        <div style="background: #0f172a; padding: 1.25rem 1.5rem; color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0;">Edit Grading Category</h3>
            <button type="button" onclick="closeEditCategoryModal()" style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        <form id="editCategoryForm" action="" method="POST" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.15rem;">
            @csrf
            @method('PUT')
            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">Category Name *</label>
                <input type="text" name="category_name" id="edit_category_name" placeholder="e.g. Quizzes, Exams, Major Output" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">Weight Percentage (%) *</label>
                <input type="number" step="0.01" min="1" max="100" name="weight_percentage" id="edit_weight_percentage" placeholder="e.g. 40" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                <button type="button" onclick="closeEditCategoryModal()" style="padding: 0.6rem 1.15rem; border: 1px solid var(--border-color); background: #ffffff; border-radius: 6px; font-weight: 700; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 0.6rem 1.15rem; background: #0f172a; color: #ffffff; border: none; border-radius: 6px; font-weight: 800; cursor: pointer;">Update Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Task -->
<div id="addTaskModal" class="crm-modal-overlay">
    <div class="crm-modal-card">
        <div style="background: #0f172a; padding: 1.25rem 1.5rem; color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0;">Add Task / Quiz</h3>
            <button type="button" onclick="closeAddTaskModal()" style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        <form action="{{ route('college.grades.task.store') }}" method="POST" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.15rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">Category *</label>
                <select name="grading_category_id" id="add_task_category_id" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->category_name }} ({{ $c->weight_percentage }}%)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">Task Title *</label>
                <input type="text" name="task_name" placeholder="e.g. Quiz 1 or Preliminary Exam" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">Max Score *</label>
                <input type="number" step="1" min="1" name="max_score" placeholder="e.g. 50" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                <button type="button" onclick="closeAddTaskModal()" style="padding: 0.6rem 1.15rem; border: 1px solid var(--border-color); background: #ffffff; border-radius: 6px; font-weight: 700; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 0.6rem 1.15rem; background: #0f172a; color: #ffffff; border: none; border-radius: 6px; font-weight: 800; cursor: pointer;">Save Task</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Task -->
<div id="editTaskModal" class="crm-modal-overlay">
    <div class="crm-modal-card">
        <div style="background: #0f172a; padding: 1.25rem 1.5rem; color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0;">Edit Task / Quiz</h3>
            <button type="button" onclick="closeEditTaskModal()" style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        <form id="editTaskForm" action="" method="POST" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.15rem;">
            @csrf
            @method('PUT')
            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">Category *</label>
                <select name="grading_category_id" id="edit_grading_category_id" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->category_name }} ({{ $c->weight_percentage }}%)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">Task Title *</label>
                <input type="text" name="task_name" id="edit_task_name" placeholder="e.g. Quiz 1 or Preliminary Exam" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
            </div>
            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">Max Score *</label>
                <input type="number" step="1" min="1" name="max_score" id="edit_max_score" placeholder="e.g. 50" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                <button type="button" onclick="closeEditTaskModal()" style="padding: 0.6rem 1.15rem; border: 1px solid var(--border-color); background: #ffffff; border-radius: 6px; font-weight: 700; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 0.6rem 1.15rem; background: #0f172a; color: #ffffff; border: none; border-radius: 6px; font-weight: 800; cursor: pointer;">Update Task</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Attendance Date -->
<div id="addAttendanceModal" class="crm-modal-overlay">
    <div class="crm-modal-card" style="max-width: 420px;">
        <div style="background: #0f172a; padding: 1.25rem 1.5rem; color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0;">Add Attendance Session</h3>
            <button type="button" onclick="closeAddAttendanceModal()" style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        <form action="{{ route('college.grades.attendance.date.store') }}" method="POST" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.15rem;">
            @csrf
            <input type="hidden" name="class_section_subject_id" value="{{ $currentSectionSubject->id ?? '' }}">
            <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">

            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">Attendance Date *</label>
                <input type="date" name="attendance_date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                <button type="button" onclick="closeAddAttendanceModal()" style="padding: 0.6rem 1.15rem; border: 1px solid var(--border-color); background: #ffffff; border-radius: 6px; font-weight: 700; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 0.6rem 1.15rem; background: #0f172a; color: #ffffff; border: none; border-radius: 6px; font-weight: 800; cursor: pointer;">Create Session</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Unified Modal Open & Close with Animations
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            if (!document.querySelector('.crm-modal-overlay.active')) {
                document.body.style.overflow = '';
            }
        }
    }

    // Modal Triggers
    function openAddCategoryModal() { openModal('addCategoryModal'); }
    function closeAddCategoryModal() { closeModal('addCategoryModal'); }
    
    function openEditCategoryModal(cat) {
        const form = document.getElementById('editCategoryForm');
        form.action = '{{ url('college/grades/category/update') }}/' + cat.id;
        document.getElementById('edit_category_name').value = cat.category_name || cat.name || '';
        document.getElementById('edit_weight_percentage').value = cat.weight_percentage || cat.weight || '';
        openModal('editCategoryModal');
    }
    function closeEditCategoryModal() { closeModal('editCategoryModal'); }

    function openAddTaskModal(categoryId = null) {
        const select = document.getElementById('add_task_category_id');
        if (select && categoryId) {
            select.value = categoryId;
        }
        openModal('addTaskModal');
    }
    function closeAddTaskModal() { closeModal('addTaskModal'); }

    function openEditTaskModal(task) {
        const form = document.getElementById('editTaskForm');
        form.action = '{{ url('college/grades/task/update') }}/' + task.id;
        document.getElementById('edit_task_name').value = task.task_name || '';
        document.getElementById('edit_max_score').value = parseFloat(task.max_score) || '';
        const catSelect = document.getElementById('edit_grading_category_id');
        if (catSelect && task.grading_category_id) {
            catSelect.value = task.grading_category_id;
        }
        openModal('editTaskModal');
    }
    function closeEditTaskModal() { closeModal('editTaskModal'); }

    function openAddAttendanceModal() { openModal('addAttendanceModal'); }
    function closeAddAttendanceModal() { closeModal('addAttendanceModal'); }

    // Toggle Category Tasks Dropdown inside Card
    function toggleCategoryTasks(catId) {
        const list = document.getElementById('cat_tasks_list_' + catId);
        const chevron = document.getElementById('cat_chevron_' + catId);
        if (list) {
            const isHidden = list.style.display === 'none' || list.style.display === '';
            list.style.display = isHidden ? 'flex' : 'none';
            if (chevron) {
                chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        }
    }

    // Close Modal on Click Outside Card
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('crm-modal-overlay')) {
            closeModal(e.target.id);
        }
    });

    // Close Modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const activeModal = document.querySelector('.crm-modal-overlay.active');
            if (activeModal) {
                closeModal(activeModal.id);
            }
        }
    });

    function saveTaskScore(taskId, enrollmentId, scoreVal, inputElem, categoryId) {
        fetch('{{ route('college.grades.score.update') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                grading_task_id: taskId,
                enrollment_id: enrollmentId,
                score: scoreVal
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                $(inputElem).addClass('score-saved');
                setTimeout(() => $(inputElem).removeClass('score-saved'), 1500);

                // Recalculate Category Total Score & WS % for this student in real-time
                if (categoryId) {
                    let catSum = 0;
                    let catMaxSum = 0;
                    document.querySelectorAll(`input[data-category-id="${categoryId}"][data-enrollment-id="${enrollmentId}"]`).forEach(inp => {
                        const val = parseFloat(inp.value);
                        const maxVal = parseFloat(inp.getAttribute('max')) || 0;
                        if (!isNaN(val)) catSum += val;
                        catMaxSum += maxVal;
                    });
                    const totalSpan = document.querySelector(`.cat-total-${categoryId}-${enrollmentId}`);
                    if (totalSpan) {
                        totalSpan.textContent = Math.round(catSum * 100) / 100;
                    }
                    const wsSpan = document.querySelector(`.cat-ws-${categoryId}-${enrollmentId}`);
                    if (wsSpan) {
                        const catWeight = parseFloat(wsSpan.getAttribute('data-weight')) || 0;
                        const wsVal = catMaxSum > 0 ? (catSum / catMaxSum) * catWeight : 0;
                        wsSpan.textContent = wsVal.toFixed(2) + '%';
                    }
                }
            }
        });
    }

    function saveAttendanceStatus(cssId, enrollmentId, attDate, statusVal, periodVal, selectElem) {
        fetch('{{ route('college.grades.attendance.status.update') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                class_section_subject_id: cssId,
                enrollment_id: enrollmentId,
                attendance_date: attDate,
                status: statusVal,
                academic_period: periodVal
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                selectElem.className = 'badge-' + statusVal.toLowerCase();

                // Recalculate Total Present for this student in real-time
                let presentCount = 0;
                document.querySelectorAll(`select[data-attendance-enrollment="${enrollmentId}"]`).forEach(sel => {
                    if (sel.value === 'P') presentCount++;
                });
                const attTotalSpan = document.querySelector(`.att-total-${enrollmentId}`);
                if (attTotalSpan) {
                    attTotalSpan.textContent = presentCount;
                }
            }
        });
    }

    function computeTotalGradesAjax() {
        if (!confirm('Compute total grades for this college subject across all terms?')) return;

        fetch('{{ route('college.grades.compute.total') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                class_section_subject_id: '{{ $currentSectionSubject->id ?? '' }}'
            })
        })
        .then(res => {
            if (!res.ok) {
                return res.json().catch(() => { throw new Error('Server error (' + res.status + ')'); }).then(errData => {
                    throw new Error(errData.message || 'Server error (' + res.status + ')');
                });
            }
            return res.json();
        })
        .then(data => {
            if (data.status === 'success') {
                data.results.forEach(resItem => {
                    const row = document.querySelector(`tr[data-breakdown-row="${resItem.enrollment_id}"]`);
                    if (row) {
                        const sgCell = row.querySelector('.sg-val');
                        const termGrade = (resItem.period_grades && resItem.period_grades['{{ $selectedPeriod }}'] !== undefined && resItem.period_grades['{{ $selectedPeriod }}'] !== null)
                            ? Number(resItem.period_grades['{{ $selectedPeriod }}']).toFixed(2)
                            : '-';
                        if (sgCell) sgCell.textContent = termGrade;
                    }
                });
                Toast.fire({ icon: 'success', title: data.message });
            } else {
                Toast.fire({ icon: 'error', title: data.message || 'Error computing grades.' });
            }
        })
        .catch(err => {
            console.error('Error computing grades:', err);
            Toast.fire({ icon: 'error', title: err.message || 'Failed to compute grades.' });
        });
    }

    function resetTotalGradesAjax() {
        if (!confirm('Are you sure you want to reset all computed total grades for this class subject?')) return;

        fetch('{{ route('college.grades.reset.total') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                class_section_subject_id: '{{ $currentSectionSubject->id ?? '' }}'
            })
        })
        .then(res => {
            if (!res.ok) {
                return res.json().catch(() => { throw new Error('Server error (' + res.status + ')'); }).then(errData => {
                    throw new Error(errData.message || 'Server error (' + res.status + ')');
                });
            }
            return res.json();
        })
        .then(data => {
            if (data.status === 'success') {
                document.querySelectorAll('.sg-val').forEach(el => el.textContent = '-');
                Toast.fire({ icon: 'success', title: data.message });
            } else {
                Toast.fire({ icon: 'error', title: data.message || 'Error resetting grades.' });
            }
        })
        .catch(err => {
            console.error('Error resetting grades:', err);
            Toast.fire({ icon: 'error', title: err.message || 'Failed to reset grades.' });
        });
    }
</script>
@endpush
