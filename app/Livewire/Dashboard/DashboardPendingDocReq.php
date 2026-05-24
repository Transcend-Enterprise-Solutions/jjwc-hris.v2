<?php

namespace App\Livewire\Dashboard;

use App\Models\DocRequest;
use Livewire\Component;

class DashboardPendingDocReq extends Component
{
    public $totalRequests;
    public $pendingRequests;
    public $completedRequests;
    public $requestsByType;
    public $requestsOverTime;
    public $averageCompletionTimeDays;
    public $averageCompletionTimeHours;
    public $averageCompletionTimeMinutes;

    public function mount()
    {
        $this->totalRequests = DocRequest::count();
        $this->pendingRequests = DocRequest::where('status', 'pending')->count();
        $this->completedRequests = DocRequest::where('status', 'completed')->count();

        $this->requestsByType = DocRequest::selectRaw('document_type, COUNT(*) as count')
            ->groupBy('document_type')
            ->pluck('count', 'document_type')
            ->toArray();

        $this->requestsOverTime = DocRequest::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Use created_at and updated_at for completion time calculation
        $averageSeconds = DocRequest::whereNotNull('updated_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as avg_time')
            ->value('avg_time');

        if ($averageSeconds !== null) {
            $this->averageCompletionTimeDays = floor($averageSeconds / 86400); // Days
            $averageSeconds %= 86400;

            $this->averageCompletionTimeHours = floor($averageSeconds / 3600); // Hours
            $averageSeconds %= 3600;

            $this->averageCompletionTimeMinutes = floor($averageSeconds / 60); // Minutes
        } else {
            $this->averageCompletionTimeDays = 0;
            $this->averageCompletionTimeHours = 0;
            $this->averageCompletionTimeMinutes = 0;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-pending-doc-req');
    }
}
