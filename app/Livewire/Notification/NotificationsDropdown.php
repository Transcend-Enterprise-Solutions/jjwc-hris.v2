<?php
namespace App\Livewire\Notification;

use App\Models\WfhLocationRequests;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\OfficialBusiness;
use App\Services\NotificationService;

class NotificationsDropdown extends Component
{
    public $notifications;
    public $unreadCount;
    public $locRequestCount;
    public $docRequestCount;
    public $obRequestCount;
    public $pendingChangeCount;
    public $latestDocNotifDate;
    public $latestLocNotifDate;
    public $latestObNotifDate;
    public $latestPendingChangeNotifDate;

    protected NotificationService $notificationService;

    public function __construct()
    {
        $this->notificationService = app(NotificationService::class);
    }

    public function mount()
    {
        $this->refreshNotifications();
    }

    public function refreshNotifications()
    {
        $user = Auth::user();
        
        if (in_array($user->user_role, ['sa', 'hr'])) {
            $this->refreshAdminNotifications();
        } elseif ($user->user_role === 'sv') {
            $this->refreshSupervisorNotifications();
        } elseif ($user->user_role === 'emp') {
            $this->refreshEmployeeNotifications();
        }
    }

    private function refreshAdminNotifications()
    {
        // Get counts using service and direct queries
        $this->locRequestCount = WfhLocationRequests::where('status', 0)->count();
        
        $this->obRequestCount = OfficialBusiness::where('status', 0)
            ->whereNotNull('date_sup_approved')
            ->count();
        
        $this->docRequestCount = $this->notificationService->getUnreadCount(
            userId: null,
            notif: 'doc_request'
        );

        $this->pendingChangeCount = $this->notificationService->getUnreadCount(
            userId: null,
            notif: 'pending_changes'
        );
        
        // Get latest notification dates
        $this->latestDocNotifDate = $this->notificationService->getLatestByType('doc_request', null);
        $this->latestLocNotifDate = $this->notificationService->getLatestByType('loc_request', null);
        $this->latestPendingChangeNotifDate = $this->notificationService->getLatestByType('pending_changes', null);
        $this->latestObNotifDate = $this->notificationService->getLatestByType('ob_request', null);
        
        // Build notifs array
        $notifs = ['doc_request', 'loc_request', 'pending_changes', 'ob_request', 'leave_request'];
        

        // Get notifications grouped by notifs
        $this->notifications = $this->notificationService->getUnread(
            userId: null,
            notif: $notifs
        )->groupBy('notif')->map(function ($group) {
            return [
                'notif' => $group->first()->notif,
                'unread_count' => $group->count(),
                'latest' => $group->first(),
            ];
        });
        
        // Get total unread count
        $this->unreadCount = $this->notificationService->getTotalUnreadCount(
            userId: null,
        );
    }

    private function refreshSupervisorNotifications()
    {
        $user = Auth::user();
        $this->obRequestCount = OfficialBusiness::where('status', 0)
            ->whereNull('approver')
            ->whereNull('date_sup_approved')
            ->count();

        $this->latestObNotifDate = $this->notificationService->getLatestByType('obrequest', null);

        $this->notifications = $this->notificationService->getUnread(
            userId: $user->id,
            notif: ['ob_request', 'leave_request']
        )->groupBy('type')->map(function ($group) {
            return [
                'notif' => $group->first()->notif,
                'unread_count' => $group->count(),
            ];
        });
                
        $this->unreadCount = $this->obRequestCount;
    }

    private function refreshEmployeeNotifications()
    {
        $user = Auth::user();
        
        $notifs = ['doc_request', 'loc_request', 'pending_changes', 'ob_request', 'leave_request'];
        
        $notifications = $this->notificationService->getUnread(
            userId: $user->id,
            notif: $notifs
        );

        $this->notifications = $notifications->groupBy('notif')
            ->map(function ($group) {
                return [
                    'notif' => $group->first()->notif,
                    'count' => $group->count(),
                    'read' => $group->first()->read,
                    'latest' => $group->first(),
                    'ids' => $group->pluck('id')->toArray(),
                ];
            });
            
        $this->unreadCount = $notifications->count();
    }

    public function markGroupAsRead($notif)
    {
        $user = Auth::user();
        
        if (in_array($user->user_role, ['sa', 'hr'])) {
            $this->notificationService->markAsRead(
                userId: null,
                notif: $notif
            );
        } else {
            // Mark user-specific notifications as read
            $this->notificationService->markAsRead(
                userId: $user->id,
                notif: $notif
            );
        }
        
        $this->refreshNotifications();
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        
        if (in_array($user->user_role, ['sa', 'hr'])) {
            // Mark all admin notifications as read
            $notifs = ['doc_request', 'loc_request', 'pending_changes', 'ob_request', 'leave_request'];
            $this->notificationService->markAllAsRead(
                userId: null,
                notif: $notifs
            );
        } elseif ($user->user_role === 'sv') {
            $this->notificationService->markAllAsRead(
                userId: null,
                notif: ['ob_request', 'leave_request']
            );
        } else {
            // Mark all employee notifications as read
            $this->notificationService->markAllAsRead(
                userId: $user->id
            );
        }
        
        $this->refreshNotifications();
    }

    private function getDocumentTypeLabel($documentType)
    {
        $documentTypes = [
            'employment' => 'Certificate of Employment',
            'employmentCompensation' => 'Certificate of Employment with Compensation',
            'leaveCredits' => 'Certificate of Leave Credits',
            'ipcrRatings' => 'Certificate of IPCR Ratings',
        ];
        return $documentTypes[$documentType] ?? $documentType;
    }

    private function getDocRequestMessage()
    {
        if ($this->docRequestCount === 1) {
            return '1 new document request pending for approval';
        }
        return ($this->docRequestCount) . ' pending document requests for approval';
    }

    private function getLocRequestMessage()
    {
        if ($this->locRequestCount === 1) {
            return '1 new WFH location request pending for approval';
        }
        return ($this->locRequestCount) . ' pending WFH location requests for approval';
    }

    private function getOBRequestMessage()
    {
        if ($this->obRequestCount === 1) {
            return '1 new OB request pending for approval';
        }
        return ($this->obRequestCount) . ' pending OB requests for approval';
    }

    private function getPendingChangeMessage()
    {
        if ($this->pendingChangeCount === 1) {
            return '1 employee information change pending for approval';
        }
        return ($this->pendingChangeCount) . ' employee information changes pending for approval';
    }

    public function render()
    {
        return view('livewire.notification.notifications-dropdown', [
            'notifications' => $this->notifications,
            'unreadCount' => $this->unreadCount,
        ]);
    }
}