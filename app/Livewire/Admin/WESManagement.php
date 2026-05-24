<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\WESESigSettings;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ESignature;
use App\Models\WorkExperienceSheetTable as WES;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Work Experience Sheet')]
class WESManagement extends Component
{
    use WithPagination;

    public $search;
    public $pdfContent;
    public $employeeName;
    public $showWorkExpSheet;
    public $pageSize = 10; 
    public $pageSizes = [10, 20, 30, 50, 100]; 


    public function render()
    {
        $users = User::whereHas('workExperienceSheet')
                ->with('workExperienceSheet')
                ->when($this->search, function ($query) {
                    return $query->search4(trim($this->search));
                })
                ->withCount(['workExperienceSheet as total_exp' => function ($query) {
                    $query->select(DB::raw('SUM(
                            CASE
                                WHEN toPresent = 1 THEN TIMESTAMPDIFF(MONTH, start_date, CURDATE())
                                WHEN end_date IS NOT NULL THEN TIMESTAMPDIFF(MONTH, start_date, end_date)
                                ELSE 0
                            END
                        )'));
                }])
                ->paginate($this->pageSize);

        
        foreach ($users as $user) {
            $totalMonths = $user->total_exp;
            $years = floor($totalMonths / 12);
            $months = $totalMonths - ($years * 12);
            $user->formatted_exp = $this->formatExperience($years, $months);
        }
    
        return view('livewire.admin.w-e-s-management', [
            'users' => $users,
        ]);
    }

    private function formatExperience($years, $months)
    {
        $result = [];
        if ($years > 0) {
            $result[] = $years . ' ' . ($years == 1 ? 'year' : 'years');
        }
        if ($months > 0) {
            $result[] = $months . ' ' . ($months == 1 ? 'month' : 'months');
        }
        return empty($result) ? '0 months' : implode(' ', $result);
    }

    public function showPDF($userId){
        $this->showWorkExpSheet = true;
        $name = User::where('id', $userId)->first()->name;
        $eSignature = ESignature::where('user_id', $userId)->first();
        $signatureImagePath = null;
        if ($eSignature && $eSignature->file_path) {
            $signatureImagePath = Storage::disk('public')->path($eSignature->file_path);
        }

        $myWorkExperiences = WES::where('user_id', $userId)
                ->orderBy('toPresent', 'desc')
                ->orderBy('start_date', 'desc')
                ->get();

        $this->employeeName = User::where('id', $userId)->first()->name;

        $sigXPos = 110;
        $sigYPos = -50;
        $sigSize = 100;
        
        $wesSetting = WESESigSettings::where('user_id', $userId)->first();
        if($wesSetting){
            $sigXPos = $wesSetting->pos_x;
            $sigYPos = $wesSetting->pos_y;
            $sigSize = $wesSetting->size;
        }


        $pdf = PDF::loadView('pdf.wes', [
            'name' => $name,
            'myWorkExperiences' => $myWorkExperiences,
            'signatureImagePath' => $signatureImagePath,
            'sigXPos' => $sigXPos,
            'sigYPos' => $sigYPos,
            'sigSize' => $sigSize,
        ]);

        $this->pdfContent = base64_encode($pdf->output());
    }

    public function closeWorkExpSheet(){
        $this->showWorkExpSheet = null;
        $this->pdfContent = null;
        $this->employeeName = null;
    }
}
