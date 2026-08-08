@extends('layouts.superadmin')

@section('title', 'GNHS - Student Profiling | ' . $student->first_name . ' ' . $student->last_name)

@section('content')
    <div style="margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ route('superadmin.students.page') }}" class="btn-back">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Student Registry
        </a>

        <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">
            Active S.Y.: <span style="color: var(--accent-emerald);">{{ $activeSchoolYear->school_year ?? 'N/A' }}</span>
        </div>
    </div>

    <!-- Student Profile Hero Banner -->
    <div class="profile-banner-card">
        <div class="profile-avatar-circle">
            {{ strtoupper(substr($student->first_name ?? 'S', 0, 1) . substr($student->last_name ?? 'T', 0, 1)) }}
        </div>
        <div class="profile-hero-info">
            <h2>{{ $student->first_name }}
                {{ $student->middle_name ? $student->middle_name . ' ' : '' }}{{ $student->last_name }}
                {{ $student->extension_name }}</h2>
            <div class="profile-hero-meta">
                <span class="profile-pill">Student ID: {{ $student->student_number }}</span>
                <span class="profile-pill">LRN: {{ $student->lrn ?? 'N/A' }}</span>
                <span class="profile-pill"
                    style="background: rgba(16, 185, 129, 0.2); border-color: rgba(16, 185, 129, 0.4); color: #a7f3d0;">
                    Status: {{ ucfirst($student->status ?? 'active') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Information Cards Grid -->
    <div class="profile-grid">
        <!-- Personal Information Card -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Personal Information
                </div>
            </div>
            <div class="card-body">
                <div class="info-item-group">
                    <span class="info-label">Full Name</span>
                    <span class="info-value">{{ $student->first_name }}
                        {{ $student->middle_name ? $student->middle_name . ' ' : '' }}{{ $student->last_name }}
                        {{ $student->extension_name }}</span>
                </div>
                <div class="info-item-group">
                    <span class="info-label">Gender</span>
                    <span class="info-value">{{ ucfirst($student->gender ?? 'N/A') }}</span>
                </div>
                <div class="info-item-group">
                    <span class="info-label">Date of Birth</span>
                    <span class="info-value">
                        {{ $student->birthday ? $student->birthday->format('F d, Y') . ' (' . $student->birthday->age . ' yrs old)' : 'N/A' }}
                    </span>
                </div>
                <div class="info-item-group">
                    <span class="info-label">Phone Number</span>
                    <span class="info-value">{{ $student->phone_number ?? 'N/A' }}</span>
                </div>
                <div class="info-item-group">
                    <span class="info-label">Address</span>
                    <span class="info-value">
                        @if ($student->barangay || $student->city || $student->province)
                            {{ implode(', ', array_filter([$student->barangay, $student->city, $student->province])) }}
                        @else
                            N/A
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Account Details Card -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    System Account & User Credentials
                </div>
            </div>
            <div class="card-body">
                <div class="info-item-group">
                    <span class="info-label">Account Name</span>
                    <span class="info-value">{{ $student->user->name ?? 'N/A' }}</span>
                </div>
                <div class="info-item-group">
                    <span class="info-label">Email Address</span>
                    <span class="info-value">{{ $student->user->email ?? 'N/A' }}</span>
                </div>
                <div class="info-item-group">
                    <span class="info-label">Role</span>
                    <span class="info-value"><span class="badge badge-student">Student</span></span>
                </div>
                <div class="info-item-group">
                    <span class="info-label">Account Status</span>
                    <span class="info-value"><span
                            class="badge badge-active">{{ ucfirst($student->user->status ?? 'active') }}</span></span>
                </div>
                <div class="info-item-group">
                    <span class="info-label">Registered Date</span>
                    <span
                        class="info-value">{{ $student->created_at ? $student->created_at->format('M d, Y') : 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Current Enrollment Status Card -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v12" />
                    </svg>
                    Current Academic Enrollment
                </div>
            </div>
            <div class="card-body">
                @if ($currentEnrollment)
                    <div class="info-item-group">
                        <span class="info-label">Active School Year</span>
                        <span class="info-value" style="color: var(--accent-emerald); font-weight: 700;">S.Y.
                            {{ $currentEnrollment->schoolYear->school_year ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item-group">
                        <span class="info-label">Grade Level</span>
                        <span class="info-value">{{ $currentEnrollment->gradeLevel->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item-group">
                        <span class="info-label">Class Section</span>
                        <span
                            class="info-value"><strong>{{ $currentEnrollment->classSection->section_name ?? 'N/A' }}</strong></span>
                    </div>
                    <div class="info-item-group">
                        <span class="info-label">Class Adviser</span>
                        <span class="info-value">
                            @if (isset($currentEnrollment->classSection->adviser))
                                {{ $currentEnrollment->classSection->adviser->first_name }}
                                {{ $currentEnrollment->classSection->adviser->last_name }}
                            @else
                                Unassigned
                            @endif
                        </span>
                    </div>
                    <div class="info-item-group">
                        <span class="info-label">Enrollment Date</span>
                        <span
                            class="info-value">{{ $currentEnrollment->enrolled_at ? $currentEnrollment->enrolled_at->format('F d, Y') : 'N/A' }}</span>
                    </div>
                @else
                    <div style="padding: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.85rem;">
                        No active enrollment record for S.Y. {{ $activeSchoolYear->school_year ?? '' }}.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Academic History & Enrollment Records Timeline -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Academic Enrollment History
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>School Year</th>
                            <th>Grade Level</th>
                            <th>Class Section</th>
                            <th>Class Adviser</th>
                            <th>Enrolled Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($student->enrollments as $enrollment)
                            <tr>
                                <td><strong>S.Y. {{ $enrollment->schoolYear->school_year ?? 'N/A' }}</strong></td>
                                <td><span class="badge badge-admin">{{ $enrollment->gradeLevel->name ?? 'N/A' }}</span>
                                </td>
                                <td><strong>{{ $enrollment->classSection->section_name ?? 'N/A' }}</strong></td>
                                <td>
                                    @if (isset($enrollment->classSection->adviser))
                                        {{ $enrollment->classSection->adviser->first_name }}
                                        {{ $enrollment->classSection->adviser->last_name }}
                                    @else
                                        <span style="color: var(--text-muted);">Unassigned</span>
                                    @endif
                                </td>
                                <td>{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : 'N/A' }}
                                </td>
                                <td><span class="badge badge-active">{{ ucfirst($enrollment->status ?? 'active') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted);">No enrollment
                                    history found for this student.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
