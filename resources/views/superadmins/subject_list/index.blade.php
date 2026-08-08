@extends('layouts.superadmin')

@section('title', 'GNHS - Subject Catalog List')

@section('content')

    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Subject Catalog List <span
                    style="font-size: 0.8rem; font-weight: 600; color: var(--accent-emerald); margin-left: 6px;">(S.Y.
                    {{ $activeSchoolYear->school_year ?? '2024-2025' }})</span>
            </div>
            <button class="btn-primary">+ Add New Subject</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Subject Name</th>
                            <th>Education Level</th>
                            <th>Units</th>
                            <th>Semester/Quarter</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subjects as $subject)
                            <tr>
                                <td><strong>{{ $subject->subject_code }}</strong></td>
                                <td>{{ $subject->subject_name }}</td>
                                <td>{{ $subject->educationLevel->name ?? 'N/A' }}</td>
                                <td>{{ $subject->units ?? 'N/A' }}</td>
                                <td><span class="badge badge-admin">{{ $subject->semester }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted);">No subjects found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1rem;">
                {{ $subjects->links() }}
            </div>
        </div>
    </div>
@endsection
