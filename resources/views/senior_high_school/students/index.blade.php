@extends('layouts.senior_high_school')

@section('title', 'GNHS - My Handled Students (SHS)')
@section('header_title', 'My Handled Students')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <style>
        .dataTables_wrapper {
            padding: 0.5rem 0;
            font-size: 0.875rem;
        }

        .dataTables_filter input {
            padding: 0.4rem 0.75rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            outline: none;
        }

        .btn-action {
            padding: 0.35rem 0.65rem;
            border-radius: 0.375rem;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            cursor: pointer;
            border: none;
        }

        .btn-view {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-edit {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-delete {
            background: #f3f4f6;
            color: #374151;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: all 0.25s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-box {
            background: #ffffff;
            border-radius: 1rem;
            width: 100%;
            max-width: 650px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            background: #7f1d1d;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-body {
            padding: 1.5rem;
            max-height: 80vh;
            overflow-y: auto;
        }
    </style>
@endpush

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.35rem; font-weight: 800; color: #0f172a;">Handled Students Registry</h2>
            <p style="font-size: 0.85rem; color: #64748b;">Senior High School Students enrolled in your Advisory and Subject Sections.</p>
        </div>
        <button onclick="openModal('addStudentModal')"
            style="background: #7f1d1d; color: #ffffff; padding: 0.65rem 1.15rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 700; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 10px rgba(127, 29, 29, 0.25);">
            <i class="fa-solid fa-user-plus"></i> Add New Student
        </button>
    </div>

    <!-- Table Card -->
    <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: var(--shadow-sm);">
        <table id="studentsTable" class="display" style="width:100%">
            <thead>
                <tr style="background: #f8fafc; text-align: left;">
                    <th style="padding: 0.75rem;">Student ID</th>
                    <th style="padding: 0.75rem;">LRN</th>
                    <th style="padding: 0.75rem;">Full Name</th>
                    <th style="padding: 0.75rem;">Grade & Strand</th>
                    <th style="padding: 0.75rem;">Advisory / Subject</th>
                    <th style="padding: 0.75rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $student)
                    @php
                        $isAdvisory = in_array($student->id, $advisoryStudentIds);
                        $fullName = trim($student->last_name . ', ' . $student->first_name . ' ' . ($student->middle_name ?? ''));
                    @endphp
                    <tr>
                        <td style="font-weight: 700; color: #7f1d1d;">{{ $student->student_number }}</td>
                        <td>{{ $student->lrn ?? 'N/A' }}</td>
                        <td style="font-weight: 600;">{{ $fullName }}</td>
                        <td>
                            <span style="background: #fee2e2; color: #991b1b; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">
                                {{ $student->gradeLevel ? $student->gradeLevel->name : 'N/A' }}
                            </span>
                            @if ($student->course)
                                <span style="background: #fef3c7; color: #92400e; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700; margin-left: 0.25rem;">
                                    {{ $student->course->course_code }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if ($isAdvisory)
                                <span style="background: #d1fae5; color: #065f46; padding: 0.2rem 0.55rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">Advisory Student</span>
                            @else
                                <span style="background: #e0e7ff; color: #3730a3; padding: 0.2rem 0.55rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">Subject Student</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.35rem;">
                                <a href="{{ route('senior_high_school.students.show', $student->id) }}" class="btn-action btn-view" title="View Profile">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                                <button onclick="openEditModal({{ json_encode($student) }})" class="btn-action btn-edit" title="Edit Student">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </button>
                                <button onclick="confirmDelete({{ $student->id }}, '{{ addslashes($fullName) }}')" class="btn-action btn-delete" title="Delete Student">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal: Add Student -->
    <div id="addStudentModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800;">Add New Senior High Student</h3>
                <button type="button" onclick="closeModal('addStudentModal')" style="background: transparent; border: none; color: #ffffff; font-size: 1.2rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('senior_high_school.students.store') }}" method="POST" class="modal-body">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">First Name *</label>
                        <input type="text" name="first_name" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Last Name *</label>
                        <input type="text" name="last_name" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Middle Name</label>
                        <input type="text" name="middle_name" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Extension Name</label>
                        <input type="text" name="extension_name" placeholder="Jr., III" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Email Address *</label>
                        <input type="email" name="email" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Password *</label>
                        <input type="password" name="password" required minlength="6" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Student Number</label>
                        <input type="text" name="student_number" value="{{ $nextStudentId }}" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">LRN</label>
                        <input type="text" name="lrn" placeholder="12-Digit LRN" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Grade Level *</label>
                        <select name="grade_level_id" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                            @foreach ($allGradeLevels as $gl)
                                <option value="{{ $gl->id }}">{{ $gl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Track / Strand</label>
                        <select name="course_id" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                            <option value="">Select Strand</option>
                            @foreach ($allStrands as $strand)
                                <option value="{{ $strand->id }}">{{ $strand->course_code }} - {{ $strand->course_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="button" onclick="closeModal('addStudentModal')" style="padding: 0.5rem 1rem; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 6px; cursor: pointer;">Cancel</button>
                    <button type="submit" style="padding: 0.5rem 1.25rem; background: #7f1d1d; color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Save Student</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Student -->
    <div id="editStudentModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800;">Edit Student Profile</h3>
                <button type="button" onclick="closeModal('editStudentModal')" style="background: transparent; border: none; color: #ffffff; font-size: 1.2rem; cursor: pointer;">&times;</button>
            </div>
            <form id="editStudentForm" method="POST" class="modal-body">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">First Name *</label>
                        <input type="text" id="edit_first_name" name="first_name" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Last Name *</label>
                        <input type="text" id="edit_last_name" name="last_name" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Middle Name</label>
                        <input type="text" id="edit_middle_name" name="middle_name" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Extension Name</label>
                        <input type="text" id="edit_extension_name" name="extension_name" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Email Address *</label>
                        <input type="email" id="edit_email" name="email" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Password (Leave blank to keep current)</label>
                        <input type="password" name="password" minlength="6" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Student Number *</label>
                        <input type="text" id="edit_student_number" name="student_number" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">LRN</label>
                        <input type="text" id="edit_lrn" name="lrn" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Grade Level *</label>
                        <select id="edit_grade_level_id" name="grade_level_id" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                            @foreach ($allGradeLevels as $gl)
                                <option value="{{ $gl->id }}">{{ $gl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Track / Strand</label>
                        <select id="edit_course_id" name="course_id" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                            <option value="">Select Strand</option>
                            @foreach ($allStrands as $strand)
                                <option value="{{ $strand->id }}">{{ $strand->course_code }} - {{ $strand->course_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="button" onclick="closeModal('editStudentModal')" style="padding: 0.5rem 1rem; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 6px; cursor: pointer;">Cancel</button>
                    <button type="submit" style="padding: 0.5rem 1.25rem; background: #7f1d1d; color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Update Student</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Form for Delete -->
    <form id="deleteStudentForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#studentsTable').DataTable({
                pageLength: 10,
                responsive: true,
                order: [[2, 'asc']]
            });
        });

        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        function openEditModal(student) {
            document.getElementById('edit_first_name').value = student.first_name || '';
            document.getElementById('edit_last_name').value = student.last_name || '';
            document.getElementById('edit_middle_name').value = student.middle_name || '';
            document.getElementById('edit_extension_name').value = student.extension_name || '';
            document.getElementById('edit_email').value = student.user ? student.user.email : '';
            document.getElementById('edit_student_number').value = student.student_number || '';
            document.getElementById('edit_lrn').value = student.lrn || '';
            document.getElementById('edit_grade_level_id').value = student.grade_level_id || '';
            document.getElementById('edit_course_id').value = student.course_id || '';

            const form = document.getElementById('editStudentForm');
            form.action = "{{ url('/senior-high-school/students/update') }}/" + student.id;

            openModal('editStudentModal');
        }

        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Delete Student Record?',
                text: "Are you sure you want to remove " + name + "? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('deleteStudentForm');
                    form.action = "{{ url('/senior-high-school/students/delete') }}/" + id;
                    form.submit();
                }
            });
        }
    </script>
@endpush
