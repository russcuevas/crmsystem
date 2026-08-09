@extends('layouts.superadmin')

@section('title', 'GNHS - Student Registry')

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
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 1.5rem 1rem;
            overflow-y: auto;
        }

        .modal-card-xl {
            background: #ffffff;
            border-radius: 16px;
            width: 100%;
            max-width: 1080px;
            max-height: 88vh;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: modalIn 0.2s ease-out;
        }

        .modal-card-xl form {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: translateY(-12px) scale(0.98);
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
            flex-shrink: 0;
        }

        .modal-body {
            padding: 1.5rem;
            overflow-y: auto;
            max-height: calc(88vh - 130px);
            flex: 1;
            background: #f8fafc;
        }

        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            flex-shrink: 0;
        }

        .modal-parts-grid {
            display: grid;
            grid-template-columns: 1fr 1.65fr;
            gap: 1.25rem;
            align-items: start;
        }

        @media (max-width: 900px) {
            .modal-parts-grid {
                grid-template-columns: 1fr;
            }
        }

        .modal-part-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.2rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .form-section-title {
            font-size: 0.82rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #2563eb;
            background: #eff6ff;
            padding: 0.45rem 0.85rem;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
            margin-bottom: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.85rem;
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.85rem;
        }

        .form-group {
            margin-bottom: 0.95rem;
        }

        .form-group label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.35rem;
        }

        .form-control-custom {
            width: 100%;
            padding: 0.55rem 0.75rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.85rem;
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
            padding: 0.55rem 1.1rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            font-size: 0.85rem;
            font-weight: 700;
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
            padding: 0.55rem 1.25rem;
            border-radius: 8px;
            border: none;
            background: var(--accent-gold, #f5b41d);
            color: #0f172a;
            font-size: 0.85rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            background: #d97706;
            color: #ffffff;
        }

        .btn-action-icon {
            padding: 0.35rem;
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

        .btn-action-view {
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #2563eb;
            font-size: 0.78rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: all 0.15s ease;
        }

        .btn-action-view:hover {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
                Enrolled Student Registry
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--accent-emerald); margin-left: 6px;">
                    (S.Y. {{ $activeSchoolYear->school_year ?? '2024-2025' }})
                </span>
            </div>

            <button class="btn-primary" onclick="openAddStudentModal()">+ Register Student</button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="custom-table" id="studentsTable">
                    <thead>
                        <tr>
                            <th>Student No.</th>
                            <th>LRN</th>
                            <th>Full Name</th>
                            <th>Level</th>
                            <th>Grade Level</th>
                            <th>Course / Strand</th>
                            <th>Gender</th>
                            <th>Location / Address</th>
                            <th>Status</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td><strong>{{ $student->student_number }}</strong></td>
                                <td>{{ $student->lrn ?? 'N/A' }}</td>
                                <td>
                                    <strong>{{ $student->first_name }}
                                        {{ $student->middle_name ? $student->middle_name . ' ' : '' }}{{ $student->last_name }}
                                        {{ $student->extension_name }}</strong>
                                </td>
                                <td>
                                    <span class="badge badge-teacher">{{ $student->educationLevel->code ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $student->gradeLevel->name ?? 'N/A' }}</td>
                                <td>{{ $student->course->course_code ?? 'N/A' }}</td>
                                <td>{{ $student->gender ?? 'N/A' }}</td>
                                <td style="font-size: 0.8rem; color: #475569;">
                                    @php
                                        $loc = array_filter([$student->barangay, $student->city, $student->province]);
                                    @endphp
                                    {{ !empty($loc) ? implode(', ', $loc) : 'N/A' }}
                                </td>
                                <td><span class="badge badge-active">{{ ucfirst($student->status ?? 'active') }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 0.35rem; justify-content: center; align-items: center;">
                                        <a href="{{ route('superadmin.students.show', $student->id) }}"
                                            class="btn-action-view" title="View Profile Details">
                                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </a>

                                        <button type="button" class="btn-action-icon" title="Edit Student Profile"
                                            onclick='openEditStudentModal(@json($student))'>
                                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <form action="{{ route('superadmin.students.destroy', $student->id) }}"
                                            method="POST" style="display: inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this student?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-icon danger" title="Delete Student">
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

    <!-- Modal: Register Student (XL 2-Column Layout) -->
    <div class="modal-overlay" id="addStudentModal">
        <div class="modal-card-xl">
            <div class="modal-header">
                <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0;">Register New Student</h3>
                <button type="button" onclick="closeAddStudentModal()"
                    style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('superadmin.students.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="modal-parts-grid">

                        <!-- COLUMN 1: PART 1 ACCOUNT DETAILS FOR LMS -->
                        <div class="modal-part-card">
                            <div class="form-section-title">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Part 1: Account Details for LMS
                            </div>

                            <div class="form-group">
                                <label>LMS Email Address <span style="color: #ef4444;">*</span></label>
                                <input type="email" name="email" class="form-control-custom" required
                                    placeholder="e.g. student@gnhs.edu.ph">
                            </div>

                            <div class="form-group">
                                <label>LMS Password <span style="color: #ef4444;">*</span></label>
                                <input type="password" name="password" class="form-control-custom" required
                                    placeholder="Min. 6 characters">
                            </div>

                            <div class="form-group">
                                <label>Account Role</label>
                                <input type="text" value="Student" class="form-control-custom" readonly
                                    style="background: #f1f5f9; font-weight: 700; color: #0284c7;">
                            </div>

                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Account Status</label>
                                <input type="text" value="Active" class="form-control-custom" readonly
                                    style="background: #f1f5f9; font-weight: 700; color: #16a34a;">
                            </div>
                        </div>

                        <!-- COLUMN 2: PART 2 STUDENT PROFILE INFORMATION -->
                        <div class="modal-part-card">
                            <div class="form-section-title">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 14l9-5-9-5-9 5 9 5z" />
                                </svg>
                                Part 2: Student Information
                            </div>

                            <!-- Academic Level Dropdowns -->
                            <div class="form-grid-3">
                                <div class="form-group">
                                    <label>Education Level <span style="color: #ef4444;">*</span></label>
                                    @php
                                        $autoLevelObj = null;
                                        if ($selectedLevel) {
                                            $autoLevelObj = $educationLevelsList->first(function ($l) use (
                                                $selectedLevel,
                                            ) {
                                                return strtoupper($l->code) == strtoupper($selectedLevel) ||
                                                    strtoupper($l->name) == strtoupper($selectedLevel);
                                            });
                                        }
                                    @endphp

                                    @if ($autoLevelObj)
                                        <input type="hidden" name="education_level_id"
                                            id="add_student_education_level_hidden" value="{{ $autoLevelObj->id }}"
                                            data-code="{{ strtoupper($autoLevelObj->code) }}">
                                        <input type="text"
                                            value="{{ $autoLevelObj->name }} ({{ $autoLevelObj->code }})"
                                            class="form-control-custom" readonly
                                            style="background: #f1f5f9; font-weight: 700; color: var(--primary-navy, #0f172a); cursor: not-allowed;">
                                    @else
                                        <select name="education_level_id" id="add_student_education_level"
                                            class="form-control-custom" required
                                            onchange="handleStudentLevelCascade('add')">
                                            <option value="">-- Select Level --</option>
                                            @foreach ($educationLevelsList as $level)
                                                <option value="{{ $level->id }}"
                                                    data-code="{{ strtoupper($level->code) }}">
                                                    {{ $level->name }} ({{ $level->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>Grade Level</label>
                                    <select name="grade_level_id" id="add_student_grade_level"
                                        class="form-control-custom">
                                        <option value="">-- Select Grade Level --</option>
                                        @foreach ($allGradeLevels as $gl)
                                            <option value="{{ $gl->id }}"
                                                data-ed-level-id="{{ $gl->education_level_id }}">
                                                {{ $gl->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" id="add_student_course_group" style="display: none;">
                                    <label>Course / Strand <span
                                            style="font-size: 0.7rem; color: #64748b;">(SHS/College)</span></label>
                                    <select name="course_id" id="add_student_course" class="form-control-custom"
                                        disabled>
                                        <option value="">-- Select Course / Strand --</option>
                                        @foreach ($allCourses as $c)
                                            <option value="{{ $c->id }}"
                                                data-level="{{ strtoupper($c->level) }}">
                                                {{ $c->course_code }} - {{ $c->course_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label>Student No. <span
                                            style="font-size: 0.72rem; color: #64748b;">(Auto)</span></label>
                                    <input type="text" name="student_number" value="{{ $nextStudentId }}"
                                        class="form-control-custom" placeholder="e.g. STU-2026-0001">
                                </div>
                                <div class="form-group">
                                    <label>LRN Number <span style="font-size: 0.72rem; color: #64748b;">(Learner Ref
                                            No.)</span></label>
                                    <input type="text" name="lrn" class="form-control-custom"
                                        placeholder="e.g. 109876543210">
                                </div>
                            </div>

                            <div class="form-grid-3">
                                <div class="form-group">
                                    <label>First Name <span style="color: #ef4444;">*</span></label>
                                    <input type="text" name="first_name" class="form-control-custom" required
                                        placeholder="First Name">
                                </div>
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input type="text" name="middle_name" class="form-control-custom"
                                        placeholder="Middle Name">
                                </div>
                                <div class="form-group">
                                    <label>Last Name <span style="color: #ef4444;">*</span></label>
                                    <input type="text" name="last_name" class="form-control-custom" required
                                        placeholder="Last Name">
                                </div>
                            </div>

                            <div class="form-grid-3">
                                <div class="form-group">
                                    <label>Ext. Name</label>
                                    <input type="text" name="extension_name" class="form-control-custom"
                                        placeholder="e.g. Jr.">
                                </div>
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="gender" class="form-control-custom">
                                        <option value="">-- Select Gender --</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Birthday</label>
                                    <input type="date" name="birthday" class="form-control-custom">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" name="phone_number" class="form-control-custom"
                                    placeholder="e.g. 09171234567">
                            </div>

                            <!-- Dynamic Location Dropdowns (PSGC API) -->
                            <div
                                style="background: #f8fafc; padding: 0.85rem; border-radius: 10px; border: 1px solid #cbd5e1; margin-top: 0.5rem;">
                                <label
                                    style="font-size: 0.78rem; font-weight: 700; color: #1e293b; display: block; margin-bottom: 0.6rem;">
                                    📍 Address Location
                                </label>
                                <div class="form-grid-3">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label>Province</label>
                                        <select name="province" id="add_student_province_select"
                                            class="form-control-custom">
                                            <option value="">Loading Provinces...</option>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label>City / Municipality</label>
                                        <select name="city" id="add_student_city_select" class="form-control-custom">
                                            <option value="">-- Select Province First --</option>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label>Barangay</label>
                                        <select name="barangay" id="add_student_barangay_select"
                                            class="form-control-custom">
                                            <option value="">-- Select City First --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddStudentModal()">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </button>
                    <button type="submit" class="btn-submit">+ Register Student</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Student Profile (XL 2-Column Layout) -->
    <div class="modal-overlay" id="editStudentModal">
        <div class="modal-card-xl">
            <div class="modal-header">
                <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0;">Edit Student Profile Details</h3>
                <button type="button" onclick="closeEditStudentModal()"
                    style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form id="editStudentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="modal-parts-grid">

                        <!-- COLUMN 1: PART 1 ACCOUNT DETAILS FOR LMS -->
                        <div class="modal-part-card">
                            <div class="form-section-title">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Part 1: Account Details for LMS
                            </div>

                            <div class="form-group">
                                <label>LMS Email Address <span style="color: #ef4444;">*</span></label>
                                <input type="email" name="email" id="edit_student_email" class="form-control-custom"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>LMS New Password <span
                                        style="font-size: 0.7rem; color: #64748b;">(Optional)</span></label>
                                <input type="password" name="password" class="form-control-custom"
                                    placeholder="Leave blank to keep existing">
                            </div>

                            <div class="form-group">
                                <label>Account Role</label>
                                <input type="text" value="Student" class="form-control-custom" readonly
                                    style="background: #f1f5f9; font-weight: 700; color: #0284c7;">
                            </div>

                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Account Status</label>
                                <input type="text" value="Active" class="form-control-custom" readonly
                                    style="background: #f1f5f9; font-weight: 700; color: #16a34a;">
                            </div>
                        </div>

                        <!-- COLUMN 2: PART 2 STUDENT PROFILE INFORMATION -->
                        <div class="modal-part-card">
                            <div class="form-section-title">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 14l9-5-9-5-9 5 9 5z" />
                                </svg>
                                Part 2: Student Information
                            </div>

                            <!-- Academic Level Dropdowns -->
                            <div class="form-grid-3">
                                <div class="form-group">
                                    <label>Education Level <span style="color: #ef4444;">*</span></label>
                                    <select name="education_level_id" id="edit_student_education_level"
                                        class="form-control-custom" required onchange="handleStudentLevelCascade('edit')">
                                        <option value="">-- Select Level --</option>
                                        @foreach ($educationLevelsList as $level)
                                            <option value="{{ $level->id }}"
                                                data-code="{{ strtoupper($level->code) }}">
                                                {{ $level->name }} ({{ $level->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Grade Level</label>
                                    <select name="grade_level_id" id="edit_student_grade_level"
                                        class="form-control-custom">
                                        <option value="">-- Select Grade Level --</option>
                                        @foreach ($allGradeLevels as $gl)
                                            <option value="{{ $gl->id }}"
                                                data-ed-level-id="{{ $gl->education_level_id }}">
                                                {{ $gl->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group" id="edit_student_course_group" style="display: none;">
                                    <label>Course / Strand <span
                                            style="font-size: 0.7rem; color: #64748b;">(SHS/College)</span></label>
                                    <select name="course_id" id="edit_student_course" class="form-control-custom"
                                        disabled>
                                        <option value="">-- Select Course / Strand --</option>
                                        @foreach ($allCourses as $c)
                                            <option value="{{ $c->id }}"
                                                data-level="{{ strtoupper($c->level) }}">
                                                {{ $c->course_code }} - {{ $c->course_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label>Student No. <span style="color: #ef4444;">*</span></label>
                                    <input type="text" name="student_number" id="edit_student_number"
                                        class="form-control-custom" required>
                                </div>
                                <div class="form-group">
                                    <label>LRN Number</label>
                                    <input type="text" name="lrn" id="edit_student_lrn"
                                        class="form-control-custom">
                                </div>
                            </div>

                            <div class="form-grid-3">
                                <div class="form-group">
                                    <label>First Name <span style="color: #ef4444;">*</span></label>
                                    <input type="text" name="first_name" id="edit_student_first_name"
                                        class="form-control-custom" required>
                                </div>
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input type="text" name="middle_name" id="edit_student_middle_name"
                                        class="form-control-custom">
                                </div>
                                <div class="form-group">
                                    <label>Last Name <span style="color: #ef4444;">*</span></label>
                                    <input type="text" name="last_name" id="edit_student_last_name"
                                        class="form-control-custom" required>
                                </div>
                            </div>

                            <div class="form-grid-3">
                                <div class="form-group">
                                    <label>Ext. Name</label>
                                    <input type="text" name="extension_name" id="edit_student_extension_name"
                                        class="form-control-custom">
                                </div>
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="gender" id="edit_student_gender" class="form-control-custom">
                                        <option value="">-- Select Gender --</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Birthday</label>
                                    <input type="date" name="birthday" id="edit_student_birthday"
                                        class="form-control-custom">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" name="phone_number" id="edit_student_phone_number"
                                    class="form-control-custom">
                            </div>

                            <!-- Dynamic Location Dropdowns (PSGC API) -->
                            <div
                                style="background: #f8fafc; padding: 0.85rem; border-radius: 10px; border: 1px solid #cbd5e1; margin-top: 0.5rem;">
                                <label
                                    style="font-size: 0.78rem; font-weight: 700; color: #1e293b; display: block; margin-bottom: 0.6rem;">
                                    📍 Address Location
                                </label>
                                <div class="form-grid-3">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label>Province</label>
                                        <select name="province" id="edit_student_province_select"
                                            class="form-control-custom">
                                            <option value="">Loading Provinces...</option>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label>City / Municipality</label>
                                        <select name="city" id="edit_student_city_select" class="form-control-custom">
                                            <option value="">-- Select Province First --</option>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label>Barangay</label>
                                        <select name="barangay" id="edit_student_barangay_select"
                                            class="form-control-custom">
                                            <option value="">-- Select City First --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditStudentModal()">
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
        const PSGC_BASE = 'https://psgc.gitlab.io/api';

        // Dynamic Level Cascade for Grade Levels and Courses
        function handleStudentLevelCascade(prefix, targetGradeLevelId = null, targetCourseId = null) {
            let edSelect = document.getElementById(prefix + '_student_education_level');
            if (!edSelect) {
                edSelect = document.getElementById(prefix + '_student_education_level_hidden');
            }
            const glSelect = document.getElementById(prefix + '_student_grade_level');
            const crsGroup = document.getElementById(prefix + '_student_course_group');
            const crsSelect = document.getElementById(prefix + '_student_course');

            if (!edSelect || !glSelect || !crsSelect) return;

            let edLevelId = edSelect.value;
            let edCode = '';

            if (edSelect.tagName.toLowerCase() === 'select') {
                const selectedOption = edSelect.options[edSelect.selectedIndex];
                edCode = selectedOption ? (selectedOption.getAttribute('data-code') || '') : '';
            } else {
                edCode = edSelect.getAttribute('data-code') || '';
            }

            // Filter Grade Levels based on edLevelId
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

            // Filter & Toggle Courses/Strands Group (Display ONLY for SHS & COLLEGE)
            if (edCode === 'SHS' || edCode === 'COLLEGE') {
                if (crsGroup) crsGroup.style.display = 'block';
                crsSelect.disabled = false;
                crsSelect.options[0].textContent = '-- Select Course / Strand --';

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
                Array.from(crsSelect.options).forEach(opt => {
                    if (opt.value) {
                        opt.style.display = 'none';
                        opt.disabled = true;
                    }
                });
            }
        }

        // Helper to load Provinces
        async function loadProvinces(provinceSelect, selectedProvince = null, citySelect = null, selectedCity = null,
            barangaySelect = null, selectedBarangay = null) {
            try {
                const response = await fetch(`${PSGC_BASE}/provinces.json`);
                const provinces = await response.json();

                provinces.sort((a, b) => a.name.localeCompare(b.name));

                provinceSelect.innerHTML = '<option value="">-- Select Province --</option>';
                let matchedProvinceCode = null;

                provinces.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.name;
                    opt.textContent = p.name;
                    opt.setAttribute('data-code', p.code);

                    if (selectedProvince && (p.name.toLowerCase() === selectedProvince.toLowerCase() || p
                            .code === selectedProvince)) {
                        opt.selected = true;
                        matchedProvinceCode = p.code;
                    }
                    provinceSelect.appendChild(opt);
                });

                if (matchedProvinceCode && citySelect) {
                    await loadCities(matchedProvinceCode, citySelect, selectedCity, barangaySelect, selectedBarangay);
                }
            } catch (err) {
                console.error("PSGC Provinces Error:", err);
                provinceSelect.innerHTML = '<option value="">-- Select Province --</option>';
            }
        }

        // Helper to load Cities/Municipalities
        async function loadCities(provinceCode, citySelect, selectedCity = null, barangaySelect = null, selectedBarangay =
            null) {
            if (!citySelect) return;
            citySelect.innerHTML = '<option value="">Loading Cities...</option>';
            if (barangaySelect) barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';

            try {
                const response = await fetch(`${PSGC_BASE}/provinces/${provinceCode}/cities-municipalities.json`);
                const cities = await response.json();

                cities.sort((a, b) => a.name.localeCompare(b.name));

                citySelect.innerHTML = '<option value="">-- Select City / Municipality --</option>';
                let matchedCityCode = null;

                cities.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.name;
                    opt.textContent = c.name;
                    opt.setAttribute('data-code', c.code);

                    if (selectedCity && (c.name.toLowerCase() === selectedCity.toLowerCase() || c.code ===
                            selectedCity)) {
                        opt.selected = true;
                        matchedCityCode = c.code;
                    }
                    citySelect.appendChild(opt);
                });

                if (matchedCityCode && barangaySelect) {
                    await loadBarangays(matchedCityCode, barangaySelect, selectedBarangay);
                }
            } catch (err) {
                console.error("PSGC Cities Error:", err);
                citySelect.innerHTML = '<option value="">-- Select City / Municipality --</option>';
            }
        }

        // Helper to load Barangays
        async function loadBarangays(cityCode, barangaySelect, selectedBarangay = null) {
            if (!barangaySelect) return;
            barangaySelect.innerHTML = '<option value="">Loading Barangays...</option>';

            try {
                const response = await fetch(`${PSGC_BASE}/cities-municipalities/${cityCode}/barangays.json`);
                const barangays = await response.json();

                barangays.sort((a, b) => a.name.localeCompare(b.name));

                barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';

                barangays.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.name;
                    opt.textContent = b.name;

                    if (selectedBarangay && b.name.toLowerCase() === selectedBarangay.toLowerCase()) {
                        opt.selected = true;
                    }
                    barangaySelect.appendChild(opt);
                });
            } catch (err) {
                console.error("PSGC Barangays Error:", err);
                barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
            }
        }

        // Attach Change Listeners for Location Cascades
        function bindLocationCascades(provinceSelect, citySelect, barangaySelect) {
            provinceSelect.addEventListener('change', function() {
                const selectedOpt = this.options[this.selectedIndex];
                const code = selectedOpt.getAttribute('data-code');
                if (code) {
                    loadCities(code, citySelect, null, barangaySelect, null);
                } else {
                    citySelect.innerHTML = '<option value="">-- Select Province First --</option>';
                    barangaySelect.innerHTML = '<option value="">-- Select City First --</option>';
                }
            });

            citySelect.addEventListener('change', function() {
                const selectedOpt = this.options[this.selectedIndex];
                const code = selectedOpt.getAttribute('data-code');
                if (code) {
                    loadBarangays(code, barangaySelect, null);
                } else {
                    barangaySelect.innerHTML = '<option value="">-- Select City First --</option>';
                }
            });
        }

        $(document).ready(function() {
            // DataTables Initialization
            if ($('#studentsTable').length) {
                $('#studentsTable').DataTable({
                    "pageLength": 10,
                    "ordering": true,
                    "order": [],
                    "responsive": true,
                    "language": {
                        "search": "",
                        "searchPlaceholder": "Search student no, LRN, name...",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ students",
                        "paginate": {
                            "previous": "‹",
                            "next": "›"
                        }
                    }
                });
            }

            // Trigger initial cascade for Add Student Modal if auto-level active
            handleStudentLevelCascade('add');

            // Bind Add Student Location Cascades
            const addProv = document.getElementById('add_student_province_select');
            const addCity = document.getElementById('add_student_city_select');
            const addBrgy = document.getElementById('add_student_barangay_select');
            if (addProv && addCity && addBrgy) {
                loadProvinces(addProv, null, addCity, null, addBrgy, null);
                bindLocationCascades(addProv, addCity, addBrgy);
            }

            // Bind Edit Student Location Cascades
            const editProv = document.getElementById('edit_student_province_select');
            const editCity = document.getElementById('edit_student_city_select');
            const editBrgy = document.getElementById('edit_student_barangay_select');
            if (editProv && editCity && editBrgy) {
                bindLocationCascades(editProv, editCity, editBrgy);
            }

            // Click outside overlay to close modal
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal-overlay')) {
                    e.target.style.display = 'none';
                }
            });
        });

        function openAddStudentModal() {
            document.getElementById('addStudentModal').style.display = 'flex';
            handleStudentLevelCascade('add');
        }

        function closeAddStudentModal() {
            document.getElementById('addStudentModal').style.display = 'none';
        }

        function openEditStudentModal(student) {
            const form = document.getElementById('editStudentForm');
            form.action = "{{ url('/superadmin/students/update') }}/" + student.id;

            // Part 1: User Account
            document.getElementById('edit_student_email').value = (student.user && student.user.email) ? student.user
                .email : '';

            // Part 2: Academic Level Dropdowns
            const editEdLevelSelect = document.getElementById('edit_student_education_level');
            if (editEdLevelSelect) {
                editEdLevelSelect.value = student.education_level_id || '';
            }

            handleStudentLevelCascade('edit', student.grade_level_id, student.course_id);

            // Profile Info
            document.getElementById('edit_student_number').value = student.student_number || '';
            document.getElementById('edit_student_lrn').value = student.lrn || '';
            document.getElementById('edit_student_first_name').value = student.first_name || '';
            document.getElementById('edit_student_middle_name').value = student.middle_name || '';
            document.getElementById('edit_student_last_name').value = student.last_name || '';
            document.getElementById('edit_student_extension_name').value = student.extension_name || '';
            document.getElementById('edit_student_gender').value = student.gender || '';

            // Format date YYYY-MM-DD
            if (student.birthday) {
                const bdate = new Date(student.birthday);
                if (!isNaN(bdate)) {
                    document.getElementById('edit_student_birthday').value = bdate.toISOString().split('T')[0];
                } else {
                    document.getElementById('edit_student_birthday').value = student.birthday.substring(0, 10);
                }
            } else {
                document.getElementById('edit_student_birthday').value = '';
            }

            document.getElementById('edit_student_phone_number').value = student.phone_number || '';

            // Populate Location Dropdowns for Edit
            const editProv = document.getElementById('edit_student_province_select');
            const editCity = document.getElementById('edit_student_city_select');
            const editBrgy = document.getElementById('edit_student_barangay_select');

            loadProvinces(editProv, student.province, editCity, student.city, editBrgy, student.barangay);

            document.getElementById('editStudentModal').style.display = 'flex';
        }

        function closeEditStudentModal() {
            document.getElementById('editStudentModal').style.display = 'none';
        }
    </script>
@endpush
