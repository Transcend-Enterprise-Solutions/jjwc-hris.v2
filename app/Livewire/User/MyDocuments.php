<?php
namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\EmployeeDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('My Documents')]
class MyDocuments extends Component
{
    use WithFileUploads;

    public $file;
    public $droppedFile;
    public $documentType = '';
    public $error = '';
    public $message = '';
    public $isUploading = false;
    public $isDeleting = false;
    public $confirmDeleteModal = false;
    public $documentToDelete = null;
    public $fileSelected = false;

    protected $rules = [
        'file' => 'required|file|mimes:pdf|max:5020',
        'documentType' => 'required|string|max:255',
    ];

    protected $listeners = ['file-dropped' => 'handleDroppedFile'];

    public function mount()
    {
        $this->file = null;
    }

    public function handleDroppedFile($fileData)
    {
        $this->droppedFile = $fileData;
        $this->fileSelected = true;
        $this->dispatch('swal', ['title' => 'File selected please click upload document to save', 'icon' => 'success']);
    }

    public function uploadDocument()
    {
        $this->validate();

        $this->isUploading = true;

        try {
            $fileName = $this->file->getClientOriginalName();
            $filePath = $this->file->storeAs('public/upload/employee_document', $fileName);

            EmployeeDocument::create([
                'user_id' => Auth::id(),
                'document_type' => $this->documentType,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'mime_type' => $this->file->getMimeType(),
                'file_size' => $this->file->getSize(),
            ]);

            $this->reset(['file', 'documentType']);
            $this->fileSelected = false;
            $this->dispatch('swal', ['title' => 'Document uploaded successfully!', 'icon' => 'success']);
            $this->dispatch('documentUploaded');
        } catch (\Exception $e) {
            $this->error = 'Error uploading document: ' . $e->getMessage();
        } finally {
            $this->isUploading = false;
        }
    }

    public function clearDroppedFile()
    {
        $this->droppedFile = null;
        $this->fileSelected = false;
    }

    protected function documentAlreadyUploaded()
    {
        return EmployeeDocument::where('user_id', Auth::id())
            ->where('document_type', $this->documentType)
            ->exists();
    }

    public function confirmDelete($documentId)
    {
        $this->documentToDelete = $documentId;
        $this->confirmDeleteModal = true;
    }

    public function deleteDocument()
    {
        $this->isDeleting = true;

        $document = EmployeeDocument::find($this->documentToDelete);

        if ($document && $document->user_id === Auth::id()) {
            Storage::delete($document->file_path);
            $document->delete();
            $this->dispatch('swal', ['title' => 'Document deleted successfully!', 'icon' => 'success']);
        } else {
            $this->dispatch('swal', ['title' => 'Document not found or unauthorized!', 'icon' => 'error']);
        }

        $this->isDeleting = false;
        $this->confirmDeleteModal = false;
        $this->documentToDelete = null;
    }

    public function downloadDocument($documentId)
    {
        $document = EmployeeDocument::findOrFail($documentId);
        return Storage::download($document->file_path, $document->file_name, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $document->file_name . '"',
        ]);
    }

    public function availableDocumentTypes()
    {
        return [
            '201_Documents' => '201 Documents',
            'SALN' => 'Statement of Assets, Liabilities and Net Worth (SALN)',
            'IPCR' => 'Individual Performance Commitment Review (IPCR)',
            'BIR1902' => 'BIR Form 1902',
            'BIR1905' => 'BIR Form 1905',
            'BIR2316' => 'BIR Form 2316',
            'COE' => 'Certificate of Employment',
            'Service Record' => 'Service Record',
            'Notarized PDS' => 'Notarized PDS',
            'Others' => 'Others',
        ];
    }

    public function getVersionedDocuments(): Collection
    {
        $documents = EmployeeDocument::where('user_id', Auth::id())
            ->orderBy('document_type')
            ->orderBy('created_at', 'desc')
            ->get();

        return $documents->groupBy('document_type')
            ->map(function ($group) {
                $count = $group->count();
                return $group->map(function ($doc, $index) use ($count) {
                    $doc->version = 'v' . ($count - $index);
                    return $doc;
                });
            })
            ->flatten();
    }

    public function render()
    {
        $versionedDocuments = $this->getVersionedDocuments();
        $availableDocumentTypes = $this->availableDocumentTypes();

        return view('livewire.user.my-documents', [
            'documents' => $versionedDocuments,
            'availableDocumentTypes' => $availableDocumentTypes,
            'isUploading' => $this->isUploading,
            'isDeleting' => $this->isDeleting,
            'confirmDeleteModal' => $this->confirmDeleteModal,
        ]);
    }
}
