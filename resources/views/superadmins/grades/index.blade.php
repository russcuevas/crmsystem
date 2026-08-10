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

        .subject-header-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 0.6rem;
        }

        @media (max-width: 768px) {
            .subject-header-actions {
                justify-content: flex-start;
                width: 100%;
                margin-top: 0.5rem;
            }

            .subject-header-actions button {
                flex: 1 1 calc(50% - 0.6rem);
                min-width: 140px;
                text-align: center;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .subject-header-actions button {
                flex: 1 1 100%;
            }
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

        .sheet-tabs-nav {
            display: flex;
            gap: 0.6rem;
            margin-bottom: 1.25rem;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 0.5rem;
        }

        .sheet-tab-btn {
            padding: 0.65rem 1.35rem;
            border-radius: 10px;
            border: 1.5px solid transparent;
            background: #ffffff;
            color: #64748b;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .sheet-tab-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .sheet-tab-btn.active {
            background: var(--primary-navy, #0f172a);
            color: #ffffff;
            border-color: var(--primary-navy, #0f172a);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .att-status-select {
            padding: 0.35rem 0.55rem;
            border-radius: 8px;
            font-weight: 800;
            font-size: 0.82rem;
            outline: none;
            border: 1.5px solid #cbd5e1;
            cursor: pointer;
            text-align: center;
            transition: all 0.15s ease;
        }

        .att-status-select.P {
            background: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }

        .att-status-select.L {
            background: #fff7ed;
            color: #c2410c;
            border-color: #ffedd5;
        }

        .att-status-select.A {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fca5a5;
        }

        .att-status-select.AEL {
            background: #f3e8ff;
            color: #6b21a8;
            border-color: #d8b4fe;
        }

        .att-status-select.E {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .att-status-select.C {
            background: #450a0a;
            color: #ffffff;
            border-color: #7f1d1d;
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
                            $hasLab = $csSubject->subject && $csSubject->subject->has_lab;
                        @endphp
                        <a href="{{ route('superadmin.grades.page', array_filter(['level' => $selectedLevel, 'section_subject_id' => $csSubject->id, 'semester' => request('semester')])) }}"
                            style="padding: 0.55rem 1rem; border-radius: 12px; font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; border: 1px solid {{ $isSelected ? 'var(--primary-navy)' : 'var(--border-color)' }}; background: {{ $isSelected ? 'var(--primary-navy)' : '#ffffff' }}; color: {{ $isSelected ? '#ffffff' : 'var(--primary-navy)' }};">
                            {{ $csSubject->classSection->section_name ?? 'Section' }} &bull;
                            {{ $csSubject->subject->subject_name ?? 'Subject' }}
                            @if ($hasLab)
                                <span
                                    style="font-size: 0.68rem; font-weight: 800; background: {{ $isSelected ? 'rgba(255,255,255,0.25)' : '#d1fae5' }}; color: {{ $isSelected ? '#ffffff' : '#065f46' }}; border: 1px solid {{ $isSelected ? 'rgba(255,255,255,0.4)' : '#a7f3d0' }}; padding: 0.12rem 0.45rem; border-radius: 6px; margin-left: 3px;">
                                    [LAB and LEC]
                                </span>
                            @endif
                            <span
                                style="font-size: 0.7rem; font-weight: 600; opacity: 0.85; margin-left: 4px; padding: 0.15rem 0.45rem; background: rgba(255, 255, 255, 0.2); border-radius: 6px;">
                                {{ $gradeLevelName ? $gradeLevelName : $lvlCode }}
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
                            style="border-radius: 20px; padding: 0.4rem 1rem; font-size: 0.8rem;">
                            {{ $period }}
                        </a>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Subject Overview Header Card -->
        <div class="card"
            style="margin-bottom: 1.5rem; background: linear-gradient(135deg, var(--primary-navy, #0f172a) 0%, #1e293b 100%); color: #ffffff; border-radius: 16px; border: none; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15);">
            <div class="card-body"
                style="padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.25rem;">
                <div>
                    <div
                        style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.85;">
                        <span>Academic Level: <strong>{{ $currGradeLevelName }}</strong></span>
                        @if ($currIsSemestral)
                            <span>&bull;</span>
                            <span
                                style="color: #a7f3d0; background: rgba(16, 185, 129, 0.2); padding: 0.15rem 0.55rem; border-radius: 10px;">
                                {{ request('semester', '1st Semester') }}
                            </span>
                        @endif
                        @if ($selectedPeriod)
                            <span>&bull;</span>
                            <span style="color: var(--accent-gold);">{{ $selectedPeriod }} Period</span>
                        @endif
                    </div>
                    <h2
                        style="font-size: 1.35rem; font-weight: 800; margin: 0.35rem 0; color: #ffffff; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        {{ $currentSectionSubject->subject->subject_name ?? 'Subject' }}
                        ({{ $currentSectionSubject->subject->subject_code ?? 'CODE' }})
                        @if ($currentSectionSubject->subject && $currentSectionSubject->subject->has_lab)
                            <span
                                style="font-size: 0.72rem; font-weight: 800; background: #10b981; color: #ffffff; padding: 0.2rem 0.6rem; border-radius: 8px; vertical-align: middle;">
                                [LAB and LEC]
                            </span>
                        @endif
                    </h2>
                    <div style="font-size: 0.85rem; color: #94a3b8;">
                        Grade Level: <strong style="color: #60a5fa;">{{ $currGradeLevelName }}</strong> <br>
                        Section: <strong
                            style="color: #60a5fa">{{ $currentSectionSubject->classSection->section_name ?? 'N/A' }}</strong>
                        <br>
                        @if ($currIsSemestral)
                            Subject Semester: <strong
                                style="color: #cbd5e1;">{{ $currentSectionSubject->subject->semester ?? request('semester', '1st Semester') }}</strong>
                            <br>
                        @endif
                        Assigned Teacher:
                        <strong
                            style="color: #60a5fa">{{ $currentSectionSubject->teacher ? $currentSectionSubject->teacher->first_name . ' ' . $currentSectionSubject->teacher->last_name : 'Unassigned' }}</strong>
                    </div>
                </div>
                <div class="subject-header-actions">
                    @if ($currentSectionSubject && $currentSectionSubject->subject && $currentSectionSubject->subject->has_lab)
                        <button type="button" class="btn-secondary"
                            style="background: #e0e7ff; color: #3730a3; border: 1.5px solid #c7d2fe; font-weight: 700; border-radius: 8px; padding: 0.5rem 0.85rem; font-size: 0.82rem; cursor: pointer; transition: all 0.2s;"
                            onclick="openModal('editLecLabWeightModal')">
                            ⚙️ Edit Lec/Lab Ratio ({{ number_format($currentSectionSubject->subject->lecture_weight, 0) }}%
                            / {{ number_format($currentSectionSubject->subject->lab_weight, 0) }}%)
                        </button>
                    @endif
                    <button type="button" class="btn-secondary"
                        style="background: #1e1b4b; color: #e0e7ff; border: 1.5px solid #4338ca; font-weight: 700; border-radius: 8px; padding: 0.5rem 0.85rem; font-size: 0.82rem; cursor: pointer; transition: all 0.2s;"
                        onclick="openModal('gradeBreakdownModal')">
                        📊 Grade Breakdown & Final SG
                    </button>
                    <button class="btn-primary"
                        style="background: var(--accent-gold); color: #0f172a; padding: 0.5rem 0.95rem; font-size: 0.82rem;"
                        onclick="openModal('addCategoryModal')">+ Add Category</button>
                    <button class="btn-primary" style="padding: 0.5rem 0.95rem; font-size: 0.82rem;"
                        onclick="openModal('addTaskModal')">+ Add Task</button>
                </div>
            </div>
        </div>

        <!-- Toggle Button for Categories & Tasks Breakdown -->
        <div style="margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
            <button type="button" class="btn-secondary" id="toggleBreakdownBtn" onclick="toggleCategoryBreakdown()"
                style="border-radius: 12px; font-size: 0.85rem; font-weight: 700; padding: 0.55rem 1.1rem; display: inline-flex; align-items: center; gap: 0.55rem; background: #ffffff; border: 1.5px solid #cbd5e1; color: var(--primary-navy, #0f172a); box-shadow: 0 1px 3px rgba(0,0,0,0.05); cursor: pointer; transition: all 0.2s ease;">
                <svg id="toggleBreakdownIcon" width="16" height="16" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" style="transition: transform 0.25s ease;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
                <span id="toggleBreakdownText">Show Grading Categories & Tasks Breakdown
                    ({{ $categories->count() }})</span>
            </button>
        </div>

        <!-- Grading Categories & Tasks Breakdown Wrapper (Hidden by default) -->
        <div id="categoryBreakdownWrapper" style="display: none; margin-bottom: 1.5rem;">
            @php
                $hasLabSubject =
                    $currentSectionSubject &&
                    $currentSectionSubject->subject &&
                    $currentSectionSubject->subject->has_lab;
                if ($hasLabSubject) {
                    $lecCategoriesGroup = $categories->filter(fn($c) => $c->component_type !== 'laboratory');
                    $labCategoriesGroup = $categories->filter(fn($c) => $c->component_type === 'laboratory');
                } else {
                    $lecCategoriesGroup = $categories;
                    $labCategoriesGroup = collect();
                }
            @endphp

            @if ($hasLabSubject)
                <!-- 2-COLUMN SIDE-BY-SIDE GRID FOR LECTURE AND LABORATORY CATEGORIES -->
                <div
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; align-items: start;">
                    <!-- LEFT COLUMN: LECTURE COMPONENT SECTION -->
                    <div>
                        <div
                            style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 1.15rem; background: linear-gradient(90deg, rgba(59, 130, 246, 0.12) 0%, rgba(239, 246, 255, 0.5) 100%); border-left: 4px solid #3b82f6; border-radius: 12px; margin-bottom: 1.1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                            <div style="display: flex; align-items: center; gap: 0.55rem;">
                                <span style="font-size: 1.2rem;">📘</span>
                                <div>
                                    <h3
                                        style="font-size: 1rem; font-weight: 800; color: #1e40af; margin: 0; display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                                        Lecture Categories
                                        <span class="badge"
                                            style="background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.7rem; font-weight: 800; padding: 0.12rem 0.5rem; border-radius: 12px;">
                                            Ratio: {{ number_format($currentSectionSubject->subject->lecture_weight, 0) }}%
                                        </span>
                                    </h3>
                                    <div style="font-size: 0.73rem; color: #3b82f6; font-weight: 600; margin-top: 2px;">
                                        Assigned Categories & Tasks for Lecture
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span class="badge"
                                    style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.78rem; font-weight: 800; padding: 0.3rem 0.65rem; border-radius: 20px;">
                                    Weight: {{ number_format($lecCategoriesGroup->sum('weight'), 0) }}%
                                </span>
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            @forelse($lecCategoriesGroup as $cat)
                                <div class="card"
                                    style="border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04); overflow: hidden; border-top: 3.5px solid #3b82f6;">
                                    <div class="card-header"
                                        style="background: #ffffff; padding: 0.9rem 1.1rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9;">
                                        <div>
                                            <div class="card-title"
                                                style="font-size: 0.95rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 0.5rem;">
                                                {{ $cat->name }}
                                                <button class="btn-icon-action" title="Edit Category"
                                                    onclick="openModal('editCategoryModal_{{ $cat->id }}')">
                                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <form action="{{ route('superadmin.grades.category.destroy', $cat->id) }}"
                                                    method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-icon-action danger"
                                                        title="Delete Category">
                                                        <svg width="14" height="14" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                            <div
                                                style="font-size: 0.72rem; color: #64748b; margin-top: 3px; display: flex; align-items: center; gap: 0.4rem;">
                                                <span>Period: <strong>{{ $cat->academic_period }}</strong></span>
                                                <span>&bull;</span>
                                                <span class="badge"
                                                    style="font-size: 0.65rem; padding: 0.12rem 0.45rem; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-weight: 800; border-radius: 6px;">
                                                    📘 LECTURE
                                                </span>
                                            </div>
                                        </div>
                                        <span class="badge"
                                            style="font-size: 0.82rem; font-weight: 800; padding: 0.35rem 0.7rem; border-radius: 20px; background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe;">
                                            Weight: {{ number_format($cat->weight, 0) }}%
                                        </span>
                                    </div>
                                    <div class="card-body" style="padding: 0.85rem 1.1rem; background: #ffffff;">
                                        @forelse($cat->gradingTasks as $task)
                                            <div
                                                style="display: flex; align-items: center; justify-content: space-between; padding: 0.55rem 0; border-bottom: 1px dashed #e2e8f0;">
                                                <div>
                                                    <div
                                                        style="font-size: 0.85rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 0.35rem;">
                                                        <span>📝</span>
                                                        {{ $task->task_name }}
                                                        <button class="btn-icon-action" title="Edit Task"
                                                            onclick="openModal('editTaskModal_{{ $task->id }}')">
                                                            <svg width="13" height="13" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                        <form
                                                            action="{{ route('superadmin.grades.task.destroy', $task->id) }}"
                                                            method="POST" style="display: inline;"
                                                            onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn-icon-action danger"
                                                                title="Delete Task">
                                                                <svg width="13" height="13" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <div
                                                        style="font-size: 0.72rem; color: #94a3b8; margin-left: 1.35rem; margin-top: 2px;">
                                                        {{ $task->task_date ? $task->task_date->format('M d, Y') : 'No Date' }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="badge"
                                                        style="font-size: 0.75rem; font-weight: 700; background: #f1f5f9; color: #334155; padding: 0.22rem 0.55rem; border-radius: 12px;">
                                                        Max: {{ number_format($task->max_score, 0) }} pts
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Edit Task Modal -->
                                            <div class="modal-overlay" id="editTaskModal_{{ $task->id }}">
                                                <div class="modal-card">
                                                    <div class="modal-header">
                                                        <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Edit
                                                            Grading Task</h3>
                                                        <button type="button" class="btn-icon-action"
                                                            style="color: #ffffff; background: rgba(255,255,255,0.15); border: none;"
                                                            onclick="closeModal('editTaskModal_{{ $task->id }}')">&times;</button>
                                                    </div>
                                                    <form action="{{ route('superadmin.grades.task.update', $task->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label>Grading Category</label>
                                                                <select name="grading_category_id" class="form-control"
                                                                    required>
                                                                    @foreach ($categories as $cOption)
                                                                        <option value="{{ $cOption->id }}"
                                                                            {{ $cOption->id == $task->grading_category_id ? 'selected' : '' }}>
                                                                            {{ $cOption->academic_period }} -
                                                                            {{ $cOption->name }}
                                                                            ({{ number_format($cOption->weight, 0) }}%)
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Task Name</label>
                                                                <input type="text" name="task_name"
                                                                    class="form-control" value="{{ $task->task_name }}"
                                                                    required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Max Score (Points)</label>
                                                                <input type="number" name="max_score"
                                                                    class="form-control"
                                                                    value="{{ number_format($task->max_score, 0) }}"
                                                                    min="1" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Task Date</label>
                                                                <input type="date" name="task_date"
                                                                    class="form-control"
                                                                    value="{{ $task->task_date ? $task->task_date->format('Y-m-d') : '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn-secondary"
                                                                onclick="closeModal('editTaskModal_{{ $task->id }}')">
                                                                Cancel
                                                            </button>
                                                            <button type="submit" class="btn-primary">Save
                                                                Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @empty
                                            <div
                                                style="font-size: 0.8rem; color: #94a3b8; text-align: center; padding: 0.75rem 0;">
                                                No tasks created for {{ $cat->academic_period }} {{ $cat->name }}.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Edit Category Modal -->
                                <div class="modal-overlay" id="editCategoryModal_{{ $cat->id }}">
                                    <div class="modal-card">
                                        <div class="modal-header">
                                            <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Edit Grading Category
                                            </h3>
                                            <button type="button" class="btn-icon-action"
                                                style="color: #ffffff; background: rgba(255,255,255,0.15); border: none;"
                                                onclick="closeModal('editCategoryModal_{{ $cat->id }}')">&times;</button>
                                        </div>
                                        <form action="{{ route('superadmin.grades.category.update', $cat->id) }}"
                                            method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Academic Period</label>
                                                    <select name="academic_period" class="form-control" required>
                                                        @foreach ($availablePeriods as $pOpt)
                                                            <option value="{{ $pOpt }}"
                                                                {{ $pOpt == $cat->academic_period ? 'selected' : '' }}>
                                                                {{ $pOpt }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Category Name</label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ $cat->name }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Subject Component Type <span
                                                            style="color: #ef4444;">*</span></label>
                                                    <select name="component_type" class="form-control" required>
                                                        <option value="lecture"
                                                            {{ $cat->component_type === 'lecture' ? 'selected' : '' }}>
                                                            Lecture Component (Lec
                                                            {{ number_format($currentSectionSubject->subject->lecture_weight, 0) }}%
                                                            ratio)
                                                        </option>
                                                        <option value="laboratory"
                                                            {{ $cat->component_type === 'laboratory' ? 'selected' : '' }}>
                                                            Laboratory Component (Lab
                                                            {{ number_format($currentSectionSubject->subject->lab_weight, 0) }}%
                                                            ratio)
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Weight Percentage (%)</label>
                                                    <input type="number" name="weight" class="form-control"
                                                        value="{{ number_format($cat->weight, 0) }}" min="0"
                                                        max="100" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn-secondary"
                                                    onclick="closeModal('editCategoryModal_{{ $cat->id }}')">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="card" style="border-radius: 12px;">
                                    <div class="card-body"
                                        style="text-align: center; padding: 1.5rem; color: var(--text-muted);">
                                        No Lecture categories configured for
                                        {{ $selectedPeriod ? $selectedPeriod : 'this period' }} yet.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: LABORATORY COMPONENT SECTION -->
                    <div>
                        <div
                            style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 1.15rem; background: linear-gradient(90deg, rgba(16, 185, 129, 0.12) 0%, rgba(236, 253, 245, 0.5) 100%); border-left: 4px solid #10b981; border-radius: 12px; margin-bottom: 1.1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                            <div style="display: flex; align-items: center; gap: 0.55rem;">
                                <span style="font-size: 1.2rem;">🔬</span>
                                <div>
                                    <h3
                                        style="font-size: 1rem; font-weight: 800; color: #065f46; margin: 0; display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                                        Laboratory Categories
                                        <span class="badge"
                                            style="background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; font-size: 0.7rem; font-weight: 800; padding: 0.12rem 0.5rem; border-radius: 12px;">
                                            Ratio: {{ number_format($currentSectionSubject->subject->lab_weight, 0) }}%
                                        </span>
                                    </h3>
                                    <div style="font-size: 0.73rem; color: #10b981; font-weight: 600; margin-top: 2px;">
                                        Assigned Categories & Tasks for Laboratory
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span class="badge"
                                    style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-size: 0.78rem; font-weight: 800; padding: 0.3rem 0.65rem; border-radius: 20px;">
                                    Weight: {{ number_format($labCategoriesGroup->sum('weight'), 0) }}%
                                </span>
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            @forelse($labCategoriesGroup as $cat)
                                <div class="card"
                                    style="border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04); overflow: hidden; border-top: 3.5px solid #10b981;">
                                    <div class="card-header"
                                        style="background: #ffffff; padding: 0.9rem 1.1rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9;">
                                        <div>
                                            <div class="card-title"
                                                style="font-size: 0.95rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 0.5rem;">
                                                {{ $cat->name }}
                                                <button class="btn-icon-action" title="Edit Category"
                                                    onclick="openModal('editCategoryModal_{{ $cat->id }}')">
                                                    <svg width="14" height="14" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <form action="{{ route('superadmin.grades.category.destroy', $cat->id) }}"
                                                    method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-icon-action danger"
                                                        title="Delete Category">
                                                        <svg width="14" height="14" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                            <div
                                                style="font-size: 0.72rem; color: #64748b; margin-top: 3px; display: flex; align-items: center; gap: 0.4rem;">
                                                <span>Period: <strong>{{ $cat->academic_period }}</strong></span>
                                                <span>&bull;</span>
                                                <span class="badge"
                                                    style="font-size: 0.65rem; padding: 0.12rem 0.45rem; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-weight: 800; border-radius: 6px;">
                                                    🔬 LABORATORY
                                                </span>
                                            </div>
                                        </div>
                                        <span class="badge"
                                            style="font-size: 0.82rem; font-weight: 800; padding: 0.35rem 0.7rem; border-radius: 20px; background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;">
                                            Weight: {{ number_format($cat->weight, 0) }}%
                                        </span>
                                    </div>
                                    <div class="card-body" style="padding: 0.85rem 1.1rem; background: #ffffff;">
                                        @forelse($cat->gradingTasks as $task)
                                            <div
                                                style="display: flex; align-items: center; justify-content: space-between; padding: 0.55rem 0; border-bottom: 1px dashed #e2e8f0;">
                                                <div>
                                                    <div
                                                        style="font-size: 0.85rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 0.35rem;">
                                                        <span>🔬</span>
                                                        {{ $task->task_name }}
                                                        <button class="btn-icon-action" title="Edit Task"
                                                            onclick="openModal('editTaskModal_{{ $task->id }}')">
                                                            <svg width="13" height="13" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                        <form
                                                            action="{{ route('superadmin.grades.task.destroy', $task->id) }}"
                                                            method="POST" style="display: inline;"
                                                            onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn-icon-action danger"
                                                                title="Delete Task">
                                                                <svg width="13" height="13" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <div
                                                        style="font-size: 0.72rem; color: #94a3b8; margin-left: 1.35rem; margin-top: 2px;">
                                                        {{ $task->task_date ? $task->task_date->format('M d, Y') : 'No Date' }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="badge"
                                                        style="font-size: 0.75rem; font-weight: 700; background: #f1f5f9; color: #334155; padding: 0.22rem 0.55rem; border-radius: 12px;">
                                                        Max: {{ number_format($task->max_score, 0) }} pts
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Edit Task Modal -->
                                            <div class="modal-overlay" id="editTaskModal_{{ $task->id }}">
                                                <div class="modal-card">
                                                    <div class="modal-header">
                                                        <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Edit
                                                            Grading Task</h3>
                                                        <button type="button" class="btn-icon-action"
                                                            style="color: #ffffff; background: rgba(255,255,255,0.15); border: none;"
                                                            onclick="closeModal('editTaskModal_{{ $task->id }}')">&times;</button>
                                                    </div>
                                                    <form action="{{ route('superadmin.grades.task.update', $task->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label>Grading Category</label>
                                                                <select name="grading_category_id" class="form-control"
                                                                    required>
                                                                    @foreach ($categories as $cOption)
                                                                        <option value="{{ $cOption->id }}"
                                                                            {{ $cOption->id == $task->grading_category_id ? 'selected' : '' }}>
                                                                            {{ $cOption->academic_period }} -
                                                                            {{ $cOption->name }}
                                                                            ({{ number_format($cOption->weight, 0) }}%)
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Task Name</label>
                                                                <input type="text" name="task_name"
                                                                    class="form-control" value="{{ $task->task_name }}"
                                                                    required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Max Score (Points)</label>
                                                                <input type="number" name="max_score"
                                                                    class="form-control"
                                                                    value="{{ number_format($task->max_score, 0) }}"
                                                                    min="1" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Task Date</label>
                                                                <input type="date" name="task_date"
                                                                    class="form-control"
                                                                    value="{{ $task->task_date ? $task->task_date->format('Y-m-d') : '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn-secondary"
                                                                onclick="closeModal('editTaskModal_{{ $task->id }}')">
                                                                Cancel
                                                            </button>
                                                            <button type="submit" class="btn-primary">Save
                                                                Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @empty
                                            <div
                                                style="font-size: 0.8rem; color: #94a3b8; text-align: center; padding: 0.75rem 0;">
                                                No tasks created for {{ $cat->academic_period }} {{ $cat->name }}.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Edit Category Modal -->
                                <div class="modal-overlay" id="editCategoryModal_{{ $cat->id }}">
                                    <div class="modal-card">
                                        <div class="modal-header">
                                            <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Edit Grading Category
                                            </h3>
                                            <button type="button" class="btn-icon-action"
                                                style="color: #ffffff; background: rgba(255,255,255,0.15); border: none;"
                                                onclick="closeModal('editCategoryModal_{{ $cat->id }}')">&times;</button>
                                        </div>
                                        <form action="{{ route('superadmin.grades.category.update', $cat->id) }}"
                                            method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Academic Period</label>
                                                    <select name="academic_period" class="form-control" required>
                                                        @foreach ($availablePeriods as $pOpt)
                                                            <option value="{{ $pOpt }}"
                                                                {{ $pOpt == $cat->academic_period ? 'selected' : '' }}>
                                                                {{ $pOpt }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Category Name</label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ $cat->name }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Subject Component Type <span
                                                            style="color: #ef4444;">*</span></label>
                                                    <select name="component_type" class="form-control" required>
                                                        <option value="lecture"
                                                            {{ $cat->component_type === 'lecture' ? 'selected' : '' }}>
                                                            Lecture Component (Lec
                                                            {{ number_format($currentSectionSubject->subject->lecture_weight, 0) }}%
                                                            ratio)
                                                        </option>
                                                        <option value="laboratory"
                                                            {{ $cat->component_type === 'laboratory' ? 'selected' : '' }}>
                                                            Laboratory Component (Lab
                                                            {{ number_format($currentSectionSubject->subject->lab_weight, 0) }}%
                                                            ratio)
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Weight Percentage (%)</label>
                                                    <input type="number" name="weight" class="form-control"
                                                        value="{{ number_format($cat->weight, 0) }}" min="0"
                                                        max="100" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn-secondary"
                                                    onclick="closeModal('editCategoryModal_{{ $cat->id }}')">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="card" style="border-radius: 12px;">
                                    <div class="card-body"
                                        style="text-align: center; padding: 1.5rem; color: var(--text-muted);">
                                        No Laboratory categories configured for
                                        {{ $selectedPeriod ? $selectedPeriod : 'this period' }} yet.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @else
                <!-- STANDARD SUBJECT CATEGORIES SECTION -->
                <div
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                    @forelse($categories as $cat)
                        <div class="card"
                            style="border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04); overflow: hidden; border-top: 3.5px solid var(--primary-navy, #0f172a);">
                            <div class="card-header"
                                style="background: #ffffff; padding: 1rem 1.15rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9;">
                                <div>
                                    <div class="card-title"
                                        style="font-size: 0.95rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 0.5rem;">
                                        {{ $cat->name }}
                                        <button class="btn-icon-action" title="Edit Category"
                                            onclick="openModal('editCategoryModal_{{ $cat->id }}')">
                                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('superadmin.grades.category.destroy', $cat->id) }}"
                                            method="POST" style="display: inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon-action danger"
                                                title="Delete Category">
                                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    <div style="font-size: 0.72rem; color: #64748b; margin-top: 3px;">
                                        Period: <strong>{{ $cat->academic_period }}</strong>
                                    </div>
                                </div>
                                <span class="badge badge-active"
                                    style="font-size: 0.82rem; font-weight: 800; padding: 0.35rem 0.7rem; border-radius: 20px;">
                                    Weight: {{ number_format($cat->weight, 0) }}%
                                </span>
                            </div>
                            <div class="card-body" style="padding: 0.85rem 1.15rem; background: #ffffff;">
                                @forelse($cat->gradingTasks as $task)
                                    <div
                                        style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0; border-bottom: 1px dashed #e2e8f0;">
                                        <div>
                                            <div
                                                style="font-size: 0.85rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 0.35rem;">
                                                <span>📝</span>
                                                {{ $task->task_name }}
                                                <button class="btn-icon-action" title="Edit Task"
                                                    onclick="openModal('editTaskModal_{{ $task->id }}')">
                                                    <svg width="13" height="13" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <form action="{{ route('superadmin.grades.task.destroy', $task->id) }}"
                                                    method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-icon-action danger"
                                                        title="Delete Task">
                                                        <svg width="13" height="13" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                            <div
                                                style="font-size: 0.72rem; color: #94a3b8; margin-left: 1.35rem; margin-top: 2px;">
                                                {{ $task->task_date ? $task->task_date->format('M d, Y') : 'No Date' }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge"
                                                style="font-size: 0.75rem; font-weight: 700; background: #f1f5f9; color: #334155; padding: 0.25rem 0.6rem; border-radius: 12px;">
                                                Max: {{ number_format($task->max_score, 0) }} pts
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Edit Task Modal -->
                                    <div class="modal-overlay" id="editTaskModal_{{ $task->id }}">
                                        <div class="modal-card">
                                            <div class="modal-header">
                                                <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Edit Grading Task
                                                </h3>
                                                <button type="button" class="btn-icon-action"
                                                    style="color: #ffffff; background: rgba(255,255,255,0.15); border: none;"
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
                                                                    {{ $cOption->academic_period }} -
                                                                    {{ $cOption->name }}
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
                                                            value="{{ number_format($task->max_score, 0) }}"
                                                            min="1" required>
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
                                                        Cancel
                                                    </button>
                                                    <button type="submit" class="btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div style="font-size: 0.8rem; color: #94a3b8; text-align: center; padding: 1rem 0;">
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
                                    <button type="button" class="btn-icon-action"
                                        style="color: #ffffff; background: rgba(255,255,255,0.15); border: none;"
                                        onclick="closeModal('editCategoryModal_{{ $cat->id }}')">&times;</button>
                                </div>
                                <form action="{{ route('superadmin.grades.category.update', $cat->id) }}"
                                    method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Academic Period</label>
                                            <select name="academic_period" class="form-control" required>
                                                @foreach ($availablePeriods as $pOpt)
                                                    <option value="{{ $pOpt }}"
                                                        {{ $pOpt == $cat->academic_period ? 'selected' : '' }}>
                                                        {{ $pOpt }}</option>
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
                                                value="{{ number_format($cat->weight, 0) }}" min="0"
                                                max="100" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn-secondary"
                                            onclick="closeModal('editCategoryModal_{{ $cat->id }}')">
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="card" style="grid-column: 1 / -1; border-radius: 12px;">
                            <div class="card-body" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                No grading categories configured for
                                {{ $selectedPeriod ? $selectedPeriod : 'this period' }} yet.
                            </div>
                        </div>
                    @endforelse
                </div>
            @endif
        </div> <!-- End #categoryBreakdownWrapper -->

        <!-- Sheet Tabs Navigation -->
        <div class="sheet-tabs-nav">
            <button type="button" id="tab-btn-grades" class="sheet-tab-btn active" onclick="switchSheetTab('grades')">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Class Record & Grades
            </button>
            <button type="button" id="tab-btn-attendance" class="sheet-tab-btn" onclick="switchSheetTab('attendance')">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Class Attendance Sheet
            </button>
        </div>

        <!-- Panel 1: Student Scores Matrix Class Record Table (Editable Table) -->
        <div id="sheet-grades-panel">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"
                        style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Class Record Sheet
                            @if ($selectedPeriod)
                                &bull; {{ $selectedPeriod }} Period
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    @php
                        $hasLab = $currentSectionSubject->subject->has_lab ?? false;
                        $lecWeightRatio = ($currentSectionSubject->subject->lecture_weight ?? 70) / 100;
                        $labWeightRatio = ($currentSectionSubject->subject->lab_weight ?? 30) / 100;

                        if ($hasLab) {
                            $lecCategories = $categories->filter(fn($c) => $c->component_type !== 'laboratory');
                            $labCategories = $categories->filter(fn($c) => $c->component_type === 'laboratory');
                        } else {
                            $lecCategories = $categories;
                            $labCategories = collect();
                        }

                        $lecTotalWeight = $lecCategories->sum('weight');
                        $labTotalWeight = $labCategories->sum('weight');
                    @endphp

                    <div class="table-responsive">
                        <table class="custom-table" id="classRecordTable">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    @if ($hasLab)
                                        <th
                                            style="text-align: center; background: rgba(59, 130, 246, 0.08); color: #1e40af;">
                                            Lec Grade
                                            <div style="font-size: 0.65rem; font-weight: 700; color: #2563eb;">
                                                ({{ number_format($currentSectionSubject->subject->lecture_weight, 0) }}%)
                                            </div>
                                        </th>
                                        <th
                                            style="text-align: center; background: rgba(16, 185, 129, 0.08); color: #065f46;">
                                            Lab Grade
                                            <div style="font-size: 0.65rem; font-weight: 700; color: #059669;">
                                                ({{ number_format($currentSectionSubject->subject->lab_weight, 0) }}%)
                                            </div>
                                        </th>
                                        <th
                                            style="text-align: center; background: rgba(16, 185, 129, 0.18); color: #064e3b; font-weight: 800;">
                                            {{ $selectedPeriod ? strtoupper($selectedPeriod) . ' GRADE' : 'FINAL GRADE' }}
                                            <div style="font-size: 0.65rem; font-weight: 700; color: #047857;">
                                                (Lec + Lab)
                                            </div>
                                        </th>
                                    @else
                                        <th
                                            style="text-align: center; background: rgba(16, 185, 129, 0.12); color: #064e3b; font-weight: 800;">
                                            {{ $selectedPeriod ? strtoupper($selectedPeriod) . ' GRADE' : 'FINAL GRADE' }}
                                        </th>
                                    @endif
                                    <th style="text-align: center; width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enrolledStudents as $enrollment)
                                    @php
                                        $lecCategoryPctSum = 0;
                                        $labCategoryPctSum = 0;
                                        $generalCategoryPctSum = 0;

                                        if ($hasLab) {
                                            foreach ($lecCategories as $cat) {
                                                $catEarned = 0;
                                                $catMax = 0;
                                                foreach ($cat->gradingTasks as $task) {
                                                    $ts = $enrollment->taskScores
                                                        ->where('grading_task_id', $task->id)
                                                        ->first();
                                                    if ($ts && $ts->score !== null) {
                                                        $catEarned += $ts->score;
                                                    }
                                                    $catMax += $task->max_score;
                                                }
                                                $lecCategoryPctSum +=
                                                    $catMax > 0 ? ($catEarned / $catMax) * $cat->weight : 0;
                                            }

                                            foreach ($labCategories as $cat) {
                                                $catEarned = 0;
                                                $catMax = 0;
                                                foreach ($cat->gradingTasks as $task) {
                                                    $ts = $enrollment->taskScores
                                                        ->where('grading_task_id', $task->id)
                                                        ->first();
                                                    if ($ts && $ts->score !== null) {
                                                        $catEarned += $ts->score;
                                                    }
                                                    $catMax += $task->max_score;
                                                }
                                                $labCategoryPctSum +=
                                                    $catMax > 0 ? ($catEarned / $catMax) * $cat->weight : 0;
                                            }

                                            $lecSubtotal =
                                                $lecTotalWeight > 0
                                                    ? ($lecCategoryPctSum / $lecTotalWeight) * 100
                                                    : $lecCategoryPctSum;
                                            $lecWeightedShare = $lecSubtotal * $lecWeightRatio;

                                            $labSubtotal =
                                                $labTotalWeight > 0
                                                    ? ($labCategoryPctSum / $labTotalWeight) * 100
                                                    : $labCategoryPctSum;
                                            $labWeightedShare = $labSubtotal * $labWeightRatio;

                                            $totalFinalGrade =
                                                $lecTotalWeight > 0 && $labTotalWeight > 0
                                                    ? $lecWeightedShare + $labWeightedShare
                                                    : $lecCategoryPctSum + $labCategoryPctSum;
                                        } else {
                                            foreach ($categories as $cat) {
                                                $catEarned = 0;
                                                $catMax = 0;
                                                foreach ($cat->gradingTasks as $task) {
                                                    $ts = $enrollment->taskScores
                                                        ->where('grading_task_id', $task->id)
                                                        ->first();
                                                    if ($ts && $ts->score !== null) {
                                                        $catEarned += $ts->score;
                                                    }
                                                    $catMax += $task->max_score;
                                                }
                                                $generalCategoryPctSum +=
                                                    $catMax > 0 ? ($catEarned / $catMax) * $cat->weight : 0;
                                            }
                                            $totalFinalGrade = $generalCategoryPctSum;
                                        }
                                    @endphp
                                    <tr data-student-row="{{ $enrollment->id }}"
                                        data-has-lab="{{ $hasLab ? '1' : '0' }}"
                                        data-lec-ratio="{{ $lecWeightRatio }}" data-lab-ratio="{{ $labWeightRatio }}">
                                        <td><strong>{{ $enrollment->student->student_number ?? 'N/A' }}</strong></td>
                                        <td>
                                            <strong>{{ $enrollment->student->first_name ?? '' }}
                                                {{ $enrollment->student->last_name ?? '' }}</strong>
                                        </td>

                                        @if ($hasLab)
                                            {{-- LECTURE SUBTOTAL TD --}}
                                            <td style="text-align: center; background: rgba(59, 130, 246, 0.04);"
                                                data-lec-summary="{{ $enrollment->id }}">
                                                <span class="badge"
                                                    style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.85rem; padding: 0.28rem 0.65rem; border-radius: 8px;">
                                                    <span
                                                        class="lec-grade-val">{{ number_format($lecSubtotal, 2) }}</span>%
                                                </span>
                                                <div
                                                    style="font-size: 0.68rem; color: #64748b; margin-top: 3px; font-weight: 600;">
                                                    Share:
                                                    <span
                                                        class="lec-share-val">{{ number_format($lecWeightedShare, 2) }}</span>%
                                                </div>
                                            </td>

                                            {{-- LABORATORY SUBTOTAL TD --}}
                                            <td style="text-align: center; background: rgba(16, 185, 129, 0.04);"
                                                data-lab-summary="{{ $enrollment->id }}">
                                                <span class="badge"
                                                    style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-size: 0.85rem; padding: 0.28rem 0.65rem; border-radius: 8px;">
                                                    <span
                                                        class="lab-grade-val">{{ number_format($labSubtotal, 2) }}</span>%
                                                </span>
                                                <div
                                                    style="font-size: 0.68rem; color: #64748b; margin-top: 3px; font-weight: 600;">
                                                    Share:
                                                    <span
                                                        class="lab-share-val">{{ number_format($labWeightedShare, 2) }}</span>%
                                                </div>
                                            </td>

                                            {{-- FINAL GRADE TD --}}
                                            <td style="text-align: center; background: rgba(16, 185, 129, 0.12);"
                                                data-final-summary="{{ $enrollment->id }}">
                                                <span class="badge badge-active final-grade-badge"
                                                    style="font-size: 0.9rem; padding: 0.35rem 0.8rem; background: #059669; color: #ffffff; border-radius: 8px;">
                                                    <span
                                                        class="final-grade-val">{{ number_format($totalFinalGrade, 2) }}</span>%
                                                </span>
                                            </td>
                                        @else
                                            <td style="text-align: center; background: rgba(16, 185, 129, 0.08);"
                                                data-final-summary="{{ $enrollment->id }}">
                                                <span class="badge badge-active final-grade-badge"
                                                    style="font-size: 0.9rem; padding: 0.35rem 0.8rem; background: #059669; color: #ffffff; border-radius: 8px;">
                                                    <span
                                                        class="final-grade-val">{{ number_format($totalFinalGrade, 2) }}</span>%
                                                </span>
                                            </td>
                                        @endif

                                        <td style="text-align: center;">
                                            <button type="button" class="btn-primary"
                                                style="padding: 0.4rem 0.85rem; font-size: 0.82rem; font-weight: 700; border-radius: 8px; background: #2563eb; color: #ffffff; border: none; display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; box-shadow: 0 2px 4px rgba(37,99,235,0.25);"
                                                onclick="openModal('studentGradeModal_{{ $enrollment->id }}')">
                                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Add Grades
                                            </button>
                                        </td>
                                        <!-- Student Grade Entry Modal -->
                                        <div class="modal-overlay" id="studentGradeModal_{{ $enrollment->id }}">
                                            <div class="modal-card"
                                                style="max-width: {{ $hasLab ? '1200px' : '750px' }}; width: 95%;">
                                                <div class="modal-header"
                                                    style="background: linear-gradient(135deg, var(--primary-navy, #0f172a) 0%, #1e293b 100%); color: #ffffff; padding: 1.15rem 1.5rem;">
                                                    <div>
                                                        <h3
                                                            style="font-size: 1.1rem; font-weight: 800; margin: 0; color: #ffffff; display: flex; align-items: center; gap: 0.5rem;">
                                                            📝 Grade Entry: {{ $enrollment->student->first_name ?? '' }}
                                                            {{ $enrollment->student->last_name ?? '' }}
                                                        </h3>
                                                        <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 2px;">
                                                            Student ID:
                                                            <strong>{{ $enrollment->student->student_number ?? 'N/A' }}</strong>
                                                            &bull;
                                                            {{ $currentSectionSubject->subject->subject_code ?? '' }} -
                                                            {{ $currentSectionSubject->subject->subject_name ?? '' }}
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn-icon-action"
                                                        style="color: #ffffff; background: rgba(255,255,255,0.15); border: none;"
                                                        onclick="closeModal('studentGradeModal_{{ $enrollment->id }}')">&times;</button>
                                                </div>

                                                <div class="modal-body"
                                                    style="max-height: 80vh; overflow-y: auto; padding: 1.5rem;">
                                                    @if ($hasLab)
                                                        <!-- 2-COLUMN GRID: LECTURE LEFT, LABORATORY RIGHT -->
                                                        <div
                                                            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(480px, 1fr)); gap: 1.5rem; align-items: start;">
                                                            <!-- LEFT COLUMN: LECTURE TASKS & SCORES -->
                                                            <div
                                                                style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; border-top: 3.5px solid #3b82f6;">
                                                                <div
                                                                    style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.6rem; margin-bottom: 0.85rem; border-bottom: 1.5px solid #cbd5e1;">
                                                                    <h4
                                                                        style="font-size: 0.92rem; font-weight: 800; color: #1e40af; margin: 0; display: flex; align-items: center; gap: 0.4rem;">
                                                                        📘 Lecture Component
                                                                    </h4>
                                                                    <span class="badge"
                                                                        style="background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.7rem; font-weight: 800; padding: 0.18rem 0.5rem; border-radius: 12px;">
                                                                        Ratio:
                                                                        {{ number_format($currentSectionSubject->subject->lecture_weight, 0) }}%
                                                                    </span>
                                                                </div>

                                                                @forelse($lecCategories as $cat)
                                                                    <div
                                                                        style="margin-bottom: 1rem; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 10px; padding: 0.75rem;">
                                                                        <div
                                                                            style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; border-bottom: 1px dashed #e2e8f0; padding-bottom: 0.35rem;">
                                                                            <span
                                                                                style="font-size: 0.85rem; font-weight: 800; color: #0f172a;">
                                                                                {{ $cat->name }}
                                                                                <small
                                                                                    style="color: #64748b; font-weight: 600;">({{ number_format($cat->weight, 0) }}%)</small>
                                                                            </span>
                                                                            <span
                                                                                style="font-size: 0.7rem; color: #64748b;">Period:
                                                                                <strong>{{ $cat->academic_period }}</strong></span>
                                                                        </div>

                                                                        @forelse($cat->gradingTasks as $task)
                                                                            @php
                                                                                $taskScoreModel = $enrollment->taskScores
                                                                                    ->where(
                                                                                        'grading_task_id',
                                                                                        $task->id,
                                                                                    )
                                                                                    ->first();
                                                                                $scoreValue = $taskScoreModel
                                                                                    ? $taskScoreModel->score
                                                                                    : null;
                                                                            @endphp
                                                                            <div
                                                                                style="display: flex; align-items: center; justify-content: space-between; padding: 0.4rem 0; border-bottom: 1px solid #f1f5f9;">
                                                                                <div>
                                                                                    <div
                                                                                        style="font-size: 0.82rem; font-weight: 700; color: #334155;">
                                                                                        📝 {{ $task->task_name }}</div>
                                                                                    <div
                                                                                        style="font-size: 0.7rem; color: #94a3b8;">
                                                                                        Max:
                                                                                        {{ number_format($task->max_score, 0) }}
                                                                                        pts</div>
                                                                                </div>
                                                                                <div>
                                                                                    <input type="number" step="0.1"
                                                                                        min="0"
                                                                                        max="{{ $task->max_score }}"
                                                                                        value="{{ $scoreValue !== null ? number_format($scoreValue, 1, '.', '') : '' }}"
                                                                                        class="score-input-cell"
                                                                                        data-task-id="{{ $task->id }}"
                                                                                        data-enrollment-id="{{ $enrollment->id }}"
                                                                                        data-max-score="{{ $task->max_score }}"
                                                                                        data-cat-id="{{ $cat->id }}"
                                                                                        data-cat-weight="{{ $cat->weight }}"
                                                                                        data-cat-type="lecture"
                                                                                        placeholder="-">
                                                                                </div>
                                                                            </div>
                                                                        @empty
                                                                            <div
                                                                                style="font-size: 0.78rem; color: #94a3b8; text-align: center; padding: 0.4rem 0;">
                                                                                No tasks configured</div>
                                                                        @endforelse
                                                                    </div>
                                                                @empty
                                                                    <div
                                                                        style="font-size: 0.8rem; color: #94a3b8; text-align: center; padding: 1rem 0;">
                                                                        No Lecture categories</div>
                                                                @endforelse
                                                            </div>

                                                            <!-- RIGHT COLUMN: LABORATORY TASKS & SCORES -->
                                                            <div
                                                                style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; border-top: 3.5px solid #10b981;">
                                                                <div
                                                                    style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.6rem; margin-bottom: 0.85rem; border-bottom: 1.5px solid #cbd5e1;">
                                                                    <h4
                                                                        style="font-size: 0.92rem; font-weight: 800; color: #065f46; margin: 0; display: flex; align-items: center; gap: 0.4rem;">
                                                                        🔬 Laboratory Component
                                                                    </h4>
                                                                    <span class="badge"
                                                                        style="background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; font-size: 0.7rem; font-weight: 800; padding: 0.18rem 0.5rem; border-radius: 12px;">
                                                                        Ratio:
                                                                        {{ number_format($currentSectionSubject->subject->lab_weight, 0) }}%
                                                                    </span>
                                                                </div>

                                                                @forelse($labCategories as $cat)
                                                                    <div
                                                                        style="margin-bottom: 1rem; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 10px; padding: 0.75rem;">
                                                                        <div
                                                                            style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; border-bottom: 1px dashed #e2e8f0; padding-bottom: 0.35rem;">
                                                                            <span
                                                                                style="font-size: 0.85rem; font-weight: 800; color: #0f172a;">
                                                                                {{ $cat->name }}
                                                                                <small
                                                                                    style="color: #64748b; font-weight: 600;">({{ number_format($cat->weight, 0) }}%)</small>
                                                                            </span>
                                                                            <span
                                                                                style="font-size: 0.7rem; color: #64748b;">Period:
                                                                                <strong>{{ $cat->academic_period }}</strong></span>
                                                                        </div>

                                                                        @forelse($cat->gradingTasks as $task)
                                                                            @php
                                                                                $taskScoreModel = $enrollment->taskScores
                                                                                    ->where(
                                                                                        'grading_task_id',
                                                                                        $task->id,
                                                                                    )
                                                                                    ->first();
                                                                                $scoreValue = $taskScoreModel
                                                                                    ? $taskScoreModel->score
                                                                                    : null;
                                                                            @endphp
                                                                            <div
                                                                                style="display: flex; align-items: center; justify-content: space-between; padding: 0.4rem 0; border-bottom: 1px solid #f1f5f9;">
                                                                                <div>
                                                                                    <div
                                                                                        style="font-size: 0.82rem; font-weight: 700; color: #334155;">
                                                                                        🔬 {{ $task->task_name }}</div>
                                                                                    <div
                                                                                        style="font-size: 0.7rem; color: #94a3b8;">
                                                                                        Max:
                                                                                        {{ number_format($task->max_score, 0) }}
                                                                                        pts</div>
                                                                                </div>
                                                                                <div>
                                                                                    <input type="number" step="0.1"
                                                                                        min="0"
                                                                                        max="{{ $task->max_score }}"
                                                                                        value="{{ $scoreValue !== null ? number_format($scoreValue, 1, '.', '') : '' }}"
                                                                                        class="score-input-cell"
                                                                                        data-task-id="{{ $task->id }}"
                                                                                        data-enrollment-id="{{ $enrollment->id }}"
                                                                                        data-max-score="{{ $task->max_score }}"
                                                                                        data-cat-id="{{ $cat->id }}"
                                                                                        data-cat-weight="{{ $cat->weight }}"
                                                                                        data-cat-type="laboratory"
                                                                                        placeholder="-">
                                                                                </div>
                                                                            </div>
                                                                        @empty
                                                                            <div
                                                                                style="font-size: 0.78rem; color: #94a3b8; text-align: center; padding: 0.4rem 0;">
                                                                                No tasks configured</div>
                                                                        @endforelse
                                                                    </div>
                                                                @empty
                                                                    <div
                                                                        style="font-size: 0.8rem; color: #94a3b8; text-align: center; padding: 1rem 0;">
                                                                        No Laboratory categories</div>
                                                                @endforelse
                                                            </div>
                                                        </div>
                                                    @else
                                                        <!-- NON-LAB SUBJECT TASKS MATRIX -->
                                                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                                                            @forelse($categories as $cat)
                                                                <div
                                                                    style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 1rem;">
                                                                    <div
                                                                        style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.65rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.4rem;">
                                                                        <span
                                                                            style="font-size: 0.9rem; font-weight: 800; color: #0f172a;">
                                                                            {{ $cat->name }}
                                                                            ({{ number_format($cat->weight, 0) }}%)
                                                                        </span>
                                                                        <span
                                                                            style="font-size: 0.75rem; color: #64748b;">Period:
                                                                            <strong>{{ $cat->academic_period }}</strong></span>
                                                                    </div>

                                                                    @forelse($cat->gradingTasks as $task)
                                                                        @php
                                                                            $taskScoreModel = $enrollment->taskScores
                                                                                ->where('grading_task_id', $task->id)
                                                                                ->first();
                                                                            $scoreValue = $taskScoreModel
                                                                                ? $taskScoreModel->score
                                                                                : null;
                                                                        @endphp
                                                                        <div
                                                                            style="display: flex; align-items: center; justify-content: space-between; padding: 0.45rem 0; border-bottom: 1px dashed #e2e8f0;">
                                                                            <div>
                                                                                <div
                                                                                    style="font-size: 0.85rem; font-weight: 700; color: #334155;">
                                                                                    📝 {{ $task->task_name }}</div>
                                                                                <div
                                                                                    style="font-size: 0.72rem; color: #94a3b8;">
                                                                                    Max:
                                                                                    {{ number_format($task->max_score, 0) }}
                                                                                    pts</div>
                                                                            </div>
                                                                            <div>
                                                                                <input type="number" step="0.1"
                                                                                    min="0"
                                                                                    max="{{ $task->max_score }}"
                                                                                    value="{{ $scoreValue !== null ? number_format($scoreValue, 1, '.', '') : '' }}"
                                                                                    class="score-input-cell"
                                                                                    data-task-id="{{ $task->id }}"
                                                                                    data-enrollment-id="{{ $enrollment->id }}"
                                                                                    data-max-score="{{ $task->max_score }}"
                                                                                    data-cat-id="{{ $cat->id }}"
                                                                                    data-cat-weight="{{ $cat->weight }}"
                                                                                    data-cat-type="general"
                                                                                    placeholder="-">
                                                                            </div>
                                                                        </div>
                                                                    @empty
                                                                        <div
                                                                            style="font-size: 0.8rem; color: #94a3b8; text-align: center; padding: 0.5rem 0;">
                                                                            No tasks configured</div>
                                                                    @endforelse
                                                                </div>
                                                            @empty
                                                                <div
                                                                    style="font-size: 0.85rem; color: #94a3b8; text-align: center; padding: 1.5rem 0;">
                                                                    No categories configured</div>
                                                            @endforelse
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="modal-footer"
                                                    style="background: #f8fafc; padding: 0.85rem 1.35rem; display: flex; align-items: center; justify-content: space-between;">
                                                    <div style="font-size: 0.85rem; font-weight: 700; color: #1e293b;"
                                                        data-modal-summary="{{ $enrollment->id }}">
                                                        @if ($hasLab)
                                                            Lec: <span class="badge"
                                                                style="background: #eff6ff; color: #1d4ed8; font-size: 0.82rem;"><span
                                                                    class="modal-lec-val">{{ number_format($lecSubtotal, 2) }}</span>%</span>
                                                            &bull; Lab: <span class="badge"
                                                                style="background: #ecfdf5; color: #047857; font-size: 0.82rem;"><span
                                                                    class="modal-lab-val">{{ number_format($labSubtotal, 2) }}</span>%</span>
                                                            &bull; {{ $selectedPeriod ? $selectedPeriod : 'Final' }}
                                                            Grade:
                                                            <span class="badge"
                                                                style="background: #059669; color: #ffffff; font-size: 0.85rem;"><span
                                                                    class="modal-final-val">{{ number_format($totalFinalGrade, 2) }}</span>%</span>
                                                        @else
                                                            {{ $selectedPeriod ? $selectedPeriod : 'Final' }} Grade: <span
                                                                class="badge"
                                                                style="background: #059669; color: #ffffff; font-size: 0.85rem;"><span
                                                                    class="modal-final-val">{{ number_format($generalCategoryPctSum, 2) }}</span>%</span>
                                                        @endif
                                                    </div>
                                                    <button type="button" class="btn-secondary"
                                                        onclick="closeModal('studentGradeModal_{{ $enrollment->id }}')">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                    <tr>
                                        <td colspan="10"
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
        </div>

        <!-- Panel 2: Class Attendance Sheet -->
        <div id="sheet-attendance-panel" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <div class="card-title"
                        style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Class Attendance Sheet
                        </div>
                        @if ($currentSectionSubject)
                            <button class="btn-primary" style="padding: 0.45rem 0.85rem; font-size: 0.82rem;"
                                onclick="openModal('addAttendanceDateModal')">
                                + Add Attendance Date
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <!-- Legend Bar -->
                    <div
                        style="display: flex; gap: 0.6rem; flex-wrap: wrap; margin-bottom: 1rem; padding: 0.75rem; background: #f8fafc; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.78rem; font-weight: 700; align-items: center;">
                        <span style="color: #475569;">Attendance Codes:</span>
                        <span class="status-badge"
                            style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">P - Present</span>
                        <span class="status-badge"
                            style="background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5;">L - Late</span>
                        <span class="status-badge"
                            style="background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5;">A - Absent</span>
                        <span class="status-badge"
                            style="background: #f3e8ff; color: #6b21a8; border: 1px solid #d8b4fe;">AEL - Excuse
                            Letter</span>
                        <span class="status-badge"
                            style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">E - Excuse</span>
                        <span class="status-badge"
                            style="background: #450a0a; color: #ffffff; border: 1px solid #7f1d1d;">C - Cutting
                            Class</span>
                    </div>

                    <div class="table-responsive">
                        <table class="custom-table" id="attendanceRecordTable">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    @foreach ($attendanceDates as $attDate)
                                        @php
                                            $formattedDate = \Carbon\Carbon::parse($attDate)->format('Y-m-d');
                                            $displayDate = \Carbon\Carbon::parse($attDate)->format('M d, Y');
                                        @endphp
                                        <th style="text-align: center; min-width: 140px;">
                                            <div
                                                style="display: flex; align-items: center; justify-content: center; gap: 0.35rem;">
                                                <span>{{ $displayDate }}</span>
                                                @if ($currentSectionSubject)
                                                    <form
                                                        action="{{ route('superadmin.grades.attendance.date.destroy') }}"
                                                        method="POST" style="display: inline;"
                                                        onsubmit="return confirm('Are you sure you want to delete attendance column for {{ $displayDate }}?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="class_section_subject_id"
                                                            value="{{ $currentSectionSubject->id }}">
                                                        <input type="hidden" name="attendance_date"
                                                            value="{{ $formattedDate }}">
                                                        <button type="submit"
                                                            style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 1.1rem; font-weight: 800; padding: 0 0 0 4px; line-height: 1;"
                                                            title="Delete Column">&times;</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enrolledStudents as $enrollment)
                                    <tr>
                                        <td><strong>{{ $enrollment->student->student_number ?? 'N/A' }}</strong></td>
                                        <td>
                                            <strong>{{ $enrollment->student->first_name ?? '' }}
                                                {{ $enrollment->student->last_name ?? '' }}</strong>
                                        </td>
                                        @foreach ($attendanceDates as $attDate)
                                            @php
                                                $formattedDate = \Carbon\Carbon::parse($attDate)->format('Y-m-d');
                                                $attRec = $attendances
                                                    ->where('enrollment_id', $enrollment->id)
                                                    ->where('attendance_date', $formattedDate)
                                                    ->first();
                                                $statusVal = strtoupper($attRec->status ?? 'P');
                                                if (!in_array($statusVal, ['P', 'L', 'A', 'AEL', 'E', 'C'])) {
                                                    $statusVal = 'P';
                                                }
                                            @endphp
                                            <td style="text-align: center;">
                                                <select class="att-status-select {{ $statusVal }}"
                                                    data-css-id="{{ $currentSectionSubject->id }}"
                                                    data-enrollment-id="{{ $enrollment->id }}"
                                                    data-date="{{ $formattedDate }}"
                                                    onchange="updateAttendanceStatusCell(this)">
                                                    <option value="P" {{ $statusVal === 'P' ? 'selected' : '' }}>P -
                                                        Present</option>
                                                    <option value="L" {{ $statusVal === 'L' ? 'selected' : '' }}>L -
                                                        Late</option>
                                                    <option value="A" {{ $statusVal === 'A' ? 'selected' : '' }}>A -
                                                        Absent</option>
                                                    <option value="AEL" {{ $statusVal === 'AEL' ? 'selected' : '' }}>
                                                        AEL - Excuse Letter</option>
                                                    <option value="E" {{ $statusVal === 'E' ? 'selected' : '' }}>E -
                                                        Excuse</option>
                                                    <option value="C" {{ $statusVal === 'C' ? 'selected' : '' }}>C -
                                                        Cutting</option>
                                                </select>
                                            </td>
                                        @endforeach
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
        </div>

        <!-- Add Category Modal -->
        <div class="modal-overlay" id="addCategoryModal">
            <div class="modal-card">
                <div class="modal-header">
                    <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Add Grading Category</h3>
                    <button type="button" class="btn-icon-action"
                        style="color: #ffffff; background: rgba(255,255,255,0.15); border: none;"
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
                        @if ($currentSectionSubject && $currentSectionSubject->subject->has_lab)
                            <div class="form-group">
                                <label>Subject Component Type <span style="color: #ef4444;">*</span></label>
                                <select name="component_type" class="form-control" required>
                                    <option value="lecture">Lecture Component (Lec
                                        {{ number_format($currentSectionSubject->subject->lecture_weight, 0) }}% ratio)
                                    </option>
                                    <option value="laboratory">Laboratory Component (Lab
                                        {{ number_format($currentSectionSubject->subject->lab_weight, 0) }}% ratio)
                                    </option>
                                </select>
                            </div>
                        @endif
                        <div class="form-group">
                            <label>Weight Percentage (%)</label>
                            <input type="number" name="weight" class="form-control" placeholder="e.g. 25, 45, 30"
                                min="0" max="100" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" onclick="closeModal('addCategoryModal')">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
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
                    <button type="button" class="btn-icon-action"
                        style="color: #ffffff; background: rgba(255,255,255,0.15); border: none;"
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
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary">+ Create Task</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Add Attendance Date Modal -->
        <div class="modal-overlay" id="addAttendanceDateModal">
            <div class="modal-card">
                <div class="modal-header">
                    <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Add Attendance Date Column</h3>
                    <button type="button" class="btn-icon-action"
                        style="color: #ffffff; background: rgba(255,255,255,0.15); border: none;"
                        onclick="closeModal('addAttendanceDateModal')">&times;</button>
                </div>
                <form action="{{ route('superadmin.grades.attendance.date.store') }}" method="POST">
                    @csrf
                    @if ($currentSectionSubject)
                        <input type="hidden" name="class_section_subject_id" value="{{ $currentSectionSubject->id }}">
                    @endif
                    <div class="modal-body">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Select Attendance Date <span style="color: #ef4444;">*</span></label>
                            <input type="date" name="attendance_date" class="form-control"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary"
                            onclick="closeModal('addAttendanceDateModal')">Cancel</button>
                        <button type="submit" class="btn-primary">+ Add Date Column</button>
                    </div>
                </form>
            </div>
        </div>

        @if ($currentSectionSubject && $currentSectionSubject->subject && $currentSectionSubject->subject->has_lab)
            <!-- Edit Subject Lec/Lab Weight Modal -->
            <div class="modal-overlay" id="editLecLabWeightModal">
                <div class="modal-card">
                    <div class="modal-header"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: #ffffff;">
                        <h3 style="font-size: 1rem; font-weight: 700; margin: 0; color: #ffffff;">⚙️ Edit Subject Lec & Lab
                            Share Percentage</h3>
                        <button type="button" class="btn-icon-action"
                            style="color: #ffffff; background: rgba(255,255,255,0.2); border: none;"
                            onclick="closeModal('editLecLabWeightModal')">&times;</button>
                    </div>
                    <form action="{{ route('superadmin.grades.subject.weights.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="subject_id" value="{{ $currentSectionSubject->subject->id }}">
                        <div class="modal-body">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>📘 Lecture Weight (%) <span style="color: #ef4444;">*</span></label>
                                    <input type="number" name="lecture_weight" id="modal_lecture_weight"
                                        class="form-control"
                                        value="{{ number_format($currentSectionSubject->subject->lecture_weight, 0) }}"
                                        min="0" max="100" required oninput="syncLabWeight(this.value)">
                                </div>
                                <div class="form-group">
                                    <label>🔬 Laboratory Weight (%) <span style="color: #ef4444;">*</span></label>
                                    <input type="number" name="lab_weight" id="modal_lab_weight" class="form-control"
                                        value="{{ number_format($currentSectionSubject->subject->lab_weight, 0) }}"
                                        min="0" max="100" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-secondary"
                                onclick="closeModal('editLecLabWeightModal')">Cancel</button>
                            <button type="submit" class="btn-primary" style="background: #2563eb;">Save Percentage
                                Ratio</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function syncLabWeight(lecVal) {
                    const lec = parseFloat(lecVal);
                    if (!isNaN(lec) && lec >= 0 && lec <= 100) {
                        const labInput = document.getElementById('modal_lab_weight');
                        if (labInput) labInput.value = Math.max(0, 100 - lec);
                    }
                }
            </script>
        @endif
    @endif
@endsection

@push('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        function openModal(id) {
            const el = document.getElementById(id);
            if (el) el.style.display = 'flex';
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        }

        function toggleCategoryBreakdown() {
            const wrapper = document.getElementById('categoryBreakdownWrapper');
            const icon = document.getElementById('toggleBreakdownIcon');
            const text = document.getElementById('toggleBreakdownText');

            if (!wrapper) return;

            if (wrapper.style.display === 'none' || wrapper.style.display === '') {
                wrapper.style.display = 'block';
                if (text) text.textContent = 'Hide Grading Categories & Tasks Breakdown';
                if (icon) icon.style.transform = 'rotate(180deg)';
            } else {
                wrapper.style.display = 'none';
                if (text) text.textContent = 'Show Grading Categories & Tasks Breakdown ({{ $categories->count() }})';
                if (icon) icon.style.transform = 'rotate(0deg)';
            }
        }

        function switchSheetTab(tabName) {
            const gradesPanel = document.getElementById('sheet-grades-panel');
            const attPanel = document.getElementById('sheet-attendance-panel');
            const btnGrades = document.getElementById('tab-btn-grades');
            const btnAtt = document.getElementById('tab-btn-attendance');

            if (tabName === 'attendance') {
                if (gradesPanel) gradesPanel.style.display = 'none';
                if (attPanel) attPanel.style.display = 'block';
                if (btnGrades) btnGrades.classList.remove('active');
                if (btnAtt) btnAtt.classList.add('active');
                localStorage.setItem('active_sheet_tab', 'attendance');
            } else {
                if (gradesPanel) gradesPanel.style.display = 'block';
                if (attPanel) attPanel.style.display = 'none';
                if (btnGrades) btnGrades.classList.add('active');
                if (btnAtt) btnAtt.classList.remove('active');
                localStorage.setItem('active_sheet_tab', 'grades');
            }
        }

        function updateAttendanceStatusCell(selectEl) {
            const cssId = selectEl.getAttribute('data-css-id');
            const enrollmentId = selectEl.getAttribute('data-enrollment-id');
            const attDate = selectEl.getAttribute('data-date');
            const statusVal = selectEl.value;

            // Update badge color class dynamically
            selectEl.className = 'att-status-select ' + statusVal;

            $.ajax({
                url: "{{ route('superadmin.grades.attendance.update_status') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    class_section_subject_id: cssId,
                    enrollment_id: enrollmentId,
                    attendance_date: attDate,
                    status: statusVal
                },
                success: function(res) {
                    if (typeof showToast === 'function') {
                        showToast('success', 'Attendance updated to ' + statusVal);
                    }
                },
                error: function(err) {
                    console.error("Failed to update attendance status:", err);
                    if (typeof showToast === 'function') {
                        showToast('error', 'Failed to update attendance status.');
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Restore active tab after form submit or refresh
            const urlParams = new URLSearchParams(window.location.search);
            const reqTab = urlParams.get('tab');
            const savedTab = reqTab || localStorage.getItem('active_sheet_tab') || 'grades';
            switchSheetTab(savedTab);

            const csrfToken = '{{ csrf_token() }}';
            const updateScoreUrl = '{{ route('superadmin.grades.update_score') }}';

            // Click outside modal card to close modal
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal-overlay')) {
                    e.target.style.display = 'none';
                }
            });

            // Input change and keyup delegation for score inputs (works inside modals & table rows!)
            document.addEventListener('change', function(e) {
                if (e.target && e.target.classList.contains('score-input-cell')) {
                    saveScore(e.target);
                    const enrollmentId = e.target.getAttribute('data-enrollment-id');
                    recalculateRowGrades(enrollmentId);
                }
            });

            document.addEventListener('keyup', function(e) {
                if (e.target && e.target.classList.contains('score-input-cell')) {
                    const enrollmentId = e.target.getAttribute('data-enrollment-id');
                    recalculateRowGrades(enrollmentId);
                }
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
                            if (typeof showToast === 'function') {
                                showToast('success', 'Task score saved successfully!');
                            }
                            setTimeout(() => {
                                inputElement.classList.remove('saved-success');
                            }, 2000);
                        }
                    })
                    .catch(err => {
                        console.error('Error saving score:', err);
                        inputElement.classList.remove('saving');
                        if (typeof showToast === 'function') {
                            showToast('error', 'Error saving score.');
                        }
                    });
            }

            function recalculateRowGrades(enrollmentId) {
                if (!enrollmentId) return;

                const row = document.querySelector(`tr[data-student-row="${enrollmentId}"]`);
                const modal = document.getElementById(`studentGradeModal_${enrollmentId}`);
                if (!row && !modal) return;

                const hasLab = (row ? row.getAttribute('data-has-lab') : '0') === '1';
                const lecRatio = parseFloat(row ? row.getAttribute('data-lec-ratio') : 0.7) || 0.7;
                const labRatio = parseFloat(row ? row.getAttribute('data-lab-ratio') : 0.3) || 0.3;

                const allInputs = document.querySelectorAll(
                    `input.score-input-cell[data-enrollment-id="${enrollmentId}"]`);

                let tasksMap = {};
                let categoriesMap = {};

                allInputs.forEach(input => {
                    const taskId = input.getAttribute('data-task-id');
                    const catId = input.getAttribute('data-cat-id');
                    const catWeight = parseFloat(input.getAttribute('data-cat-weight')) || 0;
                    const catType = input.getAttribute('data-cat-type') || 'lecture';
                    const maxScore = parseFloat(input.getAttribute('data-max-score')) || 0;
                    const val = parseFloat(input.value);

                    tasksMap[taskId] = {
                        catId: catId,
                        catWeight: catWeight,
                        catType: catType,
                        maxScore: maxScore,
                        score: !isNaN(val) ? val : null
                    };
                });

                for (let taskId in tasksMap) {
                    const t = tasksMap[taskId];
                    if (!categoriesMap[t.catId]) {
                        categoriesMap[t.catId] = {
                            earned: 0,
                            max: 0,
                            weight: t.catWeight,
                            type: t.catType
                        };
                    }
                    categoriesMap[t.catId].max += t.maxScore;
                    if (t.score !== null) {
                        categoriesMap[t.catId].earned += t.score;
                    }
                }

                let lecPctSum = 0;
                let labPctSum = 0;
                let genPctSum = 0;
                let lecWeightSum = 0;
                let labWeightSum = 0;

                for (let catId in categoriesMap) {
                    const cat = categoriesMap[catId];
                    const pct = (cat.max > 0) ? (cat.earned / cat.max) * cat.weight : 0;

                    if (hasLab) {
                        if (cat.type === 'laboratory') {
                            labPctSum += pct;
                            labWeightSum += cat.weight;
                        } else {
                            lecPctSum += pct;
                            lecWeightSum += cat.weight;
                        }
                    } else {
                        genPctSum += pct;
                    }
                }

                if (hasLab) {
                    let lecSubtotal = lecWeightSum > 0 ? (lecPctSum / lecWeightSum) * 100 : lecPctSum;
                    let labSubtotal = labWeightSum > 0 ? (labPctSum / labWeightSum) * 100 : labPctSum;

                    let lecWeightedShare = lecSubtotal * lecRatio;
                    let labWeightedShare = labSubtotal * labRatio;

                    let grandTotal = (lecWeightSum > 0 && labWeightSum > 0) ? (lecWeightedShare +
                        labWeightedShare) : (lecPctSum + labPctSum);

                    if (row) {
                        const lecTd = row.querySelector(`[data-lec-summary="${enrollmentId}"]`);
                        if (lecTd) {
                            const valSpan = lecTd.querySelector('.lec-grade-val');
                            const shareSpan = lecTd.querySelector('.lec-share-val');
                            if (valSpan) valSpan.textContent = lecSubtotal.toFixed(2);
                            if (shareSpan) shareSpan.textContent = lecWeightedShare.toFixed(2);
                        }

                        const labTd = row.querySelector(`[data-lab-summary="${enrollmentId}"]`);
                        if (labTd) {
                            const valSpan = labTd.querySelector('.lab-grade-val');
                            const shareSpan = labTd.querySelector('.lab-share-val');
                            if (valSpan) valSpan.textContent = labSubtotal.toFixed(2);
                            if (shareSpan) shareSpan.textContent = labWeightedShare.toFixed(2);
                        }

                        const finalTd = row.querySelector(`[data-final-summary="${enrollmentId}"]`);
                        if (finalTd) {
                            const finalSpan = finalTd.querySelector('.final-grade-val');
                            if (finalSpan) finalSpan.textContent = grandTotal.toFixed(2);
                        }
                    }

                    if (modal) {
                        const mLecVal = modal.querySelector('.modal-lec-val');
                        const mLabVal = modal.querySelector('.modal-lab-val');
                        const mFinalVal = modal.querySelector('.modal-final-val');
                        if (mLecVal) mLecVal.textContent = lecSubtotal.toFixed(2);
                        if (mLabVal) mLabVal.textContent = labSubtotal.toFixed(2);
                        if (mFinalVal) mFinalVal.textContent = grandTotal.toFixed(2);
                    }
                } else {
                    let grandTotal = genPctSum;
                    if (row) {
                        const finalTd = row.querySelector(`[data-final-summary="${enrollmentId}"]`);
                        if (finalTd) {
                            const finalSpan = finalTd.querySelector('.final-grade-val');
                            if (finalSpan) finalSpan.textContent = grandTotal.toFixed(2);
                        }
                    }

                    if (modal) {
                        const mFinalVal = modal.querySelector('.modal-final-val');
                        if (mFinalVal) mFinalVal.textContent = grandTotal.toFixed(2);
                    }
                }
            }
        });

        function computeTotalGradesAjax() {
            const btn = document.getElementById('btnComputeTotalGrades');
            const spinner = document.getElementById('computeBtnSpinner');
            const btnText = document.getElementById('computeBtnText');

            if (btn) btn.disabled = true;
            if (spinner) spinner.style.display = 'inline-block';
            if (btnText) btnText.textContent = 'Computing & Saving to DB...';

            fetch('{{ route('superadmin.grades.compute.total') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        class_section_subject_id: '{{ $currentSectionSubject->id ?? '' }}'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (btn) btn.disabled = false;
                    if (spinner) spinner.style.display = 'none';
                    if (btnText) btnText.textContent = '⚡ Compute Total Grade for this S.Y. and Sem';

                    if (data.status === 'success') {
                        data.results.forEach(res => {
                            const row = document.querySelector(`tr[data-breakdown-row="${res.enrollment_id}"]`);
                            if (row) {
                                for (const [pName, val] of Object.entries(res.period_grades)) {
                                    const cell = row.querySelector(`[data-period-cell="${pName}"] .period-val`);
                                    if (cell) {
                                        cell.textContent = parseFloat(val).toFixed(2) + '%';
                                    }
                                }
                                const sgCell = row.querySelector('[data-sg-cell] .sg-val');
                                if (sgCell) {
                                    sgCell.textContent = res.subject_grade + '%';
                                }
                                const remCell = row.querySelector('[data-remarks-cell]');
                                if (remCell) {
                                    const isPassed = res.remarks === 'Passed';
                                    remCell.innerHTML =
                                        `<span class="badge ${isPassed ? 'badge-active' : 'badge-danger'}" style="font-size: 0.8rem; font-weight: 800;">${res.remarks}</span>`;
                                }
                            }
                        });

                        alert('✅ ' + data.message);
                    } else {
                        alert('⚠️ Error computing grades: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    if (btn) btn.disabled = false;
                    if (spinner) spinner.style.display = 'none';
                    if (btnText) btnText.textContent = '⚡ Compute Total Grade for this S.Y. and Sem';
                    console.error('Computation error:', error);
                    alert('⚠️ An error occurred while computing total grades.');
                });
        }
    </script>

    <!-- Grade Breakdown & Final S.Y. Grade Modal -->
    @if ($currentSectionSubject)
        <div class="modal-overlay" id="gradeBreakdownModal">
            <div class="modal-card" style="max-width: 1050px; width: 95%;">
                <div class="modal-header"
                    style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: #ffffff; padding: 1.15rem 1.5rem;">
                    <div>
                        <h3
                            style="font-size: 1.1rem; font-weight: 800; margin: 0; color: #ffffff; display: flex; align-items: center; gap: 0.5rem;">
                            Grade Breakdown
                        </h3>
                        <div style="font-size: 0.78rem; color: #a5b4fc; margin-top: 2px;">
                            Subject:
                            <strong>{{ $currentSectionSubject->subject->subject_code ?? '' }} -
                                {{ $currentSectionSubject->subject->subject_name ?? '' }}</strong>
                        </div>
                    </div>
                    <button type="button" class="btn-icon-action"
                        style="color: #ffffff; background: rgba(255,255,255,0.15); border: none;"
                        onclick="closeModal('gradeBreakdownModal')">&times;</button>
                </div>

                <div class="modal-body" style="max-height: 78vh; overflow-y: auto; padding: 1.5rem;">
                    <div
                        style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; background: #f8fafc; padding: 0.85rem 1.15rem; border-radius: 12px; border: 1.5px solid #cbd5e1; flex-wrap: wrap; gap: 0.75rem;">
                        <div>
                            <div style="font-size: 0.88rem; font-weight: 800; color: #1e293b;">
                            </div>
                            <div style="font-size: 0.75rem; color: #64748b;">
                            </div>
                        </div>
                        <button type="button" id="btnComputeTotalGrades" onclick="computeTotalGradesAjax()"
                            class="btn-primary"
                            style="background: #059669; color: #ffffff; font-weight: 800; padding: 0.6rem 1.2rem; border-radius: 10px; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.3); border: none; transition: all 0.2s ease;">
                            <span id="computeBtnSpinner" style="display: none;">⌛</span>
                            <span id="computeBtnText">⚡ Compute Total Grade for this S.Y. and Sem</span>
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="custom-table" id="breakdownTable">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    @if (isset($availablePeriods) && $availablePeriods->count() > 0)
                                        @foreach ($availablePeriods as $pName)
                                            <th style="text-align: center;">{{ strtoupper($pName) }} GRADE</th>
                                        @endforeach
                                    @else
                                        <th style="text-align: center;">PRELIM</th>
                                        <th style="text-align: center;">MIDTERM</th>
                                        <th style="text-align: center;">FINALS</th>
                                    @endif
                                    <th
                                        style="text-align: center; background: #059669; color: #ffffff; font-weight: 800;">
                                        SUBJECT GRADE (SG)
                                    </th>
                                    <th style="text-align: center;">REMARKS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enrolledStudents as $enrollment)
                                    @php
                                        $stGrades = isset($savedGrades)
                                            ? $savedGrades->where('enrollment_id', $enrollment->id)
                                            : collect();
                                        $sgModel = $stGrades->where('academic_period', 'Subject Grade')->first();
                                        $sgVal = $sgModel ? number_format($sgModel->final_grade, 2) : null;
                                        $sgRemarks = $sgModel ? $sgModel->remarks : null;
                                    @endphp
                                    <tr data-breakdown-row="{{ $enrollment->id }}">
                                        <td><strong>{{ $enrollment->student->student_number ?? 'N/A' }}</strong></td>
                                        <td>
                                            <strong>{{ $enrollment->student->first_name ?? '' }}
                                                {{ $enrollment->student->last_name ?? '' }}</strong>
                                        </td>
                                        @if (isset($availablePeriods) && $availablePeriods->count() > 0)
                                            @foreach ($availablePeriods as $pName)
                                                @php
                                                    $pModel = $stGrades->where('academic_period', $pName)->first();
                                                    $pVal = $pModel ? number_format($pModel->final_grade, 2) : '-';
                                                @endphp
                                                <td style="text-align: center; font-weight: 700;"
                                                    data-period-cell="{{ $pName }}">
                                                    <span
                                                        class="period-val">{{ $pVal != '-' ? $pVal . '%' : '-' }}</span>
                                                </td>
                                            @endforeach
                                        @endif
                                        <td style="text-align: center; background: rgba(16, 185, 129, 0.08);"
                                            data-sg-cell>
                                            <span class="badge"
                                                style="font-size: 0.9rem; padding: 0.35rem 0.75rem; background: #059669; color: #ffffff; font-weight: 800; border-radius: 8px;">
                                                <span class="sg-val">{{ $sgVal ? $sgVal . '%' : '-' }}</span>
                                            </span>
                                        </td>
                                        <td style="text-align: center;" data-remarks-cell>
                                            @if ($sgRemarks)
                                                <span
                                                    class="badge {{ $sgRemarks === 'Passed' ? 'badge-active' : 'badge-danger' }}"
                                                    style="font-size: 0.8rem; font-weight: 800;">
                                                    {{ $sgRemarks }}
                                                </span>
                                            @else
                                                <span class="badge"
                                                    style="background: #f1f5f9; color: #64748b; font-size: 0.8rem;">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7"
                                            style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                                            No enrolled students found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer" style="background: #f8fafc; padding: 0.85rem 1.35rem;">
                    <button type="button" class="btn-secondary" onclick="closeModal('gradeBreakdownModal')">Close
                        Breakdown</button>
                </div>
            </div>
        </div>
    @endif
@endpush
