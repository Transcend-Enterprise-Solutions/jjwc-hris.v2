<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\DownloadableForm;
use Illuminate\Support\Facades\Storage;

class DownloadableForms extends Component
{
    use WithFileUploads;

    public $name;
    public $file;
    public $forms;
    public $showModal = false;
    public $showDeleteModal = false;
    public $isEditing = false;
    public $editingId;
    public $deleteId;

    protected $rules = [
        'name' => 'required|string|max:255',
        'file' => 'required|file|mimes:pdf,doc,docx,xlsx,xls,txt,png,jpg,jpeg|max:10240',
    ];

    protected $messages = [
        'name.required' => 'Form name is required.',
        'file.required' => 'Please select a file to upload.',
        'file.mimes' => 'File must be a PDF, DOC, DOCX, XLS, XLSX, TXT, or image file.',
        'file.max' => 'File size cannot exceed 10MB.',
    ];

    public function mount()
    {
        $this->loadForms();
    }

    public function loadForms()
    {
        $this->forms = DownloadableForm::orderBy('created_at', 'desc')->get();
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
        $this->resetValidation();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function save()
    {
        if ($this->isEditing) {
            $this->rules['file'] = 'nullable|file|mimes:pdf,doc,docx,xlsx,xls,txt,png,jpg,jpeg|max:10240';
        }

        $this->validate();

        try {
            if ($this->isEditing) {
                $this->updateForm();
            } else {
                $this->createForm();
            }

            $this->closeModal();
            $this->loadForms();

            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => $this->isEditing ? 'Form updated successfully!' : 'Form added successfully!',
                'icon' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    private function createForm()
    {
        $originalName = $this->file->getClientOriginalName();
        $filePath = $this->file->storeAs('downloadable-forms', $originalName, 'public');

        DownloadableForm::create([
            'name' => $this->name,
            'file_path' => $filePath,
            'original_name' => $originalName,
        ]);
    }

    private function updateForm()
    {
        $form = DownloadableForm::findOrFail($this->editingId);
        $form->name = $this->name;

        if ($this->file) {
            if ($form->file_path && Storage::disk('public')->exists($form->file_path)) {
                Storage::disk('public')->delete($form->file_path);
            }

            $originalName = $this->file->getClientOriginalName();
            $filePath = $this->file->storeAs('downloadable-forms', $originalName, 'public');
            $form->file_path = $filePath;
            $form->original_name = $originalName;
        }

        $form->save();
    }

    public function downloadFile($id)
    {
        $form = DownloadableForm::findOrFail($id);
        $filePath = storage_path('app/public/' . $form->file_path);

        if (!Storage::disk('public')->exists($form->file_path)) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'File not found!',
                'icon' => 'error'
            ]);
            return;
        }

        return response()->download($filePath, $form->original_name);
    }

    public function edit($id)
    {
        $form = DownloadableForm::findOrFail($id);
        $this->name = $form->name;
        $this->isEditing = true;
        $this->editingId = $id;
        $this->openModal();
    }

    public function deleteForm()
    {
        try {
            $form = DownloadableForm::findOrFail($this->deleteId);

            if ($form->file_path && Storage::disk('public')->exists($form->file_path)) {
                Storage::disk('public')->delete($form->file_path);
            }

            $form->delete();
            $this->loadForms();
            $this->closeDeleteModal();

            $this->dispatch('swal', [
                'title' => 'Success!',
                'text' => 'Form deleted successfully!',
                'icon' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text' => 'Error deleting form: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function resetFields()
    {
        $this->name = '';
        $this->file = null;
        $this->isEditing = false;
        $this->editingId = null;
    }

    protected $listeners = [
        'swalConfirmed' => 'handleSwalConfirmed'
    ];


    public function render()
    {
        return view('livewire.admin.downloadable-forms');
    }
}
