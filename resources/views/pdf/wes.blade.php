<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Work Experience Sheet</title>
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

        table,
        tbody {
            vertical-align: top;
            overflow: visible;
        }

        @page {
            margin-top: 20mm !important;
        }

        .work-experience-item {
            page-break-inside: avoid;
        }

        .work-experience-item:not(:first-child) {
            margin-top: 20px;
        }

        .work-experience-continuation {
            border-top: 1px solid black;
            padding-top: 20px;
        }
    </style>
</head>
<body>

    <div style="padding: 15px" style="margin-top: -18mm !important; margin-left: 15px; margin-right: 15px;">

        <div style="padding-left: 20px; padding-top: 20px; margin-bottom: 8px;">
            <h3 style="text-indent: 0pt; text-align: left; font-size: 14px;">
                Attachment to CS Form No. 212
            </h3>
        </div>

        <div style="border: 2px solid black; width: 100%;">

            <div style="background: #969696; border-bottom: 2px solid black;">
                <p style="text-align:center; padding: 6px 0 6px 0; color: white; font-size: 18px; font-weight: bold; font-family: 'Arial Narrow', sans-serif;"><i>WORK EXPERIENCE SHEET</i></p>
            </div>

            <div style="background: #eaeaea; border-bottom: 1px solid black; padding: 0 7px;">
                <p style="text-align:left; font-size: 16px; font-family: 'Arial', sans-serif; ">
                    <i>
                        <span style="font-weight: bold;">Instructions: &nbsp;&nbsp; 1.</span>
                        <span>
                            Include only the work experiences relevant to the position being applied to.
                        </span>
                        <br><br>
                        <span style="padding-left: 115px">
                            <span style="font-weight: bold;">2.</span>
                            The duration should include start and finish dates, if known, month in abbreviated form,
                        </span>
                        <span style="padding-left: 135px">
                            if known, and year in full. For the current position, use the word Present, e.g., 1998-
                        </span>
                        <br>
                        <span style="padding-left: 135px">
                            Present. Work experience should be listed from most recent first.
                        </span>
                    </i>
                </p>
            </div>

            <div>
                @if($myWorkExperiences)
                    @foreach ($myWorkExperiences as $we)
                        @php
                            $listOfAccomsOrContri = explode('|', $we->list_accomp_cont);
                        @endphp

                        <div class="work-experience-item {{ $loop->first ? '' : 'work-experience-continuation' }}" style="{{ $loop->last ? '' : 'border-bottom: 1px solid black;' }} padding: 20px 20px 20px 50px;">
                            <p style="font-size: 16px; font-family: 'Arial', sans-serif; line-height: 1.3;">• Duration: {{ \Carbon\Carbon::parse($we->start_date)->format('F d, Y') }} - {{ $we->toPresent ? 'Present' : \Carbon\Carbon::parse($we->end_date)->format('F d, Y') }}</p>
                            <p style="font-size: 16px; font-family: 'Arial', sans-serif; line-height: 1.3;">• Position: {{ $we->position }}</p>
                            <p style="font-size: 16px; font-family: 'Arial', sans-serif; line-height: 1.3;">• Name of Office/Unit: {{ $we->office_unit }}</p>
                            <p style="font-size: 16px; font-family: 'Arial', sans-serif; line-height: 1.3;">• Immediate Supervisor: {{ $we->supervisor }}</p>
                            <p style="font-size: 16px; font-family: 'Arial', sans-serif; line-height: 1.3;">• Name of Agency/Organization and Location: {{ $we->agency_org }}</p>

                            <p style="margin-left: 20px; font-size: 16px; font-family: 'Arial', sans-serif; line-height: 1.3; margin-top: 15px;">• List of Accomplishments and Contributions (if any)</p>
                            
                            @foreach ($listOfAccomsOrContri as $list)
                                <p style="{{ $loop->first ? 'margin-top: 15px;' : '' }} margin-left: 60px; font-size: 16px; font-family: 'Arial', sans-serif; line-height: 1.3;">o {{ $list }}</p>
                            @endforeach

                            <p style="margin-left: 20px; font-size: 16px; font-family: 'Arial', sans-serif; line-height: 1.3; margin-top: 15px;">• Summary of Actual Duties</p>
                            <p style="margin-left: 60px; font-size: 16px; font-family: 'Arial', sans-serif; line-height: 1.3; margin-top: 15px;">o {{ $we->sum_of_duties }}</p>
                        </div>
                    @endforeach
                @endif
            </div>

        </div>
        
        <div style="width: 100%; margin-top: 50px;">
            <div style="position: relative; width: 100%">
                {{-- @if ($signatureImagePath)
                    <div style="position: absolute; top: {{ $sigYPos }}px; right: {{ $sigXPos }}px;">
                        <img src="{{ $signatureImagePath }}" alt="E-Signature"
                            style="width: {{ $sigSize }}px; height: auto;" />
                    </div>
                @endif --}}
            </div>
            <div style="width: 100%;">
                <p style="text-align: center; margin-left: 425px; font-size: 16px; margin-bottom: -8px;">{{ $name }}</p>
            </div>
            <p style="text-align: right; margin-right: 40px;">_______________________________________________</p>
            <p style="text-align: right; margin-right: 50px; font-size: 16px;">
                <span style="text-align: left; margin-right: 20px;">
                    (Signature over Printed Name
                </span>
                <br>
                <span style="text-align: center; margin-right: 40px;">
                    of Employee/Applicant)
                </span>
            </p>
            <br>
            <p style="text-align: right; margin-right: 50px; font-size: 16px;">
                <span style="text-align: left; margin-right: 60px;">
                    Date: <span style="text-decoration: underline"> {{ now()->format('m/d/Y') }} </span>
                </span>
            </p>
        </div>
    </div>

</body>
</html>
