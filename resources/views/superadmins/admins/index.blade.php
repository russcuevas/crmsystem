@extends('layouts.superadmin')

@section('title', 'GNHS - Admin Management')

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

        .btn-action-delete {
            padding: 0.4rem;
            border-radius: 8px;
            border: 1px solid #fca5a5;
            background: #ffffff;
            color: #dc2626;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-action-delete:hover {
            background: #fef2f2;
            border-color: #ef4444;
            color: #991b1b;
            transform: translateY(-1px);
        }

        .btn-add-admin {
            padding: 0.55rem 1.1rem;
            background: var(--primary-navy, #0f172a);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s ease;
        }

        .btn-add-admin:hover {
            background: #1e293b;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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

    @if (session('error'))
        <div
            style="padding: 0.85rem 1.25rem; background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; border-radius: 10px; margin-bottom: 1.25rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()"
                style="background: none; border: none; font-size: 1.1rem; cursor: pointer; color: #991b1b;">&times;</button>
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
        <div class="card-header" style="display: flex; align-items: center; justify-content: space-between;">
            <div class="card-title">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Admin Management
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--accent-emerald); margin-left: 6px;">
                    (System Overview)
                </span>
            </div>
            <button type="button" class="btn-add-admin" onclick="openAddAdminModal()">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Add Admin
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="custom-table" id="adminsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($admins as $admin)
                            <tr>
                                <td>{{ $admin->id }}</td>
                                <td><strong>{{ $admin->name }}</strong></td>
                                <td>{{ $admin->email }}</td>
                                <td>
                                    <span class="badge badge-{{ $admin->role }}">
                                        {{ ucfirst($admin->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $admin->status == 'active' ? 'active' : 'inactive' }}">
                                        {{ ucfirst($admin->status ?? 'Active') }}
                                    </span>
                                </td>
                                <td>{{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Never' }}</td>
                                <td style="text-align: center;">
                                    <div style="display: inline-flex; gap: 0.35rem;">
                                        <button type="button" class="btn-action-icon" title="Edit Admin"
                                            onclick='openEditAdminModal(@json($admin))'>
                                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        @if (Auth::id() !== $admin->id)
                                            <form action="{{ route('superadmin.admins.destroy', $admin->id) }}" method="POST"
                                                id="delete-admin-form-{{ $admin->id }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn-action-delete" title="Delete Admin"
                                                    onclick="confirmDeleteAdmin({{ $admin->id }}, '{{ addslashes($admin->name) }}')">
                                                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal: Add New Admin -->
    <div class="modal-overlay" id="addAdminModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800;">Add New Admin Account</h3>
                <button type="button" onclick="closeAddAdminModal()"
                    style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('superadmin.admins.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Full Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="name" class="form-control-custom" required placeholder="e.g. Juan Dela Cruz">
                    </div>

                    <div class="form-group">
                        <label>Email Address <span style="color: #ef4444;">*</span></label>
                        <input type="email" name="email" class="form-control-custom" required placeholder="e.g. admin@gnhs.edu.ph">
                    </div>

                    <div class="form-group">
                        <label>Password <span style="color: #ef4444;">*</span></label>
                        <input type="password" name="password" class="form-control-custom" required placeholder="Enter password (min. 6 characters)">
                    </div>

                    <div class="form-group">
                        <label>Role <span style="color: #ef4444;">*</span></label>
                        <select name="role" class="form-control-custom" required>
                            <option value="admin" selected>Admin</option>
                            <option value="superadmin">Superadmin</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Status <span style="color: #ef4444;">*</span></label>
                        <select name="status" class="form-control-custom" required>
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddAdminModal()">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </button>
                    <button type="submit" class="btn-submit">Save Admin</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Admin -->
    <div class="modal-overlay" id="editAdminModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800;">Edit Admin Account</h3>
                <button type="button" onclick="closeEditAdminModal()"
                    style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form id="editAdminForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Full Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="name" id="edit_admin_name" class="form-control-custom" required placeholder="Enter full name">
                    </div>

                    <div class="form-group">
                        <label>Email Address <span style="color: #ef4444;">*</span></label>
                        <input type="email" name="email" id="edit_admin_email" class="form-control-custom" required placeholder="Enter email address">
                    </div>

                    <div class="form-group">
                        <label>Role <span style="color: #ef4444;">*</span></label>
                        <select name="role" id="edit_admin_role" class="form-control-custom" required>
                            <option value="admin">Admin</option>
                            <option value="superadmin">Superadmin</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Account Status <span style="color: #ef4444;">*</span></label>
                        <select name="status" id="edit_admin_status" class="form-control-custom" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label>New Password <span style="font-size: 0.72rem; text-transform: none; color: #64748b; font-weight: 500;">(Leave blank to keep current password)</span></label>
                        <input type="password" name="password" id="edit_admin_password" class="form-control-custom" placeholder="Enter new password (min. 6 characters)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditAdminModal()">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </button>
                    <button type="submit" class="btn-submit">Update Admin</button>
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
            if ($('#adminsTable').length) {
                $('#adminsTable').DataTable({
                    "pageLength": 10,
                    "ordering": true,
                    "order": [],
                    "responsive": true,
                    "language": {
                        "search": "",
                        "searchPlaceholder": "Search admin name, email, role...",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ admins",
                        "paginate": {
                            "previous": "‹",
                            "next": "›"
                        }
                    }
                });
            }
        });

        function openAddAdminModal() {
            document.getElementById('addAdminModal').style.display = 'flex';
        }

        function closeAddAdminModal() {
            document.getElementById('addAdminModal').style.display = 'none';
        }

        function openEditAdminModal(admin) {
            const form = document.getElementById('editAdminForm');
            form.action = "{{ url('/superadmin/admins/update') }}/" + admin.id;

            document.getElementById('edit_admin_name').value = admin.name || '';
            document.getElementById('edit_admin_email').value = admin.email || '';
            document.getElementById('edit_admin_role').value = admin.role || 'admin';

            const currentStatus = admin.status ? admin.status.toLowerCase() : 'active';
            document.getElementById('edit_admin_status').value = (currentStatus === 'inactive') ? 'inactive' : 'active';

            document.getElementById('edit_admin_password').value = '';

            document.getElementById('editAdminModal').style.display = 'flex';
        }

        function closeEditAdminModal() {
            document.getElementById('editAdminModal').style.display = 'none';
        }

        function confirmDeleteAdmin(adminId, adminName) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Admin Account?',
                    text: `Are you sure you want to delete ${adminName}? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(`delete-admin-form-${adminId}`).submit();
                    }
                });
            } else {
                if (confirm(`Are you sure you want to delete ${adminName}?`)) {
                    document.getElementById(`delete-admin-form-${adminId}`).submit();
                }
            }
        }

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.style.display = 'none';
            }
        });
    </script>
@endpush
