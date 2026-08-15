@extends('layouts.college')

@section('title', 'My College Students')
@section('header_title', 'College Students Directory')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Top Action Bar -->
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; box-shadow: var(--shadow-sm);">
        <div>
            <h2 style="font-size: 1.2rem; font-weight: 800; color: var(--text-main);">College Student Roster</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted);">View, search, and register college students for higher education courses.</p>
        </div>
        <button type="button" onclick="openAddStudentModal()" style="background: #0f172a; color: #ffffff; border: none; font-weight: 800; padding: 0.75rem 1.25rem; border-radius: 8px; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; box-shadow: var(--shadow-sm);">
            <i class="fa-solid fa-user-plus"></i> + Add New Student
        </button>
    </div>

    <!-- Student Table Card -->
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-sm);">
        <div style="overflow-x: auto;">
            <table id="collegeStudentsTable" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid var(--border-color); color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">
                        <th style="padding: 0.85rem 1rem;">Student No.</th>
                        <th style="padding: 0.85rem 1rem;">Full Name</th>
                        <th style="padding: 0.85rem 1rem;">Course / Strand</th>
                        <th style="padding: 0.85rem 1rem;">Year Level</th>
                        <th style="padding: 0.85rem 1rem;">Gender</th>
                        <th style="padding: 0.85rem 1rem;">Email</th>
                        <th style="padding: 0.85rem 1rem; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $s)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.85rem 1rem; font-weight: 800; color: #0284c7;">{{ $s->student_number }}</td>
                            <td style="padding: 0.85rem 1rem; font-weight: 700; color: var(--text-main);">
                                {{ $s->last_name }}, {{ $s->first_name }} {{ $s->middle_name }} {{ $s->extension_name ?? '' }}
                            </td>
                            <td style="padding: 0.85rem 1rem;">
                                <span style="background: #e0f2fe; color: #0369a1; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.8rem;">
                                    {{ $s->course->course_code ?? 'N/A' }}
                                </span>
                            </td>
                            <td style="padding: 0.85rem 1rem; font-weight: 600; color: var(--text-muted);">
                                {{ $s->gradeLevel->name ?? '1st Year' }}
                            </td>
                            <td style="padding: 0.85rem 1rem;">{{ $s->gender }}</td>
                            <td style="padding: 0.85rem 1rem; color: var(--text-muted);">{{ $s->user->email ?? 'N/A' }}</td>
                            <td style="padding: 0.85rem 1rem; text-align: right;">
                                <a href="{{ route('college.enrollment.page', ['student_id' => $s->id]) }}" style="background: #38bdf8; color: #020617; font-weight: 800; text-decoration: none; padding: 0.4rem 0.75rem; border-radius: 6px; font-size: 0.8rem;">
                                    <i class="fa-solid fa-plus"></i> Enroll to Subject
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Student -->
<div id="addStudentModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: #ffffff; width: 100%; max-width: 650px; border-radius: 14px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); overflow: hidden; display: flex; flex-direction: column; max-height: 90vh;">
        <div style="background: #0f172a; padding: 1.25rem 1.5rem; color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0;">Register New College Student</h3>
            <button type="button" onclick="closeAddStudentModal()" style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
        </div>
        <form action="{{ route('college.students.store') }}" method="POST" style="overflow-y: auto; padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">Student Number *</label>
                    <input type="text" name="student_number" value="{{ $nextStudentNumber }}" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-weight: 700; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">Email Address *</label>
                    <input type="email" name="email" placeholder="student@college.edu" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">First Name *</label>
                    <input type="text" name="first_name" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">Middle Name</label>
                    <input type="text" name="middle_name" style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">Last Name *</label>
                    <input type="text" name="last_name" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">Course / Program *</label>
                    <select name="course_id" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
                        <option value="">-- Select Course --</option>
                        @foreach($collegeCourses as $c)
                            <option value="{{ $c->id }}">{{ $c->course_code }} - {{ $c->course_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">Year Level *</label>
                    <select name="grade_level_id" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
                        <option value="">-- Select Year Level --</option>
                        @foreach($collegeGradeLevels as $gl)
                            <option value="{{ $gl->id }}">{{ $gl->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">Gender *</label>
                    <select name="gender" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">Birthday</label>
                    <input type="date" name="birthday" style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem;">
                <button type="button" onclick="closeAddStudentModal()" style="padding: 0.65rem 1.25rem; border: 1px solid var(--border-color); background: #ffffff; border-radius: 8px; font-weight: 700; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 0.65rem 1.25rem; background: #0f172a; color: #ffffff; border: none; border-radius: 8px; font-weight: 800; cursor: pointer;">Register Student</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddStudentModal() {
        document.getElementById('addStudentModal').style.display = 'flex';
    }

    function closeAddStudentModal() {
        document.getElementById('addStudentModal').style.display = 'none';
    }
</script>
@endsection
