<?php

namespace App\Livewire\Admin\Employee;

use App\Models\ESignature;
use App\Models\User;
use App\Models\WESESigSettings;
use Livewire\Component;
use App\Models\WorkExperienceSheetTable as WES;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class WorkExperienceSheet extends Component
{
    public $selectedUser;
    public $pdfContent;


    public function mount($userId)
    {
        $user = User::with(['position', 'officeDivision', 'officeDivisionUnit', 'userData', 'contracts'])
            ->find($userId);

        if ($user) {
            $this->selectedUser = $user;
            $eSignature = ESignature::where('user_id', $user->id)->first();
            $signatureImagePath = null;
            if ($eSignature && $eSignature->file_path) {
                $signatureImagePath = Storage::disk('public')->path($eSignature->file_path);
            }

            $myWorkExperiences = WES::where('user_id', $user->id)
                ->orderBy('toPresent', 'desc')
                ->orderBy('start_date', 'desc')
                ->get();

            $sigXPos = 110;
            $sigYPos = -50;
            $sigSize = 100;
            
            $wesSetting = WESESigSettings::where('user_id', $user->id)->first();
            if($wesSetting){
                $sigXPos = $wesSetting->pos_x;
                $sigYPos = $wesSetting->pos_y;
                $sigSize = $wesSetting->size;
            }


            $pdf = PDF::loadView('pdf.wes', [
                'name' => $user->name,
                'myWorkExperiences' => $myWorkExperiences,
                'signatureImagePath' => $signatureImagePath,
                'sigXPos' => $sigXPos,
                'sigYPos' => $sigYPos,
                'sigSize' => $sigSize,
            ]);

            $this->pdfContent = base64_encode($pdf->output());
        }
    }

    public function render()
    {
        return view('livewire.admin.employee.work-experience-sheet');
    }
}
