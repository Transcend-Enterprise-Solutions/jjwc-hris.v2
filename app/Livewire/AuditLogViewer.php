<?php

namespace App\Livewire;

use App\Models\CosRegPayrolls;
use App\Models\CosRegPayslip;
use Livewire\Component;
use Livewire\WithPagination;
use OwenIt\Auditing\Models\Audit;
use App\Models\User;
use App\Models\DocRequest;
use App\Models\DTRSchedule;
use App\Models\Holiday;
use App\Models\LeaveApplication;
use App\Models\Payrolls;
use App\Models\PlantillaPayslip;

class AuditLogViewer extends Component
{
    use WithPagination;

    public $search = '';
    public $pageSize = 10; 
    public $pageSizes = [10, 20, 30, 50, 100]; 
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $dateFrom = '';
    public $dateTo = '';

    protected $queryString = ['search', 'sortField', 'sortDirection', 'dateFrom', 'dateTo'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedPageSize()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';
        $audits = Audit::with('user')
            ->where(function ($query) use ($searchTerm) {
                $query->where('event', 'like', $searchTerm)
                      ->orWhere(function ($subQuery) use ($searchTerm) {
                          $subQuery->whereRaw("JSON_CONTAINS(LOWER(new_values), LOWER(?), '$')", [$this->search])
                                   ->orWhereRaw("JSON_CONTAINS(LOWER(old_values), LOWER(?), '$')", [$this->search]);
                      })
                      ->orWhereHas('user', function ($query) use ($searchTerm) {
                          $query->where('name', 'like', $searchTerm);
                      })
                      ->orWhereRaw("LOWER(CONCAT(
                          'User ',
                          COALESCE((SELECT name FROM users WHERE id = audits.user_id), 'System'),
                          ' ',
                          audits.event,
                          ' ',
                          CASE
                              WHEN audits.auditable_type = 'App\\Models\\DocRequest' THEN 'document request'
                              WHEN audits.auditable_type = 'App\\Models\\DTRSchedule' THEN 'schedule'
                              WHEN audits.auditable_type = 'App\\Models\\Holiday' THEN 'holiday'
                              WHEN audits.auditable_type = 'App\\Models\\LeaveApplication' THEN 'leave application'
                              WHEN audits.auditable_type = 'App\\Models\\Payrolls' THEN 'payroll'
                              WHEN audits.auditable_type = 'App\\Models\\PlantillaPayslip' THEN 'payroll'
                              WHEN audits.auditable_type = 'App\\Models\\CosRegPayrolls' THEN 'payroll'
                              WHEN audits.auditable_type = 'App\\Models\\CosRegPayslip' THEN 'payroll'
                              ELSE audits.auditable_type
                          END,
                          ' (ID: ',
                          audits.auditable_id,
                          ').'
                      )) LIKE ?", [$searchTerm]);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pageSize);

        foreach ($audits as $audit) {
            $audit->resolved_new_values = $this->resolveValues($audit, $audit->new_values);
            $audit->resolved_old_values = $this->resolveValues($audit, $audit->old_values);
        }
        return view('livewire.audit-log-viewer', [
            'audits' => $audits,
        ]);
    }

    private function resolveValues($audit, $values)
    {
        $resolved = $values;

        if ($audit->auditable_type === DocRequest::class) {
            if (isset($resolved['user_id'])) {
                $user = User::find($resolved['user_id']);
                $resolved['user_name'] = $user ? $user->name : 'Unknown User';
            }
        } elseif ($audit->auditable_type === DTRSchedule::class) {
            if (isset($resolved['emp_code'])) {
                $user = User::where('emp_code', $resolved['emp_code'])->first();
                $resolved['employee_name'] = $user ? $user->name : 'Unknown Employee';
            }
        } elseif ($audit->auditable_type === LeaveApplication::class) {
            if (isset($resolved['user_id'])) {
                $user = User::find($resolved['user_id']);
                $resolved['user_name'] = $user ? $user->name : 'Unknown User';
            }
            if (isset($resolved['type_of_leave'])) {
                $resolved['leave_type'] = $resolved['type_of_leave'];
            }
        }elseif ($audit->auditable_type === Payrolls::class) {
            if (isset($resolved['user_id'])) {
                $user = User::find($resolved['user_id']);
                $resolved['employee_name'] = $user ? $user->name : 'Unknown Employee';
            }
        }elseif ($audit->auditable_type === PlantillaPayslip::class) {
            if (isset($resolved['user_id'])) {
                $user = User::find($resolved['user_id']);
                $resolved['employee_name'] = $user ? $user->name : 'Unknown Employee';
            }
        }
        elseif ($audit->auditable_type === CosRegPayrolls::class) {
            if (isset($resolved['user_id'])) {
                $user = User::find($resolved['user_id']);
                $resolved['employee_name'] = $user ? $user->name : 'Unknown Employee';
            }
        }
        elseif ($audit->auditable_type === CosRegPayslip::class) {
            if (isset($resolved['user_id'])) {
                $user = User::find($resolved['user_id']);
                $resolved['employee_name'] = $user ? $user->name : 'Unknown Employee';
            }
        }
        elseif ($audit->auditable_type === CosRegPayslip::class) {
            if (isset($resolved['user_id'])) {
                $user = User::find($resolved['user_id']);
                $resolved['employee_name'] = $user ? $user->name : 'Unknown Employee';
            }
        }

        return $resolved;
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="flex w-full flex-col gap-2">
            <livewire:skeleton/>
        </div>
        HTML;
    }

    private function maskEmail($email)
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';
        $maskedName = substr($name, 0, 2) . str_repeat('*', strlen($name) - 2);
        return $maskedName . '@' . $domain;
    }
}