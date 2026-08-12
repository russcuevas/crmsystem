<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHS Report Card (SF9 / Form 138) - {{ $student->first_name }} {{ $student->last_name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @page {
            size: A4 landscape;
            margin: 5mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f8fafc;
            color: #000;
            font-size: 10.5px;
            line-height: 1.25;
        }

        .no-print-bar {
            background: #7f1d1d;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-print {
            background: #f59e0b;
            color: #450a0a;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-weight: 800;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .card-container {
            width: 100%;
            max-width: 950px;
            margin: 0 auto;
            background: #ffffff;
            padding: 10px;
            border: 1px solid #cbd5e1;
        }

        .student-info-header {
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px 12px;
            font-size: 10.5px;
        }

        .info-grid div span {
            font-weight: bold;
        }

        .tables-wrapper {
            display: flex;
            gap: 12px;
            width: 100%;
            align-items: flex-start;
        }

        .column-half {
            flex: 1;
            width: 50%;
        }

        .table-title {
            text-align: center;
            font-weight: bold;
            font-size: 10.5px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        table.deped-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 8px;
        }

        table.deped-table th,
        table.deped-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
        }

        table.deped-table th {
            background-color: #f1f5f9;
            font-weight: bold;
        }

        table.deped-table td.subject-name {
            text-align: left;
            font-weight: 600;
        }

        .remarks-passed {
            color: #065f46;
            font-weight: bold;
        }

        .remarks-failed {
            color: #991b1b;
            font-weight: bold;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }

            body {
                background: #ffffff !important;
            }

            .card-container {
                border: none !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>

<body>
    <div class="no-print-bar">
        <div>
            <span style="font-weight: 800; font-size: 1.1rem;">SENIOR HIGH SCHOOL REPORT CARD (SF9)</span>
            <span style="font-size: 0.85rem; margin-left: 0.75rem; color: #fecdd3;">{{ $student->first_name }} {{ $student->middle_name ? $student->middle_name . ' ' : '' }}{{ $student->last_name }}</span>
        </div>
        <button onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-print"></i> Print Report Card
        </button>
    </div>

    <div class="card-container">
        <!-- Student Information Header -->
        <div class="student-info-header">
            <div style="text-align: center; margin-bottom: 6px;">
                <h2 style="font-size: 14px; font-weight: 800; text-transform: uppercase;">Guihulngan National High
                    School</h2>
                <p style="font-size: 10px; font-weight: bold; color: #7f1d1d;">SENIOR HIGH SCHOOL REPORT CARD (SF9 /
                    FORM 138)</p>
            </div>
            <div class="info-grid">
                <div><span>Name:</span> {{ $student->last_name }}, {{ $student->first_name }}
                    {{ $student->middle_name ?? '' }}</div>
                <div><span>LRN:</span> {{ $student->lrn ?? 'N/A' }}</div>
                <div><span>Student ID:</span> {{ $student->student_number }}</div>
                <div><span>Grade Level:</span> {{ $student->gradeLevel ? $student->gradeLevel->name : 'N/A' }}</div>
                <div><span>Strand:</span>
                    {{ $student->course ? $student->course->course_code . ' (' . $student->course->course_name . ')' : 'N/A' }}
                </div>
                <div><span>Section:</span> {{ $classSection ? $classSection->section_name : 'N/A' }}</div>
                <div><span>School Year:</span>
                    {{ $enrollment->schoolYear ? $enrollment->schoolYear->school_year : 'N/A' }}</div>
                <div><span>Class Adviser:</span>
                    {{ $classSection && $classSection->adviser ? $classSection->adviser->first_name . ' ' . $classSection->adviser->last_name : 'N/A' }}
                </div>
            </div>
        </div>

        <div class="tables-wrapper">
            <!-- Learning Progress and Achievement Table -->
            <div class="column-half">
                <div class="table-title">Report on Learning Progress and Achievement</div>
                <table class="deped-table">
                    <thead>
                        <tr>
                            <th style="width: 45%;">Subjects</th>
                            <th style="width: 12%;">Prelim</th>
                            <th style="width: 12%;">Midterm</th>
                            <th style="width: 12%;">Finals</th>
                            <th style="width: 12%;">Final Rating</th>
                            <th style="width: 15%;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reportCardData as $row)
                            <tr>
                                <td class="subject-name">
                                    {{ $row['subject'] ? $row['subject']->subject_name ?? $row['subject']->name : 'N/A' }}
                                </td>
                                <td>{{ isset($row['grades']['Prelim']) && $row['grades']['Prelim'] !== null ? round($row['grades']['Prelim']) : '-' }}</td>
                                <td>{{ isset($row['grades']['Midterm']) && $row['grades']['Midterm'] !== null ? round($row['grades']['Midterm']) : '-' }}</td>
                                <td>{{ isset($row['grades']['Finals']) && $row['grades']['Finals'] !== null ? round($row['grades']['Finals']) : '-' }}</td>
                                <td style="font-weight: bold;">{{ isset($row['final_rating']) && $row['final_rating'] !== null ? round($row['final_rating']) : '-' }}</td>
                                <td>
                                    @if ($row['remarks'] === 'PASSED')
                                        <span class="remarks-passed">PASSED</span>
                                    @elseif ($row['remarks'] === 'FAILED')
                                        <span class="remarks-failed">FAILED</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr style="font-weight: bold; background-color: #f8fafc;">
                            <td class="subject-name">General Average</td>
                            <td colspan="3" style="text-align: right; padding-right: 8px;">Final Average:</td>
                            <td style="font-size: 11px; font-weight: 800; color: #7f1d1d;">
                                {{ isset($overallFinalRating) && $overallFinalRating !== null ? round($overallFinalRating) : '-' }}</td>
                            <td>
                                @if ($overallFinalRating)
                                    <span
                                        class="{{ $overallFinalRating >= 75 ? 'remarks-passed' : 'remarks-failed' }}">
                                        {{ $overallFinalRating >= 75 ? 'PASSED' : 'FAILED' }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Grading Scale & Descriptor Table -->
            <div class="column-half">
                <div class="table-title">Descriptors and Grading Scale</div>
                <table class="deped-table">
                    <thead>
                        <tr>
                            <th>Descriptor</th>
                            <th>Grading Scale</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: left;">Outstanding</td>
                            <td>90 - 100</td>
                            <td>Passed</td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Very Satisfactory</td>
                            <td>85 - 89</td>
                            <td>Passed</td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Satisfactory</td>
                            <td>80 - 84</td>
                            <td>Passed</td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Fairly Satisfactory</td>
                            <td>75 - 79</td>
                            <td>Passed</td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Did Not Meet Expectations</td>
                            <td>Below 75</td>
                            <td>Failed</td>
                        </tr>
                    </tbody>
                </table>

                <div style="margin-top: 30px; display: flex; justify-content: space-between; padding: 0 10px;">
                    <div style="text-align: center; width: 45%;">
                        <div style="border-bottom: 1px solid #000; padding-bottom: 2px; font-weight: bold;">
                            {{ $classSection && $classSection->adviser ? $classSection->adviser->first_name . ' ' . $classSection->adviser->last_name : 'Class Adviser' }}
                        </div>
                        <span style="font-size: 9.5px; color: #475569;">Class Adviser Signature</span>
                    </div>

                    <div style="text-align: center; width: 45%;">
                        <div style="border-bottom: 1px solid #000; padding-bottom: 2px; font-weight: bold;">
                            School Principal
                        </div>
                        <span style="font-size: 9.5px; color: #475569;">Principal Signature</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
