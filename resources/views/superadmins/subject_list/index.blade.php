@extends('layouts.superadmin')

@section('title', 'GNHS - Subject Catalog List')

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

        .badge-quarter {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .badge-sem {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .level-info-box {
            font-size: 0.78rem;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            margin-top: 0.4rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .level-info-box.jhs-bed {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .level-info-box.shs-college {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .action-btn-group {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-action-icon {
            padding: 0.4rem;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-action-icon:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        .btn-action-icon.danger:hover {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
        }
    </style>

    @if (session('success'))
        <div style="padding: 0.85rem 1.25rem; background: #ecfdf5; border: 1px solid #6ee7b7; color: #065f46; border-radius: 10px; margin-bottom: 1.25rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 1.1rem; cursor: pointer; color: #065f46;">&times;</button>
        </div>
    @endif

    @if ($errors->any())
        <div style="padding: 0.85rem 1.25rem; background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; border-radius: 10px; margin-bottom: 1.25rem; font-weight: 600;">
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
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Subject Catalog List
                @if (isset($currentEducationLevel) && $currentEducationLevel)
                    <span class="badge badge-admin" style="margin-left: 8px; font-size: 0.82rem;">
                        {{ $currentEducationLevel->code }}
                    </span>
                @endif
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--accent-emerald); margin-left: 6px;">
                    (S.Y. {{ $activeSchoolYear->school_year ?? '2024-2025' }})
                </span>
            </div>
            
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <form action="{{ route('superadmin.subjects.page') }}" method="GET" style="display: flex; gap: 0.5rem;">
                    @if(request('level'))
                        <input type="hidden" name="level" value="{{ request('level') }}">
                    @endif
                    @if(request('semester'))
                        <input type="hidden" name="semester" value="{{ request('semester') }}">
                    @endif
                    @if(request('academic_period'))
                        <input type="hidden" name="academic_period" value="{{ request('academic_period') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search subject code/name..." class="form-control-custom" style="padding: 0.45rem 0.75rem; width: 220px; font-size: 0.82rem;">
                    <button type="submit" class="btn-cancel" style="padding: 0.45rem 0.85rem; font-size: 0.82rem;">Search</button>
                </form>
                <button class="btn-primary" onclick="openAddSubjectModal()">+ Add New Subject</button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Subject Name</th>
                            <th>Education Level</th>
                            <th>Course</th>
                            <th>Units</th>
                            <th>Semester / Academic Period</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subjects as $subject)
                            @php
                                $lvlCode = strtoupper($subject->educationLevel->code ?? '');
                                $isJhsOrBed = in_array($lvlCode, ['JHS', 'BED']);
                            @endphp
                            <tr>
                                <td><strong>{{ $subject->subject_code }}</strong></td>
                                <td>{{ $subject->subject_name }}</td>
                                <td>
                                    <span class="badge badge-admin">
                                        {{ $subject->educationLevel->code ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $subject->course->course_code ?? '-' }}</td>
                                <td>{{ $subject->units ?? '3' }}</td>
                                <td>
                                    @if ($isJhsOrBed || str_contains(strtolower($subject->semester ?? ''), 'quarter'))
                                        <span class="badge-quarter" title="Applies to 1st, 2nd, 3rd, and 4th Quarters">
                                            All Quarters (Full Year)
                                        </span>
                                    @else
                                        <span class="badge-sem">
                                            {{ $subject->semester ?? '1st Semester' }}
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div class="action-btn-group" style="justify-content: center;">
                                        <button type="button" class="btn-action-icon" title="Edit Subject"
                                            onclick='openEditSubjectModal(@json($subject))'>
                                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('superadmin.subjects.destroy', $subject->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this subject catalog entry?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-icon danger" title="Delete Subject">
                                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    No subjects found. Click "+ Add New Subject" to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1rem;">
                {{ $subjects->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Modal: Add New Subject -->
    <div class="modal-overlay" id="addSubjectModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800;">Add New Subject</h3>
                <button type="button" onclick="closeAddSubjectModal()" style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('superadmin.subjects.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Education Level <span style="color: #ef4444;">*</span></label>
                        @if (isset($currentEducationLevel) && $currentEducationLevel)
                            <input type="hidden" name="education_level_id" id="add_education_level_id" value="{{ $currentEducationLevel->id }}" data-code="{{ strtoupper($currentEducationLevel->code) }}">
                            <div style="padding: 0.65rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 700; background: #f1f5f9; color: #0f172a; display: flex; align-items: center; justify-content: space-between;">
                                <span>{{ $currentEducationLevel->code }} - {{ $currentEducationLevel->name }}</span>
                                <span class="badge badge-admin" style="font-size: 0.72rem;">Current Level</span>
                            </div>
                        @else
                            <select name="education_level_id" id="add_education_level_id" class="form-control-custom" required onchange="handleLevelChange(this, 'add_semester', 'add_course_group', 'add_level_info')">
                                <option value="" disabled selected>-- Select Education Level --</option>
                                @foreach ($educationLevelsList as $lvl)
                                    <option value="{{ $lvl->id }}" data-code="{{ strtoupper($lvl->code) }}">
                                        {{ $lvl->code }} - {{ $lvl->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div id="add_level_info" style="display: none;" class="level-info-box"></div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label>Subject Code <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="subject_code" class="form-control-custom" placeholder="e.g. MATH101 or ENG7" required>
                    </div>

                    <div class="form-group">
                        <label>Subject Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="subject_name" class="form-control-custom" placeholder="e.g. General Mathematics" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Units</label>
                            <input type="number" name="units" class="form-control-custom" value="3" min="0">
                        </div>

                        <div class="form-group">
                            <label>Semester / Academic Period <span style="color: #ef4444;">*</span></label>
                            <select name="semester" id="add_semester" class="form-control-custom" required>
                                <!-- Populated dynamically by JS -->
                            </select>
                        </div>
                    </div>

                    <div class="form-group" id="add_course_group" style="display: block;">
                        <label>Course (College / Strand Optional)</label>
                        <select name="course_id" class="form-control-custom">
                            <option value="">-- None / General Subject --</option>
                            @foreach ($coursesList as $course)
                                <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->course_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddSubjectModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Save Subject</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Subject -->
    <div class="modal-overlay" id="editSubjectModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 style="font-size: 1.1rem; font-weight: 800;">Edit Subject</h3>
                <button type="button" onclick="closeEditSubjectModal()" style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <form id="editSubjectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Education Level <span style="color: #ef4444;">*</span></label>
                        <select name="education_level_id" id="edit_education_level_id" class="form-control-custom" required onchange="handleLevelChange(this, 'edit_semester', 'edit_course_group', 'edit_level_info')">
                            @foreach ($educationLevelsList as $lvl)
                                <option value="{{ $lvl->id }}" data-code="{{ strtoupper($lvl->code) }}">
                                    {{ $lvl->code }} - {{ $lvl->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="edit_level_info" style="display: none;" class="level-info-box"></div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label>Subject Code <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="subject_code" id="edit_subject_code" class="form-control-custom" required>
                    </div>

                    <div class="form-group">
                        <label>Subject Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="subject_name" id="edit_subject_name" class="form-control-custom" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Units</label>
                            <input type="number" name="units" id="edit_units" class="form-control-custom" min="0">
                        </div>

                        <div class="form-group">
                            <label>Semester / Academic Period <span style="color: #ef4444;">*</span></label>
                            <select name="semester" id="edit_semester" class="form-control-custom" required>
                                <!-- Populated dynamically by JS -->
                            </select>
                        </div>
                    </div>

                    <div class="form-group" id="edit_course_group">
                        <label>Course (College / Strand Optional)</label>
                        <select name="course_id" id="edit_course_id" class="form-control-custom">
                            <option value="">-- None / General Subject --</option>
                            @foreach ($coursesList as $course)
                                <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->course_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditSubjectModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Update Subject</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateSemesterOptions(semSelect, code, selectedValue) {
            if (!semSelect) return;

            semSelect.innerHTML = '';

            if (code === 'BED' || code === 'JHS') {
                const options = [
                    { value: 'All Quarters', text: 'All Quarters (Full Year)' }
                ];

                options.forEach(optData => {
                    const opt = document.createElement('option');
                    opt.value = optData.value;
                    opt.textContent = optData.text;
                    opt.selected = true;
                    semSelect.appendChild(opt);
                });
            } else {
                // SHS, COLLEGE, or default semestral levels
                const options = [
                    { value: '1st Semester', text: '1st Semester' },
                    { value: '2nd Semester', text: '2nd Semester' }
                ];

                options.forEach(optData => {
                    const opt = document.createElement('option');
                    opt.value = optData.value;
                    opt.textContent = optData.text;
                    if (selectedValue && selectedValue === optData.value) {
                        opt.selected = true;
                    } else if (!selectedValue && optData.value === '1st Semester') {
                        opt.selected = true;
                    }
                    semSelect.appendChild(opt);
                });
            }
        }

        function openAddSubjectModal() {
            document.getElementById('addSubjectModal').style.display = 'flex';
            const levelEl = document.getElementById('add_education_level_id');
            if (levelEl) {
                handleLevelChange(levelEl, 'add_semester', 'add_course_group', 'add_level_info');
            }
        }

        function closeAddSubjectModal() {
            document.getElementById('addSubjectModal').style.display = 'none';
        }

        function openEditSubjectModal(subject) {
            const form = document.getElementById('editSubjectForm');
            form.action = "{{ url('/superadmin/subjects/update') }}/" + subject.id;

            document.getElementById('edit_education_level_id').value = subject.education_level_id;
            document.getElementById('edit_subject_code').value = subject.subject_code;
            document.getElementById('edit_subject_name').value = subject.subject_name;
            document.getElementById('edit_units').value = subject.units ?? 3;
            document.getElementById('edit_course_id').value = subject.course_id ?? '';

            const levelSelect = document.getElementById('edit_education_level_id');
            handleLevelChange(levelSelect, 'edit_semester', 'edit_course_group', 'edit_level_info', subject.semester);

            document.getElementById('editSubjectModal').style.display = 'flex';
        }

        function closeEditSubjectModal() {
            document.getElementById('editSubjectModal').style.display = 'none';
        }

        function handleLevelChange(selectEl, semSelectId, courseGroupId, infoBoxId, initialSemValue) {
            if (!selectEl) return;
            let code = '';
            if (selectEl.tagName === 'SELECT') {
                const selectedOption = selectEl.options[selectEl.selectedIndex];
                if (selectedOption) {
                    code = selectedOption.getAttribute('data-code');
                }
            } else {
                code = selectEl.getAttribute('data-code');
            }

            const semSelect = document.getElementById(semSelectId);
            const courseGroup = document.getElementById(courseGroupId);
            const infoBox = document.getElementById(infoBoxId);

            const currentVal = initialSemValue || (semSelect ? semSelect.value : '');

            updateSemesterOptions(semSelect, code, currentVal);

            if (code === 'BED' || code === 'JHS') {
                if (infoBox) {
                    infoBox.className = 'level-info-box jhs-bed';
                    infoBox.style.display = 'flex';
                    infoBox.innerHTML = `
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Basic Education / JHS subjects are automatically set to <strong>All Quarters (Full Year)</strong>.</span>
                    `;
                }
                if (courseGroup) {
                    courseGroup.style.display = 'none';
                }
            } else if (code === 'SHS' || code === 'COLLEGE') {
                if (infoBox) {
                    infoBox.className = 'level-info-box shs-college';
                    infoBox.style.display = 'flex';
                    infoBox.innerHTML = `
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Select whether offered in <strong>1st Semester</strong> or <strong>2nd Semester</strong>.</span>
                    `;
                }
                if (courseGroup) {
                    courseGroup.style.display = 'block';
                }
            } else {
                if (infoBox) {
                    infoBox.style.display = 'none';
                }
                if (courseGroup) {
                    courseGroup.style.display = 'block';
                }
            }
        }
    </script>
@endpush
