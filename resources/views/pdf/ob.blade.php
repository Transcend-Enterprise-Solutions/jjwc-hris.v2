<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Official Business Form</title>
    <meta name="author" content="Art Audea" />
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            text-indent: 0;
        }

        body {
            font-family: Arial, sans-serif;
        }

        h3 {
            color: black;
            font-family: Calibri, sans-serif;
            font-style: italic;
            font-weight: bold;
            text-decoration: none;
            font-size: 6.5pt;
        }

        h2 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: italic;
            font-weight: bold;
            text-decoration: none;
            font-size: 7.5pt;
        }

        p {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 10px;
            margin: 0pt;
        }

        table,
        tbody {
            vertical-align: top;
            overflow: visible;
            font-size: 11px;
        }

        .td {
            padding: 3px;
        }

        .border-y {
            border-top: 1px solid black;
            border-bottom: 1px solid black;
        }

        .border-b {
            border-bottom: 1px solid black;
        }

        .border-t {
            border-top: 1px solid black;
        }

        .border-r {
            border-right: 1px solid black;
        }

        @page {
            margin-top: 10mm !important;
        }
    </style>
</head>
<body>

    <div style="padding: 0 3rem 3rem 3rem">

        {{-- <div style="width: 100%; text-align: right;">
            <p style="display: inline-block; text-align: left;">
                Doc.No.: HRM-DI-FORM-35<br>
                Rev.No: 00<br>
                Effectivity: February 20, 2025
            </p>
        </div> --}}

        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td width="20">
                        <img src="images/logo.png" alt="" width="70" style="margin-left: 20px">
                    </td>
                    <td width="60" style="text-align: center;">
                        <p style="text-align: center; font-size: 14px; font-weight: bold; margin-top: 10px;">
                            JUVENILE JUSTICE AND WELFARE COUNCIL <br>
                            OFFICIAL BUSINESS (OB) SLIP
                        </p>
                    </td>
                    <td width="20">
                        <img src="images/bagong-pilipinas-logo.png" alt="" width="80" style="float: right; margin-right: 20px">
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="border: 2px solid black; width: 100%; margin-top: 5px;">
            <p style="font-weight: bold; font-size: 11px; padding: 3px;">
                Note: This form must be forwarded to the HR Unit before going on Official Business or immediately upon reporting <br>
                back from Official Business.
            </p>

            <table style="border-collapse: collapse; width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 20%;" class="td border-y border-r">Date filed: </td>
                        <td style="width: 80%;" class="td border-y">{{ \Carbon\Carbon::parse($ob->created_at)->format('m/d/Y') }}</td>
                    </tr>
                </tbody>
            </table>

            <table style="border-collapse: collapse; width: 100%; margin-top: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 20%;" class="td border-y border-r">Name of Employee: </td>
                        <td style="width: 80%;" class="td border-y">{{ $userName }}</td>
                    </tr>
                    <tr>
                        <td style="width: 20%;" class="td border-y border-r">Workgroup / Unit: </td> 
                        <td style="width: 80%;" class="td border-y">{{ $workGroup->office_division }} {{ $unit ? ' - ' . $unit->unit : '' }}</td>
                    </tr>
                </tbody>
            </table>

            <p style="font-size: 11px; padding: 5px 3px; text-align: center; border-top: 1px solid black; margin-top: 7px;">
                TO THE HR FOCAL, the above-named employee is being sent out on official business:
            </p>

            <table style="border-collapse: collapse; width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 20%;" class="td border-y border-r">Date of OB: </td>
                        <td style="width: 80%;" class="td border-y">
                            @if($ob->dates->count() > 0)
                                @foreach($ob->dates as $index => $dateRecord)
                                    {{ \Carbon\Carbon::parse($dateRecord->date)->format('m/d/Y') }}@if(!$loop->last), @endif
                                @endforeach
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;" class="td border-y border-r">Destination: </td>
                        <td style="width: 80%;" class="td border-y">{{ $ob->company }} - {{ $ob->address }}</td>
                    </tr>
                    <tr>
                        <td style="width: 20%;" class="td border-y border-r">Purpose: </td>
                        <td style="width: 80%;" class="td border-y">{{ $ob->purpose }}</td>
                    </tr>
                    <tr>
                        <td style="width: 20%;" class="td border-y border-r">Duration: </td>
                        <td style="width: 80%;" class="td"></td>
                    </tr>
                    <tr>
                        <td style="width: 20%; font-style: italic;" class="td border-t border-r">
                            (Check appropriate <br>
                            box)
                        </td>
                        <td style="width: 80%;" class="td">
                            <p style="font-family: 'DejaVu Sans', sans-serif; text-align: center; font-size: 11px; margin-top: -8px;">
                                {{ $ob->duration == 'whole_day' ? '☑' : '☐' }} Whole day &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                {{ $ob->duration == 'half_day' ? '☑' : '☐' }} Half day / less than 8 hours &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                {{ $ob->duration == 'am' ? '☑' : '☐' }} AM &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                {{ $ob->duration == 'pm' ? '☑' : '☐' }} PM
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table style="border-collapse: collapse; width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 20%; font-size: 10px;" class="td border-t border-r">
                            (Applicable only for half day / <br>
                            less than 8 hours)
                        </td>
                        <td style="width: 20%; vertical-align: middle;" class="td border-t border-r">Time of Departure: </td>
                        <td style="width: 20%; vertical-align: middle;" class="td border-t border-r">
                            {{ $ob->time_start ? \Carbon\Carbon::parse($ob->time_start)->format('h:i A') : 'N/A' }}
                        </td>
                        <td style="width: 20%; vertical-align: middle;" class="td border-t border-r">Estimated Time of <br>Return: </td>
                        <td style="width: 20%; vertical-align: middle;" class="td border-t">
                            {{ $ob->time_end ? \Carbon\Carbon::parse($ob->time_end)->format('h:i A') : 'N/A' }}
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

        <div style="border: 2px solid black; width: 100%; margin-top: 5px; margin-bottom: 20px;">
            <table style="border-collapse: collapse; width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 40%;" class="td border-b border-r">Requested by: </td>
                        <td style="width: 60%;" class="td border-b">Approved by: </td>
                    </tr>
                    <tr>
                        <td style="width: 40%; height: 60px; text-align: center; vertical-align: bottom; font-size: 14px" class="td border-y border-r">{{ $userName }}</td>
                        <td style="width: 60%; height: 60px;" class="td border-y"></td>
                    </tr>
                    <tr>
                        <td style="width: 40%; text-align: center;" class="td border-t border-r">NAME OF EMPLOYEE / SIGNATURE</td>
                        <td style="width: 60%; text-align: center;" class="td border-t">NAME OF IMMEDIATE SUPERVISOR / SIGNATURE</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Second Page ---------------------------------------------------------------------- --}}

        {{-- <div style="width: 100%; text-align: right; margin-top: 20px;">
            <p style="display: inline-block; text-align: left;">
                Doc.No.: HRM-DI-FORM-35<br>
                Rev.No: 00<br>
                Effectivity: February 20, 2025
            </p>
        </div> --}}

        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td width="20">
                        <img src="images/logo.png" alt="" width="70" style="margin-left: 20px">
                    </td>
                    <td width="60" style="text-align: center;">
                        <p style="text-align: center; font-size: 14px; font-weight: bold; margin-top: 10px;">
                            JUVENILE JUSTICE AND WELFARE COUNCIL <br>
                            OFFICIAL BUSINESS (OB) SLIP
                        </p>
                    </td>
                    <td width="20">
                        <img src="images/bagong-pilipinas-logo.png" alt="" width="80" style="float: right; margin-right: 20px">
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="border: 2px solid black; width: 100%; margin-top: 5px;">
            <p style="font-weight: bold; font-size: 11px; padding: 3px;">
                Note: This form must be forwarded to the HR Unit before going on Official Business or immediately upon reporting <br>
                back from Official Business.
            </p>

            <table style="border-collapse: collapse; width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 20%;" class="td border-y border-r">Date filed: </td>
                        <td style="width: 80%;" class="td border-y">{{ \Carbon\Carbon::parse($ob->created_at)->format('m/d/Y') }}</td>
                    </tr>
                </tbody>
            </table>

            <table style="border-collapse: collapse; width: 100%; margin-top: 4px;">
                <tbody>
                    <tr>
                        <td style="width: 20%;" class="td border-y border-r">Name of Employee: </td>
                        <td style="width: 80%;" class="td border-y">{{ $userName }}</td>
                    </tr>
                    <tr>
                        <td style="width: 20%;" class="td border-y border-r">Workgroup / Unit: </td> 
                        <td style="width: 80%;" class="td border-y">{{ $workGroup->office_division }} {{ $unit ? ' - ' . $unit->unit : '' }}</td>
                    </tr>
                </tbody>
            </table>

            <p style="font-size: 11px; padding: 5px 3px; text-align: center; border-top: 1px solid black; margin-top: 7px;">
                TO THE HR FOCAL, the above-named employee is being sent out on official business:
            </p>

            <table style="border-collapse: collapse; width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 20%;" class="td border-y border-r">Date of OB: </td>
                        <td style="width: 80%;" class="td border-y">
                            @if($ob->dates->count() > 0)
                                @foreach($ob->dates as $index => $dateRecord)
                                    {{ \Carbon\Carbon::parse($dateRecord->date)->format('m/d/Y') }}@if(!$loop->last), @endif
                                @endforeach
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;" class="td border-y border-r">Destination: </td>
                        <td style="width: 80%;" class="td border-y">{{ $ob->company }} - {{ $ob->address }}</td>
                    </tr>
                    <tr>
                        <td style="width: 20%;" class="td border-y border-r">Purpose: </td>
                        <td style="width: 80%;" class="td border-y">{{ $ob->purpose }}</td>
                    </tr>
                    <tr>
                        <td style="width: 20%;" class="td border-y border-r">Duration: </td>
                        <td style="width: 80%;" class="td"></td>
                    </tr>
                    <tr>
                        <td style="width: 20%; font-style: italic;" class="td border-t border-r">
                            (Check appropriate <br>
                            box)
                        </td>
                        <td style="width: 80%;" class="td">
                            <p style="font-family: 'DejaVu Sans', sans-serif; text-align: center; font-size: 11px; margin-top: -8px;">
                                {{ $ob->duration == 'whole_day' ? '☑' : '☐' }} Whole day &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                {{ $ob->duration == 'half_day' ? '☑' : '☐' }} Half day / less than 8 hours &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                {{ $ob->duration == 'am' ? '☑' : '☐' }} AM &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                {{ $ob->duration == 'pm' ? '☑' : '☐' }} PM
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table style="border-collapse: collapse; width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 20%; font-size: 10px;" class="td border-t border-r">
                            (Applicable only for half day / <br>
                            less than 8 hours)
                        </td>
                        <td style="width: 20%; vertical-align: middle;" class="td border-t border-r">Time of Departure: </td>
                        <td style="width: 20%; vertical-align: middle;" class="td border-t border-r">
                            {{ $ob->time_start ? \Carbon\Carbon::parse($ob->time_start)->format('h:i A') : 'N/A' }}
                        </td>
                        <td style="width: 20%; vertical-align: middle;" class="td border-t border-r">Estimated Time of <br>Return: </td>
                        <td style="width: 20%; vertical-align: middle;" class="td border-t">
                            {{ $ob->time_end ? \Carbon\Carbon::parse($ob->time_end)->format('h:i A') : 'N/A' }}
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

        <div style="border: 2px solid black; width: 100%; margin-top: 5px;">
            <table style="border-collapse: collapse; width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 40%;" class="td border-b border-r">Requested by: </td>
                        <td style="width: 60%;" class="td border-b">Approved by: </td>
                    </tr>
                    <tr>
                        <td style="width: 40%; height: 60px; text-align: center; vertical-align: bottom; font-size: 14px" class="td border-y border-r">{{ $userName }}</td>
                        <td style="width: 60%; height: 60px;" class="td border-y"></td>
                    </tr>
                    <tr>
                        <td style="width: 40%; text-align: center;" class="td border-t border-r">NAME OF EMPLOYEE / SIGNATURE</td>
                        <td style="width: 60%; text-align: center;" class="td border-t">NAME OF IMMEDIATE SUPERVISOR / SIGNATURE</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>
