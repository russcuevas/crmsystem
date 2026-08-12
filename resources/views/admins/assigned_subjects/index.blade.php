@extends('layouts.admin')

@section('title', 'GNHS-P - Assigned Subjects Roster')

@push('styles')
    <!-- jQuery & DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            border-radius: 8px;
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
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Assigned Subjects Roster
                @if (isset($currentEducationLevel) && $currentEducationLevel)
                    <span class="badge badge-admin" style="margin-left: 8px; font-size: 0.82rem;">
                        {{ $currentEducationLevel->code }}
                    </span>
                @endif
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--accent-emerald); margin-left: 6px;">
                    (S.Y. {{ $activeSchoolYear->school_year ?? '2024-2025' }})
                </span>
            </div>

            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <button class="btn-primary" onclick="openAddAssignedSubjectModal()">+ Assign Subject to Section</button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="custom-table" id="assignedSubjectsTable">
                    <thead>
                        <tr>
                            <th>Class Section</th>
                            <th>Level</th>
                            <th>Assigned Subject</th>
                            <th>Assigned Instructor</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assignedSubjects as $assigned)
                            @php
                                $secLevelCode = strtoupper(
                                    $assigned->classSection->gradeLevel->educationLevel->code ?? 'N/A',
                                );
                                $teacherName =
                                    $assigned->teacher->user->name ??
                                    ($assigned->teacher
                                        ? $assigned->teacher->first_name . ' ' . $assigned->teacher->last_name
                                        : 'Unassigned');
                                $hasAssignedSub =
                                    $assigned->subject &&
                                    ($assigned->subject->is_parent ||
                                        ($assigned->assignedSubSubjects &&
                                            $assigned->assignedSubSubjects->isNotEmpty()));
                            @endphp
                            <tr class="{{ $hasAssignedSub ? 'parent-row' : '' }}">
                                <td>
                                    <strong>{{ $assigned->classSection->section_name ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <span class="badge badge-admin">{{ $secLevelCode }}</span>
                                </td>
                                <td>
                                    @if ($hasAssignedSub)
                                        <button type="button" class="btn-toggle-sub"
                                            onclick="toggleAssignedSubRows({{ $assigned->id }}, this)"
                                            title="Click to view assigned sub-subjects"
                                            style="background: #0f172a; color: #ffffff; border: none; border-radius: 6px; width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px; cursor: pointer; margin-right: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.15); transition: all 0.2s;">
                                            <span id="icon-assigned-sub-{{ $assigned->id }}"
                                                style="line-height: 1;">+</span>
                                        </button>
                                    @endif
                                    <strong>{{ $assigned->subject->subject_code ?? 'N/A' }}</strong> -
                                    {{ $assigned->subject->subject_name ?? '' }}
                                    @if ($assigned->subject && $assigned->subject->is_parent)
                                        <span class="badge"
                                            style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; font-size: 0.7rem; margin-left: 6px; font-weight: 700;">Parent
                                            Subject</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" style="color: #2563eb;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>{{ $teacherName }}</span>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 0.35rem; justify-content: center;">
                                        <button type="button" class="btn-action-icon" title="Edit Assignment"
                                            onclick='openEditAssignedSubjectModal(@json($assigned))'>
                                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <form action="{{ route('admin.assigned_subjects.destroy', $assigned->id) }}"
                                            method="POST" style="display: inline;"
                                            onsubmit="return confirm('Are you sure you want to remove this assigned subject and its sub-subjects?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-icon danger" title="Delete Assignment">
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
                            @if ($hasAssignedSub)
                                @foreach ($assigned->assignedSubSubjects as $subAssigned)
                                    @php
                                        $subTeacherName =
                                            $subAssigned->teacher->user->name ??
                                            $subAssigned->teacher->first_name . ' ' . $subAssigned->teacher->last_name;
                                    @endphp
                                    <tr class="assigned-sub-row-{{ $assigned->id }}"
                                        style="display: none; background: #f8fafc;">
                                        <td style="opacity: 0.7;">{{ $assigned->classSection->section_name ?? 'N/A' }}</td>
                                        <td><span class="badge badge-admin"
                                                style="opacity: 0.85;">{{ $secLevelCode }}</span></td>
                                        <td style="padding-left: 2rem; color: #334155;">
                                            <i class="fa-solid fa-arrow-turn-up fa-rotate-90"
                                                style="color: #94a3b8; margin-right: 8px;"></i>
                                            <strong>{{ $subAssigned->subject->subject_code ?? 'N/A' }}</strong> -
                                            {{ $subAssigned->subject->subject_name ?? '' }}
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 0.4rem; color: #475569;">
                                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" style="color: #64748b;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <span>{{ $subTeacherName }}</span>
                                            </div>
                                        </td>
                                        <td style="text-align: center; color: #94a3b8; font-size: 0.8rem;">Auto-Assigned
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

    <!-- Modal: Assign Subject to Section -->
    <div class="modal-overlay" id="addAssignedSubjectModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0;">Assign Subject to Section</h3>
                <button type="button" onclick="closeAddAssignedSubjectModal()"
                    style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.assigned_subjects.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Target Class Section <span style="color: #ef4444;">*</span></label>
                        <select name="class_section_id" id="add_class_section_id" class="form-control-custom" required
                            onchange="handleAssignedSectionCascade('add')">
                            <option value="">-- Select Class Section --</option>
                            @foreach ($sectionsList ?? $classSections ?? [] as $sec)
                                <option value="{{ $sec->id }}"
                                    data-ed-level-id="{{ $sec->gradeLevel->education_level_id ?? '' }}">
                                    {{ $sec->section_name }} ({{ $sec->gradeLevel->name ?? 'N/A' }} -
                                    S.Y. {{ $sec->schoolYear->school_year ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Subject Catalog <span style="color: #ef4444;">*</span></label>
                        <select name="subject_id" id="add_subject_id" class="form-control-custom" required>
                            <option value="">-- Select Subject --</option>
                            @foreach ($subjectsList ?? $subjects ?? [] as $subj)
                                <option value="{{ $subj->id }}" data-ed-level-id="{{ $subj->education_level_id }}"
                                    data-sem="{{ $subj->semester }}">
                                    {{ $subj->subject_code }} - {{ $subj->subject_name }}
                                    ({{ $subj->educationLevel->code ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Assign Instructor / Teacher <span style="color: #ef4444;">*</span></label>
                        <select name="teacher_id" id="add_teacher_id" class="form-control-custom" required>
                            <option value="">-- Select Teacher --</option>
                            @foreach ($teachersList ?? $teachers ?? [] as $t)
                                <option value="{{ $t->id }}" data-ed-level-id="{{ $t->education_level_id }}">
                                    {{ $t->first_name }} {{ $t->last_name }} ({{ $t->position ?? 'Teacher' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddAssignedSubjectModal()">Cancel</button>
                    <button type="submit" class="btn-submit">+ Assign Subject</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Assigned Subject -->
    <div class="modal-overlay" id="editAssignedSubjectModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0;">Edit Assigned Subject</h3>
                <button type="button" onclick="closeEditAssignedSubjectModal()"
                    style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form id="editAssignedSubjectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Class Section <span style="color: #ef4444;">*</span></label>
                        <select name="class_section_id" id="edit_class_section_id" class="form-control-custom" required
                            onchange="handleAssignedSectionCascade('edit')">
                            <option value="">-- Select Class Section --</option>
                            @foreach ($sectionsList ?? $classSections ?? [] as $sec)
                                <option value="{{ $sec->id }}"
                                    data-ed-level-id="{{ $sec->gradeLevel->education_level_id ?? '' }}">
                                    {{ $sec->section_name }} ({{ $sec->gradeLevel->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Subject Catalog <span style="color: #ef4444;">*</span></label>
                        <select name="subject_id" id="edit_subject_id" class="form-control-custom" required>
                            <option value="">-- Select Subject --</option>
                            @foreach ($subjectsList ?? $subjects ?? [] as $subj)
                                <option value="{{ $subj->id }}" data-ed-level-id="{{ $subj->education_level_id }}"
                                    data-sem="{{ $subj->semester }}">
                                    {{ $subj->subject_code }} - {{ $subj->subject_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Assigned Instructor <span style="color: #ef4444;">*</span></label>
                        <select name="teacher_id" id="edit_teacher_id" class="form-control-custom" required>
                            <option value="">-- Select Teacher --</option>
                            @foreach ($teachersList ?? $teachers ?? [] as $t)
                                <option value="{{ $t->id }}" data-ed-level-id="{{ $t->education_level_id }}">
                                    {{ $t->first_name }} {{ $t->last_name }} ({{ $t->position ?? 'Teacher' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditAssignedSubjectModal()">Cancel</button>
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
            if ($('#assignedSubjectsTable').length) {
                $('#assignedSubjectsTable').DataTable({
                    "pageLength": 10,
                    "ordering": true,
                    "order": [],
                    "responsive": true,
                    "language": {
                        "search": "",
                        "searchPlaceholder": "Search section, subject code, instructor...",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ assignments",
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

        function toggleAssignedSubRows(parentId, btn) {
            const rows = document.querySelectorAll('.assigned-sub-row-' + parentId);
            const icon = document.getElementById('icon-assigned-sub-' + parentId);
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

        function handleAssignedSectionCascade(prefix, targetSubjectId = null, targetTeacherId = null) {
            const secSelect = document.getElementById(`${prefix}_class_section_id`);
            const subjSelect = document.getElementById(`${prefix}_subject_id`);
            const teachSelect = document.getElementById(`${prefix}_teacher_id`);

            if (!secSelect || !subjSelect || !teachSelect) return;

            const selectedSecOpt = secSelect.options[secSelect.selectedIndex];
            const secEdLevelId = selectedSecOpt ? selectedSecOpt.getAttribute('data-ed-level-id') : null;

            const activeSem = "{{ request('semester', '1st Semester') }}";

            // 1. Filter Subjects by Section Education Level & Active Semester
            Array.from(subjSelect.options).forEach(opt => {
                if (!opt.value) return;
                const subjEdId = opt.getAttribute('data-ed-level-id');
                const subjSem = opt.getAttribute('data-sem');

                let matchLevel = !secEdLevelId || subjEdId === secEdLevelId;
                let matchSem = true;

                if (activeSem && subjSem) {
                    const activeKey = activeSem.toLowerCase().includes('2nd') ? '2nd' : '1st';
                    const subjKey = subjSem.toLowerCase().includes('2nd') ? '2nd' : (subjSem.toLowerCase().includes(
                        '1st') ? '1st' : '');
                    if (subjKey && subjKey !== activeKey && subjSem !== 'All Quarters') {
                        matchSem = false;
                    }
                }

                if (matchLevel && matchSem) {
                    opt.style.display = 'block';
                    opt.disabled = false;
                } else {
                    opt.style.display = 'none';
                    opt.disabled = true;
                }
            });

            if (targetSubjectId) {
                subjSelect.value = targetSubjectId;
            } else if (!subjSelect.options[subjSelect.selectedIndex] || subjSelect.options[subjSelect.selectedIndex]
                .disabled) {
                subjSelect.value = '';
            }

            // 2. Filter Teachers / Instructors by Section Education Level
            Array.from(teachSelect.options).forEach(opt => {
                if (!opt.value) return;
                const teachEdId = opt.getAttribute('data-ed-level-id');
                if (secEdLevelId && teachEdId === secEdLevelId) {
                    opt.style.display = 'block';
                    opt.disabled = false;
                } else {
                    opt.style.display = 'none';
                    opt.disabled = true;
                }
            });

            if (targetTeacherId) {
                teachSelect.value = targetTeacherId;
            } else if (!teachSelect.options[teachSelect.selectedIndex] || teachSelect.options[teachSelect.selectedIndex]
                .disabled) {
                teachSelect.value = '';
            }
        }

        function openAddAssignedSubjectModal() {
            document.getElementById('addAssignedSubjectModal').style.display = 'flex';
            handleAssignedSectionCascade('add');
        }

        function closeAddAssignedSubjectModal() {
            document.getElementById('addAssignedSubjectModal').style.display = 'none';
        }

        function openEditAssignedSubjectModal(assigned) {
            const form = document.getElementById('editAssignedSubjectForm');
            form.action = "{{ url('/admin/assigned-subjects/update') }}/" + assigned.id;

            document.getElementById('edit_class_section_id').value = assigned.class_section_id || '';

            handleAssignedSectionCascade('edit', assigned.subject_id, assigned.teacher_id);

            document.getElementById('editAssignedSubjectModal').style.display = 'flex';
        }

        function closeEditAssignedSubjectModal() {
            document.getElementById('editAssignedSubjectModal').style.display = 'none';
        }
    </script>
@endpush
