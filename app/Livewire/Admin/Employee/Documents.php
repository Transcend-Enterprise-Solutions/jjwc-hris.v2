<?php

namespace App\Livewire\Admin\Employee;

use App\Models\EmployeeDocument;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Documents extends Component
{
    public $documents;
    public $documentName;
    public $selectedUser;
    public $pdfContent;


    public function mount($userId)
    {
        $user = User::with(['position', 'officeDivision', 'officeDivisionUnit', 'userData', 'contracts'])
            ->find($userId);

        if ($user) {
            $this->documents = EmployeeDocument::where('user_id', $user->id)->get();

            $this->selectedUser = $user;
        }
    }

    public function render()
    {
        return view('livewire.admin.employee.documents');
    }

    public function downloadDocument($id)
    {
        $document = EmployeeDocument::findOrFail($id);
        return Storage::download($document->file_path, $document->file_name, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $document->file_name . '"',
        ]);
    }

    public function viewDocument($id){
        $document = EmployeeDocument::findOrFail($id);
        $this->documentName = $document->document_type;
        if (Storage::disk('local')->exists($document->file_path)) {
            $filePath = Storage::disk('local')->path($document->file_path);
            
            if (mime_content_type($filePath) === 'application/pdf') {
                $this->pdfContent = base64_encode(Storage::disk('local')->get($document->file_path));
            }
        }else{
            $this->dispatch('swal', [
                'title' => 'File not found!',
                'icon' => 'error'
            ]);
        }
    }
}
