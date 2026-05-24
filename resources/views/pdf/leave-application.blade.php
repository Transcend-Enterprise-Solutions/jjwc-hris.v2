<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Leave Application</title>
    <meta name="author" content="Art Audea" />

    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            text-indent: 0;
        }

        h3 {
            color: black;
            font-family: Calibri, sans-serif;
            font-style: italic;
            font-weight: bold;
            text-decoration: none;
            font-size: 6.5pt;
        }

        .s1 {
            color: black;
            font-family: "Arial Narrow", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 6pt;
        }

        .s2 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 7.5pt;
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
            font-size: 7.5pt;
            margin: 0pt;
        }

        .s3 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: italic;
            font-weight: normal;
            text-decoration: none;
            font-size: 7.5pt;
        }

        .s4 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 8pt;
        }

        h1 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 12pt;
        }

        .s5 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 8pt;
        }

        .s7 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 9pt;
        }

        .s8 {
            color: black;
            font-family: "Arial Narrow", sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 5pt;
        }

        .s9 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: italic;
            font-weight: normal;
            text-decoration: none;
            font-size: 7.5pt;
        }

        .s10 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 7.5pt;
        }

        table,
        tbody {
            vertical-align: top;
            overflow: visible;
        }
    </style>
</head>

<body>
    <h3 style="padding-top: 3pt;padding-left: 60pt;text-indent: 0pt;line-height: 112%;text-align: left;">Civil Service
        Form No. 6</h3>
    <h3 style="padding-top: 3pt;padding-left: 60pt;text-indent: 0pt;line-height: 112%;text-align: left;">Revised 2020
    </h3>

    <p style="text-indent: 0pt;text-align: left;" />

    {{-- Header --}}
    {{-- <p class="s2" style="padding-top: 9pt; text-indent: 0pt; text-align: center;">Republic of the Philippines</p>
    <h2 style="text-indent: 0pt; line-height: 110%; text-align: center;">JUVENILE JUSTICE AND WELFARE COUNCIL</h2>
    <h2 style="text-indent: 0pt; line-height: 110%; text-align: center;"> (Agency Address)</h2> --}}

    <div style="text-align: center;">
        <div style="display: inline-block; vertical-align: middle; text-align: center;">
            <img src="images/logo.png" alt="JJWC Logo"
                style="height: 40px; display: inline-block; vertical-align: middle;">
        </div>
        <div
            style="display: inline-block; vertical-align: middle; text-align: left; margin-left: 30px; margin-right: 30px;">
            <p class="s2" style="text-align: center; margin: 0;">Republic of the Philippines</p>
            <h2 style="text-align: center; line-height: 110%; margin: 0;">JUVENILE JUSTICE AND WELFARE COUNCIL</h2>
            <h2 style="text-align: center; line-height: 110%; margin: 0;">#56 Matimtiman Street, Teacher’s Village East, Quezon City, Philippines 1101
            </h2>
        </div>
        <div style="display: inline-block; vertical-align: middle; text-align: center;">
            <img src="images/bagong-pilipinas-logo.png" alt="Bagong Pilipinas Logo"
                style="height: 40px; display: inline-block; vertical-align: middle;">
        </div>
    </div>

    <p style="padding-top: 4pt; text-indent: 0pt; text-align: left;"><br /></p>

    {{-- Title --}}
    <h1 style="text-indent: 0pt; text-align: center;">APPLICATION FOR LEAVE</h1>

    <p style="text-indent: 0pt; text-align: left;"><br /></p>

    {{-- Border --}}
    <div style="text-align: center;">
        <table style="border-collapse: collapse; margin: 0 auto;" cellspacing="0">
            <tr style="height:29pt">
                <td style="width: 165pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid;border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt"
                    colspan="3">
                    <p class="s5" style="padding-top: 1pt; padding-left: 1pt; text-indent: 0pt; text-align: left;">
                        1.
                        OFFICE/DEPARTMENT</p>
                    <u
                        style="margin-left: 25px; color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; font-size: 8pt;">{{ $leaveApplication->office_or_department }}</u>
                </td>
                <td style="width:303pt;border-top-style:solid;border-top-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
                    colspan="3">
                    <p class="s5" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">2.
                        NAME :
                        <span>(Last)</span>
                        <span style="margin-left: 50px;">(First)</span>
                        <span style="margin-left: 50px;">(Middle)</span>
                    </p>
                    <u
                        style="margin-left: 50px; color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; font-size: 8pt;">{{ $leaveApplication->user->userData->surname }},
                        {{ $leaveApplication->user->userData->first_name }}
                        {{ $leaveApplication->user->userData->middle_name }}</u>
                </td>
            </tr>
            <tr style="height: 29pt;">
                <td style="width:212pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid;border-bottom-width: 1pt; "
                    colspan="3">
                    <p class="s5"
                        style="padding-top: 8pt;padding-left: 1pt;text-indent: 0pt;text-align: left; padding-bottom: 5pt;">
                        3. DATE OF FILING:
                        <u
                            style="margin-left: 10px; color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; font-size: 8pt;">{{ $leaveApplication->date_of_filing }}</u>
                    </p>
                </td>
                <td style="width:303pt;border-top-style:solid;border-top-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
                    colspan="3">
                    <p class="s5"
                        style="padding-top: 8pt;padding-left: 1pt;text-indent: 0pt;text-align: left; padding-bottom: 5pt;">
                        4. POSITION:
                        <u
                            style="margin-left: 10px; margin-right: 15px; color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; font-size: 8pt;">{{ $leaveApplication->position }}</u>
                        5. SALARY:
                        <u
                            style="margin-left: 10px; color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; font-size: 8pt;">{{ $leaveApplication->salary }}</u>
                    </p>
                </td>
            </tr>
            <tr style="height:13pt">
                <td style="width:468pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
                    colspan="6">
                    <p class="s7"
                        style="text-indent: 0pt;text-align: center; padding-top: 1pt; padding-bottom: 1pt;">6. DETAILS
                        OF
                        APPLICATION</p>
                </td>
            </tr>
            <tr style="height:16pt">
                <td style="width:165pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt"
                    colspan="3">
                    <p class="s5"
                        style="padding-top: 3pt; padding-bottom: 3pt; padding-left: 1pt; text-indent: 0pt; text-align: left;">
                        6.A TYPE OF LEAVE TO BE AVAILED OF
                    </p>
                    <div style="padding-left: 3px; display: grid;">
                        @foreach (['Vacation Leave', 'Mandatory/Forced Leave', 'Sick Leave', 'Maternity Leave', 'Paternity Leave', 'Special Privilege Leave', 'Solo Parent Leave', 'Study Leave', '10-Day VAWC Leave', 'Rehabilitation Privilege', 'Special Leave Benefits for Women', 'Special Emergency (Calamity) Leave', 'Adoption Leave'] as $leaveType)
                            <div style="display: block; width: 100%; margin-left: 5px;">
                                <div style="display: inline-block; vertical-align: middle;">
                                    <input type="checkbox"
                                        {{ in_array($leaveType, $selectedLeaveTypes) ? 'checked' : '' }}>
                                </div>
                                <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                                    <span class="s5">{{ $leaveType }}</span>
                                </div>
                            </div>
                        @endforeach

                        {{-- <label style="display: flex; align-items: center; margin-left: 15px;">
                            <span class="s5">Others:</span>
                        </label>

                        <div style="display: block; width: 100%; margin-left: 5px;">
                            <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                                <u
                                    style="color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; font-size: 8pt;"> --}}
                        {{-- {{ $otherLeave ?: str_repeat('_', 20) }} --}}
                        {{-- @if (!empty($otherLeave))
                                        {{ $otherLeave }}
                                    @else
                                        {{ str_repeat('_', 20) }}
                                    @endif
                                </u>
                            </div>
                        </div> --}}
                        <label style="display: flex; align-items: center; margin-left: 15px;">
                            <span class="s5">Others:</span>
                        </label>

                        <div style="display: block; width: 100%; margin-left: 5px;">
                            <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                                <u
                                    style="color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; font-size: 8pt;">
                                    @if (!empty($otherLeave))
                                        {{ $otherLeave }}
                                    @elseif(in_array($leaveApplication->type_of_leave, ['Birthday Leave', 'CTO Leave', 'Emergency Leave']))
                                        {{ $leaveApplication->type_of_leave }}
                                    @else
                                        {{ str_repeat('_', 20) }}
                                    @endif
                                </u>
                            </div>
                        </div>
                    </div>
                </td>
                <td style="width:73pt;border-top-style:solid;border-top-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:18pt;border-top-style:solid;border-top-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:212pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p class="s5" style="padding-top: 3pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                        6.B DETAILS OF LEAVE</p>

                    <div style="display: block; width: 100%;">
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5" style="font-style: italic !important; font-weight: bold !important;">
                                In case of Vacation/Special Privilege Leave:</span>
                        </div>
                    </div>

                    <div style="display: block; width: 100%; margin-left: 5px;">
                        <div style="display: inline-block; vertical-align: middle;">
                            <input type="checkbox" {{ $isDetailPresent('Within the Philippines') ? 'checked' : '' }}>
                        </div>
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5">Within the Philippines:</span>
                        </div>
                        <u
                            style="color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; font-size: 8pt;">
                            {{ $getDetailValue('Within the Philippines') ?: str_repeat('_', 20) }}
                        </u>
                    </div>

                    <div style="display: block; width: 100%; margin-left: 5px;">
                        <div style="display: inline-block; vertical-align: middle;">
                            <input type="checkbox" {{ $isDetailPresent('Abroad') ? 'checked' : '' }}>
                        </div>
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5">Abroad (Specify):</span>
                        </div>
                        <u
                            style="color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; font-size: 8pt;">
                            {{ $getDetailValue('Abroad') ?: str_repeat('_', 18) }}
                        </u>
                    </div>

                    <div style="display: block; width: 100%;">
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5"
                                style="font-style: italic !important; font-weight: bold !important;">In
                                case of Sick Leave:</span>
                        </div>
                    </div>

                    <div style="display: block; width: 100%; margin-left: 5px;">
                        <div style="display: inline-block; vertical-align: middle;">
                            <input type="checkbox" {{ $isDetailPresent('In Hospital') ? 'checked' : '' }}>
                        </div>
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5">In Hospital (Special Illness):</span>
                        </div>
                        <u
                            style="color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; font-size: 8pt;">
                            {{ $getDetailValue('In Hospital') ?: str_repeat('_', 14) }}
                        </u>
                    </div>

                    <div style="display: block; width: 100%; margin-left: 5px;">
                        <div style="display: inline-block; vertical-align: middle;">
                            <input type="checkbox" {{ $isDetailPresent('Out Patient') ? 'checked' : '' }}>
                        </div>
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5">Out Patient (Special Illness):</span>
                        </div>
                        <u
                            style="color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; font-size: 8pt;">
                            {{ $getDetailValue('Out Patient') ?: str_repeat('_', 13) }}
                        </u>
                    </div>

                    <div style="display: block; width: 100%;">
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5"
                                style="font-style: italic !important; font-weight: bold !important;">In case of Special
                                Leave Benefits for Women:</span>
                        </div>
                    </div>

                    <div style="display: block; width: 100%; margin-left: 5px;">
                        <div style="display: inline-block; vertical-align: middle;">
                            <input type="checkbox" {{ $isDetailPresent('Women Special Illness') ? 'checked' : '' }}>
                        </div>
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5">(Special Illness):</span>
                        </div>
                        <u
                            style="color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; font-size: 8pt;">
                            {{ $getDetailValue('Women Special Illness') ?: str_repeat('_', 20) }}
                        </u>
                    </div>

                    <div style="display: block; width: 100%;">
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5"
                                style="font-style: italic !important; font-weight: bold !important;">In case of Study
                                Leave:</span>
                        </div>
                    </div>

                    <div style="display: block; width: 100%; margin-left: 5px;">
                        <div style="display: inline-block; vertical-align: middle;">
                            <input type="checkbox"
                                {{ $isDetailPresent('Completion of Masters Degree') ? 'checked' : '' }}>
                        </div>
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5">Completion of Master's Degree</span>
                        </div>
                    </div>

                    <div style="display: block; width: 100%; margin-left: 5px;">
                        <div style="display: inline-block; vertical-align: middle;">
                            <input type="checkbox"
                                {{ $isDetailPresent('BAR/Board Examination Review') ? 'checked' : '' }}>
                        </div>
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5">BAR/Board Examination Review</span>
                        </div>
                    </div>

                    <div style="display: block; width: 100%;">
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5"
                                style="font-style: italic !important; font-weight: bold !important;">Other
                                Purpose:</span>
                        </div>
                    </div>

                    <div style="display: block; width: 100%; margin-left: 5px;">
                        <div style="display: inline-block; vertical-align: middle;">
                            <input type="checkbox"
                                {{ $isDetailPresent('Monetization of Leave Credits') ? 'checked' : '' }}>
                        </div>
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5">Monetization of Leave Credits</span>
                        </div>
                    </div>

                    <div style="display: block; width: 100%; margin-left: 5px;">
                        <div style="display: inline-block; vertical-align: middle;">
                            <input type="checkbox" {{ $isDetailPresent('Terminal Leave') ? 'checked' : '' }}>
                        </div>
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5">Terminal Leave</span>
                        </div>
                    </div>
                </td>
            </tr>

            <tr style="height:16pt">
                <td style="width:165pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt"
                    colspan="3">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td style="width:73pt;border-bottom-style:solid;border-bottom-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:18pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:212pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                </td>
            </tr>
            <tr style="height:14pt">
                <td style="width:238pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt"
                    colspan="4">
                    <p class="s5" style="padding-top: 2pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                        6.C NUMBER OF WORKING DAYS APPLIED FOR
                    </p>

                    <div style="display: block; width: 100%; margin-left: 5px;">
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <u
                                style="color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; font-size: 8pt;">{{ $leaveApplication->number_of_days }}
                                Day/s</u>
                        </div>
                    </div>

                    <p class="s5"
                        style="padding-top: 4pt; text-indent: 0pt; text-align: left; margin-left: 13px; font-weight: bold;">
                        INCLUSIVE DATES
                    </p>

                    <div style="display: block; width: 100%; margin-left: 5px;">
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <u
                                style="color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; font-size: 8pt;">
                                {{-- {{ $leaveApplication->list_of_dates }} --}}
                                @php
                                    use Carbon\Carbon;

                                    $formattedDates = '';

                                    if (str_contains($leaveApplication->list_of_dates, ' - ')) {
                                        // Handle date range
                                        [$startDate, $endDate] = explode(' - ', $leaveApplication->list_of_dates);
                                        $formattedDates =
                                            Carbon::parse(trim($startDate))->format('M d, Y') .
                                            ' - ' .
                                            Carbon::parse(trim($endDate))->format('M d, Y');
                                    } else {
                                        // Handle comma-separated list of dates
                                        $formattedDates = collect(explode(',', $leaveApplication->list_of_dates))
                                            ->map(fn($date) => Carbon::parse(trim($date))->format('M d, Y'))
                                            ->implode(', ');
                                    }
                                @endphp

                                {{ $formattedDates }}

                            </u>
                        </div>
                    </div>
                </td>

                <td
                    style="width:18pt;border-top-style:solid;border-top-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>

                <td
                    style="width:212pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"></p>
                    <p class="s5" style="padding-top: 2pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                        6.D COMMUTATION
                    </p>

                    <div style="display: block; width: 100%; margin-left: 5px;">
                        <div style="display: inline-block; vertical-align: middle;">
                            <input type="checkbox" @if ($leaveApplication->commutation == 'Requested') checked @endif>
                        </div>
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5">Requested</span>
                        </div>
                    </div>

                    <div style="display: block; width: 100%; margin-left: 5px;">
                        <div style="display: inline-block; vertical-align: middle;">
                            <input type="checkbox" @if ($leaveApplication->commutation == 'Not Requested') checked @endif>
                        </div>
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5">Not Requested</span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr style="height:25pt">
                <td
                    style="width:19pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td style="width:219pt;border-bottom-style:solid;border-bottom-width:1pt" colspan="3">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                    <p style="text-indent: 0pt;line-height: 1pt;text-align: left;" />
                </td>
                <td
                    style="width:18pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:212pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    {{-- <p style="padding-top: 0pt;text-indent: 0pt;text-align: left;"><br /></p> --}}
                    <div style="position:relative;padding-top:30px;">
                        <p class="s5"
                            style="padding-left: 1pt; text-indent: 0pt; text-align: center; border-top-style: solid; border-top-width: medium; width: 50%; margin: 0 auto;">
                            (Signature of Applicant)
                        </p>
                    </div>
                </td>
            </tr>
            <tr style="height:13pt">
                <td style="width:468pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
                    colspan="6">
                    <p class="s7"
                        style="text-indent: 0pt;text-align: center; padding-top: 1pt; padding-bottom: 1pt;">7. DETAILS
                        OF
                        ACTION ON APPLICATION</p>
                </td>
            </tr>
            <tr style="height:15pt">
                <td style="width:165pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt"
                    colspan="3">
                    <p class="s5" style="padding-top: 3pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                        7.A CERTIFICATION OF LEAVE CREDITS</p>
                </td>

                <td style="width:73pt;border-top-style:solid;border-top-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>

                <td
                    style="width:18pt;border-top-style:solid;border-top-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>

                <td
                    style="width:212pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p class="s5" style="padding-top: 3pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                        7.B RECOMMENDATION</p>
                    <div style="display: block; width: 100%; margin-left: 5px;">
                        <div style="display: inline-block; vertical-align: middle;">
                            <input type="checkbox" @if ($leaveApplication->status === 'Approved') checked @endif>
                        </div>
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5">For approval</span>
                        </div>
                    </div>

                    <div style="display: block; width: 100%; margin-left: 5px;">
                        <div style="display: inline-block; vertical-align: middle;">
                            <input type="checkbox" @if ($leaveApplication->status === 'Disapproved') checked @endif>
                        </div>
                        <div style="display: inline-block; vertical-align: middle; margin-left: 8px;">
                            <span class="s5">For disapproval due to: </span>
                            <u
                                style="color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: normal; font-size: 8pt;">
                                {{ $leaveApplication->status === 'Disapproved' ? $leaveApplication->remarks : str_repeat('_', 20) }}
                            </u>
                        </div>

                    </div>
                </td>
            </tr>

            <tr style="height:15pt">
                <td style="width:256pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt"
                    colspan="5">

                    <p class="s5"
                        style="padding-top: 1pt;padding-left: 30pt; padding-bottom: 10px;text-indent: 0pt;text-align: left;">
                        As of
                        <u>{{ $leaveApplication->date_of_filing }}</u>
                    </p>
                </td>
                <td
                    style="width:212pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                </td>
            </tr>
            <tr style="height:9pt">
                <td style="width:19pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt"
                    rowspan="7">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:74pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:72pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p class="s5" style="padding-left: 10pt;text-indent: 0pt;line-height: 8pt;text-align: left;">
                        Vacation Leave</p>
                </td>
                <td
                    style="width:73pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p class="s5" style="padding-left: 17pt;text-indent: 0pt;line-height: 8pt;text-align: left;">
                        Sick Leave</p>
                </td>
                <td style="width:18pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt"
                    rowspan="7">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:212pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                </td>
            </tr>

            <tr style="height:4pt">
                <td
                    style="width:74pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:72pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:73pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:212pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
            </tr>

            <tr style="height:4pt">
                <td
                    style="width:74pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p class="s9" style="text-indent: 0pt;line-height: 3pt;text-align: center;">Total Earned</p>
                </td>
                <td
                    style="width:72pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;line-height: 3pt;text-align: center;">
                        @php
                            // Calculate VL Total Earned
                            $vlBalance = $leaveCredits->vl_claimable_credits ?? 0;
                            $vlLess =
                                $leaveApplication->type_of_leave === 'Vacation Leave' &&
                                $leaveApplication->approved_days !== null
                                    ? $leaveApplication->approved_days
                                    : 0;
                            $vlTotalEarned = number_format($vlBalance + $vlLess, 3);
                        @endphp
                        {{ $vlTotalEarned }}
                    </p>
                </td>
                <td
                    style="width:73pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;line-height: 3pt;text-align: center;">
                        @php
                            // Calculate SL Total Earned
                            $slBalance = $leaveCredits->sl_claimable_credits ?? 0;
                            $slLess =
                                $leaveApplication->type_of_leave === 'Sick Leave' &&
                                $leaveApplication->approved_days !== null
                                    ? $leaveApplication->approved_days
                                    : 0;
                            $slTotalEarned = number_format($slBalance + $slLess, 3);
                        @endphp
                        {{ $slTotalEarned }}
                    </p>
                </td>
                <td
                    style="width:212pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
            </tr>

            <tr style="height:4pt">
                <td
                    style="width:74pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:72pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:73pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:212pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
            </tr>

            <tr style="height:4pt">
                <td
                    style="width:74pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p class="s9" style="text-indent: 0pt;line-height: 3pt;text-align: center;">Less this
                        application
                    </p>
                </td>
                <td
                    style="width:72pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;line-height: 3pt;text-align: center;">
                        {{ $leaveApplication->type_of_leave === 'Vacation Leave'
                            ? ($leaveApplication->approved_days !== null
                                ? number_format($leaveApplication->approved_days, 3)
                                : '')
                            : '0.000' }}

                    </p>
                </td>
                <td
                    style="width:73pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;line-height: 3pt;text-align: center;">
                        {{ $leaveApplication->type_of_leave === 'Sick Leave'
                            ? ($leaveApplication->approved_days !== null
                                ? number_format($leaveApplication->approved_days, 3)
                                : '')
                            : '0.000' }}
                    </p>
                </td>
                <td
                    style="width:212pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
            </tr>

            <tr style="height:4pt">
                <td
                    style="width:74pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:72pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:73pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:212pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
            </tr>

            <tr style="height:4pt">
                <td
                    style="width:74pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p class="s9" style="text-indent: 0pt;line-height: 3pt;text-align: center;">Balance</p>
                </td>
                <td
                    style="width:72pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;line-height: 3pt;text-align: center;">
                        {{ $leaveCredits->vl_claimable_credits ?? 'N/A' }}</p>
                </td>
                <td
                    style="width:73pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;line-height: 3pt;text-align: center;">
                        {{ $leaveCredits->sl_claimable_credits ?? 'N/A' }}</p>
                </td>
                <td
                    style="width:212pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
            </tr>

            <tr style="height:22pt">
                <td style="width:256pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt;position:relative"
                    colspan="5" rowspan="2">
                    <p style="padding-top: 10pt;text-indent: 0pt;text-align: left;"><br /></p>
                    <p style="padding-left: 18pt;text-indent: 0pt;line-height: 1pt;text-align: left;" />
                    <p class="s5"
                        style="padding-left: 1pt; text-indent: 0pt; text-align: center; border-top-style: solid; border-top-width: medium; width: 50%; margin: 0 auto;">
                        (Authorized Officer)
                    </p>
                </td>
                <td
                    style="width:212pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="padding-top: 4pt;text-indent: 0pt;text-align: left;"><br /></p>
                    <p style="text-indent: 0pt;text-align: left;" />
                    <p class="s5" style="padding-right: 7pt;text-indent: 0pt;line-height: 7pt;text-align: right;">
                    </p>
                </td>
            </tr>
            <tr style="height:14pt">
                <td
                    style="width:212pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt;position:relative">
                    <p class="s5"
                        style="padding-left: 1pt; text-indent: 0pt; text-align: center; border-top-style: solid; border-top-width: medium; width: 50%; margin: 0 auto;">
                        (Authorized Officer)
                    </p>
                </td>
            </tr>
            <tr style="height:13pt">
                <td style="width:93pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt"
                    colspan="2">
                    <p class="s5"
                        style="padding-top: 3pt;padding-left: 1pt;text-indent: 0pt;line-height: 8pt;text-align: left;">
                        7.C APPROVED FOR:</p>
                </td>
                <td style="width:72pt;border-top-style:solid;border-top-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td style="width:73pt;border-top-style:solid;border-top-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td style="width:18pt;border-top-style:solid;border-top-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td
                    style="width:212pt;border-top-style:solid;border-top-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;" />
                    <p class="s5"
                        style="padding-top: 3pt;padding-left: 1pt;text-indent: 0pt;line-height: 8pt;text-align: left;">
                        7.D DISAPPROVED DUE TO:
                        <u>{{ $leaveApplication->status === 'Disapproved' ? $leaveApplication->remarks : str_repeat('_', 20) }}</u>
                    </p>
                </td>
            </tr>
            <tr style="height:10pt">
                <td style="width:19pt;border-left-style:solid;border-left-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td style="width:146pt" colspan="2">
                    <p class="s5"
                        style="padding-top: 2pt;padding-left: 1pt;text-indent: 0pt;line-height: 7pt;text-align: left;">
                        <u>{{ str_pad($daysWithPay ?? '', 10, '_', STR_PAD_BOTH) }}</u> days with pay
                    </p>
                </td>
                <td style="width:73pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td style="width:18pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td style="width:212pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
            </tr>
            <tr style="height:10pt">
                <td style="width:19pt;border-left-style:solid;border-left-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td style="width:146pt" colspan="2">
                    <p style="text-indent: 0pt;text-align: left;"></p>
                    <p class="s5"
                        style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;line-height: 8pt;text-align: left;">
                        <u>{{ str_pad($daysWithoutPay ?? '', 10, '_', STR_PAD_BOTH) }}</u> days without pay
                    </p>
                </td>
                <td style="width:73pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td style="width:18pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td style="width:212pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
            </tr>
            <tr style="height:28pt">
                <td style="width:19pt;border-left-style:solid;border-left-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>

                <td style="width:146pt" colspan="2">
                    <p class="s5" style="padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                        Others (Specify):
                        <u>{{ $otherRemarks . str_repeat('_', max(20 - strlen($otherRemarks), 0)) }}</u>
                    </p>
                </td>



                <td style="width:73pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td style="width:18pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td style="width:212pt;border-right-style:solid;border-right-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
            </tr>

            <tr style="height:36pt">
                <td
                    style="width:19pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td style="width:146pt;border-bottom-style:solid;border-bottom-width:1pt" colspan="2">
                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                </td>
                <td style="width:303pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt;position:relative"
                    colspan="3">

                    <p style="text-indent: 0pt;text-align: left;"><br /></p>
                    <p style="text-indent: 0pt;text-align: left;" />
                    <p style="text-indent: 0pt;line-height: 1pt;text-align: left;" />
                    <p class="s10"
                        style="text-indent: 0pt;line-height: 7pt;text-align: center; padding-bottom: 2pt; padding-top: 2pt; border-top-style: solid; border-top-width: medium; width: 50%; margin: 0 auto;">
                        (Authorized Official)</p>
                </td>
            </tr>

        </table>
    </div>
</body>

</html>
