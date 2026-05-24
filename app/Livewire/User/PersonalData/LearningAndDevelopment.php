<?php

namespace App\Livewire\User\PersonalData;

use App\Models\User;
use App\Models\LearningAndDevelopment as LD;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LearningAndDevelopment extends Component
{
    use WithFileUploads;

    public $learnAndDevs = [];
    public $newLearnAndDevs = [];
    public $addLearningDevelopment = false;
    public $editLearningDevelopment = false;
    public $deleteId;

    public function render()
    {
        return view('livewire.user.personal-data.learning-and-development', [
            'lds' => $this->getLD(),
        ]);
    }

    protected function getLD(){
        $user = User::with(['learningAndDevelopment'])->find(Auth::user()->id);
        return $user->learningAndDevelopment;
    }

    public function toggleEditLearnAndDev(){
        $this->editLearningDevelopment = true;
        try{
            $user = User::with(['learningAndDevelopment'])->find(Auth::user()->id);
            $this->learnAndDevs = $user->learningAndDevelopment->toArray();
        }catch(Exception $e){
            throw $e;
        }
    }

    public function toggleAddLearnAndDev(){
        $this->editLearningDevelopment = true;
        $this->addLearningDevelopment = true;
        $this->newLearnAndDevs[] = [
            'title' => '', 
            'start_date' => '',
            'end_date' => '',
            'toPresent' => '',
            'no_of_hours' => '',
            'type_of_ld' => '',
            'conducted_by' => '',
            'certificate' => null,
        ];
    }

    public function addNewLearnAndDev(){
        $this->newLearnAndDevs[] = [
            'title' => '', 
            'start_date' => '',
            'end_date' => '',
            'toPresent' => '',
            'no_of_hours' => '',
            'type_of_ld' => '',
            'conducted_by' => '',
            'certificate' => null,
        ];
    }

    public function removeFile($index){
        $this->newLearnAndDevs[$index]['certificate'] = '';
    }

    public function removeNewLearnAndDev($index){
        unset($this->newLearnAndDevs[$index]);
        $this->newLearnAndDevs = array_values($this->newLearnAndDevs);
    }

    public function saveLearnAndDev(){
        try {
            $user = Auth::user();
            if ($user) {

                if($this->addLearningDevelopment != true){
                    foreach ($this->learnAndDevs as $index => $ld) {
                        $validationRules = [
                            'learnAndDevs.'.$index.'.title' => 'required|string',
                            'learnAndDevs.'.$index.'.type_of_ld' => 'required|string',
                            'learnAndDevs.'.$index.'.no_of_hours' => 'required|numeric',
                            'learnAndDevs.'.$index.'.start_date' => 'required|date',
                            'learnAndDevs.'.$index.'.conducted_by' => 'required|string',
                        ];
                
                        $this->validate($validationRules);

                        $filePath = null;
                        $ldRecord = $user->learningAndDevelopment->find($ld['id']);
                        if($ld['certificate']){
                            if(!is_string($ld['certificate'])){
                                // New file uploaded - delete old file if exists
                                if($ldRecord && $ldRecord->certificate) {
                                    Storage::disk('public')->delete($ldRecord->certificate);
                                }
                                
                                $originalFilename = $ld['certificate']->getClientOriginalName();
                                $filePath = $ld['certificate']->storeAs('ld-certificates', $originalFilename, 'public');
                            } elseif(is_string($ld['certificate']) && $ld['certificate'] != null){
                                // Keep existing file
                                $filePath = $ld['certificate'];
                            }
                        } else {
                            // No certificate provided - delete old file if exists
                            if($ldRecord && $ldRecord->certificate) {
                                Storage::disk('public')->delete($ldRecord->certificate);
                            }
                        }

                        $ldRecord = $user->learningAndDevelopment->find($ld['id']);
                        if ($ldRecord) {
                            $ldRecord->update([
                                'start_date' => $ld['start_date'] ?: null,
                                'end_date' => $ld['end_date'] ?: null,
                                'toPresent' => $ld['end_date'] ? null : 'Present',
                                'title' => $ld['title'] ?: null,
                                'type_of_ld' => $ld['type_of_ld'] ?: null,
                                'no_of_hours' => $ld['no_of_hours'] ?: null,
                                'conducted_by' => $ld['conducted_by'] ?: null,
                                'certificate' => $filePath,
                            ]);
                        }
                    }

                    $this->resetVariables();
                    $this->dispatch('swal', [
                        'title' => "Learning and Development updated successfully!",
                        'icon' => 'success'
                    ]);
                }else{
                    foreach ($this->newLearnAndDevs as $index => $ld) {
                        $validationRules = [
                            'newLearnAndDevs.'.$index.'.title' => 'required|string',
                            'newLearnAndDevs.'.$index.'.type_of_ld' => 'required|string',
                            'newLearnAndDevs.'.$index.'.no_of_hours' => 'required|numeric',
                            'newLearnAndDevs.'.$index.'.start_date' => 'required|date',
                            'newLearnAndDevs.'.$index.'.conducted_by' => 'required|string',
                        ];
                
                        $this->validate($validationRules);

                        $filePath = null;
                        if($ld['certificate']){
                            $originalFilename = $ld['certificate']->getClientOriginalName();
                            $filePath = $ld['certificate']->storeAs('ld-certificates', $originalFilename, 'public');
                        }

                        LD::create([
                            'user_id' => $user->id,
                            'start_date' => $ld['start_date'] ?: null,
                            'end_date' => $ld['end_date'] ?: null,
                            'toPresent' => $ld['end_date'] ? null : 'Present',
                            'title' => $ld['title'] ?: null,
                            'type_of_ld' => $ld['type_of_ld'] ?: null,
                            'no_of_hours' => $ld['no_of_hours'] ?: null,
                            'conducted_by' => $ld['conducted_by'] ?: null,
                            'certificate' => $filePath,
                        ]);
                    }
                    
                    $this->resetVariables();
                    $this->dispatch('swal', [
                        'title' => "Learning and Development added successfully!",
                        'icon' => 'success'
                    ]);
                }
            }
        } catch (Exception $e) {
            $this->resetValidation();
            $this->dispatch('swal', [
                'title' => "Learning and Development update was unsuccessful!",
                'icon' => 'error'
            ]);
            throw $e;
        }
    }

    public function downloadCertificate($documentId)
    {
        $document = LD::findOrFail($documentId);
        $filePath = $document->certificate;
        $fileName = basename($filePath);

        if (!Storage::disk('public')->exists($filePath)) {
            throw new NotFoundHttpException("The file does not exist.");
        }

        $fileSize = Storage::disk('public')->size($filePath);

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => $fileSize,
        ];

        return new StreamedResponse(function () use ($filePath) {
            $stream = Storage::disk('public')->readStream($filePath);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $headers);
    }

    public function cancelEdit()
    {
        $this->resetVariables();
    }

    public function resetVariables(){
        $this->editLearningDevelopment = false;
        $this->addLearningDevelopment = false;
        $this->newLearnAndDevs = [];
        $this->learnAndDevs = [];
        $this->resetValidation();
    }

    public function toggleDelete($id){
        $this->deleteId = $id;
    }

    public function deleteData(){
        try{
            $user = Auth::user();
            if($user){
                $ld = $user->learningAndDevelopment->find($this->deleteId);

                if($ld && $ld->certificate) {
                    Storage::disk('public')->delete($ld->certificate);
                }

                if ($ld) {
                    $ld->delete();
                }

                $this->dispatch('swal', [
                    'title' => "Learning and development deleted successfully!",
                    'icon' => 'success'
                ]);

                $this->deleteId = null;
            }
        }catch(Exception $e){
            $this->dispatch('swal', [
                'title' => "Deletion was unsuccessful!",
                'icon' => 'error'
            ]);
            throw $e;
        }
    }
}
