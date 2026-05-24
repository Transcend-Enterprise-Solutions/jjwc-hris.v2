<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\DownloadableForm;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Forms')]
class DownloadableForms extends Component
{
    public $forms;
    public $search = '';

    public function mount()
    {
        $this->loadForms();
    }

    public function loadForms()
    {
        $this->forms = DownloadableForm::orderBy('name', 'asc')->get();
    }

    public function getFilteredFormsProperty()
    {
        if (empty($this->search)) {
            return $this->forms;
        }

        return $this->forms->filter(function ($form) {
            return str_contains(strtolower($form->name), strtolower($this->search)) ||
                   str_contains(strtolower($form->original_name ?? basename($form->file_path)), strtolower($this->search));
        });
    }

    public function downloadFile($id)
    {
        try {
            $form = DownloadableForm::findOrFail($id);

            if (!Storage::disk('public')->exists($form->file_path)) {
                $this->dispatch('swal', [
                    'title' => 'File Not Found',
                    'text' => 'The requested file could not be found. Please contact support.',
                    'icon' => 'error'
                ]);
                return;
            }

            $filePath = storage_path('app/public/' . $form->file_path);
            $fileName = $form->original_name ?? basename($form->file_path);

            // Optional: Log download activity
            \Illuminate\Support\Facades\Log::info('Form downloaded', [
                'form_id' => $form->id,
                'form_name' => $form->name,
                'file_name' => $fileName,
                'user_ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()
            ]);

            return response()->download($filePath, $fileName);

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Download Error',
                'text' => 'There was an error downloading the file. Please try again.',
                'icon' => 'error'
            ]);

            \Illuminate\Support\Facades\Log::error('Form download error', [
                'form_id' => $id,
                'error' => $e->getMessage(),
                'user_ip' => request()->ip()
            ]);
        }
    }

    public function updatedSearch()
    {
        // Automatically filter when search is updated
        // The filtered forms will be recalculated via the computed property
    }

    public function clearSearch()
    {
        $this->search = '';
    }

    public function render()
    {
        return view('livewire.user.downloadable-forms', [
            'filteredForms' => $this->filteredForms
        ]);
    }
}
