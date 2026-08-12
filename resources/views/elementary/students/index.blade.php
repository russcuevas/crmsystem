@extends('layouts.elementary')

@section('title', 'GNHS-P BED - My Elementary Students')
@section('header_title', 'My Elementary Students')

@section('content')
    <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">Enrolled Elementary Students</h3>
                <p style="font-size: 0.825rem; color: #64748b; margin: 0.2rem 0 0 0;">Students enrolled in your advisory and subject sections.</p>
            </div>
            <span style="background: #d1fae5; color: #065f46; padding: 0.35rem 0.85rem; border-radius: 9999px; font-weight: 800; font-size: 0.8rem;">
                Total: {{ $students->count() }}
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569; font-size: 0.8rem; text-transform: uppercase;">
                        <th style="padding: 0.75rem;">#</th>
                        <th style="padding: 0.75rem;">Student Number</th>
                        <th style="padding: 0.75rem;">LRN</th>
                        <th style="padding: 0.75rem;">Full Name</th>
                        <th style="padding: 0.75rem;">Grade Level</th>
                        <th style="padding: 0.75rem;">Section</th>
                        <th style="padding: 0.75rem; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $index => $stu)
                        @php
                            $enr = $stu->enrollments->first();
                            $sec = $enr ? $enr->classSection : null;
                        @endphp
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 0.75rem;">{{ $index + 1 }}</td>
                            <td style="padding: 0.75rem; font-weight: 700; color: #059669;">{{ $stu->student_number }}</td>
                            <td style="padding: 0.75rem;">{{ $stu->lrn ?? 'N/A' }}</td>
                            <td style="padding: 0.75rem; font-weight: 700; color: #0f172a;">
                                {{ trim(($stu->last_name ? $stu->last_name . ', ' : '') . $stu->first_name . ($stu->middle_name ? ' ' . $stu->middle_name : '') . ($stu->extension_name ? ' ' . $stu->extension_name : '')) }}
                            </td>
                            <td style="padding: 0.75rem;">
                                <span style="background: #e0e7ff; color: #3730a3; padding: 0.2rem 0.5rem; border-radius: 6px; font-weight: 700; font-size: 0.75rem;">
                                    {{ $stu->gradeLevel ? $stu->gradeLevel->name : 'N/A' }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem; font-weight: 600;">
                                {{ $sec ? $sec->section_name : 'N/A' }}
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                <a href="{{ route('elementary.students.show', $stu->id) }}" style="background: #f1f5f9; color: #0f172a; text-decoration: none; padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 700; font-size: 0.775rem; display: inline-flex; align-items: center; gap: 0.3rem;">
                                    <i class="fa-solid fa-eye"></i> Profile
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #94a3b8;">
                                <i class="fa-solid fa-user-slash" style="font-size: 2.5rem; margin-bottom: 0.75rem;"></i>
                                <p style="font-weight: 700; margin: 0;">No elementary students found in your assigned sections.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
