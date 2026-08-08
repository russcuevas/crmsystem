@extends('layouts.superadmin')

@section('title', 'GNHS - Faculty Information Roster')

@section('content')

    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                Faculty Information Roster <span
                    style="font-size: 0.8rem; font-weight: 600; color: var(--accent-emerald); margin-left: 6px;">(S.Y.
                    {{ $activeSchoolYear->school_year ?? '2024-2025' }})</span>
            </div>
            <button class="btn-primary">+ Add Faculty Member</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Teacher ID</th>
                            <th>Full Name</th>
                            <th>Position</th>
                            <th>Education Level</th>
                            <th>Phone Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teachers as $teacher)
                            <tr>
                                <td><strong>{{ $teacher->teacher_id }}</strong></td>
                                <td>{{ $teacher->first_name }} {{ $teacher->last_name }}</td>
                                <td>{{ $teacher->position }}</td>
                                <td><span class="badge badge-teacher">{{ $teacher->educationLevel->code ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $teacher->phone_number }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted);">No faculty members
                                    found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1rem;">
                {{ $teachers->links() }}
            </div>
        </div>
    </div>
@endsection
