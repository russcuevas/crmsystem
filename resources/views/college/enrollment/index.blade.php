@extends('layouts.college')

@section('title', 'Enroll College Students')
@section('header_title', 'College Class Subject Enrollment')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Top Action Card -->
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; box-shadow: var(--shadow-sm);">
        <div>
            <h2 style="font-size: 1.2rem; font-weight: 800; color: var(--text-main);">Subject Enrollment</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted);">Enroll college students into your handled class sections for S.Y. {{ $activeSchoolYear->school_year ?? '' }}.</p>
        </div>
        <button type="button" onclick="openEnrollModal()" style="background: #38bdf8; color: #020617; border: none; font-weight: 800; padding: 0.75rem 1.25rem; border-radius: 8px; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; box-shadow: 0 4px 10px rgba(56, 189, 248, 0.3);">
            <i class="fa-solid fa-plus"></i> + Enroll Student
        </button>
    </div>

    <!-- Enrollments List Card -->
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-sm);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid var(--border-color); color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">
                        <th style="padding: 0.85rem 1rem;">Student No.</th>
                        <th style="padding: 0.85rem 1rem;">Student Name</th>
                        <th style="padding: 0.85rem 1rem;">Course</th>
                        <th style="padding: 0.85rem 1rem;">Class Section</th>
                        <th style="padding: 0.85rem 1rem;">Academic Year</th>
                        <th style="padding: 0.85rem 1rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $e)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.85rem 1rem; font-weight: 800; color: #0284c7;">{{ $e->student->student_number ?? 'N/A' }}</td>
                            <td style="padding: 0.85rem 1rem; font-weight: 700; color: var(--text-main);">
                                {{ $e->student->last_name ?? '' }}, {{ $e->student->first_name ?? '' }} {{ $e->student->middle_name ?? '' }} {{ $e->student->extension_name ?? '' }}
                            </td>
                            <td style="padding: 0.85rem 1rem; color: var(--text-muted);">
                                {{ $e->student->course->course_code ?? 'General College' }}
                            </td>
                            <td style="padding: 0.85rem 1rem;">
                                <span style="background: #f1f5f9; color: #0f172a; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.8rem;">
                                    {{ $e->classSection->section_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td style="padding: 0.85rem 1rem; color: var(--text-muted);">S.Y. {{ $e->schoolYear->school_year ?? '' }}</td>
                            <td style="padding: 0.85rem 1rem;">
                                <span style="background: #dcfce7; color: #15803d; font-weight: 800; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.75rem;">
                                    Enrolled
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                                No students enrolled in your handled college sections yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Enroll Student -->
<div id="enrollModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: #ffffff; width: 100%; max-width: 500px; border-radius: 14px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); overflow: hidden; display: flex; flex-direction: column;">
        <div style="background: #0f172a; padding: 1.25rem 1.5rem; color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0;">Enroll Student to Section</h3>
            <button type="button" onclick="closeEnrollModal()" style="background: none; border: none; color: #ffffff; font-size: 1.4rem; cursor: pointer;">&times;</button>
        </div>
        <form action="{{ route('college.enrollment.store') }}" method="POST" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf

            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">Select College Student *</label>
                <select name="student_id" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
                    <option value="">-- Select Student --</option>
                    @foreach($students as $st)
                        <option value="{{ $st->id }}" {{ request('student_id') == $st->id ? 'selected' : '' }}>
                            {{ $st->student_number }} - {{ $st->last_name }}, {{ $st->first_name }} {{ $st->middle_name ?? '' }} {{ $st->extension_name ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">Target Class Section *</label>
                <select name="class_section_id" required style="width: 100%; padding: 0.65rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.875rem;">
                    <option value="">-- Select Handled Section --</option>
                    @foreach($sections as $sec)
                        <option value="{{ $sec->id }}">
                            {{ $sec->section_name }} ({{ $sec->course->course_code ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem;">
                <button type="button" onclick="closeEnrollModal()" style="padding: 0.65rem 1.25rem; border: 1px solid var(--border-color); background: #ffffff; border-radius: 8px; font-weight: 700; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 0.65rem 1.25rem; background: #0f172a; color: #ffffff; border: none; border-radius: 8px; font-weight: 800; cursor: pointer;">Complete Enrollment</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEnrollModal() {
        document.getElementById('enrollModal').style.display = 'flex';
    }

    function closeEnrollModal() {
        document.getElementById('enrollModal').style.display = 'none';
    }

    @if(request('student_id'))
        document.addEventListener('DOMContentLoaded', function() {
            openEnrollModal();
        });
    @endif
</script>
@endsection
