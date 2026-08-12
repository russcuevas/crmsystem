@extends('layouts.admin')

@section('title', 'GNHS-P - Admin Dashboard')

@section('content')
    <div class="dashboard-content-grid">
        <!-- Left Main Panel -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    System User Accounts {{ $selectedLevel ? '(' . $selectedLevel . ')' : 'Overview' }}
                </div>
                <a href="{{ route('admin.accounts.page', array_filter(['level' => $selectedLevel])) }}"
                    class="btn-action">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Email Address</th>
                                <th>System Role</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentUsers as $user)
                                <tr>
                                    <td><strong>{{ $user->name }}</strong></td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge badge-{{ $user->role }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $user->status == 'active' ? 'active' : 'inactive' }}">
                                            {{ ucfirst($user->status ?? 'Active') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--text-muted);">No recent
                                        accounts registered.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Panel: Education Levels & Faculty Overview -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Education Levels -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v12" />
                        </svg>
                        Education Levels
                    </div>
                </div>
                <div class="card-body">
                    <div class="level-grid">
                        @foreach ($educationLevels as $lvl)
                            @php
                                $isActiveLevel = $selectedLevel == $lvl->code;
                            @endphp
                            <div class="level-card"
                                style="{{ $isActiveLevel ? 'border-color: var(--accent-gold); background: rgba(245, 180, 29, 0.04);' : '' }}">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div class="level-card-code">{{ $lvl->code }}</div>
                                    <span class="badge badge-admin"
                                        style="font-size: 0.68rem; padding: 0.15rem 0.5rem;">{{ $lvl->grade_levels_count }}
                                        Grade Levels</span>
                                </div>
                                <div class="level-card-title" style="margin-top: 4px;">{{ $lvl->name }}</div>
                                <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 4px;">
                                    {{ $lvl->subjects_count }} Subjects Active
                                </div>
                                <div style="margin-top: 10px; display: flex; gap: 6px;">
                                    <a href="{{ route('admin.students.page', ['level' => $lvl->code]) }}"
                                        class="level-action-btn" title="View {{ $lvl->code }} Students">
                                        Students
                                    </a>
                                    <a href="{{ route('admin.faculty.page', ['level' => $lvl->code]) }}"
                                        class="level-action-btn" title="View {{ $lvl->code }} Faculty">
                                        Faculty
                                    </a>
                                    <a href="{{ route('admin.sections.page', ['level' => $lvl->code]) }}"
                                        class="level-action-btn" title="View {{ $lvl->code }} Sections">
                                        Sections
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Recent Faculty Roster -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Assigned Faculty {{ $selectedLevel ? '(' . $selectedLevel . ')' : '' }}
                    </div>
                    <a href="{{ route('admin.faculty.page', array_filter(['level' => $selectedLevel])) }}"
                        class="btn-action">View All</a>
                </div>
                <div class="card-body" style="padding: 1rem;">
                    @forelse ($recentTeachers as $t)
                        <div
                            style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.5rem; border-bottom: 1px solid var(--border-color);">
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 700; color: var(--primary-navy);">
                                    {{ $t->first_name }} {{ $t->last_name }}
                                </div>
                                <div style="font-size: 0.72rem; color: var(--text-muted);">
                                    {{ $t->position }} ({{ $t->teacher_id }})
                                </div>
                            </div>
                            <span class="badge badge-teacher">{{ $t->educationLevel->code ?? 'N/A' }}</span>
                        </div>
                    @empty
                        <div style="font-size: 0.8rem; color: var(--text-muted); text-align: center; padding: 1rem;">No
                            faculty members assigned {{ $selectedLevel ? 'to ' . $selectedLevel : '' }} in S.Y.
                            {{ $activeSchoolYear->school_year ?? 'this School Year' }}.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
