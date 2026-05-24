<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\DocRequest;

class DocRequestStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $docRequest;
    protected $status;
    protected $userName;

    public function __construct(DocRequest $docRequest, $status, $userName)
    {
        $this->docRequest = $docRequest;
        $this->status = $status;
        $this->userName = $userName;
    }

    public function build()
    {
        $emailData = $this->getEmailData();

        return $this->from(config('mail.from.address'))
            ->view('livewire.emails.email-template')
            ->subject($emailData['subject'])
            ->with([
                'header' => $emailData['header'],
                'greetings' => $emailData['greetings'],
                'message_body' => $emailData['message_body'],
                'footer' => $emailData['footer'],
            ]);
    }

    private function getEmailData()
    {
        return match($this->status) {
            'approved' => $this->getApprovedEmailData(),
            'rejected' => $this->getRejectedEmailData(),
            'completed' => $this->getCompletedEmailData(),
            default => $this->getDefaultEmailData(),
        };
    }

    private function getApprovedEmailData()
    {
        return [
            'subject' => 'Document Request Approved - ' . $this->docRequest->document_type,
            'header' => 'DOCUMENT REQUEST APPROVED',
            'greetings' => 'Hello ' . $this->userName . ',',
            'message_body' => '
                <p class="message">
                    Great news! Your document request has been approved and is now being prepared.
                </p>
                
                <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 5px 0;"><strong>Document Type:</strong> ' . $this->docRequest->document_type . '</p>
                    <p style="margin: 5px 0;"><strong>Request Date:</strong> ' . $this->docRequest->created_at->format('F d, Y') . '</p>
                    <p style="margin: 5px 0;"><strong>Purpose:</strong> ' . $this->docRequest->purpose . '</p>
                    <p style="margin: 5px 0;"><strong>Status:</strong> <span style="color: #28a745; font-weight: bold;">PREPARING</span></p>
                </div>

                <p class="message">
                    We will notify you once your document is ready for download. You can check the status of your 
                    request anytime by visiting the document request page.
                </p>

                <div class="action-wrapper">
                    <a href="' . url('/my-records/doc-request') . '" target="_blank">
                        <button class="action-btn">
                            View Request Status
                        </button>
                    </a>
                </div>

                <p class="message">
                    Thank you for your patience!
                </p>

                <p class="message">
                    Best regards, <br>
                    <strong style="font-size: 18px">Document Management Team</strong>
                </p>',
            'footer' => '© ' . date('Y') . ' All rights reserved.',
        ];
    }

    private function getRejectedEmailData()
    {
        return [
            'subject' => 'Document Request Rejected - ' . $this->docRequest->document_type,
            'header' => 'DOCUMENT REQUEST REJECTED',
            'greetings' => 'Hello ' . $this->userName . ',',
            'message_body' => '
                <p class="message">
                    We regret to inform you that your document request has been rejected.
                </p>
                
                <div style="background-color: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ffc107;">
                    <p style="margin: 5px 0;"><strong>Document Type:</strong> ' . $this->docRequest->document_type . '</p>
                    <p style="margin: 5px 0;"><strong>Request Date:</strong> ' . $this->docRequest->created_at->format('F d, Y') . '</p>
                    <p style="margin: 5px 0;"><strong>Purpose:</strong> ' . $this->docRequest->purpose . '</p>
                    <p style="margin: 5px 0;"><strong>Status:</strong> <span style="color: #dc3545; font-weight: bold;">REJECTED</span></p>
                </div>

                <p class="message">
                    If you have questions about this decision or need further clarification, please contact 
                    the administrator or submit a new request with updated information.
                </p>

                <div class="action-wrapper">
                    <a href="' . url('/my-records/doc-request') . '" target="_blank">
                        <button class="action-btn">
                            View Request Details
                        </button>
                    </a>
                </div>

                <p class="message">
                    We appreciate your understanding.
                </p>

                <p class="message">
                    Best regards, <br>
                    <strong style="font-size: 18px">Document Management Team</strong>
                </p>',
            'footer' => '© ' . date('Y') . ' All rights reserved.',
        ];
    }

    private function getCompletedEmailData()
    {
        return [
            'subject' => 'Document Ready for Download - ' . $this->docRequest->document_type,
            'header' => 'DOCUMENT READY FOR DOWNLOAD',
            'greetings' => 'Hello ' . $this->userName . ',',
            'message_body' => '
                <p class="message">
                    Excellent news! Your requested document is now ready and available for download.
                </p>
                
                <div style="background-color: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #28a745;">
                    <p style="margin: 5px 0;"><strong>Document Type:</strong> ' . $this->docRequest->document_type . '</p>
                    <p style="margin: 5px 0;"><strong>Request Date:</strong> ' . $this->docRequest->created_at->format('F d, Y') . '</p>
                    <p style="margin: 5px 0;"><strong>Completed Date:</strong> ' . $this->docRequest->date_completed->format('F d, Y') . '</p>
                    <p style="margin: 5px 0;"><strong>Purpose:</strong> ' . $this->docRequest->purpose . '</p>
                    <p style="margin: 5px 0;"><strong>Status:</strong> <span style="color: #28a745; font-weight: bold;">COMPLETED</span></p>
                </div>

                <p class="message">
                    Click the button below to access and download your document. The document will be available 
                    for download for the next 30 days.
                </p>

                <div class="action-wrapper">
                    <a href="' . url('/my-records/doc-request') . '" target="_blank">
                        <button class="action-btn" style="background-color: #28a745;">
                            Download Document Now
                        </button>
                    </a>
                </div>

                <p class="message">
                    Thank you for using our document request system!
                </p>

                <p class="message">
                    Best regards, <br>
                    <strong style="font-size: 18px">Document Management Team</strong>
                </p>',
            'footer' => '© ' . date('Y') . ' All rights reserved.',
        ];
    }

    private function getDefaultEmailData()
    {
        return [
            'subject' => 'Document Request Update - ' . $this->docRequest->document_type,
            'header' => 'DOCUMENT REQUEST UPDATE',
            'greetings' => 'Hello ' . $this->userName . ',',
            'message_body' => '
                <p class="message">
                    There has been an update to your document request.
                </p>
                
                <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <p style="margin: 5px 0;"><strong>Document Type:</strong> ' . $this->docRequest->document_type . '</p>
                    <p style="margin: 5px 0;"><strong>Status:</strong> <span style="font-weight: bold;">' . strtoupper($this->status) . '</span></p>
                </div>

                <div class="action-wrapper">
                    <a href="' . url('/my-records/doc-request') . '" target="_blank">
                        <button class="action-btn">
                            View Request
                        </button>
                    </a>
                </div>

                <p class="message">
                    Thank you!
                </p>

                <p class="message">
                    Best regards, <br>
                    <strong style="font-size: 18px">Document Management Team</strong>
                </p>',
            'footer' => '© ' . date('Y') . ' All rights reserved.',
        ];
    }

    public function envelope(): Envelope
    {
        $emailData = $this->getEmailData();
        return new Envelope(
            subject: $emailData['subject'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'livewire.emails.email-template',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}