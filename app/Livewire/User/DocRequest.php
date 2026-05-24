<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\DocRequest as Request;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Document Requests')]
class DocRequest extends Component
{

    public $preparingCount = 0;
    public $completedCount = 0;
    public $rejectedCount = 0;
    public $documentType;
    public $otherDocumentType = ''; // New property for "Others"
    
    public $availableDocumentTypes = [
        'employment' => 'Certificate of Employment',
        'employmentCompensation' => 'Certificate of Employment with Compensation',
        'leaveCredits' => 'Certificate of Leave Credits',
        'ipcrRatings' => 'Certificate of IPCR Ratings',
        'serviceRecord' => 'Service Record',
        'ctcNotarizedSaln' => 'CTC Notarized SALN',
        'certificateLwop' => 'Certificate of LWOP',
        'certificateNoPendingCase' => 'Certificate of No Pending Case',
        'others' => 'Others (Please Specify)',
    ];

    public $showRatingModal = false;
    public $currentDocRequestId;
    public $ratings = [
        'responsiveness' => null,
        'reliability' => null,
        'access_facilities' => null,
        'communication' => null,
        'cost' => null,
        'integrity' => null,
        'assurance' => null,
        'outcome' => null,
    ];

    public $descriptions = [
        'responsiveness' => 'I spent a reasonable amount of time for my transaction',
        'reliability' => 'The office followed the transaction\'s requirements and steps based on the information provided',
        'access_facilities' => 'The steps I needed to do for my transaction were easy and simple',
        'communication' => 'I easily found information about my transaction from the office or its website',
        'cost' => 'I paid a reasonable amount of fees for my transaction',
        'integrity' => 'I feel the office was fair to everyone, or "walang palakasan", during my transaction',
        'assurance' => 'I was treated courteously by the staff, and (if asked for help) the staff was helpful',
        'outcome' => 'I got what I needed from the government office, or (if denied) denial of request was sufficiently explained to me',
    ];

    protected NotificationService $notificationService;

    public function __construct()
    {
        $this->notificationService = app(NotificationService::class);
    }


    public function updateNotificationCounts()
    {
        $userId = Auth::id();

        $this->preparingCount = $this->notificationService->getTotalUnreadCount(
            $userId,
            'approved',
            'doc_request'
        );

        $this->completedCount = $this->notificationService->getTotalUnreadCount(
            $userId,
            'completed',
            'doc_request'
        );

        $this->rejectedCount = $this->notificationService->getTotalUnreadCount(
            $userId,
            'rejected',
            'doc_request'
        );
    }

    public function markNotificationsAsRead($type)
    {  
        $this->notificationService->markAsRead(
            Auth::id(),
            'doc_request',
            null,
            null,
            $type
        );

        $this->updateNotificationCounts();
    }

    public function mount()
    {
        $this->updateNotificationCounts();
    }

    public function requestDocument()
    {
        $this->validate([
            'documentType' => 'required',
            'otherDocumentType' => $this->documentType === 'others' ? 'required|string|max:255' : '',
        ], [
            'documentType.required' => 'Please select a document type.',
            'otherDocumentType.required' => 'Please specify the document type.',
        ]);

        // Determine the final document type
        $finalDocumentType = $this->documentType === 'others' 
            ? 'Others: ' . $this->otherDocumentType 
            : $this->availableDocumentTypes[$this->documentType];

        $docRequest = Request::create([
            'user_id' => Auth::id(),
            'document_type' => $finalDocumentType,
            'date_requested' => now(),
            'status' => 'pending',
        ]);

        // Create a notification entry
        $this->notificationService->create(
            null,
            'request',
            "doc_request",
            $docRequest->id,
            'doc_request'
        );

        $this->dispatch('swal', [
            'title' => 'Document Request sent successfully!',
            'icon' => 'success'
        ]);
        
        // Reset form
        $this->documentType = null;
        $this->otherDocumentType = '';
    }


    public function downloadDocument($id)
    {
        $request = Request::find($id);
        if (!$request || !$request->file_path) {
            $this->dispatch('swal', [
                'title' => 'Document not found.',
                'icon' => 'error'
            ]);
            return;
        }

        $rating = Rating::where('user_id', Auth::id())
            ->where('doc_request_id', $id)
            ->first();

        if (!$rating) {
            $this->currentDocRequestId = $id;
            $this->showRatingModal = true;

        } else {
            return $this->performDownload($request);

        }
    }
    
    public function performDownload($request)
    {
        $filePath = 'public/' . $request->file_path;

        if (Storage::exists($filePath)) {
            $this->dispatch('swal', [
                'title' => 'Document downloaded successfully',
                'icon' => 'success'
            ]);
            return Storage::download($filePath, $request->filename);
        } else {
            $this->dispatch('swal', [
                'title' => 'File not found on the server.',
                'icon' => 'error'
            ]);
        }
    }

    public function submitRating()
    {
        $this->validate([
            'ratings.*' => 'required|integer|between:1,5',
        ], [
            'ratings.*.required' => 'Please rate all the criteria in the form!',
        ]);
        // Calculate the overall rating
        $overallRating = array_sum($this->ratings) / count($this->ratings);
        Rating::create([
            'user_id' => Auth::id(),
            'doc_request_id' => $this->currentDocRequestId,
            'responsiveness' => $this->ratings['responsiveness'],
            'reliability' => $this->ratings['reliability'],
            'access_facilities' => $this->ratings['access_facilities'],
            'communication' => $this->ratings['communication'],
            'cost' => $this->ratings['cost'],
            'integrity' => $this->ratings['integrity'],
            'assurance' => $this->ratings['assurance'],
            'outcome' => $this->ratings['outcome'],
            'overall' => $overallRating,
        ]);

        $this->showRatingModal = false;
        $request = Request::find($this->currentDocRequestId);
        if ($request) {
            return $this->performDownload($request);
        }
    }



    public function getRequestsProperty()
    {
        return Request::where('user_id', Auth::id())
            ->with('rating')
            ->orderBy('date_requested', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.user.doc-request', [
            'requests' => $this->requests,
            'preparingCount' => $this->preparingCount,
            'completedCount' => $this->completedCount,
            'rejectedCount' => $this->rejectedCount,
        ]);
    }
}