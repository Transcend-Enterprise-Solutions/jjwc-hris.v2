<?php

namespace App\Livewire\User\PersonalData;

use Illuminate\Support\Facades\Auth;
use App\Models\ESignature as ESig;
use App\Models\PdsPhoto;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\Component;

class ESignature extends Component
{
    use WithFileUploads;
    public $e_signature;
    public $temporaryUrl;

    public function render()
    {
        $user = Auth::user();
        $eSignature = ESig::where('user_id', $user->id)->first();

        return view('livewire.user.personal-data.e-signature', [
            'eSignature' => $eSignature,
        ]);
    }

    public function savePhoto(){
        try {
            $message = "";
            $user = Auth::user();            
            if ($user && $this->pdsPhoto instanceof UploadedFile){
                $originalFilename = $this->pdsPhoto->getClientOriginalName();
                $uniqueFilename = time() . '_' . $originalFilename;
                $photo = PdsPhoto::where('user_id', $user->id)->first();
                $pathToDelete = "";
                if($photo){
                    $pathToDelete = str_replace('public/', '', $photo->photo);
                }
                if (Storage::disk('public')->exists($pathToDelete)) {
                    Storage::disk('public')->delete($pathToDelete);
                }
                $filePath = $this->pdsPhoto->storeAs('pds-photos', $uniqueFilename, 'public');
                if($photo){
                    $photo->update([
                        'photo' => 'public/' . $filePath,
                    ]);
                    $message = "Signature updated successfully!";
                }else{
                    PdsPhoto::create([
                        'user_id' => $user->id,
                        'photo' => 'public/' . $filePath,
                    ]);
                    $message = "Signature added successfully!";
                }

            }
            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => $message,
                'icon' => "success",
            ]);
        } catch (Exception $e) {
            $this->dispatch('swal', [
                'title' => "Update was unsuccessfull!",
                'icon' => 'error'
            ]);
           throw $e;
        }
    }

    public function updatedESignature()
    {
        if ($this->e_signature) {
            $this->temporaryUrl = $this->e_signature->temporaryUrl();
        }
    }

    public function uploadSignature()
    {
        $this->validate([
            'e_signature' => 'image|max:1024',
        ]);

        $existingSignature = Esig::where('user_id', Auth::id())->first();

        if ($existingSignature && Storage::disk('public')->exists($existingSignature->file_path)) {
            Storage::disk('public')->delete($existingSignature->file_path);
        }

        $filePath = $this->e_signature->store('signatures', 'public');

        Esig::updateOrCreate(
            ['user_id' => Auth::id()],
            ['file_path' => $filePath]
        );

        $this->e_signature = null;
        $this->temporaryUrl = null;
        $this->dispatch('swal', [
            'title' => "E-Signature uploaded successfully!",
            'icon' => 'success'
        ]);
    }
}
