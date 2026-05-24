<!DOCTYPE html>
<html>
<head>
    <title>Leave Application Status</title>
</head>
<body>
    <h2>Leave Application {{ ucfirst($status) }}</h2>
    
    <p>Dear {{ $userName }},</p>
    
    <p>Your leave application has been <strong>{{ $status }}</strong>.</p>
    
    <h3>Application Details:</h3>
    <ul>
        <li><strong>Type of Leave:</strong> {{ $leaveApplication->type_of_leave }}</li>
        <li><strong>Number of Days:</strong> {{ $leaveApplication->number_of_days }}</li>
        <li><strong>Dates:</strong> {{ $leaveApplication->list_of_dates }}</li>
        <li><strong>Status:</strong> {{ ucfirst($status) }}</li>
        @if($leaveApplication->remarks)
        <li><strong>Remarks:</strong> {{ $leaveApplication->remarks }}</li>
        @endif
    </ul>
    
    <p>Thank you for using our leave management system.</p>
    
    <p>Best regards,<br>
    HR Department</p>
</body>
</html>