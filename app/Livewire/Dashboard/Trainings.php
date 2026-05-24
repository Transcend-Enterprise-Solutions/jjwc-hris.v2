<?php

namespace App\Livewire\Dashboard;

use App\Models\EmployeesTrainings;
use Livewire\Component;
use App\Models\Trainings as TrainingsModel;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Trainings extends Component
{
    
    public $availableTrainings = [];
    
    public function mount()
    {
        $this->loadAvailableTrainings();
    }

    public function loadAvailableTrainings()
    {
        $user = Auth::user();
        
        if (!$user) {
            $this->availableTrainings = [];
            return;
        }

        $trainings = TrainingsModel::where('date_start', '>=', now())
            ->orderBy('date_start', 'asc')
            ->get();

        $eligibleTrainings = [];

        foreach ($trainings as $training) {
            if ($this->isUserEligible($training, $user)) {
                if (!$this->hasUserApplied($training->id, $user->id)) {
                    $eligibleTrainings[] = $training;
                }
            }
        }

        $this->availableTrainings = $eligibleTrainings;
    }

    private function isUserEligible($training, $user)
    {
        switch ($training->target_audience_type) {
            case 'all':
                return true;
            case 'division':
                if ($training->target_audience_ids) {
                    $targetDivisionIds = explode(',', $training->target_audience_ids);
                    return in_array($user->office_division_id, $targetDivisionIds);
                }
                return false;
                
            case 'unit':
                if ($training->target_audience_ids) {
                    $targetUnitIds = explode(',', $training->target_audience_ids);
                    return in_array($user->unit_id, $targetUnitIds);
                }
                return false;
            case 'employees':
                if ($training->target_audience_ids) {
                    $targetUserIds = explode(',', $training->target_audience_ids);
                    return in_array($user->id, $targetUserIds);
                }
                return false;
            case 'sg':
                if ($training->target_audience_ids) {
                    $targetSgIds = explode(',', $training->target_audience_ids);

                    $userSg = null;
                    
                    if ($user->userData->appointment === 'plantilla') {
                        $userSgStep = DB::table('payrolls')
                                    ->where('user_id', $user->id)
                                    ->value('sg_step');
                        
                        if ($userSgStep) {
                            $sgParts = explode('-', $userSgStep);
                            $userSg = $sgParts[0];
                        }
                    } else {
                        $userSg = DB::table('cos_reg_payrolls')
                                ->where('user_id', $user->id)
                                ->value('sg_step');
                    }
                    
                    return $userSg && in_array($userSg, $targetSgIds);
                }
                return false;
            default:
                return false;
        }
    }

    private function hasUserApplied($trainingId, $userId)
    {
        return EmployeesTrainings::where('training_id', $trainingId)
            ->where('user_id', $userId)
            ->exists();
    }

    public function applyForTraining($trainingId)
    {
        $training = TrainingsModel::find($trainingId);
        try{    
            if($training){
                EmployeesTrainings::create([
                    'user_id' => Auth::user()->id,
                    'training_id' => $training->id,
                    'lto' => $training->lto,
                    'type' => $training->type,
                    'program_title' => $training->program_title,
                    'training_provider' => $training->training_provider,
                    'date_start' => $training->date_start,
                    'date_end' => $training->date_end,
                    'venue' => $training->venue,
                    'status' => 'pending',
                ]);
            }
            $this->dispatch('swal', [
                'title' => 'Applied successfully',
                'icon' => 'success'
            ]);

            $this->loadAvailableTrainings();
        }catch(Exception $e){
            throw $e;
        }

        // return redirect()->route('/training-and-development/seminars-and-trainings', ['id' => $trainingId]);
    }

    public function downloadDocument($id, $file = null)
    {
        $request = TrainingsModel::findOrFail($id);
        if ($request->attachment && Storage::disk('public')->exists($request->attachment)) {
            $fileContent = Storage::disk('public')->get($request->attachment);
            $fileName = basename($request->attachment);

            return response()->streamDownload(function () use ($fileContent) {
                echo $fileContent;
            }, $fileName);
        } else {
            $this->dispatch('swal', [
                'title' => 'File not found!',
                'icon' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.dashboard.trainings');
    }
}
