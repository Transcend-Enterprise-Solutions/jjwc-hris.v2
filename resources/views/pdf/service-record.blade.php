<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Service Record</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: 'Arial Narrow', Arial, Helvetica, sans-serif;
            font-size: 10px;
        }
    </style>
</head>
<body>

    <div style="margin: 30px 80px;">
        <table style="width: 100%;">
            <tbody style="width: 100%">
                <tr style="width: 100%;">
                    <td><img src="images/logo.png" alt="" width="100px"></td>
                    <td style="width: 100%">
                    </td>
                    <td><img src="images/bagong-pilipinas-logo.png" alt="" width="70px"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <center><p style="font-weight: 700; margin-top: -20px; font-size: 14px;">SERVICE RECORD</p></center>
    <center><p style="margin-bottom: 20px; line-height: 8px;">(To be accomplished by Employer)</p></center>

    <div style="margin: 0 20px 0 30px; font-size: 10px !important;">
        <table>
            <tbody>
                <tr>
                    <td>
                        <div style="width: 480px">
                            <table>
                                <tbody>
                                    <tr style="font-weight: bold">
                                        <td>NAME</td>
                                        <td>
                                            <div style="margin-left: 13px;">:</div>
                                        </td>
                                        <td style="text-transform:uppercase;">
                                            <div style="margin-left: 5px; width: 83px;">{{ $userData->surname }}</div>
                                        </td>
                                        <td style="text-transform:uppercase;">
                                            <div style="width: 100px;">{{ $userData->first_name }}</div>
                                        </td>
                                        <td style="text-transform:uppercase;">
                                            <div style="width: 120px;">{{ $userData->middle_name ?: '' }}</div>
                                        </td>
                                        <td style="text-transform: uppercase;">
                                            <div style="width: 170px;">{{ $userData->maiden_name ?: '' }}</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div style="border-bottom: 1px solid black; width: 470px; height: 1px; margin-left: 50px;"></div>
                            <p style="margin-left: 55px;"><span>(Surname)</span><span style="margin-left: 40px;">(Given Name)</span><span style="margin-left: 40px;">(Middle Name)</span><span style="margin-left: 50px;">(Full Maiden Name)</span></p>
                        </div>
                    </td>
                    <td>
                        <p style="margin-left: 45px; margin-top: 5px;">(If married woman, give also <br> full Maiden Name)</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="width: 480px">
                            <table>
                                <tbody>
                                    <tr style="font-weight: bold">
                                        <td>BIRTH</td>
                                        <td>
                                            <div style="margin-left: 13px;">:</div>
                                        </td>
                                        <td style="text-align: center">
                                            <div style="margin-left: 70px; width: 100px;">08/09/1997</div>
                                        </td>
                                        <td style="text-align: center">
                                            <div style="width: 290px;">Metro Manila</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div style="border-bottom: 1px solid black; width: 470px; height: 1px; margin-left: 50px;"></div>
                            <p style="margin-left: 145px;"><span>(Date of Birth)</span><span style="margin-left: 128px;">(Place of Birth)</span></p>
                        </div>
                    </td>
                    <td>
                        <p style="margin-left: 45px; margin-top: 15px;">(Date herein should be checked from birth <br> 
                            or baptismal certificate or some other <br> reliable documents)</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <center><p style="margin-top: 10px;">(To be accomplished by Employer)</p></center>
    <center><p style="margin-bottom: 5px; margin-top: 8px;">
        This is to certify that the employee named hereinabove actually 
        rendered services in this Office as shown by the service record below <br>
        each libe of which supported by appointment papers actually issued by this Office 
        and approved by the authorities concerned.
    </p></center>

    <div style="margin: 0 30px; font-size: 10px !important;">

        <div style="border-bottom: 1px solid black; width: 100%; height: 1px;"></div>
        <p>
            <span style="margin-left: 30px">SERVICE</span>
            <span style="margin-left: 60px">RECORD OF APPOINTMENT</span>
            <span style="margin-left: 120px">OFFICE ENTITY/DIVISION</span>
        </p>

        <table style="margin-top: 5PX">
            <tbody>
                <tr>
                    <td>
                        <div style="margin-left: 5px"><p style="font-size: 10px">(INCLUSIVE DATES)</p></div>
                    </td>
                    <td>
                        <div ></div>
                    </td>
                    <td>
                        <div style="margin-left: 50px"><p style="font-size: 10px"></p></div>
                    </td>
                    <td>
                        <div style="margin-left: 30px"><p style="font-size: 10px"></p></div>
                    </td>
                    <td>
                        <div style="margin-left: 140px"><p style="font-size: 10px">SALARY/</p></div>
                    </td>
                    <td>
                        <div style="margin-left: 30px"><p style="font-size: 10px">STATION/PLACE</p></div>
                    </td>
                    <td>
                        <div style="margin-left: 20px"><p style="font-size: 10px"></p></div>
                    </td>
                    <td>
                        <div style="margin-left: 75px"><p style="font-size: 10px">L/V ABS</p></div>
                    </td>
                    <td>
                        <div style="margin-left: 50px"><p style="font-size: 10px"></p></div>
                    </td>
                </tr>
            </tbody>
        </table>

        <table>
            <tbody>
                <tr>
                    <td>
                        <p style="text-align: center; line-height: 8px; font-size: 10px; width: 55px;">FROM</p>
                    </td>
                    <td>
                        <p style="text-align: center; line-height: 8px; font-size: 10px; width: 55px;">TO</p>
                    </td>
                    <td>
                        <p style="text-align: center; line-height: 8px; font-size: 10px; width: 130px;">DESIGNATION</p>
                    </td>
                    <td>
                        <p style="text-align: center; line-height: 8px; font-size: 10px; width: 70px;">STATUS</p>
                    </td>
                    <td>
                        <p style="text-align: center; line-height: 8px; font-size: 10px; width: 60px;">ANNUM</p>
                    </td>
                    <td>
                        <p style="text-align: center; line-height: 8px; font-size: 10px; width: 120px;">OF ASSIGNMENT</p>
                    </td>
                    <td>
                        <p style="text-align: center; line-height: 8px; font-size: 10px; width: 70px;">BRANCH</p>
                    </td>
                    <td>
                        <p style="text-align: center; line-height: 8px; font-size: 10px; width: 50px;">W/O PAY</p>
                    </td>
                    <td>
                        <p style="text-align: center; line-height: 8px; font-size: 10px; width: 105px;">REMARKS</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="border-bottom: 1px solid black; width: 100%; height: 1px;"></div>

        <table>
            <tbody>
                @foreach ($myWorkExperiences as $exp)
                    <tr>
                        <td>
                            <p style="padding: 7px 0; text-align: center; line-height: 8px; font-size: 10px; width: 55px;">{{ \Carbon\Carbon::parse($exp->start_date)->format('m-d-Y') }}</p>
                        </td>
                        <td>
                            <p style="padding: 7px 0; text-align: center; line-height: 8px; font-size: 10px; width: 55px;">{{ \Carbon\Carbon::parse($exp->end_date)->format('m-d-Y') }}</p>
                        </td>
                        <td>
                            <p style="padding: 7px 0; text-align: center; line-height: 8px; font-size: 10px; width: 130px;">DESIGNATION</p>
                        </td>
                        <td>
                            <p style="padding: 7px 0; text-align: center; line-height: 8px; font-size: 10px; width: 70px;">STATUS</p>
                        </td>
                        <td>
                            <p style="padding: 7px 0; text-align: center; line-height: 8px; font-size: 10px; width: 60px;">ANNUM</p>
                        </td>
                        <td>
                            <p style="padding: 7px 0; text-align: center; line-height: 8px; font-size: 10px; width: 120px;">OF ASSIGNMENT</p>
                        </td>
                        <td>
                            <p style="padding: 7px 0; text-align: center; line-height: 8px; font-size: 10px; width: 70px;">BRANCH</p>
                        </td>
                        <td>
                            <p style="padding: 7px 0; text-align: center; line-height: 8px; font-size: 10px; width: 50px;">W/O PAY</p>
                        </td>
                        <td>
                            <p style="padding: 7px 0; text-align: center; line-height: 8px; font-size: 10px; width: 105px;">REMARKS</p>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>



</body>
</html>