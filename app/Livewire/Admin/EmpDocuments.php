<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EmployeeDocument;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Employee Documents')]
class EmpDocuments extends Component
{
    use WithPagination;

    public $employeesWithoutUpload = [];
    public $tabs;
    public $documentToDelete = null;
    public $isDeleting = false;
    public $perPage = 10;
    public $selectedTab = '201_Documents';

    protected $queryString = ['selectedTab'];

    public function mount()
    {
        $this->tabs = [
            '201_Documents' => '201 Documents',
            'SALN' => 'SALN',
            'IPCR' => 'IPCR',
            'BIR1902' => 'BIR Form1902',
            'BIR1905' => 'BIR Form1905',
            'BIR2316' => 'BIR Form2316',
            'COE' => 'Certificate of Employment',
            'Service Record' => 'Service Record',
            'Notarized PDS' => 'PDS',
        ];

        $this->loadEmployeesWithoutUpload();
    }
    public function downloadDocument($documentId)
    {
        $document = EmployeeDocument::findOrFail($documentId);

        // Check if the file exists in storage
        if (Storage::exists($document->file_path)) {
            // Use response()->download to ensure proper headers and download
            return Storage::download($document->file_path, $document->file_name);
        } else {
            // Handle file not found
            $this->dispatch('swal', [
                'title' => 'File not found!',
                'icon' => 'error'
            ]);
        }
    }

    public function loadEmployeesWithoutUpload()
    {
        $this->employeesWithoutUpload = [];
        $allEmployees = User::where('user_role', 'emp')->get();

        foreach ($this->tabs as $key => $label) {
            $employeesWithDocument = EmployeeDocument::where('document_type', $key)
                                        ->pluck('user_id')
                                        ->toArray();

            $this->employeesWithoutUpload[$key] = $allEmployees->reject(function ($employee) use ($employeesWithDocument) {
                return in_array($employee->id, $employeesWithDocument);
            });
        }
    }

    public function confirmDelete($id)
    {
        $this->documentToDelete = $id;
        $this->dispatch('show-delete-modal');
    }

    public function deleteRequest()
    {
        if ($this->documentToDelete) {
            $this->isDeleting = true;

            try {
                $document = EmployeeDocument::findOrFail($this->documentToDelete);

                if (Storage::exists($document->file_path)) {
                    Storage::delete($document->file_path);
                }
                $document->delete();

                $this->loadEmployeesWithoutUpload();
                $this->dispatch('swal', [
                    'title' => 'Document deleted successfully!',
                    'icon' => 'success'
                ]);
            } catch (\Exception $e) {
                $this->dispatch('swal', [
                    'title' => 'An error occurred while deleting the document.',
                    'icon' => 'error'
                ]);
            } finally {
                $this->isDeleting = false;
                $this->documentToDelete = null;
            }
        } else {
            $this->dispatch('swal', [
                'title' => 'No document selected for deletion!',
                'icon' => 'error'
            ]);
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function getDocumentsProperty()
    {
        return EmployeeDocument::where('document_type', $this->selectedTab)
            ->with('user')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin.emp-documents', [
            'documents' => $this->documents,
            'employeesWithoutUpload' => $this->employeesWithoutUpload,
            'tabs' => $this->tabs
        ]);
    }
}
