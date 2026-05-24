<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTR Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 15px;
            position: relative;
            font-size: 11px;
        }
        .form-number {
            position: absolute;
            top: 5px;
            left: 5px;
            font-size: 10px;
            color: #333;
        }
        .header-section {
            text-align: center;
            margin-bottom: 15px;
        }
        .logo-container {
            margin-bottom: 5px;
            text-align: center;
        }
        .logo {
            width: 1.0in;
            height: 0.8in;
            margin-bottom: 2px;
        }
        .org-title {
            font-size: 14px;
            font-weight: bold;
            margin: 2px 0;
        }
        .org-subtitle {
            font-size: 13px;
            margin: 2px 0;
        }
        .dtr-title {
            font-size: 14px;
            font-weight: bold;
            margin: 5px 0;
        }
        .employee-info-container {
            margin-top: 15px;
            margin-bottom: 10px;
        }
        .employee-details {
            text-align: left;
            font-size: 13px;
        }
        .employee-name-label {
            font-weight: bold;
            display: inline-block;
            margin-right: 5px;
        }
        .employee-name {
            display: inline-block;
        }
        .month-header {
            text-align: center;
            font-weight: bold;
            margin: 10px 0;
            font-size: 13px;
            border: 1px solid #000;
            padding: 3px;
            background-color: #f2f2f2;
        }
        table.dtr-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table.dtr-table th,
        table.dtr-table td {
            border: 1px solid black;
            padding: 4px;
            text-align: center;
            font-size: 11px;
        }
        table.dtr-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .weekend {
            background-color: #f8f8f8;
        }
        .total-summary {
            text-align: left;
            margin-left: 3px;
            margin-top: 8px;
            margin-bottom: 3px;
            font-weight: bold;
            font-size: 12px;
        }
        .certification {
            margin-top: 15px;
            font-size: 11px;
            text-align: center;
            font-style: italic;
        }
        .signature-container {
            margin-top: 30px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0;
        }
        .signature-line {
            border-top: 1px solid black;
            width: 80%;
            margin: 0 auto 5px;
        }
        .signature-name {
            font-weight: bold;
        }
        .signature-title {
            font-size: 11px;
        }
        .timestamp {
            position: absolute;
            bottom: 5px;
            left: 5px;
            font-size: 10px;
            color: #666;
        }
        .month-separator {
            page-break-after: always;
        }
        .month-container:last-child .month-separator {
            page-break-after: auto;
        }
    </style>
</head>
<body>
    @php
        // Group DTRs by employee and month
        $groupedData = [];
        foreach($dtrsWithSummary as $employeeName => $employeeData) {
            $monthlyGroups = [];
            foreach($employeeData['dtrs'] as $dtr) {
                $monthYear = Carbon\Carbon::parse($dtr->date)->format('Y-m');
                if (!isset($monthlyGroups[$monthYear])) {
                    $monthlyGroups[$monthYear] = [
                        'dtrs' => [],
                        'summary' => [
                            'days_worked' => 0,
                            'absences' => 0,
                            'overtime' => '00:00',
                            'late' => '00:00',
                            'undertime' => '00:00',
                            'tardiness' => '00:00',
                            'leave_days' => 0,
                            'holidays' => 0
                        ],
                        'signatory' => $employeeData['signatory']
                    ];
                }
                $monthlyGroups[$monthYear]['dtrs'][] = $dtr;
            }

            // Calculate summaries for each month
            foreach($monthlyGroups as $month => &$monthData) {
                $totalOvertimeMinutes = 0;
                $totalLateMinutes = 0;
                $totalUndertimeMinutes = 0;

                foreach($monthData['dtrs'] as $dtr) {
                    // Days worked
                    $hasTimeEntries = $dtr->effective_time_in || $dtr->effective_time_out ||
                                    $dtr->effective_break_in || $dtr->effective_break_out;

                    if (strtolower($dtr->effective_remarks) === 'absent') {
                        $monthData['summary']['absences']++;
                    } elseif (str_contains(strtolower($dtr->effective_remarks), 'leave')) {
                        $monthData['summary']['leave_days']++;
                    } elseif (str_contains(strtolower($dtr->effective_remarks), 'holiday')) {
                        $monthData['summary']['holidays']++;
                    } elseif ($hasTimeEntries) {
                        $monthData['summary']['days_worked']++;
                    }

                    // Overtime calculation
                    if (!empty($dtr->effective_overtime) && $dtr->effective_overtime !== '00:00') {
                        list($hours, $minutes) = explode(':', $dtr->effective_overtime);
                        $totalOvertimeMinutes += (intval($hours) * 60) + intval($minutes);
                    }

                    // Late calculation
                    if ($hasTimeEntries && !empty($dtr->effective_late) && $dtr->effective_late !== '00:00') {
                        list($hours, $minutes) = explode(':', $dtr->effective_late);
                        $totalLateMinutes += (intval($hours) * 60) + intval($minutes);
                    }

                    // Undertime calculation
                    if ($hasTimeEntries && !empty($dtr->effective_ut) && $dtr->effective_ut !== '00:00') {
                        list($hours, $minutes) = explode(':', $dtr->effective_ut);
                        $totalUndertimeMinutes += (intval($hours) * 60) + intval($minutes);
                    }
                }

                $monthData['summary']['overtime'] = sprintf("%02d:%02d", floor($totalOvertimeMinutes / 60), $totalOvertimeMinutes % 60);
                $monthData['summary']['late'] = sprintf("%02d:%02d", floor($totalLateMinutes / 60), $totalLateMinutes % 60);
                $monthData['summary']['undertime'] = sprintf("%02d:%02d", floor($totalUndertimeMinutes / 60), $totalUndertimeMinutes % 60);
                $totalTardinessMinutes = $totalLateMinutes + $totalUndertimeMinutes;
                $monthData['summary']['tardiness'] = sprintf("%02d:%02d", floor($totalTardinessMinutes / 60), $totalTardinessMinutes % 60);
            }

            $groupedData[$employeeName] = $monthlyGroups;
        }
    @endphp

    @foreach($groupedData as $employeeName => $monthlyData)
        @foreach($monthlyData as $month => $data)
            <div class="month-container">
                <div class="form-number">Civil Service Form No.48</div>

                <div class="header-section">
                    <div class="logo-container" style="display: flex; align-items: center;">
                        <img src="{{ public_path('images/logo.png') }}" alt="JJWC Logo" class="logo" style="margin-right: 20px;">
                        <img src="{{ public_path('images/bagong-pilipinas-logo.png') }}" alt="Bagong Pilipinas Logo" class="logo" style="position: relative; top: -6px;">
                    </div>

                    <div class="org-title">Republic of the Philippines</div>
                    <div class="org-subtitle">JUVENILE JUSTICE AND WELFARE COUNCIL</div>
                    <div class="dtr-title">DAILY TIME RECORD</div>
                </div>

                <div class="employee-info-container">
                    <div class="employee-details">
                        <div>
                            <span class="employee-name-label">Name:</span>
                            <span class="employee-name">{{ $employeeName }}</span>
                        </div>
                        <div>
                            <span class="employee-name-label">Position:</span>
                            <span class="employee-name">{{ $data['dtrs'][0]->user_position ?? '' }}</span>
                        </div>
                        <div>
                            <span class="employee-name-label">Department:</span>
                            <span class="employee-name">{{ $data['dtrs'][0]->user_department ?? '' }}</span>
                        </div>
                    </div>
                </div>

                <div class="month-header">
                    FOR THE MONTH OF {{ strtoupper(Carbon\Carbon::parse($month . '-01')->format('F Y')) }}
                </div>

                <!-- DTR Table -->
                <table class="dtr-table">
                    <thead>
                        <tr>
                            <th rowspan="2">Day</th>
                            <th colspan="2">A.M.</th>
                            <th colspan="2">P.M.</th>
                            <th rowspan="2">Late</th>
                            <th rowspan="2">UT</th>
                            <th rowspan="2">OT</th>
                            <th rowspan="2" style="width: 120px;">REMARKS</th>
                        </tr>
                        <tr>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['dtrs'] as $dtr)
                            @php
                                $hasTimeEntries = $dtr->effective_time_in || $dtr->effective_time_out ||
                                                 $dtr->effective_break_in || $dtr->effective_break_out;
                                $dayOfWeek = $dtr->date ? Carbon\Carbon::parse($dtr->date)->format('D') : '';
                                $dayNum = $dtr->date ? Carbon\Carbon::parse($dtr->date)->format('j') : '';
                                $isWeekend = in_array($dayOfWeek, ['Sat', 'Sun']);
                            @endphp
                            <tr class="{{ $isWeekend ? 'weekend' : '' }}">
                                <td>{{ $dayNum }} {{ $dayOfWeek }}</td>
                                <td>{{ $dtr->effective_time_in && $dtr->effective_time_in != '00:00' ? $dtr->effective_time_in : '--:--' }}</td>
                                <td>{{ $dtr->effective_break_out && $dtr->effective_break_out != '00:00' ? $dtr->effective_break_out : '--:--' }}</td>
                                <td>{{ $dtr->effective_break_in && $dtr->effective_break_in != '00:00' ? $dtr->effective_break_in : '--:--' }}</td>
                                <td>{{ $dtr->effective_time_out && $dtr->effective_time_out != '00:00' ? $dtr->effective_time_out : '--:--' }}</td>
                                <td>{{ $hasTimeEntries && $dtr->effective_late ? $dtr->effective_late : '--:--' }}</td>
                                <td>{{ $hasTimeEntries && $dtr->effective_ut ? $dtr->effective_ut : '--:--' }}</td>
                                <td>{{ $dtr->effective_overtime && $dtr->effective_overtime != '00:00' ? $dtr->effective_overtime : '--:--' }}</td>
                                <td>{{ $dtr->effective_remarks !== 'Present' ? $dtr->effective_remarks : '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="total-summary">TOTAL SUMMARY</div>

                <table style="width: 100%; border-collapse: collapse; margin-top: 3px; font-size: 11px;">
                    <tr>
                        <td style="border: none; padding: 1px 3px; text-align: left;">
                            <strong>Days Worked : </strong> {{ $data['summary']['days_worked'] }}
                        </td>
                        <td style="border: none; padding: 1px 3px; text-align: left;">
                            <strong>Late : </strong> {{ $data['summary']['late'] }}
                        </td>
                        <td style="border: none; padding: 1px 3px; text-align: left;">
                            <strong>Leave : </strong> {{ $data['summary']['leave_days'] }}
                        </td>
                        <td style="border: none; padding: 1px 3px; text-align: left;">
                            <strong>OT : </strong> {{ $data['summary']['overtime'] }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 1px 3px; text-align: left;">
                            <strong>Absences : </strong> {{ $data['summary']['absences'] }}
                        </td>
                        <td style="border: none; padding: 1px 3px; text-align: left;">
                            <strong>UT : </strong> {{ $data['summary']['undertime'] }}
                        </td>
                        <td style="border: none; padding: 1px 3px; text-align: left;">
                            <strong>Holiday : </strong> {{ $data['summary']['holidays'] }}
                        </td>
                        <td style="border: none; padding: 1px 3px; text-align: left;">
                            <strong>Total Tardiness : </strong> {{ $data['summary']['tardiness'] }}
                        </td>
                    </tr>
                </table>

                <div class="certification">
                    I CERTIFY on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.
                </div>

                <div class="signature-container">
                    <table class="signature-table">
                        <tr>
                            <td>
                                @if($eSignaturePath)
                                    <img src="{{ storage_path('app/public/' . $eSignaturePath) }}"
                                         style="width: 80px; height: auto; margin-bottom: 5px; display: block; margin-left: auto; margin-right: auto;">
                                @endif
                                <div class="signature-line"></div>
                                <div class="signature-name">{{ $employeeName }}</div>
                                <div class="signature-title">Employee's Signature</div>
                            </td>
                            <td>
                                <div class="signature-line"></div>
                                <div class="signature-name">{{ $data['signatory']['name'] ?? '' }}</div>
                                <div class="signature-title">{{ $data['signatory']['position'] ?? '' }}</div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="timestamp">Generated on: {{ now()->format('F d, Y H:i:s') }}</div>

                @if(!$loop->last)
                    <div class="month-separator"></div>
                @endif
            </div>
        @endforeach

        @if(!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach
</body>
</html>