<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CaseTracking;
use App\Models\GeneratedDocument;
use App\Models\GeneratedNotice;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('My Administrative Cases')]
class EmpAdministrativeCases extends Component
{
    use WithPagination, WithFileUploads;

    public $caseSearch = '';
    public $casePageSize = 5;
    public $casePageSizes = [5, 10, 20, 30, 50, 100];
    public $uploadFile;
    public $currentCaseId;
    public $showUploadModal = false;

    public function downloadDocument($documentId)
    {
        $document = GeneratedDocument::findOrFail($documentId);
        
        if (!Storage::exists($document->file_path)) {
            $this->dispatch('swal', [
                'title' => "File Not Found",
                'icon' => 'error'
            ]);
            return;
        }

        return Storage::download($document->file_path, $document->file_name);
    }

    public function downloadNotice($noticeId)
    {
        $notice = GeneratedNotice::findOrFail($noticeId);
        
        if (!Storage::exists($notice->file_path)) {
            $this->dispatch('swal', [
                'title' => "File Not Found",
                'icon' => 'error'
            ]);
            return;
        }

        return Storage::download($notice->file_path, $notice->file_name);
    }

    public function setCurrentCase($caseId)
    {
        $this->currentCaseId = $caseId;
        $this->showUploadModal = true;
    }

    public function uploadExplanation()
    {
        $this->validate([
            'uploadFile' => 'required|file|mimes:pdf|max:10240',
        ]);

        $case = CaseTracking::findOrFail($this->currentCaseId);
        
        // Store the file
        $filePath = $this->uploadFile->store('case_explanations');
        $fileName = $this->uploadFile->getClientOriginalName();
        
        // Update the case tracking
        $case->update([
            'answered_file_path' => $filePath,
            'answered_date' => now(),
        ]);

        $this->dispatch('swal', [
            'title' => "File Uploaded Successfully",
            'icon' => 'success'
        ]);

        $this->reset(['uploadFile', 'currentCaseId', 'showUploadModal']);
    }

    public function downloadExplanation($caseId)
    {
        $case = CaseTracking::findOrFail($caseId);
        
        if (!$case->answered_file_path || !Storage::exists($case->answered_file_path)) {
            $this->dispatch('swal', [
                'title' => "File Not Found",
                'icon' => 'error'
            ]);
            return;
        }

        return Storage::download($case->answered_file_path, 'MyExplanation'.$case->admin_case_number.'.pdf');
    }

    public function render()
    {
        $casesQuery = CaseTracking::with(['document', 'notice'])
            ->where('employee_id', Auth::id())
            ->when($this->caseSearch, function ($query) {
                $query->where(function($q) {
                    $q->whereHas('document', function($q) {
                        $q->where('document_type', 'like', '%' . $this->caseSearch . '%')
                          ->orWhere('file_name', 'like', '%' . $this->caseSearch . '%');
                    })
                    ->orWhereHas('notice', function($q) {
                        $q->where('notice_type', 'like', '%' . $this->caseSearch . '%');
                    })
                    ->orWhere('status', 'like', '%' . $this->caseSearch . '%')
                    ->orWhere('admin_case_number', 'like', '%' . $this->caseSearch . '%');
                });
            })
            ->latest();

        return view('livewire.user.emp-administrative-cases', [
            'cases' => $casesQuery->paginate($this->casePageSize, ['*'], 'casePage')
        ]);
    }
}