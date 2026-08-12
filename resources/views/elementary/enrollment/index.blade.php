@extends('layouts.elementary')

@section('title', 'GNHS-P BED - Elementary Student Enrollment')
@section('header_title', 'Elementary Student Enrollment Management')

@section('content')
    <div style="background: #ffffff; border-radius: 1rem; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">Enrolled Students List</h3>
                <p style="font-size: 0.825rem; color: #64748b; margin: 0.2rem 0 0 0;">School Year: <strong>{{ $activeSchoolYear ? $activeSchoolYear->school_year : '2026-2027' }}</strong></p>
            </div>
            @if ($advisorySections->isNotEmpty())
                <button type="button" onclick="$('#enrollModal').css('display', 'flex')" style="background: #059669; color: #ffffff; border: none; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <i class="fa-solid fa-user-plus"></i> Enroll Student
                </button>
            @endif
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569; font-size: 0.8rem; text-transform: uppercase;">
                        <th style="padding: 0.75rem;">#</th>
                        <th style="padding: 0.75rem;">Student Number</th>
                        <th style="padding: 0.75rem;">Student Name</th>
                        <th style="padding: 0.75rem;">Grade & Section</th>
                        <th style="padding: 0.75rem;">Status</th>
                        <th style="padding: 0.75rem; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($enrollments as $idx => $enr)
                        @php
                            $stu = $enr->student;
                            $fullName = $stu ? trim(($stu->last_name ? $stu->last_name . ', ' : '') . $stu->first_name . ($stu->middle_name ? ' ' . $stu->middle_name : '') . ($stu->extension_name ? ' ' . $stu->extension_name : '')) : 'N/A';
                        @endphp
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 0.75rem;">{{ $idx + 1 }}</td>
                            <td style="padding: 0.75rem; font-weight: 700; color: #059669;">{{ $stu ? $stu->student_number : 'N/A' }}</td>
                            <td style="padding: 0.75rem; font-weight: 700; color: #0f172a;">{{ $fullName }}</td>
                            <td style="padding: 0.75rem;">
                                {{ $enr->classSection ? $enr->classSection->section_name : 'N/A' }}
                                <span style="background: #e0e7ff; color: #3730a3; padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.725rem; font-weight: 700; margin-left: 0.25rem;">
                                    {{ $enr->classSection && $enr->classSection->gradeLevel ? $enr->classSection->gradeLevel->name : '' }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem;">
                                @if ($enr->status == 'active' || $enr->status == 'enrolled')
                                    <span style="background: #d1fae5; color: #065f46; padding: 0.2rem 0.6rem; border-radius: 9999px; font-weight: 700; font-size: 0.75rem;">Enrolled</span>
                                @else
                                    <span style="background: #fee2e2; color: #991b1b; padding: 0.2rem 0.6rem; border-radius: 9999px; font-weight: 700; font-size: 0.75rem;">{{ ucfirst($enr->status) }}</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                <form action="{{ route('elementary.enrollment.destroy', $enr->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Remove enrollment record for {{ addslashes($fullName) }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: #fee2e2; color: #dc2626; border: none; padding: 0.35rem 0.65rem; border-radius: 6px; font-weight: 700; font-size: 0.775rem; cursor: pointer;">
                                        <i class="fa-solid fa-trash"></i> Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #94a3b8;">
                                <i class="fa-solid fa-user-xmark" style="font-size: 2.5rem; margin-bottom: 0.75rem;"></i>
                                <p style="font-weight: 700; margin: 0;">No active enrollment records for your advisory classes.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Enroll Student Modal -->
    <div id="enrollModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 200; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 1rem; width: 100%; max-width: 480px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0;">Enroll Student to Section</h3>
                <button type="button" onclick="$('#enrollModal').css('display', 'none')" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: #64748b;">&times;</button>
            </div>
            <form action="{{ route('elementary.enrollment.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">Select Student</label>
                    <select name="student_id" required style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                        <option value="">-- Choose Student --</option>
                        @foreach ($studentsList as $s)
                            <option value="{{ $s->id }}">{{ $s->last_name }}, {{ $s->first_name }} {{ $s->middle_name }} ({{ $s->student_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">Select Advisory Section</label>
                    <select name="class_section_id" required style="width: 100%; padding: 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem;">
                        <option value="">-- Choose Section --</option>
                        @foreach ($advisorySections as $sec)
                            <option value="{{ $sec->id }}">{{ $sec->section_name }} ({{ $sec->gradeLevel ? $sec->gradeLevel->name : '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="$('#enrollModal').css('display', 'none')" style="background: #f1f5f9; color: #475569; border: none; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Cancel</button>
                    <button type="submit" style="background: #059669; color: #ffffff; border: none; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Enroll Student</button>
                </div>
            </form>
        </div>
    </div>
@endsection
