<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Documentation')]
class Documentation extends Component
{
    public string $pdfBase64 = '';
    public string $manualTitle = '';
    public bool $pdfReady = false;
 
    public function mount(): void
    {
        $user = Auth::user();

        if ($user && $user->user_role != 'emp') {
            $filename = 'admin.pdf';
            $this->manualTitle = 'Administrator User Manual';
        } else {
            $filename = 'employee.pdf';
            $this->manualTitle = 'Employee User Manual';
        }

        $path = 'manuals/' . $filename;

        if (Storage::disk('local')->exists($path)) {
            $this->pdfBase64 = base64_encode(
                Storage::disk('local')->get($path)
            );
            $this->pdfReady = true;
        }
    }

    public function render()
    {
        return view('livewire.documentation');
    }
}
