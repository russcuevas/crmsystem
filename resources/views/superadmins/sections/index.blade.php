@extends('layouts.superadmin')

@section('title', 'GNHS - Manage Class Sections')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v12"/>
            </svg>
            Manage Class Sections
        </div>
        <button class="btn-primary">+ Create New Section</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Section Name</th>
                        <th>School Year</th>
                        <th>Grade Level</th>
                        <th>Class Adviser</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sections as $section)
                        <tr>
                            <td><strong>{{ $section->section_name }}</strong></td>
                            <td>S.Y. {{ $section->schoolYear->school_year ?? 'N/A' }}</td>
                            <td><span class="badge badge-admin">{{ $section->gradeLevel->name ?? 'N/A' }}</span></td>
                            <td>{{ $section->adviser ? $section->adviser->first_name . ' ' . $section->adviser->last_name : 'Unassigned' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-muted);">No class sections found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $sections->links() }}
        </div>
    </div>
</div>
@endsection
