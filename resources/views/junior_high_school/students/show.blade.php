@extends('layouts.junior_high_school')

@section('title', 'GNHS - Student Profile')
@section('header_title', 'Student Profile Information')

@section('content')
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('junior_high_school.students.page') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; color: #3730a3; font-weight: 700; font-size: 0.9rem;">
            <i class="fa-solid fa-arrow-left"></i> Back to Handled Students List
        </a>
    </div>

    <!-- Student Details Card -->
    <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 2rem; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 1.5rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
            <div style="width: 72px; height: 72px; border-radius: 50%; background: #1e1b4b; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; font-weight: 800;">
                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
            </div>
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }} {{ $student->extension_name }}</h2>
                <p style="font-size: 0.9rem; color: #64748b; margin-top: 0.2rem;">
                    Student No: <strong style="color: #3730a3; font-family: monospace;">{{ $student->student_number }}</strong>
                    @if ($student->lrn)
                        &bull; LRN: <strong style="color: #0f172a; font-family: monospace;">{{ $student->lrn }}</strong>
                    @endif
                    &bull; Grade Level: <span style="background: #e0e7ff; color: #3730a3; padding: 0.2rem 0.5rem; border-radius: 0.25rem; font-weight: 700; font-size: 0.8rem;">{{ $student->gradeLevel ? $student->gradeLevel->name : 'N/A' }}</span>
                </p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem;">
            <div>
                <span style="font-size: 0.775rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Email Address</span>
                <p style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">{{ $student->user ? $student->user->email : 'N/A' }}</p>
            </div>
            <div>
                <span style="font-size: 0.775rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Gender</span>
                <p style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">{{ $student->gender ?? 'Not Specified' }}</p>
            </div>
            <div>
                <span style="font-size: 0.775rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Phone Number</span>
                <p style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">{{ $student->phone_number ?? 'N/A' }}</p>
            </div>
            <div>
                <span style="font-size: 0.775rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Birthday</span>
                <p style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">{{ $student->birthday ? $student->birthday->format('F d, Y') : 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Enrollment History -->
    <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: var(--shadow-sm);">
        <h3 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-graduation-cap" style="color: #4f46e5;"></i> Class Section Enrollment History
        </h3>

        @if ($student->enrollments->isNotEmpty())
            <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left;">
                        <th style="padding: 0.75rem 1rem;">School Year</th>
                        <th style="padding: 0.75rem 1rem;">Class Section</th>
                        <th style="padding: 0.75rem 1rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($student->enrollments as $enr)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 0.75rem 1rem; font-weight: 700;">{{ $enr->schoolYear ? $enr->schoolYear->school_year : 'N/A' }}</td>
                            <td style="padding: 0.75rem 1rem;">{{ $enr->classSection ? $enr->classSection->section_name : 'N/A' }}</td>
                            <td style="padding: 0.75rem 1rem;">
                                <span style="background: #d1fae5; color: #065f46; padding: 0.2rem 0.6rem; border-radius: 0.25rem; font-weight: 700; font-size: 0.75rem; text-transform: uppercase;">
                                    {{ $enr->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="font-size: 0.875rem; color: #64748b; text-align: center; padding: 2rem 0;">No enrollment records found for this student.</p>
        @endif
    </div>
@endsection
