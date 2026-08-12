@extends('layouts.senior_high_school')

@section('title', 'GNHS-P - SHS Student Enrollment')
@section('header_title', 'Student Section Enrollment')

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
            max-width: 600px;
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
        }
    </style>
@endpush

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.35rem; font-weight: 800; color: #0f172a;">SHS Student Enrollments</h2>
            <p style="font-size: 0.875rem; color: #64748b;">Enroll and assign Senior High School students into class sections
                for active S.Y. {{ $activeSchoolYear ? $activeSchoolYear->school_year : '' }}.</p>
        </div>
        <button type="button" onclick="openEnrollModal()"
            style="background: #7f1d1d; color: #ffffff; padding: 0.65rem 1.25rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.875rem; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 10px rgba(127, 29, 29, 0.25);">
            <i class="fa-solid fa-user-plus"></i> Enroll Student to Section
        </button>
    </div>

    <!-- Enrollment Table Card -->
    <div
        style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: var(--shadow-sm);">
        <table id="enrollmentTable" class="display" style="width: 100%;">
            <thead>
                <tr style="background: #f8fafc; text-align: left;">
                    <th>Student No.</th>
                    <th>Student Name</th>
                    <th>Class Section</th>
                    <th>Grade & Strand</th>
                    <th>School Year</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($enrollments as $enr)
                    @php
                        $st = $enr->student;
                        $stName = $st ? $st->last_name . ', ' . $st->first_name : 'N/A';
                        $sec = $enr->classSection;
                    @endphp
                    <tr>
                        <td style="font-weight: 700; color: #7f1d1d;">{{ $st ? $st->student_number : 'N/A' }}</td>
                        <td style="font-weight: 600;">{{ $stName }}</td>
                        <td style="font-weight: 700;">{{ $sec ? $sec->section_name : 'N/A' }}</td>
                        <td>
                            <span
                                style="background: #fee2e2; color: #991b1b; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">
                                {{ $enr->gradeLevel ? $enr->gradeLevel->name : ($sec && $sec->gradeLevel ? $sec->gradeLevel->name : 'N/A') }}
                            </span>
                            @if ($sec && $sec->course)
                                <span
                                    style="background: #fef3c7; color: #92400e; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700; margin-left: 0.25rem;">
                                    {{ $sec->course->course_code }}
                                </span>
                            @endif
                        </td>
                        <td>{{ $enr->schoolYear ? $enr->schoolYear->school_year : 'N/A' }}</td>
                        <td>
                            <span
                                style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;">
                                {{ $enr->status }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.35rem;">
                                <button type="button" onclick="openEditEnrollModal({{ json_encode($enr) }})"
                                    class="btn-action btn-edit" title="Edit Section/Status">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </button>
                                <button type="button"
                                    onclick="confirmDeleteEnroll({{ $enr->id }}, '{{ addslashes($stName) }}')"
                                    class="btn-action btn-delete" title="Remove Enrollment">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal: Enroll Student -->
    <div id="enrollModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800;">Enroll Student into Class Section</h3>
                <button type="button" onclick="closeModal('enrollModal')"
                    style="background: transparent; border: none; color: #ffffff; font-size: 1.2rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('senior_high_school.enrollment.store') }}" method="POST" class="modal-body">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Select Senior High Student *</label>
                    <select name="student_id" required
                        style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                        <option value="">-- Choose Student --</option>
                        @foreach ($allShsStudents as $s)
                            <option value="{{ $s->id }}">
                                {{ $s->last_name }}, {{ $s->first_name }} ({{ $s->student_number }}) -
                                {{ $s->gradeLevel ? $s->gradeLevel->name : 'N/A' }}
                                {{ $s->course ? '[' . $s->course->course_code . ']' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Assign to Section *</label>
                    <select name="class_section_id" required
                        style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                        <option value="">-- Choose Section --</option>
                        @foreach ($availableSections as $sec)
                            <option value="{{ $sec->id }}">
                                {{ $sec->section_name }} ({{ $sec->gradeLevel ? $sec->gradeLevel->name : 'N/A' }}
                                {{ $sec->course ? '- ' . $sec->course->course_code : '' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Enrollment Status *</label>
                    <select name="status" required
                        style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                        <option value="enrolled" selected>Enrolled</option>
                        <option value="pending">Pending</option>
                        <option value="dropped">Dropped</option>
                        <option value="transferred">Transferred</option>
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="closeModal('enrollModal')"
                        style="padding: 0.5rem 1rem; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 6px; cursor: pointer;">Cancel</button>
                    <button type="submit"
                        style="padding: 0.5rem 1.25rem; background: #7f1d1d; color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Confirm
                        Enrollment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Enrollment -->
    <div id="editEnrollModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800;">Edit Enrollment Details</h3>
                <button type="button" onclick="closeModal('editEnrollModal')"
                    style="background: transparent; border: none; color: #ffffff; font-size: 1.2rem; cursor: pointer;">&times;</button>
            </div>
            <form id="editEnrollForm" method="POST" class="modal-body">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Class Section *</label>
                    <select id="edit_class_section_id" name="class_section_id" required
                        style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                        @foreach ($availableSections as $sec)
                            <option value="{{ $sec->id }}">
                                {{ $sec->section_name }} ({{ $sec->gradeLevel ? $sec->gradeLevel->name : 'N/A' }}
                                {{ $sec->course ? '- ' . $sec->course->course_code : '' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #475569;">Enrollment Status *</label>
                    <select id="edit_status" name="status" required
                        style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 0.25rem;">
                        <option value="enrolled">Enrolled</option>
                        <option value="pending">Pending</option>
                        <option value="dropped">Dropped</option>
                        <option value="transferred">Transferred</option>
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="closeModal('editEnrollModal')"
                        style="padding: 0.5rem 1rem; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 6px; cursor: pointer;">Cancel</button>
                    <button type="submit"
                        style="padding: 0.5rem 1.25rem; background: #7f1d1d; color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Update
                        Enrollment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Form for Delete -->
    <form id="deleteEnrollForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#enrollmentTable').DataTable({
                pageLength: 10,
                responsive: true
            });
        });

        function openEnrollModal() {
            document.getElementById('enrollModal').classList.add('active');
        }

        function openEditEnrollModal(enrollment) {
            document.getElementById('edit_class_section_id').value = enrollment.class_section_id;
            document.getElementById('edit_status').value = enrollment.status;

            const form = document.getElementById('editEnrollForm');
            form.action = "{{ url('/senior-high-school/enrollment/update') }}/" + enrollment.id;

            document.getElementById('editEnrollModal').classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        function confirmDeleteEnroll(id, studentName) {
            Swal.fire({
                title: 'Remove Enrollment?',
                text: "Are you sure you want to delete enrollment for " + studentName + "?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Remove'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('deleteEnrollForm');
                    form.action = "{{ url('/senior-high-school/enrollment/delete') }}/" + id;
                    form.submit();
                }
            });
        }
    </script>
@endpush
