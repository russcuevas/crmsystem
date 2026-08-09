@extends('layouts.superadmin')

@section('title', 'GNHS - Enroll Students')

@push('styles')
    <!-- jQuery & DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
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

        .status-badge {
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.76rem;
            text-transform: capitalize;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .status-badge.active {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .status-badge.inactive {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #cbd5e1;
        }

        .status-badge.completed {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .status-badge.dropped,
        .status-badge.transferred {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }
    </style>
@endpush

@section('content')
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
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Enroll Students
                @if ($selectedLevel)
                    <span class="badge badge-admin" style="margin-left: 8px; font-size: 0.82rem;">{{ $selectedLevel }}</span>
                @endif
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--accent-emerald); margin-left: 6px;">
                    (S.Y. {{ $activeSchoolYear->school_year ?? '2024-2025' }})
                </span>
            </div>
            <button class="btn-primary" onclick="openAddEnrollmentModal()">+ Enroll Student</button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="custom-table" id="enrollmentsTable">
                    <thead>
                        <tr>
                            <th>Student Name & LRN</th>
                            <th>Education Level</th>
                            <th>Grade Level</th>
                            <th>Class Section</th>
                            <th>School Year</th>
                            <th>Status</th>
                            <th>Enrolled Date</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($enrollments as $enr)
                            @php
                                $stName = $enr->student->user->name ?? ($enr->student ? $enr->student->first_name . ' ' . $enr->student->last_name : 'N/A');
                                $lrn = $enr->student->lrn ?? ($enr->student->student_id ?? 'N/A');
                                $edCode = strtoupper($enr->gradeLevel->educationLevel->code ?? ($enr->classSection->gradeLevel->educationLevel->code ?? ''));
                            @endphp
                            <tr>
                                <td>
                                    <div><strong>{{ $stName }}</strong></div>
                                    <div style="font-size: 0.78rem; color: #64748b;">LRN / ID: {{ $lrn }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-admin">{{ $edCode ?: 'N/A' }}</span>
                                </td>
                                <td>{{ $enr->gradeLevel->name ?? ($enr->classSection->gradeLevel->name ?? 'N/A') }}</td>
                                <td>
                                    @if ($enr->classSection)
                                        <strong>{{ $enr->classSection->section_name }}</strong>
                                        @if ($enr->classSection->course)
                                            <span style="font-size: 0.75rem; color: #2563eb; margin-left: 4px;">
                                                ({{ $enr->classSection->course->course_code }})
                                            </span>
                                        @endif
                                    @else
                                        <span style="color: #94a3b8;">Unassigned</span>
                                    @endif
                                </td>
                                <td>S.Y. {{ $enr->schoolYear->school_year ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $stClass = strtolower($enr->status ?? 'active');
                                    @endphp
                                    <span class="status-badge {{ $stClass }}">
                                        @if ($stClass === 'active')
                                            <span style="width: 6px; height: 6px; background: #10b981; border-radius: 50%; display: inline-block;"></span>
                                        @endif
                                        {{ ucfirst($enr->status ?? 'Active') }}
                                    </span>
                                </td>
                                <td>
                                    {{ $enr->enrolled_at ? \Carbon\Carbon::parse($enr->enrolled_at)->format('M d, Y') : 'N/A' }}
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 0.35rem; justify-content: center;">
                                        <button type="button" class="btn-action-icon" title="Edit Enrollment"
                                            onclick='openEditEnrollmentModal(@json($enr))'>
                                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <form action="{{ route('superadmin.enrollment.destroy', $enr->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this student enrollment record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-icon danger" title="Delete Enrollment">
                                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

    <!-- Modal: Enroll Student -->
    <div class="modal-overlay" id="addEnrollmentModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0;">Enroll Student to Class Section</h3>
                <button type="button" onclick="closeAddEnrollmentModal()" style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('superadmin.enrollment.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Student <span style="color: #ef4444;">*</span></label>
                        <select name="student_id" id="add_enrollment_student_id" class="form-control-custom" required onchange="handleStudentCascade('add')">
                            <option value="">-- Select Student --</option>
                            @foreach ($students as $st)
                                @php
                                    $stName = $st->user->name ?? ($st->first_name . ' ' . $st->last_name);
                                    $stCode = $st->educationLevel->code ?? '';
                                @endphp
                                <option value="{{ $st->id }}" data-ed-level-id="{{ $st->education_level_id ?? '' }}">
                                    {{ $st->student_id ?: ($st->lrn ?: 'No-ID') }} - {{ $stName }} {{ $stCode ? "($stCode)" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>School Year <span style="color: #ef4444;">*</span></label>
                            <select name="school_year_id" id="add_enrollment_school_year" class="form-control-custom" required>
                                @foreach ($allSchoolYears as $sy)
                                    <option value="{{ $sy->id }}" {{ $activeSchoolYear && $activeSchoolYear->id == $sy->id ? 'selected' : '' }}>
                                        S.Y. {{ $sy->school_year }} {{ $sy->is_active ? '(Active)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Select Class Section <span style="color: #ef4444;">*</span></label>
                            <select name="class_section_id" id="add_enrollment_class_section_id" class="form-control-custom" required>
                                <option value="">-- Select Class Section --</option>
                                @foreach ($classSections as $sec)
                                    <option value="{{ $sec->id }}" data-ed-level-id="{{ $sec->gradeLevel->education_level_id ?? '' }}">
                                        {{ $sec->section_name }} ({{ $sec->gradeLevel->name ?? '' }}{{ $sec->course ? ' - ' . $sec->course->course_code : '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Enrollment Status <span style="color: #ef4444;">*</span></label>
                            <select name="status" class="form-control-custom" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="completed">Completed</option>
                                <option value="dropped">Dropped</option>
                                <option value="transferred">Transferred</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Enrolled Date <span style="color: #ef4444;">*</span></label>
                            <input type="date" name="enrolled_at" class="form-control-custom" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddEnrollmentModal()">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </button>
                    <button type="submit" class="btn-submit">+ Enroll Student</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Enrollment -->
    <div class="modal-overlay" id="editEnrollmentModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0;">Edit Student Enrollment</h3>
                <button type="button" onclick="closeEditEnrollmentModal()" style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form id="editEnrollmentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Student Name</label>
                        <input type="text" id="edit_student_name_display" class="form-control-custom" readonly style="background: #f1f5f9; font-weight: 700; color: #0f172a;">
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>School Year <span style="color: #ef4444;">*</span></label>
                            <select name="school_year_id" id="edit_enrollment_school_year" class="form-control-custom" required>
                                @foreach ($allSchoolYears as $sy)
                                    <option value="{{ $sy->id }}">
                                        S.Y. {{ $sy->school_year }} {{ $sy->is_active ? '(Active)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Select Class Section <span style="color: #ef4444;">*</span></label>
                            <select name="class_section_id" id="edit_enrollment_class_section_id" class="form-control-custom" required>
                                <option value="">-- Select Class Section --</option>
                                @foreach ($classSections as $sec)
                                    <option value="{{ $sec->id }}" data-ed-level-id="{{ $sec->gradeLevel->education_level_id ?? '' }}">
                                        {{ $sec->section_name }} ({{ $sec->gradeLevel->name ?? '' }}{{ $sec->course ? ' - ' . $sec->course->course_code : '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Enrollment Status <span style="color: #ef4444;">*</span></label>
                            <select name="status" id="edit_enrollment_status" class="form-control-custom" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="completed">Completed</option>
                                <option value="dropped">Dropped</option>
                                <option value="transferred">Transferred</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Enrolled Date <span style="color: #ef4444;">*</span></label>
                            <input type="date" name="enrolled_at" id="edit_enrollment_date" class="form-control-custom" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditEnrollmentModal()">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
            if ($('#enrollmentsTable').length) {
                $('#enrollmentsTable').DataTable({
                    "pageLength": 10,
                    "ordering": true,
                    "order": [],
                    "responsive": true,
                    "language": {
                        "search": "",
                        "searchPlaceholder": "Search student name, LRN, section...",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ student enrollments",
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

        // Filter Class Sections by Student's Education Level
        function handleStudentCascade(prefix, targetSectionId = null) {
            const stSelect = document.getElementById(prefix + '_enrollment_student_id');
            const secSelect = document.getElementById(prefix + '_enrollment_class_section_id');

            if (!stSelect || !secSelect) return;

            const selectedOpt = stSelect.options[stSelect.selectedIndex];
            const stEdLevelId = selectedOpt ? selectedOpt.getAttribute('data-ed-level-id') : null;

            Array.from(secSelect.options).forEach(opt => {
                if (!opt.value) return;
                const secEdId = opt.getAttribute('data-ed-level-id');
                if (!stEdLevelId || secEdId === stEdLevelId) {
                    opt.style.display = 'block';
                    opt.disabled = false;
                } else {
                    opt.style.display = 'none';
                    opt.disabled = true;
                }
            });

            if (targetSectionId) {
                secSelect.value = targetSectionId;
            } else if (secSelect.options[secSelect.selectedIndex] && secSelect.options[secSelect.selectedIndex].disabled) {
                secSelect.value = '';
            }
        }

        function openAddEnrollmentModal() {
            document.getElementById('addEnrollmentModal').style.display = 'flex';
            handleStudentCascade('add');
        }

        function closeAddEnrollmentModal() {
            document.getElementById('addEnrollmentModal').style.display = 'none';
        }

        function openEditEnrollmentModal(enr) {
            const form = document.getElementById('editEnrollmentForm');
            form.action = "{{ url('/superadmin/enrollment/update') }}/" + enr.id;

            const stName = enr.student && enr.student.user ? enr.student.user.name : (enr.student ? enr.student.first_name + ' ' + enr.student.last_name : 'N/A');
            const lrn = enr.student ? (enr.student.student_id || enr.student.lrn || '') : '';
            document.getElementById('edit_student_name_display').value = stName + (lrn ? ' (' + lrn + ')' : '');

            document.getElementById('edit_enrollment_school_year').value = enr.school_year_id || '';
            document.getElementById('edit_enrollment_class_section_id').value = enr.class_section_id || '';
            document.getElementById('edit_enrollment_status').value = enr.status || 'active';

            if (enr.enrolled_at) {
                document.getElementById('edit_enrollment_date').value = enr.enrolled_at.substring(0, 10);
            }

            document.getElementById('editEnrollmentModal').style.display = 'flex';
        }

        function closeEditEnrollmentModal() {
            document.getElementById('editEnrollmentModal').style.display = 'none';
        }
    </script>
@endpush
