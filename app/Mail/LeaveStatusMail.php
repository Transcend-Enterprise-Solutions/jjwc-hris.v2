<?php

namespace App\Mail;

use App\Models\LeaveApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveApplication;

    public $status;

    public $userName;

    public function __construct(LeaveApplication $leaveApplication, $status, $userName)
    {
        $this->leaveApplication = $leaveApplication;
        $this->status = $status;
        $this->userName = $userName;
    }

    public function build()
    {
        $subject = $this->status === 'approved'
            ? 'Leave Application Approved'
            : 'Leave Application Disapproved';

        return $this->subject($subject)
            ->view('livewire.emails.leave-status')
            ->with([
                'leaveApplication' => $this->leaveApplication,
                'status' => $this->status,
                'userName' => $this->userName,
            ]);
    }
}
