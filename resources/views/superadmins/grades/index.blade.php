@extends('layouts.superadmin')

@section('title', 'GNHS - Class Record & Grading Management')

@section('content')
    <style>
        .score-input-cell {
            width: 62px;
            padding: 0.3rem 0.4rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            text-align: center;
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--primary-navy, #0f172a);
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .score-input-cell:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
            background: #eff6ff;
        }

        .score-input-cell.saved-success {
            border-color: #10b981 !important;
            background: #ecfdf5 !important;
        }

        .score-input-cell.saving {
            border-color: #f59e0b !important;
            background: #fffbeb !important;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-card {
            background: #ffffff;
            border-radius: 16px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            animation: modalIn 0.2s ease-out;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            background: var(--primary-navy, #0f172a);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .form-group {
            margin-bottom: 1.1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.4rem;
        }

        .form-control {
            width: 100%;
            padding: 0.55rem 0.85rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.88rem;
            color: #0f172a;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .btn-icon-action {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            padding: 0.35rem;
            color: #475569;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-icon-action:hover {
            color: #2563eb;
            background: #eff6ff;
            border-color: #93c5fd;
            transform: translateY(-1px);
        }

        .btn-icon-action.danger {
            color: #64748b;
        }

        .btn-icon-action.danger:hover {
            color: #dc2626;
            background: #fef2f2;
            border-color: #fca5a5;
            transform: translateY(-1px);
        }

        .btn-secondary {
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-secondary:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
            color: #0f172a;
            transform: translateY(-1px);
        }
    </style>

    <!-- Class Section Subject Selector -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header">
            <div class="card-title">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 01-2-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Class Record & Subject Selection
                <span
                    style="font-size: 0.8rem; font-weight: 700; color: var(--accent-emerald); margin-left: 6px; padding: 0.2rem 0.6rem; background: rgba(16, 185, 129, 0.1); border-radius: 12px;">
                    S.Y. {{ $activeSchoolYear->school_year ?? '2024-2025' }}
                    @if (in_array($selectedLevel, ['SHS', 'COLLEGE']))
                        &bull; {{ request('semester', '1st Semester') }}
                    @else
                        &bull; DepEd Quarters
                    @endif
                </span>
            </div>
        </div>
        <div class="card-body">
            @if ($classSectionSubjects->count() > 0)
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                    @foreach ($classSectionSubjects as $csSubject)
                        @php
                            $isSelected = $currentSectionSubject && $currentSectionSubject->id == $csSubject->id;
                            $lvlCode = $csSubject->classSection->gradeLevel->educationLevel->code ?? '';
                            $gradeLevelName = $csSubject->classSection->gradeLevel->name ?? '';
                            $isSemestral = in_array($lvlCode, ['SHS', 'COLLEGE']);
                        @endphp
                        <a href="{{ route('superadmin.grades.page', array_filter(['level' => $selectedLevel, 'section_subject_id' => $csSubject->id, 'semester' => request('semester')])) }}"
                            style="padding: 0.55rem 1rem; border-radius: 12px; font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; border: 1px solid {{ $isSelected ? 'var(--primary-navy)' : 'var(--border-color)' }}; background: {{ $isSelected ? 'var(--primary-navy)' : '#ffffff' }}; color: {{ $isSelected ? '#ffffff' : 'var(--primary-navy)' }};">
                            {{ $csSubject->classSection->section_name ?? 'Section' }} &bull;
                            {{ $csSubject->subject->subject_name ?? 'Subject' }}
                            <span
                                style="font-size: 0.7rem; font-weight: 600; opacity: 0.85; margin-left: 4px; padding: 0.15rem 0.45rem; background: rgba(255, 255, 255, 0.2); border-radius: 6px;">
                                {{ $gradeLevelName ? $gradeLevelName : $lvlCode }}
                                @if ($isSemestral)
                                    &bull; {{ $csSubject->subject->semester ?? request('semester', '1st Sem') }}
                                @endif
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                    No assigned class section subjects found for {{ $selectedLevel ? $selectedLevel . ' Level in' : '' }}
                    S.Y. {{ $activeSchoolYear->school_year ?? '' }} ({{ request('semester', '1st Semester') }}).
                </div>
            @endif
        </div>
    </div>

    @if ($currentSectionSubject)
        @php
            $currLvlCode = $currentSectionSubject->classSection->gradeLevel->educationLevel->code ?? '';
            $currGradeLevelName = $currentSectionSubject->classSection->gradeLevel->name ?? 'N/A';
            $currIsSemestral = in_array($currLvlCode, ['SHS', 'COLLEGE']);
        @endphp

        <!-- Academic Period Filter Pills (Prelim, Midterm, Finals vs 1st-4th Quarters) -->
        <div
            style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <span style="font-size: 0.82rem; font-weight: 700; color: var(--text-muted); margin-right: 4px;">Academic
                    Period:</span>
                @if (isset($availablePeriods) && $availablePeriods->count() > 0)
                    @foreach ($availablePeriods as $period)
                        <a href="{{ route('superadmin.grades.page', array_filter(['level' => $selectedLevel, 'section_subject_id' => $currentSectionSubject->id, 'academic_period' => $period, 'semester' => request('semester')])) }}"
                            class="level-tab-item {{ $selectedPeriod == $period ? 'active' : '' }}"
                            style="font-size: 0.75rem; padding: 0.35rem 0.85rem;">
                            {{ $period }}
                        </a>
                    @endforeach
                @endif
            </div>
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--accent-emerald);">
                Active Term: S.Y. {{ $activeSchoolYear->school_year ?? '' }}
                @if ($currIsSemestral)
                    &bull; {{ request('semester', '1st Semester') }}
                @else
                    &bull; DepEd Quarters
                @endif
            </div>
        </div>

        <!-- Class Record Header Meta Banner -->
        <div class="card"
            style="margin-bottom: 1.5rem; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
            <div class="card-body"
                style="padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div
                        style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--accent-gold); font-weight: 700; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <span>{{ $currentSectionSubject->classSection->gradeLevel->educationLevel->name ?? 'Education Level' }}</span>
                        <span>&bull;</span>
                        <span style="color: #60a5fa;">{{ $currGradeLevelName }}</span>
                        <span>&bull;</span>
                        <span>S.Y. {{ $activeSchoolYear->school_year ?? '' }}</span>
                        @if ($currIsSemestral)
                            <span>&bull;</span>
                            <span
                                style="color: #a7f3d0; background: rgba(16, 185, 129, 0.2); padding: 0.15rem 0.5rem; border-radius: 10px;">
                                {{ request('semester', '1st Semester') }}
                            </span>
                        @endif
                        @if ($selectedPeriod)
                            <span>&bull;</span>
                            <span style="color: var(--accent-gold);">{{ $selectedPeriod }} Period</span>
                        @endif
                    </div>
                    <h2 style="font-size: 1.35rem; font-weight: 800; margin: 0.35rem 0; color: #ffffff;">
                        {{ $currentSectionSubject->subject->subject_name ?? 'Subject' }}
                        ({{ $currentSectionSubject->subject->subject_code ?? 'CODE' }})
                    </h2>
                    <div style="font-size: 0.85rem; color: #94a3b8;">
                        Section: <strong>{{ $currentSectionSubject->classSection->section_name ?? 'N/A' }}</strong> |
                        Grade Level: <strong style="color: #60a5fa;">{{ $currGradeLevelName }}</strong> |
                        @if ($currIsSemestral)
                            Subject Semester: <strong
                                style="color: #cbd5e1;">{{ $currentSectionSubject->subject->semester ?? request('semester', '1st Semester') }}</strong>
                            |
                        @endif
                        Assigned Teacher:
                        <strong>{{ $currentSectionSubject->teacher ? $currentSectionSubject->teacher->first_name . ' ' . $currentSectionSubject->teacher->last_name : 'Unassigned' }}</strong>
                    </div>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <button class="btn-primary" style="background: var(--accent-gold); color: #0f172a;"
                        onclick="openModal('addCategoryModal')">+ Add Category</button>
                    <button class="btn-primary" onclick="openModal('addTaskModal')">+ Add Task</button>
                </div>
            </div>
        </div>

        <!-- Grading Categories & Tasks Breakdown -->
        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
            @forelse($categories as $cat)
                <div class="card">
                    <div class="card-header"
                        style="background: #f8fafc; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <div class="card-title"
                                style="font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
                                {{ $cat->name }}
                                <button class="btn-icon-action" title="Edit Category"
                                    onclick="openModal('editCategoryModal_{{ $cat->id }}')">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <form action="{{ route('superadmin.grades.category.destroy', $cat->id) }}" method="POST"
                                    style="display: inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon-action danger" title="Delete Category">
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            <div style="font-size: 0.72rem; color: var(--text-muted);">Period:
                                <strong>{{ $cat->academic_period }}</strong>
                            </div>
                        </div>
                        <span class="badge badge-active" style="font-size: 0.8rem; padding: 0.3rem 0.65rem;">
                            Weight: {{ number_format($cat->weight, 0) }}%
                        </span>
                    </div>
                    <div class="card-body" style="padding: 0.75rem 1rem;">
                        @forelse($cat->gradingTasks as $task)
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0; border-bottom: 1px dashed #e2e8f0;">
                                <div>
                                    <div
                                        style="font-size: 0.85rem; font-weight: 700; color: var(--primary-navy); display: flex; align-items: center; gap: 0.35rem;">
                                        {{ $task->task_name }}
                                        <button class="btn-icon-action" title="Edit Task"
                                            onclick="openModal('editTaskModal_{{ $task->id }}')">
                                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('superadmin.grades.task.destroy', $task->id) }}"
                                            method="POST" style="display: inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this task?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon-action danger" title="Delete Task">
                                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    <div style="font-size: 0.72rem; color: var(--text-muted);">
                                        {{ $task->task_date ? $task->task_date->format('M d, Y') : 'No Date' }}
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <span class="badge badge-admin" style="font-size: 0.75rem;">
                                        Max: {{ number_format($task->max_score, 0) }} pts
                                    </span>
                                </div>
                            </div>

                            <!-- Edit Task Modal -->
                            <div class="modal-overlay" id="editTaskModal_{{ $task->id }}">
                                <div class="modal-card">
                                    <div class="modal-header">
                                        <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Edit Grading Task</h3>
                                        <button type="button" class="btn-icon-action" style="color: #ffffff; background: rgba(255,255,255,0.15); border: none;"
                                            onclick="closeModal('editTaskModal_{{ $task->id }}')">&times;</button>
                                    </div>
                                    <form action="{{ route('superadmin.grades.task.update', $task->id) }}"
                                        method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Grading Category</label>
                                                <select name="grading_category_id" class="form-control" required>
                                                    @foreach ($categories as $cOption)
                                                        <option value="{{ $cOption->id }}"
                                                            {{ $cOption->id == $task->grading_category_id ? 'selected' : '' }}>
                                                            {{ $cOption->academic_period }} - {{ $cOption->name }}
                                                            ({{ number_format($cOption->weight, 0) }}%)
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Task Name</label>
                                                <input type="text" name="task_name" class="form-control"
                                                    value="{{ $task->task_name }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Max Score (Points)</label>
                                                <input type="number" name="max_score" class="form-control"
                                                    value="{{ number_format($task->max_score, 0) }}" min="1"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label>Task Date</label>
                                                <input type="date" name="task_date" class="form-control"
                                                    value="{{ $task->task_date ? $task->task_date->format('Y-m-d') : '' }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn-secondary"
                                                onclick="closeModal('editTaskModal_{{ $task->id }}')">
                                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Cancel
                                            </button>
                                            <button type="submit" class="btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div style="font-size: 0.8rem; color: var(--text-muted); text-align: center; padding: 1rem;">
                                No tasks created for {{ $cat->academic_period }} {{ $cat->name }}.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Edit Category Modal -->
                <div class="modal-overlay" id="editCategoryModal_{{ $cat->id }}">
                    <div class="modal-card">
                        <div class="modal-header">
                            <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Edit Grading Category</h3>
                            <button type="button" class="btn-icon-action" style="color: #ffffff; background: rgba(255,255,255,0.15); border: none;"
                                onclick="closeModal('editCategoryModal_{{ $cat->id }}')">&times;</button>
                        </div>
                        <form action="{{ route('superadmin.grades.category.update', $cat->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Academic Period</label>
                                    <select name="academic_period" class="form-control" required>
                                        @foreach ($availablePeriods as $pOpt)
                                            <option value="{{ $pOpt }}"
                                                {{ $pOpt == $cat->academic_period ? 'selected' : '' }}>{{ $pOpt }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Category Name</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ $cat->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Weight Percentage (%)</label>
                                    <input type="number" name="weight" class="form-control"
                                        value="{{ number_format($cat->weight, 0) }}" min="0" max="100"
                                        required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn-secondary"
                                    onclick="closeModal('editCategoryModal_{{ $cat->id }}')">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Cancel
                                </button>
                                <button type="submit" class="btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="card" style="grid-column: 1 / -1;">
                    <div class="card-body" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No grading categories configured for {{ $selectedPeriod ? $selectedPeriod : 'this period' }} yet.
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Student Scores Matrix Class Record Table (Editable Table) -->
        <div class="card">
            <div class="card-header">
                <div class="card-title"
                    style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Class Record Sheet
                        @if ($selectedPeriod)
                            &bull; {{ $selectedPeriod }} Period
                        @endif
                    </div>
                    <span
                        style="font-size: 0.75rem; font-weight: 600; color: #10b981; background: #ecfdf5; padding: 0.25rem 0.75rem; border-radius: 12px; border: 1px solid #a7f3d0;">
                        ✓ Live Auto-Save Enabled
                    </span>
                </div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="custom-table" id="classRecordTable">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                @foreach ($categories as $cat)
                                    @foreach ($cat->gradingTasks as $task)
                                        <th style="text-align: center;"
                                            title="{{ $cat->academic_period }} - {{ $cat->name }} - {{ $task->task_name }}">
                                            <div style="font-size: 0.68rem; color: var(--accent-gold); font-weight: 700;">
                                                [{{ $cat->academic_period }}]</div>
                                            {{ $task->task_name }}
                                            <div style="font-size: 0.65rem; font-weight: 500; color: var(--text-muted);">
                                                (Max: {{ number_format($task->max_score, 0) }})
                                            </div>
                                        </th>
                                    @endforeach
                                    <th style="text-align: center; background: rgba(30, 41, 59, 0.04);">
                                        {{ $cat->name }}
                                        <div style="font-size: 0.65rem; font-weight: 700; color: var(--accent-gold);">
                                            ({{ number_format($cat->weight, 0) }}%)
                                        </div>
                                    </th>
                                @endforeach
                                <th style="text-align: center; background: rgba(16, 185, 129, 0.1);">Computed Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrolledStudents as $enrollment)
                                @php
                                    $totalFinalGrade = 0;
                                @endphp
                                <tr data-student-row="{{ $enrollment->id }}">
                                    <td><strong>{{ $enrollment->student->student_number ?? 'N/A' }}</strong></td>
                                    <td>
                                        <strong>{{ $enrollment->student->first_name ?? '' }}
                                            {{ $enrollment->student->last_name ?? '' }}</strong>
                                    </td>

                                    @foreach ($categories as $cat)
                                        @php
                                            $catEarned = 0;
                                            $catMax = 0;
                                        @endphp

                                        @foreach ($cat->gradingTasks as $task)
                                            @php
                                                $taskScoreModel = $enrollment->taskScores
                                                    ->where('grading_task_id', $task->id)
                                                    ->first();
                                                $scoreValue = $taskScoreModel ? $taskScoreModel->score : null;
                                                if ($scoreValue !== null) {
                                                    $catEarned += $scoreValue;
                                                }
                                                $catMax += $task->max_score;
                                            @endphp
                                            <td style="text-align: center;">
                                                <input type="number" step="0.1" min="0"
                                                    max="{{ $task->max_score }}"
                                                    value="{{ $scoreValue !== null ? number_format($scoreValue, 1, '.', '') : '' }}"
                                                    class="score-input-cell" data-task-id="{{ $task->id }}"
                                                    data-enrollment-id="{{ $enrollment->id }}"
                                                    data-max-score="{{ $task->max_score }}"
                                                    data-cat-id="{{ $cat->id }}"
                                                    data-cat-weight="{{ $cat->weight }}" placeholder="-">
                                            </td>
                                        @endforeach

                                        @php
                                            $catPercentage = $catMax > 0 ? ($catEarned / $catMax) * $cat->weight : 0;
                                            $totalFinalGrade += $catPercentage;
                                        @endphp

                                        <td style="text-align: center; background: rgba(30, 41, 59, 0.03); font-weight: 700; color: var(--primary-navy);"
                                            data-cat-summary="{{ $enrollment->id }}-{{ $cat->id }}">
                                            <span class="cat-pct-val">{{ number_format($catPercentage, 2) }}</span>%
                                        </td>
                                    @endforeach

                                    <td style="text-align: center; background: rgba(16, 185, 129, 0.08);"
                                        data-final-summary="{{ $enrollment->id }}">
                                        <span class="badge badge-active final-grade-badge"
                                            style="font-size: 0.85rem; padding: 0.3rem 0.75rem;">
                                            <span class="final-grade-val">{{ number_format($totalFinalGrade, 2) }}</span>%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="20"
                                        style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                                        No students enrolled in this section for S.Y.
                                        {{ $activeSchoolYear->school_year ?? '' }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Category Modal -->
        <div class="modal-overlay" id="addCategoryModal">
            <div class="modal-card">
                <div class="modal-header">
                    <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Add Grading Category</h3>
                    <button type="button" class="btn-icon-action" style="color: #ffffff; background: rgba(255,255,255,0.15); border: none;"
                        onclick="closeModal('addCategoryModal')">&times;</button>
                </div>
                <form action="{{ route('superadmin.grades.category.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="class_section_subject_id" value="{{ $currentSectionSubject->id }}">
                    <div class="modal-body">
                        @if ($selectedPeriod)
                            <input type="hidden" name="academic_period" value="{{ $selectedPeriod }}">
                            <div class="form-group">
                                <label>Academic Period</label>
                                <input type="text" class="form-control" value="{{ $selectedPeriod }}" readonly
                                    style="background: #f1f5f9; font-weight: 700; color: var(--primary-navy);">
                            </div>
                        @else
                            <div class="form-group">
                                <label>Academic Period</label>
                                <select name="academic_period" class="form-control" required>
                                    @foreach ($availablePeriods as $pOpt)
                                        <option value="{{ $pOpt }}">{{ $pOpt }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="form-group">
                            <label>Category Name</label>
                            <input type="text" name="name" class="form-control"
                                placeholder="e.g. Written Works, Performance Tasks, Exam" required>
                        </div>
                        <div class="form-group">
                            <label>Weight Percentage (%)</label>
                            <input type="number" name="weight" class="form-control" placeholder="e.g. 25, 45, 30"
                                min="0" max="100" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary"
                            onclick="closeModal('addCategoryModal')">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary">+ Create Category</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Task Modal -->
        <div class="modal-overlay" id="addTaskModal">
            <div class="modal-card">
                <div class="modal-header">
                    <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Add Grading Task</h3>
                    <button type="button" class="btn-icon-action" style="color: #ffffff; background: rgba(255,255,255,0.15); border: none;"
                        onclick="closeModal('addTaskModal')">&times;</button>
                </div>
                <form action="{{ route('superadmin.grades.task.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Grading Category</label>
                            <select name="grading_category_id" id="addTaskCategorySelect" class="form-control" required>
                                @php
                                    $periodCategories = $selectedPeriod
                                        ? $categories->where('academic_period', $selectedPeriod)
                                        : $categories;
                                    if ($periodCategories->isEmpty()) {
                                        $periodCategories = $categories;
                                    }
                                @endphp
                                @foreach ($periodCategories as $cOption)
                                    <option value="{{ $cOption->id }}">
                                        [{{ $cOption->academic_period }}] {{ $cOption->name }}
                                        ({{ number_format($cOption->weight, 0) }}%)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Task Name</label>
                            <input type="text" name="task_name" class="form-control"
                                placeholder="e.g. Quiz 1, CLI Project, Midterm Exam" required>
                        </div>
                        <div class="form-group">
                            <label>Max Score (Points)</label>
                            <input type="number" name="max_score" class="form-control" placeholder="e.g. 50, 100"
                                min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Task Date</label>
                            <input type="date" name="task_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" onclick="closeModal('addTaskModal')">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary">+ Create Task</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        function openModal(id) {
            const el = document.getElementById(id);
            if (el) el.style.display = 'flex';
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = '{{ csrf_token() }}';
            const updateScoreUrl = '{{ route('superadmin.grades.update_score') }}';

            // Click outside modal card to close modal
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal-overlay')) {
                    e.target.style.display = 'none';
                }
            });

            const inputs = document.querySelectorAll('.score-input-cell');

            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    saveScore(this);
                    recalculateRowGrades(this.closest('tr'));
                });

                input.addEventListener('keyup', function() {
                    recalculateRowGrades(this.closest('tr'));
                });
            });

            function saveScore(inputElement) {
                const taskId = inputElement.getAttribute('data-task-id');
                const enrollmentId = inputElement.getAttribute('data-enrollment-id');
                const scoreVal = inputElement.value;

                inputElement.classList.remove('saved-success');
                inputElement.classList.add('saving');

                fetch(updateScoreUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            grading_task_id: taskId,
                            enrollment_id: enrollmentId,
                            score: scoreVal
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        inputElement.classList.remove('saving');
                        if (data.success) {
                            inputElement.classList.add('saved-success');
                            setTimeout(() => {
                                inputElement.classList.remove('saved-success');
                            }, 2000);
                        }
                    })
                    .catch(err => {
                        console.error('Error saving score:', err);
                        inputElement.classList.remove('saving');
                    });
            }

            function recalculateRowGrades(row) {
                if (!row) return;

                const enrollmentId = row.getAttribute('data-student-row');
                const rowInputs = row.querySelectorAll('.score-input-cell');

                let categoriesMap = {};

                rowInputs.forEach(input => {
                    const catId = input.getAttribute('data-cat-id');
                    const catWeight = parseFloat(input.getAttribute('data-cat-weight')) || 0;
                    const maxScore = parseFloat(input.getAttribute('data-max-score')) || 0;
                    const val = parseFloat(input.value);

                    if (!categoriesMap[catId]) {
                        categoriesMap[catId] = {
                            earned: 0,
                            max: 0,
                            weight: catWeight
                        };
                    }

                    categoriesMap[catId].max += maxScore;
                    if (!isNaN(val)) {
                        categoriesMap[catId].earned += val;
                    }
                });

                let grandTotal = 0;

                for (let catId in categoriesMap) {
                    const cat = categoriesMap[catId];
                    const pct = (cat.max > 0) ? (cat.earned / cat.max) * cat.weight : 0;
                    grandTotal += pct;

                    const summaryTd = row.querySelector(`[data-cat-summary="${enrollmentId}-${catId}"]`);
                    if (summaryTd) {
                        const valSpan = summaryTd.querySelector('.cat-pct-val');
                        if (valSpan) {
                            valSpan.textContent = pct.toFixed(2);
                        }
                    }
                }

                const finalSummaryTd = row.querySelector(`[data-final-summary="${enrollmentId}"]`);
                if (finalSummaryTd) {
                    const finalSpan = finalSummaryTd.querySelector('.final-grade-val');
                    if (finalSpan) {
                        finalSpan.textContent = grandTotal.toFixed(2);
                    }
                }
            }
        });
    </script>
@endpush
