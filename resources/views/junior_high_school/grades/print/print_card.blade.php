<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card (SF9 / Form 138) - {{ $student->first_name }} {{ $student->last_name }}</title>
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
            background: #1e1b4b;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .btn-print {
            background: #f59e0b;
            color: #1e1b4b;
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
            font-size: 9.5px;
            table-layout: fixed;
        }

        table.deped-table th, 
        table.deped-table td {
            border: 1px solid #000000;
            padding: 3px 4px;
            vertical-align: middle;
        }

        table.deped-table th {
            text-align: center;
            font-weight: bold;
            background: #ffffff;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-bold {
            font-weight: bold;
        }

        .indent-sub {
            padding-left: 14px !important;
        }

        /* Bottom Grading Scale & Marking Tables */
        .legends-wrapper {
            display: flex;
            gap: 12px;
            margin-top: 10px;
            font-size: 9px;
        }

        .legend-box {
            flex: 1;
        }

        .legend-title-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .legend-row-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            margin-bottom: 1px;
        }

        .marking-title-grid {
            display: grid;
            grid-template-columns: 75px 1fr;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .marking-row-grid {
            display: grid;
            grid-template-columns: 75px 1fr;
            margin-bottom: 1px;
        }

        @media print {
            html, body {
                width: 100% !important;
                height: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
            }
            .no-print-bar {
                display: none !important;
            }
            .card-container {
                border: none !important;
                padding: 0 !important;
                margin: 0 auto !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar (Hidden when printing) -->
    <div class="no-print-bar">
        <div style="font-weight: 700; font-size: 1rem;">
            <i class="fa-solid fa-id-card"></i> DepEd Form 138 (Report Card) Preview
        </div>
        <div>
            <button type="button" class="btn-print" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Print Report Card
            </button>
        </div>
    </div>

    <div class="card-container">
        <!-- Student Header Info -->
        <div class="student-info-header">
            <div class="info-grid">
                <div>Name: <span>{{ strtoupper($student->last_name . ', ' . $student->first_name . ' ' . ($student->middle_name ? substr($student->middle_name, 0, 1) . '.' : '')) }}</span></div>
                <div>LRN: <span>{{ $student->lrn ?? ($student->student_number ?? 'N/A') }}</span></div>
                <div>Grade & Section: <span>{{ $currentSection ? $currentSection->section_name : 'N/A' }} ({{ $currentSection && $currentSection->gradeLevel ? $currentSection->gradeLevel->name : '' }})</span></div>
                <div>Class Adviser: <span>{{ $currentSection && $currentSection->adviser ? $currentSection->adviser->first_name . ' ' . $currentSection->adviser->last_name : 'N/A' }}</span></div>
                <div>School Year: <span>{{ $activeSchoolYear ? ($activeSchoolYear->school_year ?? $activeSchoolYear->name) : 'N/A' }}</span></div>
                <div>Gender: <span>{{ ucfirst($student->gender ?? 'N/A') }}</span></div>
            </div>
        </div>

        <div class="tables-wrapper">
            <!-- Left Side: REPORT ON LEARNING PROGRESS AND ACHIEVEMENT -->
            <div class="column-half">
                <div class="table-title">REPORT ON LEARNING PROGRESS AND ACHIEVEMENT</div>
                <table class="deped-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 48%; text-align: center;">Learning Areas</th>
                            <th colspan="4" style="width: 28%;">Quarter</th>
                            <th rowspan="2" style="width: 12%;">Final<br>Grade</th>
                            <th rowspan="2" style="width: 12%;">Remarks</th>
                        </tr>
                        <tr>
                            <th style="width: 7%;">1</th>
                            <th style="width: 7%;">2</th>
                            <th style="width: 7%;">3</th>
                            <th style="width: 7%;">4</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Standard DepEd JHS subjects structure
                            $standardSubjects = [
                                'Filipino',
                                'English',
                                'Mathematics',
                                'Science',
                                'Araling Panlipunan (AP)',
                                'Edukasyon sa Pagpapakatao (EsP)',
                                'Technology and Livelihood Economics (TLE)',
                                'MAPEH',
                                'Music',
                                'Arts',
                                'Physical Education (PE)',
                                'Health'
                            ];

                            // Map assigned section subjects by code/name
                            $mappedSubjects = [];
                            foreach($sectionSubjects as $subItem) {
                                $sName = $subItem->subject ? ($subItem->subject->subject_name ?? $subItem->subject->subject_code) : '';
                                $mappedSubjects[$subItem->id] = $sName;
                            }
                        @endphp

                        @if ($sectionSubjects->isNotEmpty())
                            @foreach ($sectionSubjects as $secSub)
                                @php
                                    $subData = $gradesBySubject[$secSub->id] ?? ['q1'=>null, 'q2'=>null, 'q3'=>null, 'q4'=>null, 'final_grade'=>null, 'remarks'=>''];
                                    $sName = $secSub->subject ? ($secSub->subject->subject_name ?? $secSub->subject->subject_code) : 'Subject';
                                    $isSubMAPEH = in_array(strtolower($sName), ['music', 'arts', 'physical education', 'pe', 'health', 'physical education (pe)']);
                                @endphp
                                <tr>
                                    <td class="{{ $isSubMAPEH ? 'indent-sub' : '' }}">{{ $sName }}</td>
                                    <td class="text-center">{{ $subData['q1'] !== null ? number_format($subData['q1'], 0) : '' }}</td>
                                    <td class="text-center">{{ $subData['q2'] !== null ? number_format($subData['q2'], 0) : '' }}</td>
                                    <td class="text-center">{{ $subData['q3'] !== null ? number_format($subData['q3'], 0) : '' }}</td>
                                    <td class="text-center">{{ $subData['q4'] !== null ? number_format($subData['q4'], 0) : '' }}</td>
                                    <td class="text-center text-bold">{{ $subData['final_grade'] !== null ? number_format($subData['final_grade'], 0) : '' }}</td>
                                    <td class="text-center" style="font-size: 9px;">{{ $subData['remarks'] }}</td>
                                </tr>
                            @endforeach
                        @else
                            <!-- Fallback standard template rows if no custom subjects set -->
                            @foreach ($standardSubjects as $subName)
                                @php
                                    $isSubMAPEH = in_array($subName, ['Music', 'Arts', 'Physical Education (PE)', 'Health']);
                                @endphp
                                <tr>
                                    <td class="{{ $isSubMAPEH ? 'indent-sub' : '' }}">{{ $subName }}</td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center text-bold"></td>
                                    <td class="text-center"></td>
                                </tr>
                            @endforeach
                        @endif

                        <!-- General Average Row -->
                        <tr>
                            <td colspan="5" class="text-bold text-center" style="padding: 6px;">General Average</td>
                            <td class="text-center text-bold" style="font-size: 11px;">
                                {{ $gwa !== null ? number_format($gwa, 0) : '' }}
                            </td>
                            <td class="text-center text-bold" style="font-size: 9px;">
                                {{ $remarks != 'Pending' ? $remarks : '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Bottom Legend: Descriptors & Grading Scale -->
                <div style="margin-top: 15px; font-size: 9.5px;">
                    <div class="legend-title-grid">
                        <div>Descriptors</div>
                        <div class="text-center">Grading Scale</div>
                        <div class="text-center">Remarks</div>
                    </div>
                    <div class="legend-row-grid">
                        <div>Outstanding</div>
                        <div class="text-center">90-100</div>
                        <div class="text-center">Passed</div>
                    </div>
                    <div class="legend-row-grid">
                        <div>Very Satisfactory</div>
                        <div class="text-center">85-89</div>
                        <div class="text-center">Passed</div>
                    </div>
                    <div class="legend-row-grid">
                        <div>Satisfactory</div>
                        <div class="text-center">80-84</div>
                        <div class="text-center">Passed</div>
                    </div>
                    <div class="legend-row-grid">
                        <div>Fairly Satisfactory</div>
                        <div class="text-center">75-79</div>
                        <div class="text-center">Passed</div>
                    </div>
                    <div class="legend-row-grid">
                        <div>Did Not Meet Expectations</div>
                        <div class="text-center">Below 75</div>
                        <div class="text-center">Failed</div>
                    </div>
                </div>
            </div>

            <!-- Right Side: REPORT ON LEARNER'S OBSERVED VALUES -->
            <div class="column-half">
                <div class="table-title">REPORT ON LEARNER'S OBSERVED VALUES</div>
                <table class="deped-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 22%;">Core Values</th>
                            <th rowspan="2" style="width: 54%;">Behavior Statements</th>
                            <th colspan="4" style="width: 24%;">Quarter</th>
                        </tr>
                        <tr>
                            <th style="width: 6%;">1</th>
                            <th style="width: 6%;">2</th>
                            <th style="width: 6%;">3</th>
                            <th style="width: 6%;">4</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 1. Maka-Diyos -->
                        <tr>
                            <td rowspan="2" class="text-bold" style="vertical-align: top;">1 Maka-Diyos</td>
                            <td>Expresses one's spiritual beliefs while respecting the spiritual beliefs of others.</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                        </tr>
                        <tr>
                            <td>Shows adherence to ethical principles by upholding truth</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                        </tr>

                        <!-- 2. Makatao -->
                        <tr>
                            <td rowspan="2" class="text-bold" style="vertical-align: top;">2 Makatao</td>
                            <td>Is sensitive to individual, social, and cultural differences</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                        </tr>
                        <tr>
                            <td>Demonstrates contributions toward solidarity</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                        </tr>

                        <!-- 3. Makakalikasan -->
                        <tr>
                            <td class="text-bold" style="vertical-align: top;">3 Makakalikasan</td>
                            <td>Cares for the environment and utilizes resources wisely, judiciously, and economically</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                        </tr>

                        <!-- 4. Makabansa -->
                        <tr>
                            <td rowspan="2" class="text-bold" style="vertical-align: top;">4 Makabansa</td>
                            <td>Demonstrates pride in being a Filipino; exercises the rights and responsibilities of a Filipino citizen</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                        </tr>
                        <tr>
                            <td>Demonstrates appropriate behavior in carrying out activities in the school, community, and country</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                        </tr>
                    </tbody>
                </table>

                <!-- Bottom Legend: Markings & Non-numerical Rating -->
                <div style="margin-top: 15px; font-size: 9.5px;">
                    <div class="marking-title-grid">
                        <div>Marking</div>
                        <div>Non-numerical Rating</div>
                    </div>
                    <div class="marking-row-grid">
                        <div class="text-bold">AO</div>
                        <div>Always Observed</div>
                    </div>
                    <div class="marking-row-grid">
                        <div class="text-bold">SO</div>
                        <div>Sometimes Observed</div>
                    </div>
                    <div class="marking-row-grid">
                        <div class="text-bold">RO</div>
                        <div>Rarely Observed</div>
                    </div>
                    <div class="marking-row-grid">
                        <div class="text-bold">NO</div>
                        <div>Not Observed</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
