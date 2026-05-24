<?php


namespace App\Livewire;

use Livewire\Component;
use App\Models\AuditLog;
use Livewire\WithPagination;

class ApiAuditLogComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $type = '';

    protected $queryString = ['search', 'type'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = AuditLog::query()
            ->when($this->search, function ($query) {
                $query->where('message', 'like', '%' . $this->search . '%');
            })
            ->when($this->type, function ($query) {
                $query->where('type', $this->type);
            })
            ->orderByDesc('created_at');

        $auditLogs = $query->paginate(10);

        return view('livewire.api-audit-log-component', [
            'auditLogs' => $auditLogs,
        ]);
    }
    public function placeholder()
    {
        return <<<'HTML'
        <div class="flex w-full flex-col gap-2">
            <livewire:skeleton/>
        </div>
        HTML;
    }
}