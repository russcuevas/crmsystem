@extends('layouts.superadmin')

@section('title', 'GNHS - Assigned Subjects Roster')

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
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Assigned Subjects Roster
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--accent-emerald); margin-left: 6px;">
                    (S.Y. {{ $activeSchoolYear->school_year ?? '2024-2025' }})
                </span>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <button class="btn-primary" onclick="openAddAssignedSubjectModal()">+ Assign Subject to Section</button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="custom-table" id="assignedSubjectsTable">
                    <thead>
                        <tr>
                            <th>Class Section</th>
                            <th>Education Level</th>
                            <th>Subject Code & Title</th>
                            <th>Assigned Teacher</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assignedSubjects as $assigned)
                            @php
                                $secLevelCode = $assigned->classSection->gradeLevel->educationLevel->code ?? 'N/A';
                                $secCourseCode = $assigned->classSection->course->course_code ?? null;
                                $teacherName =
                                    $assigned->teacher->user->name ??
                                    $assigned->teacher->first_name . ' ' . $assigned->teacher->last_name;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $assigned->classSection->section_name ?? 'N/A' }}</strong>
                                    @if ($secCourseCode)
                                        <span
                                            style="font-size: 0.75rem; color: #64748b; margin-left: 4px;">({{ $secCourseCode }})</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-admin">{{ $secLevelCode }}</span>
                                </td>
                                <td>
                                    <strong>{{ $assigned->subject->subject_code ?? 'N/A' }}</strong> -
                                    {{ $assigned->subject->subject_name ?? '' }}
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

                                        <form action="{{ route('superadmin.assigned_subjects.destroy', $assigned->id) }}"
                                            method="POST" style="display: inline;"
                                            onsubmit="return confirm('Are you sure you want to remove this assigned subject?');">
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
            <form action="{{ route('superadmin.assigned_subjects.store') }}" method="POST">
                @csrf
                <div class="modal-body">

                    <div class="form-group">
                        <label>Select Class Section <span style="color: #ef4444;">*</span></label>
                        <select name="class_section_id" id="add_class_section_id" class="form-control-custom" required
                            onchange="handleAssignedSectionCascade('add')">
                            <option value="">-- Select Class Section --</option>
                            @foreach ($classSections as $sec)
                                <option value="{{ $sec->id }}"
                                    data-ed-level-id="{{ $sec->gradeLevel->education_level_id ?? '' }}">
                                    {{ $sec->section_name }}
                                    ({{ $sec->gradeLevel->educationLevel->code ?? 'N/A' }}{{ $sec->course ? ' - ' . $sec->course->course_code : '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Select Subject <span style="color: #ef4444;">*</span></label>
                        <select name="subject_id" id="add_subject_id" class="form-control-custom" required>
                            <option value="">-- Select Subject --</option>
                            @foreach ($subjects as $subj)
                                <option value="{{ $subj->id }}"
                                    data-ed-level-id="{{ $subj->education_level_id ?? '' }}">
                                    {{ $subj->subject_code }} - {{ $subj->subject_name }}
                                    ({{ $subj->educationLevel->code ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Assigned Teacher / Instructor <span style="color: #ef4444;">*</span></label>
                        <select name="teacher_id" id="add_teacher_id" class="form-control-custom" required>
                            <option value="">-- Select Teacher --</option>
                            @foreach ($teachers as $t)
                                @php
                                    $tName = $t->user->name ?? $t->first_name . ' ' . $t->last_name;
                                    $tCode = $t->educationLevel->code ?? '';
                                @endphp
                                <option value="{{ $t->id }}"
                                    data-ed-level-id="{{ $t->education_level_id ?? '' }}">
                                    {{ $t->teacher_id }} - {{ $tName }} {{ $tCode ? "($tCode)" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddAssignedSubjectModal()">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </button>
                    <button type="submit" class="btn-submit">+ Assign Subject</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Assigned Subject -->
    <div class="modal-overlay" id="editAssignedSubjectModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0;">Edit Subject Assignment</h3>
                <button type="button" onclick="closeEditAssignedSubjectModal()"
                    style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form id="editAssignedSubjectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Class Section <span style="color: #ef4444;">*</span></label>
                        <select name="class_section_id" id="edit_class_section_id" class="form-control-custom" required
                            onchange="handleAssignedSectionCascade('edit')">
                            <option value="">-- Select Class Section --</option>
                            @foreach ($classSections as $sec)
                                <option value="{{ $sec->id }}"
                                    data-ed-level-id="{{ $sec->gradeLevel->education_level_id ?? '' }}">
                                    {{ $sec->section_name }}
                                    ({{ $sec->gradeLevel->educationLevel->code ?? 'N/A' }}{{ $sec->course ? ' - ' . $sec->course->course_code : '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Select Subject <span style="color: #ef4444;">*</span></label>
                        <select name="subject_id" id="edit_subject_id" class="form-control-custom" required>
                            <option value="">-- Select Subject --</option>
                            @foreach ($subjects as $subj)
                                <option value="{{ $subj->id }}"
                                    data-ed-level-id="{{ $subj->education_level_id ?? '' }}">
                                    {{ $subj->subject_code }} - {{ $subj->subject_name }}
                                    ({{ $subj->educationLevel->code ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Assigned Teacher / Instructor <span style="color: #ef4444;">*</span></label>
                        <select name="teacher_id" id="edit_teacher_id" class="form-control-custom" required>
                            <option value="">-- Select Teacher --</option>
                            @foreach ($teachers as $t)
                                @php
                                    $tName = $t->user->name ?? $t->first_name . ' ' . $t->last_name;
                                    $tCode = $t->educationLevel->code ?? '';
                                @endphp
                                <option value="{{ $t->id }}"
                                    data-ed-level-id="{{ $t->education_level_id ?? '' }}">
                                    {{ $t->teacher_id }} - {{ $tName }} {{ $tCode ? "($tCode)" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditAssignedSubjectModal()">
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
            if ($('#assignedSubjectsTable').length) {
                $('#assignedSubjectsTable').DataTable({
                    "pageLength": 10,
                    "ordering": true,
                    "order": [],
                    "responsive": true,
                    "language": {
                        "search": "",
                        "searchPlaceholder": "Search section, subject, teacher...",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ assigned subjects",
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

        function handleAssignedSectionCascade(prefix, targetSubjectId = null, targetTeacherId = null) {
            const secSelect = document.getElementById(prefix + '_class_section_id');
            const subjSelect = document.getElementById(prefix + '_subject_id');
            const teachSelect = document.getElementById(prefix + '_teacher_id');

            if (!secSelect || !subjSelect || !teachSelect) return;

            const selectedOpt = secSelect.options[secSelect.selectedIndex];
            const secEdLevelId = selectedOpt ? selectedOpt.getAttribute('data-ed-level-id') : null;

            // 1. Filter Subjects by Section Education Level
            Array.from(subjSelect.options).forEach(opt => {
                if (!opt.value) return;
                const subjEdId = opt.getAttribute('data-ed-level-id');
                if (secEdLevelId && subjEdId === secEdLevelId) {
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
            form.action = "{{ url('/superadmin/assigned-subjects/update') }}/" + assigned.id;

            document.getElementById('edit_class_section_id').value = assigned.class_section_id || '';

            handleAssignedSectionCascade('edit', assigned.subject_id, assigned.teacher_id);

            document.getElementById('editAssignedSubjectModal').style.display = 'flex';
        }

        function closeEditAssignedSubjectModal() {
            document.getElementById('editAssignedSubjectModal').style.display = 'none';
        }
    </script>
@endpush
