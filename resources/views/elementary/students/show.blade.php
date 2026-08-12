@extends('layouts.elementary')

@section('title', 'GNHS-P BED - Student Profile')
@section('header_title', 'Student Profile Details')

@section('content')
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('elementary.students.page') }}" style="color: #059669; text-decoration: none; font-weight: 700; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.4rem;">
            <i class="fa-solid fa-arrow-left"></i> Back to Student List
        </a>
    </div>

    <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: var(--shadow-sm); max-width: 800px;">
        <div style="display: flex; align-items: center; gap: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1.25rem; margin-bottom: 1.25rem;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: #d1fae5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; font-weight: 800;">
                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
            </div>
            <div>
                <h3 style="font-size: 1.35rem; font-weight: 800; color: #0f172a; margin: 0;">
                    {{ trim(($student->last_name ? $student->last_name . ', ' : '') . $student->first_name . ($student->middle_name ? ' ' . $student->middle_name : '') . ($student->extension_name ? ' ' . $student->extension_name : '')) }}
                </h3>
                <p style="color: #64748b; font-size: 0.85rem; margin: 0.2rem 0 0 0;">
                    Student ID: <strong>{{ $student->student_number }}</strong> | LRN: <strong>{{ $student->lrn ?? 'N/A' }}</strong>
                </p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; font-size: 0.875rem;">
            <div>
                <span style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; display: block;">Education Level</span>
                <strong style="color: #0f172a;">Basic Education Department (BED / Elementary)</strong>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; display: block;">Grade Level</span>
                <strong style="color: #0f172a;">{{ $student->gradeLevel ? $student->gradeLevel->name : 'N/A' }}</strong>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; display: block;">Gender</span>
                <strong style="color: #0f172a;">{{ ucfirst($student->gender ?? 'N/A') }}</strong>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; display: block;">Birthdate</span>
                <strong style="color: #0f172a;">{{ $student->birthday ? $student->birthday->format('F d, Y') : 'N/A' }}</strong>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; display: block;">Contact Phone</span>
                <strong style="color: #0f172a;">{{ $student->phone_number ?? 'N/A' }}</strong>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; display: block;">Address Location</span>
                <strong style="color: #0f172a;">{{ implode(', ', array_filter([$student->barangay, $student->city, $student->province])) ?: 'N/A' }}</strong>
            </div>
        </div>
    </div>
@endsection
