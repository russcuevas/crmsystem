@extends('layouts.junior_high_school')

@section('title', 'GNHS - My Handled Students (JHS)')
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
            background: #e0e7ff;
            color: #3730a3;
        }

        .btn-edit {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
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
            background: #1e1b4b;
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
            <p style="font-size: 0.875rem; color: #64748b;">Showing students enrolled in your advisory & teaching class
                sections (Grade 7 to Grade 10).</p>
        </div>

    </div>

    <!-- Students Table Card -->
    <div
        style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: var(--shadow-sm);">
        <table id="studentsTable" class="display" style="width: 100%;">
            <thead>
                <tr style="background: #f8fafc; text-align: left;">
                    <th>Student No.</th>
                    <th>LRN</th>
                    <th>Full Name</th>
                    <th>Email Address</th>
                    <th>Grade Level</th>
                    <th>Section(s)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $stu)
                    <tr>
                        <td style="font-family: monospace; font-weight: 700; color: #3730a3;">{{ $stu->student_number }}
                        </td>
                        <td>{{ $stu->lrn ?? 'N/A' }}</td>
                        <td style="font-weight: 700; color: #0f172a;">
                            {{ trim(($stu->last_name ? $stu->last_name . ', ' : '') . $stu->first_name . ' ' . $stu->middle_name . ' ' . $stu->extension_name) ?: ($stu->user ? $stu->user->name : 'N/A') }}
                        </td>
                        <td>{{ $stu->user ? $stu->user->email : 'N/A' }}</td>
                        <td>
                            <span
                                style="background: #e0e7ff; color: #3730a3; padding: 0.2rem 0.55rem; border-radius: 0.25rem; font-weight: 700; font-size: 0.775rem;">
                                {{ $stu->gradeLevel ? $stu->gradeLevel->name : 'N/A' }}
                            </span>
                        </td>
                        <td>
                            @foreach ($stu->enrollments as $enr)
                                @if ($enr->classSection)
                                    <span
                                        style="background: #f3f4f6; color: #374151; padding: 0.15rem 0.45rem; border-radius: 0.25rem; font-size: 0.75rem; margin-right: 0.25rem;">
                                        {{ $enr->classSection->section_name }}
                                    </span>
                                @endif
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('junior_high_school.students.show', $stu->id) }}" class="btn-action btn-view">
                                <i class="fa-solid fa-eye"></i> View
                            </a>
                            <button type="button" onclick='openEditModal(@json($stu))'
                                class="btn-action btn-edit">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </button>
                            <form action="{{ route('junior_high_school.students.destroy', $stu->id) }}" method="POST"
                                style="display: inline;"
                                onsubmit="return confirm('Are you sure you want to delete this student record?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete">
                                    <i class="fa-solid fa-trash-can"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Form (Add / Edit Student) -->
    <div class="modal-overlay" id="studentModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="modalTitle" style="font-size: 1.1rem; font-weight: 800;">Add New Student</h3>
                <button type="button" onclick="closeStudentModal()"
                    style="background: transparent; border: none; color: #ffffff; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <div class="modal-body">
                <form id="studentForm" action="{{ route('junior_high_school.students.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="formMethod" name="_method" value="POST">

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Student No.</label>
                            <input type="text" id="student_number" name="student_number" class="form-control"
                                style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;"
                                value="{{ $nextStudentId }}">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">LRN</label>
                            <input type="text" id="lrn" name="lrn" class="form-control"
                                style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;"
                                placeholder="Optional LRN">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Grade Level *</label>
                            <select id="grade_level_id" name="grade_level_id" required
                                style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                                <option value="">-- Select Grade Level --</option>
                                @foreach ($allGradeLevels as $gl)
                                    <option value="{{ $gl->id }}">{{ $gl->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">First Name *</label>
                            <input type="text" id="first_name" name="first_name" required
                                style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name"
                                style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" required
                                style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Email Address *</label>
                            <input type="email" id="email" name="email" required
                                style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Password <span
                                    id="passHelp" style="font-weight: normal; color: #64748b;">(Leave blank to keep
                                    current)</span></label>
                            <input type="password" id="password" name="password"
                                style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Gender</label>
                            <select id="gender" name="gender"
                                style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                                <option value="">-- Select Gender --</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Phone Number</label>
                            <input type="text" id="phone_number" name="phone_number"
                                style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button type="button" onclick="closeStudentModal()"
                            style="padding: 0.5rem 1rem; border: 1px solid #cbd5e1; background: #ffffff; border-radius: 6px; font-weight: 600; cursor: pointer;">Cancel</button>
                        <button type="submit"
                            style="padding: 0.5rem 1.25rem; background: #1e1b4b; color: #ffffff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Save
                            Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#studentsTable').DataTable({
                pageLength: 10,
                ordering: true
            });

            // Close modal when clicking outside modal box
            $(document).on('click', '#studentModal', function(e) {
                if (e.target === this) {
                    closeStudentModal();
                }
            });
        });

        function openAddModal() {
            $('#studentForm').attr('action', "{{ route('junior_high_school.students.store') }}");
            $('#formMethod').val('POST');
            $('#modalTitle').text('Add New Student');
            $('#password').prop('required', true);
            $('#passHelp').hide();
            $('#student_number').val("{{ $nextStudentId }}");
            $('#lrn').val('');
            $('#first_name').val('');
            $('#middle_name').val('');
            $('#last_name').val('');
            $('#email').val('');
            $('#password').val('');
            $('#grade_level_id').val('');
            $('#gender').val('');
            $('#phone_number').val('');
            $('#studentModal').addClass('active');
        }

        function openEditModal(student) {
            $('#studentForm').attr('action', "/junior-high-school/students/update/" + student.id);
            $('#formMethod').val('POST');
            $('#modalTitle').text('Edit Student Information');
            $('#password').prop('required', false);
            $('#passHelp').show();
            $('#student_number').val(student.student_number);
            $('#lrn').val(student.lrn);
            $('#first_name').val(student.first_name);
            $('#middle_name').val(student.middle_name);
            $('#last_name').val(student.last_name);
            $('#email').val(student.user ? student.user.email : '');
            $('#password').val('');
            $('#grade_level_id').val(student.grade_level_id);
            $('#gender').val(student.gender);
            $('#phone_number').val(student.phone_number);
            $('#studentModal').addClass('active');
        }

        function closeStudentModal() {
            $('#studentModal').removeClass('active');
        }
    </script>
@endpush
