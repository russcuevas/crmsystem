@extends('layouts.superadmin')

@section('title', 'GNHS - Enrolled Student Registry')

@section('content')


    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
                Enrolled Student Registry <span
                    style="font-size: 0.8rem; font-weight: 600; color: var(--accent-emerald); margin-left: 6px;">(S.Y.
                    {{ $activeSchoolYear->school_year ?? '2024-2025' }})</span>
            </div>
            <button class="btn-primary">+ Register Student</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Student No.</th>
                            <th>LRN</th>
                            <th>Full Name</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr>
                                <td><strong>{{ $student->student_number }}</strong></td>
                                <td>{{ $student->lrn ?? 'N/A' }}</td>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ $student->gender }}</td>
                                <td><span class="badge badge-active">{{ ucfirst($student->status ?? 'active') }}</span></td>
                                <td style="text-align: center;">
                                    <a href="{{ route('superadmin.students.show', $student->id) }}" class="btn-action-view">
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted);">No students
                                    registered.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1rem;">
                {{ $students->links() }}
            </div>
        </div>
    </div>
@endsection
