@extends('layouts.superadmin')

@section('title', 'GNHS - Enrolled Student Registry')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
            </svg>
            Enrolled Student Registry
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted);">No students registered.</td>
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
