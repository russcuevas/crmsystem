@extends('layouts.admin')

@section('title', 'GNHS-P - Faculty Information Roster')

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

        .modal-card-xl {
            background: #ffffff;
            border-radius: 16px;
            width: 100%;
            max-width: 900px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
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
            padding: 1.15rem 1.5rem;
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
            flex: 1;
        }

        .modal-parts-grid {
            display: grid;
            grid-template-columns: 1fr 1.6fr;
            gap: 1.25rem;
        }

        @media (max-width: 768px) {
            .modal-parts-grid {
                grid-template-columns: 1fr;
            }
        }

        .modal-part-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.15rem;
        }

        .form-section-title {
            font-size: 0.82rem;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1.5px solid #cbd5e1;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            flex-shrink: 0;
        }

        .form-group {
            margin-bottom: 0.95rem;
        }

        .form-group label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.3rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
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
            padding: 0.55rem 1.2rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
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
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                Faculty Information Roster
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--accent-emerald); margin-left: 6px;">
                    (S.Y. {{ $activeSchoolYear->school_year ?? '2024-2025' }})
                </span>
            </div>

            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <button class="btn-primary" onclick="openAddFacultyModal()">+ Add Faculty Member</button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="custom-table" id="facultyTable">
                    <thead>
                        <tr>
                            <th>Teacher ID</th>
                            <th>Full Name</th>
                            <th>Position</th>
                            <th>Education Level</th>
                            <th>Phone Number</th>
                            <th>Location / Address</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($teachers as $teacher)
                            <tr>
                                <td><strong>{{ $teacher->teacher_id }}</strong></td>
                                <td>
                                    <strong>{{ $teacher->first_name }}
                                        {{ $teacher->middle_name ? $teacher->middle_name . ' ' : '' }}{{ $teacher->last_name }}
                                        {{ $teacher->extension_name }}</strong>
                                </td>
                                <td>{{ $teacher->position ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-teacher">{{ $teacher->educationLevel->code ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $teacher->phone_number ?? 'N/A' }}</td>
                                <td style="font-size: 0.8rem; color: #475569;">
                                    @php
                                        $loc = array_filter([$teacher->barangay, $teacher->city, $teacher->province]);
                                    @endphp
                                    {{ !empty($loc) ? implode(', ', $loc) : 'N/A' }}
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 0.35rem; justify-content: center;">
                                        <button type="button" class="btn-action-icon" title="Edit Faculty Member"
                                            onclick='openEditFacultyModal(@json($teacher))'>
                                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <form action="{{ route('admin.faculty.destroy', $teacher->id) }}" method="POST"
                                            style="display: inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this faculty member?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-icon danger"
                                                title="Delete Faculty Member">
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

    <!-- Modal: Add Faculty Member (XL 2-Column Layout) -->
    <div class="modal-overlay" id="addFacultyModal">
        <div class="modal-card-xl">
            <div class="modal-header">
                <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0;">Add New Faculty Member</h3>
                <button type="button" onclick="closeAddFacultyModal()"
                    style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.faculty.store') }}" method="POST">
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
                                    placeholder="e.g. teacher@gnhs.edu.ph">
                            </div>

                            <div class="form-group">
                                <label>LMS Password <span style="color: #ef4444;">*</span></label>
                                <input type="password" name="password" class="form-control-custom" required
                                    placeholder="Min. 6 characters">
                            </div>

                            <div
                                style="margin-top: 1.5rem; padding: 0.75rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; font-size: 0.75rem; color: #1e40af;">
                                <strong>Note:</strong> Creating this faculty member will automatically generate a
                                <strong>Teacher Account</strong> for logging into the Class Record System.
                            </div>
                        </div>

                        <!-- COLUMN 2: PART 2 TEACHER INFORMATION -->
                        <div class="modal-part-card">
                            <div class="form-section-title">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6" />
                                </svg>
                                Part 2: Faculty Profile Information
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div class="form-group">
                                    <label>Teacher ID</label>
                                    <input type="text" name="teacher_id" class="form-control-custom"
                                        value="{{ $nextTeacherId }}" placeholder="Auto-generated if empty">
                                </div>
                                <div class="form-group">
                                    <label>Education Level <span style="color: #ef4444;">*</span></label>
                                    <select name="education_level_id" class="form-control-custom" required>
                                        <option value="" disabled selected>-- Select Level --</option>
                                        @foreach ($educationLevelsList as $lvl)
                                            <option value="{{ $lvl->id }}">{{ $lvl->code }} -
                                                {{ $lvl->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div class="form-group">
                                    <label>First Name <span style="color: #ef4444;">*</span></label>
                                    <input type="text" name="first_name" class="form-control-custom" required
                                        placeholder="e.g. Maria">
                                </div>
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input type="text" name="middle_name" class="form-control-custom"
                                        placeholder="e.g. Santos">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div class="form-group">
                                    <label>Last Name <span style="color: #ef4444;">*</span></label>
                                    <input type="text" name="last_name" class="form-control-custom" required
                                        placeholder="e.g. Cruz">
                                </div>
                                <div class="form-group">
                                    <label>Ext. Name (e.g. Jr, III)</label>
                                    <input type="text" name="extension_name" class="form-control-custom"
                                        placeholder="Optional">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div class="form-group">
                                    <label>Position / Rank</label>
                                    <input type="text" name="position" class="form-control-custom"
                                        placeholder="e.g. Teacher I / Professor">
                                </div>
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="gender" class="form-control-custom">
                                        <option value="">-- Select Gender --</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div class="form-group">
                                    <label>Birthday</label>
                                    <input type="date" name="birthday" class="form-control-custom">
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="text" name="phone_number" class="form-control-custom"
                                        placeholder="09123456789">
                                </div>
                            </div>

                            <!-- Philippine Address Dropdowns (PSGC Cascading) -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem;">
                                <div class="form-group">
                                    <label>Province</label>
                                    <select name="province" id="add_province_select" class="form-control-custom">
                                        <option value="">-- Select Province --</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>City / Municipality</label>
                                    <select name="city" id="add_city_select" class="form-control-custom" disabled>
                                        <option value="">-- Select City --</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Barangay</label>
                                    <select name="barangay" id="add_barangay_select" class="form-control-custom"
                                        disabled>
                                        <option value="">-- Select Barangay --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddFacultyModal()">Cancel</button>
                    <button type="submit" class="btn-submit">+ Save Faculty Member</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Faculty Member -->
    <div class="modal-overlay" id="editFacultyModal">
        <div class="modal-card-xl">
            <div class="modal-header">
                <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0;">Edit Faculty Member Details</h3>
                <button type="button" onclick="closeEditFacultyModal()"
                    style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form id="editFacultyForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="modal-parts-grid">

                        <!-- COLUMN 1: PART 1 ACCOUNT DETAILS -->
                        <div class="modal-part-card">
                            <div class="form-section-title">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Part 1: Account Credentials
                            </div>

                            <div class="form-group">
                                <label>LMS Email Address <span style="color: #ef4444;">*</span></label>
                                <input type="email" name="email" id="edit_email" class="form-control-custom"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>New Password <span
                                        style="font-size: 0.7rem; text-transform: none; color: #64748b; font-weight: 500;">(Leave
                                        blank to keep current)</span></label>
                                <input type="password" name="password" class="form-control-custom"
                                    placeholder="Enter new password">
                            </div>
                        </div>

                        <!-- COLUMN 2: PART 2 TEACHER PROFILE -->
                        <div class="modal-part-card">
                            <div class="form-section-title">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6" />
                                </svg>
                                Part 2: Faculty Profile Details
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div class="form-group">
                                    <label>Teacher ID <span style="color: #ef4444;">*</span></label>
                                    <input type="text" name="teacher_id" id="edit_teacher_id"
                                        class="form-control-custom" required>
                                </div>
                                <div class="form-group">
                                    <label>Education Level <span style="color: #ef4444;">*</span></label>
                                    <select name="education_level_id" id="edit_education_level_id"
                                        class="form-control-custom" required>
                                        @foreach ($educationLevelsList as $lvl)
                                            <option value="{{ $lvl->id }}">{{ $lvl->code }} -
                                                {{ $lvl->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div class="form-group">
                                    <label>First Name <span style="color: #ef4444;">*</span></label>
                                    <input type="text" name="first_name" id="edit_first_name"
                                        class="form-control-custom" required>
                                </div>
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input type="text" name="middle_name" id="edit_middle_name"
                                        class="form-control-custom">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div class="form-group">
                                    <label>Last Name <span style="color: #ef4444;">*</span></label>
                                    <input type="text" name="last_name" id="edit_last_name"
                                        class="form-control-custom" required>
                                </div>
                                <div class="form-group">
                                    <label>Ext. Name</label>
                                    <input type="text" name="extension_name" id="edit_extension_name"
                                        class="form-control-custom">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div class="form-group">
                                    <label>Position / Rank</label>
                                    <input type="text" name="position" id="edit_position"
                                        class="form-control-custom">
                                </div>
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="gender" id="edit_gender" class="form-control-custom">
                                        <option value="">-- Select Gender --</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div class="form-group">
                                    <label>Birthday</label>
                                    <input type="date" name="birthday" id="edit_birthday"
                                        class="form-control-custom">
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="text" name="phone_number" id="edit_phone_number"
                                        class="form-control-custom">
                                </div>
                            </div>

                            <!-- Philippine Address Dropdowns (Edit Mode) -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem;">
                                <div class="form-group">
                                    <label>Province</label>
                                    <select name="province" id="edit_province_select" class="form-control-custom">
                                        <option value="">-- Select Province --</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>City / Municipality</label>
                                    <select name="city" id="edit_city_select" class="form-control-custom">
                                        <option value="">-- Select City --</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Barangay</label>
                                    <select name="barangay" id="edit_barangay_select" class="form-control-custom">
                                        <option value="">-- Select Barangay --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditFacultyModal()">Cancel</button>
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
        // Philippine PSGC Address API Integration
        const PSGC_BASE_URL = 'https://psgc.gitlab.io/api';

        async function loadProvinces(provSelectEl, defaultProv = null, citySelectEl = null, defaultCity = null,
            brgySelectEl = null, defaultBrgy = null) {
            try {
                provSelectEl.innerHTML = '<option value="">Loading Provinces...</option>';
                const res = await fetch(`${PSGC_BASE_URL}/provinces.json`);
                const provinces = await res.json();
                provinces.sort((a, b) => a.name.localeCompare(b.name));

                provSelectEl.innerHTML = '<option value="">-- Select Province --</option>';
                let matchedProvCode = null;

                provinces.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.name;
                    opt.setAttribute('data-code', p.code);
                    if (defaultProv && defaultProv.toLowerCase() === p.name.toLowerCase()) {
                        opt.selected = true;
                        matchedProvCode = p.code;
                    }
                    provSelectEl.appendChild(opt);
                });

                if (matchedProvCode && citySelectEl) {
                    loadCities(citySelectEl, matchedProvCode, defaultCity, brgySelectEl, defaultBrgy);
                }
            } catch (err) {
                console.error('Error fetching provinces:', err);
                provSelectEl.innerHTML = '<option value="">Error loading provinces</option>';
            }
        }

        async function loadCities(citySelectEl, provCode, defaultCity = null, brgySelectEl = null, defaultBrgy = null) {
            if (!provCode) {
                citySelectEl.innerHTML = '<option value="">-- Select City --</option>';
                citySelectEl.disabled = true;
                if (brgySelectEl) {
                    brgySelectEl.innerHTML = '<option value="">-- Select Barangay --</option>';
                    brgySelectEl.disabled = true;
                }
                return;
            }

            try {
                citySelectEl.disabled = false;
                citySelectEl.innerHTML = '<option value="">Loading Cities...</option>';

                const [munRes, cityRes] = await Promise.all([
                    fetch(`${PSGC_BASE_URL}/provinces/${provCode}/municipalities.json`),
                    fetch(`${PSGC_BASE_URL}/provinces/${provCode}/cities.json`)
                ]);

                const muns = await munRes.json();
                const cities = await cityRes.json();
                const combined = [...muns, ...cities];
                combined.sort((a, b) => a.name.localeCompare(b.name));

                citySelectEl.innerHTML = '<option value="">-- Select City / Municipality --</option>';
                let matchedCityCode = null;

                combined.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.name;
                    opt.setAttribute('data-code', c.code);
                    opt.setAttribute('data-type', c.municipalityCode ? 'municipality' : 'city');
                    if (defaultCity && defaultCity.toLowerCase() === c.name.toLowerCase()) {
                        opt.selected = true;
                        matchedCityCode = c.code;
                    }
                    citySelectEl.appendChild(opt);
                });

                if (matchedCityCode && brgySelectEl) {
                    const selectedOpt = citySelectEl.options[citySelectEl.selectedIndex];
                    const type = selectedOpt ? selectedOpt.getAttribute('data-type') : 'municipality';
                    loadBarangays(brgySelectEl, matchedCityCode, type, defaultBrgy);
                }
            } catch (err) {
                console.error('Error fetching cities:', err);
                citySelectEl.innerHTML = '<option value="">Error loading cities</option>';
            }
        }

        async function loadBarangays(brgySelectEl, cityCode, type = 'municipality', defaultBrgy = null) {
            if (!cityCode) {
                brgySelectEl.innerHTML = '<option value="">-- Select Barangay --</option>';
                brgySelectEl.disabled = true;
                return;
            }

            try {
                brgySelectEl.disabled = false;
                brgySelectEl.innerHTML = '<option value="">Loading Barangays...</option>';

                const endpoint = (type === 'city') ?
                    `${PSGC_BASE_URL}/cities/${cityCode}/barangays.json` :
                    `${PSGC_BASE_URL}/municipalities/${cityCode}/barangays.json`;

                const res = await fetch(endpoint);
                const barangays = await res.json();
                barangays.sort((a, b) => a.name.localeCompare(b.name));

                brgySelectEl.innerHTML = '<option value="">-- Select Barangay --</option>';
                barangays.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.name;
                    if (defaultBrgy && defaultBrgy.toLowerCase() === b.name.toLowerCase()) {
                        opt.selected = true;
                    }
                    brgySelectEl.appendChild(opt);
                });
            } catch (err) {
                console.error('Error fetching barangays:', err);
                brgySelectEl.innerHTML = '<option value="">Error loading barangays</option>';
            }
        }

        function bindLocationCascades(provEl, cityEl, brgyEl) {
            provEl.addEventListener('change', function() {
                const selectedOpt = provEl.options[provEl.selectedIndex];
                const provCode = selectedOpt ? selectedOpt.getAttribute('data-code') : null;
                loadCities(cityEl, provCode, null, brgyEl, null);
            });

            cityEl.addEventListener('change', function() {
                const selectedOpt = cityEl.options[cityEl.selectedIndex];
                const cityCode = selectedOpt ? selectedOpt.getAttribute('data-code') : null;
                const type = selectedOpt ? selectedOpt.getAttribute('data-type') : 'municipality';
                loadBarangays(brgyEl, cityCode, type, null);
            });
        }

        $(document).ready(function() {
            if ($('#facultyTable').length) {
                $('#facultyTable').DataTable({
                    "pageLength": 10,
                    "ordering": true,
                    "order": [],
                    "responsive": true,
                    "language": {
                        "search": "",
                        "searchPlaceholder": "Search teacher ID, name, position...",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ faculty members",
                        "paginate": {
                            "previous": "‹",
                            "next": "›"
                        }
                    }
                });
            }

            // Initialize Address Cascade for Add Modal
            const addProv = document.getElementById('add_province_select');
            const addCity = document.getElementById('add_city_select');
            const addBrgy = document.getElementById('add_barangay_select');
            if (addProv && addCity && addBrgy) {
                loadProvinces(addProv);
                bindLocationCascades(addProv, addCity, addBrgy);
            }

            // Initialize Address Cascade for Edit Modal
            const editProv = document.getElementById('edit_province_select');
            const editCity = document.getElementById('edit_city_select');
            const editBrgy = document.getElementById('edit_barangay_select');
            if (editProv && editCity && editBrgy) {
                bindLocationCascades(editProv, editCity, editBrgy);
            }

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal-overlay')) {
                    e.target.style.display = 'none';
                }
            });
        });

        function openAddFacultyModal() {
            document.getElementById('addFacultyModal').style.display = 'flex';
        }

        function closeAddFacultyModal() {
            document.getElementById('addFacultyModal').style.display = 'none';
        }

        function openEditFacultyModal(teacher) {
            const form = document.getElementById('editFacultyForm');
            form.action = "{{ url('/admin/faculty/update') }}/" + teacher.id;

            document.getElementById('edit_email').value = (teacher.user && teacher.user.email) ? teacher.user.email : '';
            document.getElementById('edit_teacher_id').value = teacher.teacher_id || '';
            document.getElementById('edit_education_level_id').value = teacher.education_level_id || '';
            document.getElementById('edit_first_name').value = teacher.first_name || '';
            document.getElementById('edit_middle_name').value = teacher.middle_name || '';
            document.getElementById('edit_last_name').value = teacher.last_name || '';
            document.getElementById('edit_extension_name').value = teacher.extension_name || '';
            document.getElementById('edit_position').value = teacher.position || '';
            document.getElementById('edit_gender').value = teacher.gender || '';

            if (teacher.birthday) {
                const bdate = new Date(teacher.birthday);
                if (!isNaN(bdate)) {
                    document.getElementById('edit_birthday').value = bdate.toISOString().split('T')[0];
                } else {
                    document.getElementById('edit_birthday').value = teacher.birthday.substring(0, 10);
                }
            } else {
                document.getElementById('edit_birthday').value = '';
            }

            document.getElementById('edit_phone_number').value = teacher.phone_number || '';

            const editProv = document.getElementById('edit_province_select');
            const editCity = document.getElementById('edit_city_select');
            const editBrgy = document.getElementById('edit_barangay_select');

            loadProvinces(editProv, teacher.province, editCity, teacher.city, editBrgy, teacher.barangay);

            document.getElementById('editFacultyModal').style.display = 'flex';
        }

        function closeEditFacultyModal() {
            document.getElementById('editFacultyModal').style.display = 'none';
        }
    </script>
@endpush
