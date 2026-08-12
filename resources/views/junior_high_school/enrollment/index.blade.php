@extends('layouts.junior_high_school')

@section('title', 'GNHS-P - JHS Student Enrollment')
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
            background: #1e1b4b;
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
            <h2 style="font-size: 1.35rem; font-weight: 800; color: #0f172a;">JHS Student Enrollments</h2>
            <p style="font-size: 0.875rem; color: #64748b;">Enroll and assign Junior High School students into class sections
                for active S.Y. {{ $activeSchoolYear ? $activeSchoolYear->school_year : '' }}.</p>
        </div>
        <button type="button" onclick="openEnrollModal()"
            style="background: #1e1b4b; color: #ffffff; padding: 0.65rem 1.25rem; border-radius: 0.5rem; font-weight: 700; font-size: 0.875rem; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
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
                    <th>Grade Level</th>
                    <th>School Year</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($enrollments as $enr)
                    <tr>
                        <td style="font-family: monospace; font-weight: 700; color: #3730a3;">
                            {{ $enr->student ? $enr->student->student_number : 'N/A' }}
                        </td>
                        <td style="font-weight: 700; color: #0f172a;">
                            {{ $enr->student ? trim(($enr->student->last_name ? $enr->student->last_name . ', ' : '') . $enr->student->first_name . ' ' . $enr->student->middle_name . ' ' . $enr->student->extension_name) : 'N/A' }}
                        </td>
                        <td>
                            <strong
                                style="color: #0f172a;">{{ $enr->classSection ? $enr->classSection->section_name : 'N/A' }}</strong>
                        </td>
                        <td>
                            <span
                                style="background: #e0e7ff; color: #3730a3; padding: 0.2rem 0.55rem; border-radius: 0.25rem; font-weight: 700; font-size: 0.775rem;">
                                {{ $enr->classSection && $enr->classSection->gradeLevel ? $enr->classSection->gradeLevel->name : ($enr->gradeLevel ? $enr->gradeLevel->name : 'N/A') }}
                            </span>
                        </td>
                        <td>{{ $enr->schoolYear ? $enr->schoolYear->school_year : 'N/A' }}</td>
                        <td>
                            <span
                                style="background: #d1fae5; color: #065f46; padding: 0.2rem 0.6rem; border-radius: 0.25rem; font-weight: 700; font-size: 0.75rem; text-transform: uppercase;">
                                {{ $enr->status }}
                            </span>
                        </td>
                        <td>
                            <button type="button" onclick='openEditEnrollModal(@json($enr))'
                                class="btn-action btn-edit">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </button>
                            <form action="{{ route('junior_high_school.enrollment.destroy', $enr->id) }}" method="POST"
                                style="display: inline;"
                                onsubmit="return confirm('Are you sure you want to remove this enrollment record?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete">
                                    <i class="fa-solid fa-trash-can"></i> Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Enroll Student Modal -->
    <div class="modal-overlay" id="enrollModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="modalTitle" style="font-size: 1.1rem; font-weight: 800;">Enroll Student into Section</h3>
                <button type="button" onclick="closeEnrollModal()"
                    style="background: transparent; border: none; color: #ffffff; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <div class="modal-body">
                <form id="enrollForm" action="{{ route('junior_high_school.enrollment.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="formMethod" name="_method" value="POST">

                    <div style="margin-bottom: 1.25rem;" id="studentSelectContainer">
                        <label
                            style="font-size: 0.825rem; font-weight: 700; color: #475569; display: block; margin-bottom: 0.35rem;">Select
                            Student *</label>
                        <select id="student_id" name="student_id" required
                            style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 8px;">
                            <option value="">-- Select JHS Student --</option>
                            @foreach ($allJhsStudents as $st)
                                <option value="{{ $st->id }}">
                                    [{{ $st->student_number }}]
                                    {{ trim(($st->last_name ? $st->last_name . ', ' : '') . $st->first_name . ' ' . $st->middle_name . ' ' . $st->extension_name) }}
                                    ({{ $st->gradeLevel ? $st->gradeLevel->name : 'No Level' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 1.25rem;">
                        <label
                            style="font-size: 0.825rem; font-weight: 700; color: #475569; display: block; margin-bottom: 0.35rem;">Select
                            Class Section *</label>
                        <select id="class_section_id" name="class_section_id" required
                            style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 8px;">
                            <option value="">-- Select Class Section --</option>
                            @foreach ($availableSections as $sec)
                                <option value="{{ $sec->id }}">
                                    {{ $sec->section_name }} ({{ $sec->gradeLevel ? $sec->gradeLevel->name : 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label
                            style="font-size: 0.825rem; font-weight: 700; color: #475569; display: block; margin-bottom: 0.35rem;">Enrollment
                            Status *</label>
                        <select id="status" name="status" required
                            style="width: 100%; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 8px;">
                            <option value="enrolled">Enrolled</option>
                            <option value="pending">Pending</option>
                            <option value="dropped">Dropped</option>
                            <option value="transferred">Transferred</option>
                        </select>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button type="button" onclick="closeEnrollModal()"
                            style="padding: 0.5rem 1rem; border: 1px solid #cbd5e1; background: #ffffff; border-radius: 6px; font-weight: 600; cursor: pointer;">Cancel</button>
                        <button type="submit"
                            style="padding: 0.5rem 1.25rem; background: #1e1b4b; color: #ffffff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">Save
                            Enrollment</button>
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
            $('#enrollmentTable').DataTable({
                pageLength: 10,
                ordering: true
            });

            // Close modal when clicking outside modal box
            $(document).on('click', '#enrollModal', function(e) {
                if (e.target === this) {
                    closeEnrollModal();
                }
            });
        });

        function openEnrollModal() {
            $('#enrollForm').attr('action', "{{ route('junior_high_school.enrollment.store') }}");
            $('#formMethod').val('POST');
            $('#modalTitle').text('Enroll Student into Section');
            $('#studentSelectContainer').show();
            $('#student_id').prop('required', true);
            $('#student_id').val('');
            $('#class_section_id').val('');
            $('#status').val('enrolled');
            $('#enrollModal').addClass('active');
        }

        function openEditEnrollModal(enrollment) {
            $('#enrollForm').attr('action', "/junior-high-school/enrollment/update/" + enrollment.id);
            $('#formMethod').val('POST');
            $('#modalTitle').text('Edit Enrollment Record');
            $('#studentSelectContainer').hide();
            $('#student_id').prop('required', false);
            $('#class_section_id').val(enrollment.class_section_id);
            $('#status').val(enrollment.status);
            $('#enrollModal').addClass('active');
        }

        function closeEnrollModal() {
            $('#enrollModal').removeClass('active');
        }
    </script>
@endpush
