@extends('layouts.superadmin')

@section('title', 'GNHS - Manage Class Sections')

@push('styles')
    <!-- jQuery & DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <style>
        /* DataTables Custom Theme Styling */
        .dataTables_wrapper {
            padding: 0.5rem 0;
            font-size: 0.85rem;
        }

        .dataTables_length {
            margin-bottom: 1rem;
            float: left;
        }

        .dataTables_length label {
            font-weight: 600;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dataTables_length select {
            padding: 0.35rem 0.6rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.82rem;
            outline: none;
            background: #ffffff;
        }

        .dataTables_filter {
            margin-bottom: 1rem;
            float: right;
        }

        .dataTables_filter label {
            font-weight: 600;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dataTables_filter input {
            padding: 0.45rem 0.85rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.85rem;
            outline: none;
            transition: all 0.2s;
            width: 260px;
        }

        .dataTables_filter input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .dataTables_info {
            font-size: 0.82rem;
            color: #64748b;
            padding-top: 0.85rem;
            font-weight: 600;
        }

        .dataTables_paginate {
            padding-top: 0.85rem;
            float: right;
        }

        .dataTables_paginate ul,
        .dataTables_paginate ul.pagination {
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
        }

        .dataTables_paginate ul li,
        .dataTables_paginate ul.pagination li {
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
            display: inline-block !important;
        }

        .dataTables_paginate ul.pagination li a.page-link,
        .dataTables_paginate .paginate_button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 32px !important;
            height: 32px !important;
            padding: 0 0.65rem !important;
            border-radius: 8px !important;
            border: 1px solid #cbd5e1 !important;
            background: #ffffff !important;
            color: #475569 !important;
            font-size: 0.82rem !important;
            font-weight: 700 !important;
            text-decoration: none !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
        }

        .dataTables_paginate ul.pagination li a.page-link:hover,
        .dataTables_paginate .paginate_button:hover {
            background: #eff6ff !important;
            color: #2563eb !important;
            border-color: #bfdbfe !important;
        }

        .dataTables_paginate ul.pagination li.active a.page-link,
        .dataTables_paginate .paginate_button.current,
        .dataTables_paginate .paginate_button.current:hover {
            background: var(--primary-navy, #0f172a) !important;
            color: #ffffff !important;
            border-color: var(--primary-navy, #0f172a) !important;
        }

        .dataTables_paginate ul.pagination li.disabled a.page-link {
            opacity: 0.4 !important;
            cursor: not-allowed !important;
            background: #f8fafc !important;
            color: #94a3b8 !important;
        }
    </style>
@endpush

@section('content')
    <style>
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
            max-width: 580px;
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
            max-height: 78vh;
            overflow-y: auto;
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
            font-size: 0.82rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control-custom {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: inherit;
            color: #0f172a;
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .btn-cancel {
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .btn-cancel:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .btn-submit {
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            border: none;
            background: var(--accent-gold, #f5b41d);
            color: #0f172a;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            background: #d97706;
            color: #ffffff;
        }

        .btn-action-icon {
            padding: 0.4rem;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-action-icon:hover {
            background: #eff6ff;
            border-color: #93c5fd;
            color: #2563eb;
            transform: translateY(-1px);
        }

        .btn-action-icon.danger:hover {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
            transform: translateY(-1px);
        }
    </style>

    @if (session('success'))
        <div
            style="padding: 0.85rem 1.25rem; background: #ecfdf5; border: 1px solid #6ee7b7; color: #065f46; border-radius: 10px; margin-bottom: 1.25rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()"
                style="background: none; border: none; font-size: 1.1rem; cursor: pointer; color: #065f46;">&times;</button>
        </div>
    @endif

    @if ($errors->any())
        <div
            style="padding: 0.85rem 1.25rem; background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; border-radius: 10px; margin-bottom: 1.25rem; font-weight: 600;">
            <ul style="margin: 0; padding-left: 1.2rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v12" />
                </svg>
                Manage Class Sections
                @if ($selectedLevel)
                    <span class="badge badge-admin"
                        style="margin-left: 8px; font-size: 0.82rem;">{{ $selectedLevel }}</span>
                @endif
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--accent-emerald); margin-left: 6px;">
                    (S.Y. {{ $activeSchoolYear->school_year ?? '2024-2025' }})
                </span>
            </div>
            <button class="btn-primary" onclick="openAddSectionModal()">+ Create New Section</button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                @php
                    $selectedLevelCode = strtoupper($selectedLevel ?? '');
                    $isJhsOrBedFilter = in_array($selectedLevelCode, ['JHS', 'BED']);
                    if (!$isJhsOrBedFilter && isset($sections) && $sections->isNotEmpty()) {
                        $isJhsOrBedFilter = $sections->every(function ($sec) {
                            $edCode = strtoupper($sec->gradeLevel->educationLevel->code ?? '');
                            return in_array($edCode, ['JHS', 'BED']);
                        });
                    }

                    $isCollegeFilter = ($selectedLevelCode === 'COLLEGE');
                    if (!$isCollegeFilter && isset($sections) && $sections->isNotEmpty()) {
                        $isCollegeFilter = $sections->every(function ($sec) {
                            return strtoupper($sec->gradeLevel->educationLevel->code ?? '') === 'COLLEGE';
                        });
                    }
                @endphp
                <table class="custom-table" id="sectionsTable">
                    <thead>
                        <tr>
                            <th>Section Name</th>
                            <th>Education Level</th>
                            <th>Grade Level</th>
                            @if (!$isJhsOrBedFilter)
                                <th>Course / Strand</th>
                            @endif
                            <th>School Year</th>
                            @if (!$isCollegeFilter)
                                <th>Class Adviser</th>
                            @endif
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sections as $section)
                            @php
                                $edCode = strtoupper($section->gradeLevel->educationLevel->code ?? '');
                                $isJhsOrBed = in_array($edCode, ['JHS', 'BED']);
                                $adviserName =
                                    $section->adviser->user->name ??
                                    ($section->adviser
                                        ? $section->adviser->first_name . ' ' . $section->adviser->last_name
                                        : null);
                            @endphp
                            <tr>
                                <td><strong>{{ $section->section_name }}</strong></td>
                                <td>
                                    <span class="badge badge-admin">{{ $edCode ?: 'N/A' }}</span>
                                </td>
                                <td>{{ $section->gradeLevel->name ?? 'N/A' }}</td>
                                @if (!$isJhsOrBedFilter)
                                    <td>
                                        @if ($isJhsOrBed)
                                            <span style="color: #94a3b8;">-</span>
                                        @elseif ($section->course)
                                            <strong>{{ $section->course->course_code }}</strong> -
                                            {{ $section->course->course_name }}
                                        @else
                                            <span style="color: #94a3b8;">-</span>
                                        @endif
                                    </td>
                                @endif
                                <td>S.Y. {{ $section->schoolYear->school_year ?? 'N/A' }}</td>
                                @if (!$isCollegeFilter)
                                    <td>
                                        @if ($edCode === 'COLLEGE')
                                            <span style="font-size: 0.8rem; color: #94a3b8; font-style: italic;">N/A
                                                (College)</span>
                                        @elseif ($adviserName)
                                            <div style="display: flex; align-items: center; gap: 0.35rem;">
                                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" style="color: #2563eb;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <span>{{ $adviserName }}</span>
                                            </div>
                                        @else
                                            <span style="color: #ef4444; font-size: 0.8rem;">Unassigned</span>
                                        @endif
                                    </td>
                                @endif
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 0.35rem; justify-content: center;">
                                        <button type="button" class="btn-action-icon" title="Edit Section"
                                            onclick='openEditSectionModal(@json($section))'>
                                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <form action="{{ route('superadmin.sections.destroy', $section->id) }}"
                                            method="POST" style="display: inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this class section?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-icon danger" title="Delete Section">
                                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal: Create New Section -->
    <div class="modal-overlay" id="addSectionModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0;">Create New Class Section</h3>
                <button type="button" onclick="closeAddSectionModal()"
                    style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('superadmin.sections.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>School Year <span style="color: #ef4444;">*</span></label>
                            <select name="school_year_id" id="add_section_school_year" class="form-control-custom" required>
                                @foreach ($allSchoolYears as $sy)
                                    <option value="{{ $sy->id }}"
                                        {{ $activeSchoolYear && $activeSchoolYear->id == $sy->id ? 'selected' : '' }}>
                                        S.Y. {{ $sy->school_year }} {{ $sy->is_active ? '(Active)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Education Level <span style="color: #ef4444;">*</span></label>
                            @php
                                $autoLevelObj = null;
                                if ($selectedLevel) {
                                    $autoLevelObj = $educationLevelsList->first(function ($l) use ($selectedLevel) {
                                        return strtoupper($l->code) == strtoupper($selectedLevel);
                                    });
                                }
                            @endphp

                            @if ($autoLevelObj)
                                <input type="hidden" id="add_section_education_level_hidden"
                                    value="{{ $autoLevelObj->id }}" data-code="{{ strtoupper($autoLevelObj->code) }}">
                                <input type="text" value="{{ $autoLevelObj->name }} ({{ $autoLevelObj->code }})"
                                    class="form-control-custom" readonly
                                    style="background: #f1f5f9; font-weight: 700; color: var(--primary-navy, #0f172a); cursor: not-allowed;">
                            @else
                                <select id="add_section_education_level" class="form-control-custom" required
                                    onchange="handleSectionLevelCascade('add')">
                                    <option value="">-- Select Level --</option>
                                    @foreach ($educationLevelsList as $lvl)
                                        <option value="{{ $lvl->id }}" data-code="{{ strtoupper($lvl->code) }}">
                                            {{ $lvl->name }} ({{ $lvl->code }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Grade Level <span style="color: #ef4444;">*</span></label>
                            <select name="grade_level_id" id="add_section_grade_level" class="form-control-custom"
                                required>
                                <option value="">-- Select Grade Level --</option>
                                @foreach ($allGradeLevels as $gl)
                                    <option value="{{ $gl->id }}"
                                        data-ed-level-id="{{ $gl->education_level_id }}">
                                        {{ $gl->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="add_section_course_group" style="display: none;">
                            <label>Course / Strand <span
                                    style="font-size: 0.7rem; color: #64748b;">(SHS/College)</span></label>
                            <select name="course_id" id="add_section_course" class="form-control-custom" disabled>
                                <option value="">-- Select Course / Strand --</option>
                                @foreach ($allCourses as $c)
                                    <option value="{{ $c->id }}" data-level="{{ strtoupper($c->level) }}">
                                        {{ $c->course_code }} - {{ $c->course_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Section Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="section_name" id="add_section_name" class="form-control-custom"
                            placeholder="e.g. Grade 10 - Rizal or BSIT-1A" required>
                    </div>

                    <div class="form-group" id="add_section_adviser_group" style="display: block;">
                        <label>Class Adviser <span style="color: #ef4444;">*</span></label>
                        <select name="class_adviser_id" id="add_section_adviser" class="form-control-custom">
                            <option value="">-- Select Class Adviser --</option>
                            @foreach ($teachers as $t)
                                @php
                                    $tName = $t->user->name ?? $t->first_name . ' ' . $t->last_name;
                                    $tCode = $t->educationLevel->code ?? '';
                                @endphp
                                <option value="{{ $t->id }}" data-ed-level-id="{{ $t->education_level_id }}">
                                    {{ $t->teacher_id }} - {{ $tName }} {{ $tCode ? "($tCode)" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddSectionModal()">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </button>
                    <button type="submit" class="btn-submit">+ Save Section</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Section -->
    <div class="modal-overlay" id="editSectionModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0;">Edit Class Section</h3>
                <button type="button" onclick="closeEditSectionModal()"
                    style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form id="editSectionForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>School Year <span style="color: #ef4444;">*</span></label>
                            <select name="school_year_id" id="edit_section_school_year" class="form-control-custom"
                                required>
                                @foreach ($allSchoolYears as $sy)
                                    <option value="{{ $sy->id }}">
                                        S.Y. {{ $sy->school_year }} {{ $sy->is_active ? '(Active)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Education Level <span style="color: #ef4444;">*</span></label>
                            <select id="edit_section_education_level" class="form-control-custom" required
                                onchange="handleSectionLevelCascade('edit')">
                                <option value="">-- Select Level --</option>
                                @foreach ($educationLevelsList as $lvl)
                                    <option value="{{ $lvl->id }}" data-code="{{ strtoupper($lvl->code) }}">
                                        {{ $lvl->name }} ({{ $lvl->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Grade Level <span style="color: #ef4444;">*</span></label>
                            <select name="grade_level_id" id="edit_section_grade_level" class="form-control-custom"
                                required>
                                <option value="">-- Select Grade Level --</option>
                                @foreach ($allGradeLevels as $gl)
                                    <option value="{{ $gl->id }}"
                                        data-ed-level-id="{{ $gl->education_level_id }}">
                                        {{ $gl->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="edit_section_course_group" style="display: none;">
                            <label>Course / Strand <span
                                    style="font-size: 0.7rem; color: #64748b;">(SHS/College)</span></label>
                            <select name="course_id" id="edit_section_course" class="form-control-custom" disabled>
                                <option value="">-- Select Course / Strand --</option>
                                @foreach ($allCourses as $c)
                                    <option value="{{ $c->id }}" data-level="{{ strtoupper($c->level) }}">
                                        {{ $c->course_code }} - {{ $c->course_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Section Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="section_name" id="edit_section_name" class="form-control-custom"
                            required>
                    </div>

                    <div class="form-group" id="edit_section_adviser_group" style="display: block;">
                        <label>Class Adviser <span style="color: #ef4444;">*</span></label>
                        <select name="class_adviser_id" id="edit_section_adviser" class="form-control-custom">
                            <option value="">-- Select Class Adviser --</option>
                            @foreach ($teachers as $t)
                                @php
                                    $tName = $t->user->name ?? $t->first_name . ' ' . $t->last_name;
                                    $tCode = $t->educationLevel->code ?? '';
                                @endphp
                                <option value="{{ $t->id }}" data-ed-level-id="{{ $t->education_level_id }}">
                                    {{ $t->teacher_id }} - {{ $tName }} {{ $tCode ? "($tCode)" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditSectionModal()">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </button>
                    <button type="submit" class="btn-submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($('#sectionsTable').length) {
                $('#sectionsTable').DataTable({
                    "pageLength": 10,
                    "ordering": true,
                    "order": [],
                    "responsive": true,
                    "language": {
                        "search": "",
                        "searchPlaceholder": "Search section, level, adviser...",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ class sections",
                        "paginate": {
                            "previous": "‹",
                            "next": "›"
                        }
                    }
                });
            }

            // Trigger initial cascade if auto level present
            handleSectionLevelCascade('add');

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal-overlay')) {
                    e.target.style.display = 'none';
                }
            });
        });

        // Dynamic Level Cascade for Grade Levels, Courses, and Adviser input
        function handleSectionLevelCascade(prefix, targetGradeLevelId = null, targetCourseId = null, targetAdviserId =
            null) {
            let edSelect = document.getElementById(prefix + '_section_education_level');
            if (!edSelect) {
                edSelect = document.getElementById(prefix + '_section_education_level_hidden');
            }
            const glSelect = document.getElementById(prefix + '_section_grade_level');
            const crsGroup = document.getElementById(prefix + '_section_course_group');
            const crsSelect = document.getElementById(prefix + '_section_course');
            const advGroup = document.getElementById(prefix + '_section_adviser_group');
            const advSelect = document.getElementById(prefix + '_section_adviser');

            if (!glSelect || !crsSelect) return;

            let edLevelId = edSelect ? edSelect.value : '';
            let edCode = '';

            if (edSelect) {
                if (edSelect.tagName.toLowerCase() === 'select') {
                    const selectedOption = edSelect.options[edSelect.selectedIndex];
                    edCode = selectedOption ? (selectedOption.getAttribute('data-code') || '') : '';
                } else {
                    edCode = edSelect.getAttribute('data-code') || '';
                }
            }

            // 1. Filter Grade Levels based on edLevelId
            Array.from(glSelect.options).forEach(opt => {
                if (!opt.value) return;
                const optEdId = opt.getAttribute('data-ed-level-id');
                if (edLevelId && optEdId === edLevelId) {
                    opt.style.display = 'block';
                    opt.disabled = false;
                } else {
                    opt.style.display = 'none';
                    opt.disabled = true;
                }
            });

            if (targetGradeLevelId) {
                glSelect.value = targetGradeLevelId;
            } else if (!glSelect.options[glSelect.selectedIndex] || glSelect.options[glSelect.selectedIndex].disabled) {
                glSelect.value = '';
            }

            // 2. Filter & Toggle Courses/Strands Group (Display ONLY for SHS & COLLEGE)
            if (edCode === 'SHS' || edCode === 'COLLEGE') {
                if (crsGroup) crsGroup.style.display = 'block';
                crsSelect.disabled = false;

                Array.from(crsSelect.options).forEach(opt => {
                    if (!opt.value) return;
                    const crsLevel = opt.getAttribute('data-level');
                    if (crsLevel === edCode) {
                        opt.style.display = 'block';
                        opt.disabled = false;
                    } else {
                        opt.style.display = 'none';
                        opt.disabled = true;
                    }
                });

                if (targetCourseId) {
                    crsSelect.value = targetCourseId;
                } else if (!crsSelect.options[crsSelect.selectedIndex] || crsSelect.options[crsSelect.selectedIndex]
                    .disabled) {
                    crsSelect.value = '';
                }
            } else {
                if (crsGroup) crsGroup.style.display = 'none';
                crsSelect.disabled = true;
                crsSelect.value = '';
            }

            // 3. Class Adviser: Applicable for BED, JHS, SHS; Null/Hidden for COLLEGE; Filtered by Teacher's Education Level
            if (edCode === 'COLLEGE') {
                if (advGroup) advGroup.style.display = 'none';
                if (advSelect) {
                    advSelect.disabled = true;
                    advSelect.value = '';
                }
            } else {
                if (advGroup) advGroup.style.display = 'block';
                if (advSelect) {
                    advSelect.disabled = false;
                    Array.from(advSelect.options).forEach(opt => {
                        if (!opt.value) return;
                        const tEdLevelId = opt.getAttribute('data-ed-level-id');
                        if (edLevelId && tEdLevelId === edLevelId) {
                            opt.style.display = 'block';
                            opt.disabled = false;
                        } else {
                            opt.style.display = 'none';
                            opt.disabled = true;
                        }
                    });

                    if (targetAdviserId) {
                        advSelect.value = targetAdviserId;
                    } else if (!advSelect.options[advSelect.selectedIndex] || advSelect.options[advSelect.selectedIndex]
                        .disabled) {
                        advSelect.value = '';
                    }
                }
            }
        }

        function openAddSectionModal() {
            document.getElementById('addSectionModal').style.display = 'flex';
            handleSectionLevelCascade('add');
        }

        function closeAddSectionModal() {
            document.getElementById('addSectionModal').style.display = 'none';
        }

        function openEditSectionModal(section) {
            const form = document.getElementById('editSectionForm');
            form.action = "{{ url('/superadmin/sections/update') }}/" + section.id;

            document.getElementById('edit_section_school_year').value = section.school_year_id || '';
            document.getElementById('edit_section_name').value = section.section_name || '';

            const edLevelId = section.grade_level ? section.grade_level.education_level_id : '';
            const edSelect = document.getElementById('edit_section_education_level');
            if (edSelect) {
                edSelect.value = edLevelId;
            }

            handleSectionLevelCascade('edit', section.grade_level_id, section.course_id, section.class_adviser_id);

            document.getElementById('editSectionModal').style.display = 'flex';
        }

        function closeEditSectionModal() {
            document.getElementById('editSectionModal').style.display = 'none';
        }
    </script>
@endpush
