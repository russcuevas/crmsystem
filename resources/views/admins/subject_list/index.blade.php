@extends('layouts.admin')

@section('title', 'GNHS - Subject Catalog List')

@push('styles')
    <!-- jQuery & DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
            color: #0f172a !important;
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
        .dataTables_paginate .paginate_button.current a,
        .dataTables_paginate .paginate_button.current:hover {
            background: #0f172a !important;
            color: #ffffff !important;
            border-color: #0f172a !important;
            font-weight: 800 !important;
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
            max-width: 520px;
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

        .btn-cancel {
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #f1f5f9;
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

        .badge-quarter {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .badge-sem {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .level-info-box {
            font-size: 0.78rem;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            margin-top: 0.4rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .level-info-box.jhs-bed {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .level-info-box.shs-college {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .action-btn-group {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-action-icon {
            padding: 0.4rem;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-action-icon:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        .btn-action-icon.danger:hover {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
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
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Subject Catalog List
                @if (isset($currentEducationLevel) && $currentEducationLevel)
                    <span class="badge badge-admin" style="margin-left: 8px; font-size: 0.82rem;">
                        {{ $currentEducationLevel->code }}
                    </span>
                @endif
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--accent-emerald); margin-left: 6px;">
                    (S.Y. {{ $activeSchoolYear->school_year ?? '2024-2025' }})
                </span>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <button class="btn-primary" onclick="openAddSubjectModal()">+ Add New Subject</button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                @php
                    $selectedLevelCode = strtoupper($selectedLevel ?? ($currentEducationLevel->code ?? ''));
                    $isJhsOrBedFilter = in_array($selectedLevelCode, ['JHS', 'BED']);
                    if (!$isJhsOrBedFilter && isset($subjects) && $subjects->isNotEmpty()) {
                        $isJhsOrBedFilter = $subjects->every(function ($s) {
                            return in_array(strtoupper($s->educationLevel->code ?? ''), ['JHS', 'BED']);
                        });
                    }
                @endphp
                <table class="custom-table" id="subjectsTable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Subject Name</th>
                            <th>Education Level</th>
                            @if (!$isJhsOrBedFilter)
                                <th>Course</th>
                                <th>Units</th>
                            @endif
                            <th>Semester / Academic Period</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subjects as $subject)
                            @php
                                $lvlCode = strtoupper($subject->educationLevel->code ?? '');
                                $isJhsOrBed = in_array($lvlCode, ['JHS', 'BED']);
                                $hasSub = $subject->is_parent || ($subject->subSubjects && $subject->subSubjects->isNotEmpty());
                            @endphp
                            <tr class="{{ $hasSub ? 'parent-row' : '' }}">
                                <td><strong>{{ $subject->subject_code }}</strong></td>
                                <td>
                                    @if ($hasSub)
                                        <button type="button" class="btn-toggle-sub" onclick="toggleSubRows({{ $subject->id }}, this)"
                                            title="Click to view sub-subjects"
                                            style="background: #0f172a; color: #ffffff; border: none; border-radius: 6px; width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px; cursor: pointer; margin-right: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.15); transition: all 0.2s;">
                                            <span id="icon-sub-{{ $subject->id }}" style="line-height: 1;">+</span>
                                        </button>
                                    @endif
                                    <strong>{{ $subject->subject_name }}</strong>
                                    @if ($subject->is_parent)
                                        <span class="badge" style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; font-size: 0.7rem; margin-left: 6px; font-weight: 700;">Parent Subject</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-admin">
                                        {{ $subject->educationLevel->code ?? 'N/A' }}
                                    </span>
                                </td>
                                @if (!$isJhsOrBedFilter)
                                    <td>{{ $isJhsOrBed ? '-' : $subject->course->course_code ?? 'General' }}</td>
                                    <td>{{ $isJhsOrBed ? '-' : $subject->units ?? '3' }}</td>
                                @endif
                                <td>
                                    @if ($isJhsOrBed || str_contains(strtolower($subject->semester ?? ''), 'quarter'))
                                        <span class="badge-quarter" title="Applies to 1st, 2nd, 3rd, and 4th Quarters">
                                            1st - 4th Quarter
                                        </span>
                                    @else
                                        <span class="badge-sem">
                                            {{ $subject->semester ?? '1st Semester' }}
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div class="action-btn-group" style="justify-content: center;">
                                        <button type="button" class="btn-action-icon" title="Edit Subject"
                                            onclick='openEditSubjectModal(@json($subject))'>
                                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('admin.subjects.destroy', $subject->id) }}"
                                            method="POST" style="display: inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this subject catalog entry?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-icon danger" title="Delete Subject">
                                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @if ($hasSub)
                                @foreach ($subject->subSubjects as $sub)
                                    <tr class="sub-row-{{ $subject->id }}" style="display: none; background: #f8fafc;">
                                        <td style="padding-left: 2rem;"><strong>{{ $sub->subject_code }}</strong></td>
                                        <td style="padding-left: 2rem; color: #334155;">
                                            <i class="fa-solid fa-arrow-turn-up fa-rotate-90" style="color: #94a3b8; margin-right: 8px;"></i>
                                            {{ $sub->subject_name }}
                                        </td>
                                        <td>
                                            <span class="badge badge-admin" style="opacity: 0.85;">
                                                {{ $sub->educationLevel->code ?? ($subject->educationLevel->code ?? 'N/A') }}
                                            </span>
                                        </td>
                                        @if (!$isJhsOrBedFilter)
                                            <td>-</td>
                                            <td>-</td>
                                        @endif
                                        <td>
                                            <span class="badge-quarter" style="opacity: 0.85;">1st - 4th Quarter</span>
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="action-btn-group" style="justify-content: center;">
                                                <button type="button" class="btn-action-icon" title="Edit Sub-Subject"
                                                    onclick='openEditSubjectModal(@json($sub))'>
                                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <form action="{{ route('admin.subjects.destroy', $sub->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this sub-subject?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action-icon danger" title="Delete Sub-Subject">
                                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal: Add New Subject -->
    <div class="modal-overlay" id="addSubjectModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800;">Add New Subject</h3>
                <button type="button" onclick="closeAddSubjectModal()"
                    style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.subjects.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Education Level <span style="color: #ef4444;">*</span></label>
                        @if (isset($currentEducationLevel) && $currentEducationLevel)
                            <input type="hidden" name="education_level_id" id="add_education_level_id"
                                value="{{ $currentEducationLevel->id }}"
                                data-code="{{ strtoupper($currentEducationLevel->code) }}">
                            <div
                                style="padding: 0.65rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 700; background: #f1f5f9; color: #0f172a; display: flex; align-items: center; justify-content: space-between;">
                                <span>{{ $currentEducationLevel->code }} - {{ $currentEducationLevel->name }}</span>
                                <span class="badge badge-admin" style="font-size: 0.72rem;">Current Level</span>
                            </div>
                        @else
                            <select name="education_level_id" id="add_education_level_id" class="form-control-custom"
                                required
                                onchange="handleLevelChange(this, 'add_semester', 'add_course_group', 'add_level_info', null, 'add_units')">
                                <option value="" disabled selected>-- Select Education Level --</option>
                                @foreach ($educationLevelsList as $lvl)
                                    <option value="{{ $lvl->id }}" data-code="{{ strtoupper($lvl->code) }}">
                                        {{ $lvl->code }} - {{ $lvl->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div id="add_level_info" style="display: none;" class="level-info-box"></div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label>Subject Code <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="subject_code" class="form-control-custom"
                            placeholder="e.g. MATH101 or ENG7" required>
                    </div>

                    <div class="form-group">
                        <label>Subject Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="subject_name" class="form-control-custom"
                            placeholder="e.g. General Mathematics" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group" id="add_units_group">
                            <label>Units</label>
                            <input type="number" name="units" id="add_units" class="form-control-custom"
                                value="3" min="0">
                        </div>

                        <div class="form-group">
                            <label>Semester / Academic Period <span style="color: #ef4444;">*</span></label>
                            <select name="semester" id="add_semester" class="form-control-custom" required>
                                <!-- Populated dynamically by JS -->
                            </select>
                        </div>
                    </div>

                    <div class="form-group" id="add_course_group" style="display: block;">
                        <label>Course (College / Strand )</label>
                        <select name="course_id" id="add_course_id" class="form-control-custom">
                            <option value="">GE - General Subject</option>
                            @foreach ($coursesList as $course)
                                <option value="{{ $course->id }}" data-level="{{ strtoupper($course->level) }}">
                                    {{ $course->course_code }} - {{ $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" id="add_parent_subject_group"
                        style="display: none; background: #f0fdf4; padding: 0.85rem; border-radius: 8px; border: 1.5px dashed #bbf7d0; margin-top: 0.75rem;">
                        <label
                            style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; text-transform: none; font-size: 0.85rem; color: #166534;">
                            <input type="checkbox" name="is_parent" id="add_is_parent" value="1"
                                onchange="toggleParentSubjectSelect('add')">
                            <strong>Is Parent Subject? (e.g. MAPEH)</strong>
                        </label>
                        <div id="add_parent_select_wrapper" style="margin-top: 0.65rem;">
                            <label style="font-size: 0.75rem; color: #166534;">Assign to Parent Subject (Optional):</label>
                            <select name="parent_subject_id" id="add_parent_subject_id" class="form-control-custom">
                                <option value="">None (Standalone Subject)</option>
                                @foreach ($parentSubjects as $pSub)
                                    <option value="{{ $pSub->id }}">{{ $pSub->subject_name }} ({{ $pSub->subject_code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group" id="add_lab_component_group"
                        style="display: none; background: #f8fafc; padding: 0.85rem; border-radius: 8px; border: 1.5px dashed #cbd5e1; margin-top: 0.75rem;">
                        <label
                            style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; text-transform: none; font-size: 0.85rem;">
                            <input type="checkbox" name="has_lab" id="add_has_lab" value="1"
                                onchange="toggleLabWeights('add')">
                            <strong>Subject Has Laboratory Component?</strong>
                        </label>
                        <div id="add_lab_weights_wrapper"
                            style="display: none; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.75rem;">
                            <div>
                                <label style="font-size: 0.75rem;">Lecture Weight (%)</label>
                                <input type="number" name="lecture_weight" id="add_lecture_weight"
                                    class="form-control-custom" value="70" min="0" max="100">
                            </div>
                            <div>
                                <label style="font-size: 0.75rem;">Lab Weight (%)</label>
                                <input type="number" name="lab_weight" id="add_lab_weight" class="form-control-custom"
                                    value="30" min="0" max="100">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddSubjectModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Save Subject</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Subject -->
    <div class="modal-overlay" id="editSubjectModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800;">Edit Subject</h3>
                <button type="button" onclick="closeEditSubjectModal()"
                    style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form id="editSubjectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Education Level <span style="color: #ef4444;">*</span></label>
                        <select name="education_level_id" id="edit_education_level_id" class="form-control-custom"
                            required
                            onchange="handleLevelChange(this, 'edit_semester', 'edit_course_group', 'edit_level_info', null, 'edit_units', 'edit_course_id')">
                            @foreach ($educationLevelsList as $lvl)
                                <option value="{{ $lvl->id }}" data-code="{{ strtoupper($lvl->code) }}">
                                    {{ $lvl->code }} - {{ $lvl->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="edit_level_info" style="display: none;" class="level-info-box"></div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label>Subject Code <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="subject_code" id="edit_subject_code" class="form-control-custom"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Subject Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="subject_name" id="edit_subject_name" class="form-control-custom"
                            required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group" id="edit_units_group">
                            <label>Units</label>
                            <input type="number" name="units" id="edit_units" class="form-control-custom"
                                min="0">
                        </div>

                        <div class="form-group">
                            <label>Semester / Academic Period <span style="color: #ef4444;">*</span></label>
                            <select name="semester" id="edit_semester" class="form-control-custom" required>
                                <!-- Populated dynamically by JS -->
                            </select>
                        </div>
                    </div>

                    <div class="form-group" id="edit_course_group">
                        <label>Course (College / Strand )</label>
                        <select name="course_id" id="edit_course_id" class="form-control-custom">
                            <option value="">GE - General Subject</option>
                            @foreach ($coursesList as $course)
                                <option value="{{ $course->id }}" data-level="{{ strtoupper($course->level) }}">
                                    {{ $course->course_code }} - {{ $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" id="edit_parent_subject_group"
                        style="display: none; background: #f0fdf4; padding: 0.85rem; border-radius: 8px; border: 1.5px dashed #bbf7d0; margin-top: 0.75rem;">
                        <label
                            style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; text-transform: none; font-size: 0.85rem; color: #166534;">
                            <input type="checkbox" name="is_parent" id="edit_is_parent" value="1"
                                onchange="toggleParentSubjectSelect('edit')">
                            <strong>Is Parent Subject? (e.g. MAPEH)</strong>
                        </label>
                        <div id="edit_parent_select_wrapper" style="margin-top: 0.65rem;">
                            <label style="font-size: 0.75rem; color: #166534;">Assign to Parent Subject (Optional):</label>
                            <select name="parent_subject_id" id="edit_parent_subject_id" class="form-control-custom">
                                <option value="">None (Standalone Subject)</option>
                                @foreach ($parentSubjects as $pSub)
                                    <option value="{{ $pSub->id }}">{{ $pSub->subject_name }} ({{ $pSub->subject_code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group" id="edit_lab_component_group"
                        style="display: none; background: #f8fafc; padding: 0.85rem; border-radius: 8px; border: 1.5px dashed #cbd5e1; margin-top: 0.75rem;">
                        <label
                            style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; text-transform: none; font-size: 0.85rem;">
                            <input type="checkbox" name="has_lab" id="edit_has_lab" value="1"
                                onchange="toggleLabWeights('edit')">
                            <strong>Subject Has Laboratory Component?</strong>
                        </label>
                        <div id="edit_lab_weights_wrapper"
                            style="display: none; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.75rem;">
                            <div>
                                <label style="font-size: 0.75rem;">Lecture Weight (%)</label>
                                <input type="number" name="lecture_weight" id="edit_lecture_weight"
                                    class="form-control-custom" min="0" max="100">
                            </div>
                            <div>
                                <label style="font-size: 0.75rem;">Lab Weight (%)</label>
                                <input type="number" name="lab_weight" id="edit_lab_weight" class="form-control-custom"
                                    min="0" max="100">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditSubjectModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Update Subject</button>
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
            if ($('#subjectsTable').length) {
                $('#subjectsTable').DataTable({
                    "pageLength": 10,
                    "ordering": true,
                    "order": [],
                    "responsive": true,
                    "language": {
                        "search": "",
                        "searchPlaceholder": "Search subject code, name...",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ subjects",
                        "paginate": {
                            "previous": "‹",
                            "next": "›"
                        }
                    }
                });
            }

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal-overlay')) {
                    e.target.style.display = 'none';
                }
            });
        });

        function toggleSubRows(parentId, btn) {
            const rows = document.querySelectorAll('.sub-row-' + parentId);
            const icon = document.getElementById('icon-sub-' + parentId);
            let isExpanding = false;
            rows.forEach(r => {
                if (r.style.display === 'none' || !r.style.display) {
                    r.style.display = 'table-row';
                    isExpanding = true;
                } else {
                    r.style.display = 'none';
                }
            });
            if (icon) {
                icon.innerText = isExpanding ? '−' : '+';
            }
            if (btn) {
                btn.style.background = isExpanding ? '#dc2626' : '#0f172a';
            }
        }

        function updateSemesterOptions(semSelect, code, selectedValue) {
            if (!semSelect) return;

            semSelect.innerHTML = '';

            if (code === 'BED' || code === 'JHS') {
                const options = [{
                    value: 'All Quarters',
                    text: '1st - 4th Quarter'
                }];

                options.forEach(optData => {
                    const opt = document.createElement('option');
                    opt.value = optData.value;
                    opt.textContent = optData.text;
                    opt.selected = true;
                    semSelect.appendChild(opt);
                });
            } else {
                const options = [{
                        value: '1st Semester',
                        text: '1st Semester'
                    },
                    {
                        value: '2nd Semester',
                        text: '2nd Semester'
                    }
                ];

                options.forEach(optData => {
                    const opt = document.createElement('option');
                    opt.value = optData.value;
                    opt.textContent = optData.text;
                    if (selectedValue && selectedValue === optData.value) {
                        opt.selected = true;
                    } else if (!selectedValue && optData.value === '1st Semester') {
                        opt.selected = true;
                    }
                    semSelect.appendChild(opt);
                });
            }
        }

        function toggleLabWeights(prefix) {
            const chk = document.getElementById(prefix + '_has_lab');
            const wrapper = document.getElementById(prefix + '_lab_weights_wrapper');
            if (chk && wrapper) {
                wrapper.style.display = chk.checked ? 'grid' : 'none';
            }
        }

        function toggleParentSubjectSelect(prefix) {
            const chk = document.getElementById(prefix + '_is_parent');
            const wrapper = document.getElementById(prefix + '_parent_select_wrapper');
            const parentSelect = document.getElementById(prefix + '_parent_subject_id');
            if (chk && wrapper) {
                if (chk.checked) {
                    wrapper.style.display = 'none';
                    if (parentSelect) parentSelect.value = '';
                } else {
                    wrapper.style.display = 'block';
                }
            }
        }

        function openAddSubjectModal() {
            document.getElementById('addSubjectModal').style.display = 'flex';
            const levelEl = document.getElementById('add_education_level_id');
            if (levelEl) {
                handleLevelChange(levelEl, 'add_semester', 'add_course_group', 'add_level_info', null, 'add_units',
                    'add_course_id');
            }
            toggleLabWeights('add');
            toggleParentSubjectSelect('add');
        }

        function closeAddSubjectModal() {
            document.getElementById('addSubjectModal').style.display = 'none';
        }

        function openEditSubjectModal(subject) {
            const form = document.getElementById('editSubjectForm');
            form.action = "{{ url('/admin/subjects/update') }}/" + subject.id;

            document.getElementById('edit_education_level_id').value = subject.education_level_id;
            document.getElementById('edit_subject_code').value = subject.subject_code;
            document.getElementById('edit_subject_name').value = subject.subject_name;
            document.getElementById('edit_units').value = subject.units ?? '';

            const isParentChk = document.getElementById('edit_is_parent');
            if (isParentChk) {
                isParentChk.checked = !!subject.is_parent;
                document.getElementById('edit_parent_subject_id').value = subject.parent_subject_id ?? '';
                toggleParentSubjectSelect('edit');
            }

            const hasLabChk = document.getElementById('edit_has_lab');
            if (hasLabChk) {
                hasLabChk.checked = !!subject.has_lab;
                document.getElementById('edit_lecture_weight').value = subject.lecture_weight ? parseFloat(subject
                    .lecture_weight) : 70;
                document.getElementById('edit_lab_weight').value = subject.lab_weight ? parseFloat(subject.lab_weight) : 30;
                toggleLabWeights('edit');
            }

            const levelSelect = document.getElementById('edit_education_level_id');
            handleLevelChange(levelSelect, 'edit_semester', 'edit_course_group', 'edit_level_info', subject.semester,
                'edit_units', 'edit_course_id', subject.course_id);

            document.getElementById('editSubjectModal').style.display = 'flex';
        }

        function closeEditSubjectModal() {
            document.getElementById('editSubjectModal').style.display = 'none';
        }

        function handleLevelChange(selectEl, semSelectId, courseGroupId, infoBoxId, initialSemValue, unitsInputId,
            courseSelectId = null, targetCourseId = null) {
            if (!selectEl) return;
            let code = '';
            if (selectEl.tagName === 'SELECT') {
                const selectedOption = selectEl.options[selectEl.selectedIndex];
                if (selectedOption) {
                    code = selectedOption.getAttribute('data-code');
                }
            } else {
                code = selectEl.getAttribute('data-code');
            }

            const prefix = (selectEl.id && selectEl.id.startsWith('add')) ? 'add' : 'edit';
            const labGroup = document.getElementById(prefix + '_lab_component_group');
            const parentGroup = document.getElementById(prefix + '_parent_subject_group');

            const semSelect = document.getElementById(semSelectId);
            const courseGroup = document.getElementById(courseGroupId);
            const infoBox = document.getElementById(infoBoxId);
            const unitsInput = unitsInputId ? document.getElementById(unitsInputId) : null;
            const courseSelect = courseSelectId ? document.getElementById(courseSelectId) : (courseGroup ? courseGroup
                .querySelector('select') : null);

            const currentVal = initialSemValue || (semSelect ? semSelect.value : '');

            updateSemesterOptions(semSelect, code, currentVal);

            if (courseSelect) {
                Array.from(courseSelect.options).forEach(opt => {
                    if (!opt.value) return;
                    const crsLevel = opt.getAttribute('data-level');
                    if (code && crsLevel === code) {
                        opt.style.display = 'block';
                        opt.disabled = false;
                    } else {
                        opt.style.display = 'none';
                        opt.disabled = true;
                    }
                });

                if (targetCourseId) {
                    courseSelect.value = targetCourseId;
                } else if (courseSelect.options[courseSelect.selectedIndex] && courseSelect.options[courseSelect
                        .selectedIndex].disabled) {
                    courseSelect.value = '';
                }
            }

            if (code === 'BED' || code === 'JHS') {
                if (labGroup) labGroup.style.display = 'none';
                if (parentGroup) parentGroup.style.display = 'block';
                if (unitsInput) {
                    unitsInput.value = '';
                    unitsInput.disabled = true;
                    unitsInput.placeholder = 'N/A';
                    const unitsGroup = unitsInput.closest('.form-group');
                    if (unitsGroup) {
                        unitsGroup.style.display = 'none';
                        if (unitsGroup.parentElement) {
                            unitsGroup.parentElement.style.gridTemplateColumns = '1fr';
                        }
                    }
                }
                if (infoBox) {
                    infoBox.className = 'level-info-box jhs-bed';
                    infoBox.style.display = 'flex';
                    infoBox.innerHTML = `
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Basic Education / JHS subjects are automatically set to <strong>1st - 4th Quarter</strong> (No Units).</span>
                    `;
                }
                if (courseGroup) {
                    courseGroup.style.display = 'none';
                }
            } else if (code === 'SHS' || code === 'COLLEGE') {
                if (labGroup) labGroup.style.display = 'block';
                if (parentGroup) parentGroup.style.display = 'none';
                if (unitsInput) {
                    unitsInput.disabled = false;
                    unitsInput.placeholder = '';
                    if (unitsInput.value === '') {
                        unitsInput.value = '3';
                    }
                    const unitsGroup = unitsInput.closest('.form-group');
                    if (unitsGroup) {
                        unitsGroup.style.display = 'block';
                        if (unitsGroup.parentElement) {
                            unitsGroup.parentElement.style.gridTemplateColumns = '1fr 1fr';
                        }
                    }
                }
                if (infoBox) {
                    infoBox.className = 'level-info-box shs-college';
                    infoBox.style.display = 'flex';
                    infoBox.innerHTML = `
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Select whether offered in <strong>1st Semester</strong> or <strong>2nd Semester</strong>.</span>
                    `;
                }
                if (courseGroup) {
                    courseGroup.style.display = 'block';
                }
            } else {
                if (labGroup) labGroup.style.display = 'block';
                if (unitsInput) {
                    unitsInput.disabled = false;
                    unitsInput.placeholder = '';
                    const unitsGroup = unitsInput.closest('.form-group');
                    if (unitsGroup) {
                        unitsGroup.style.display = 'block';
                        if (unitsGroup.parentElement) {
                            unitsGroup.parentElement.style.gridTemplateColumns = '1fr 1fr';
                        }
                    }
                }
                if (infoBox) {
                    infoBox.style.display = 'none';
                }
                if (courseGroup) {
                    courseGroup.style.display = 'block';
                }
            }
        }
    </script>
@endpush
