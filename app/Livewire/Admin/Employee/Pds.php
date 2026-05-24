<?php

namespace App\Livewire\Admin\Employee;

use App\Exports\PDSExport;
use App\Models\PdsC4Answers;
use App\Models\PdsGovIssuedId;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Livewire\Component;

class Pds extends Component
{
    public $selectedUser;
    public $pds;

    // User's Data
    public $userData;
    public $userSpouse;
    public $userMother;
    public $userFather;
    public $userChildren;
    public $educBackground;
    public $eligibility;
    public $workExperience;
    public $voluntaryWorks;
    public $lds;
    public $skills;
    public $hobbies;
    public $non_acads_distinctions;
    public $assOrgMemberships;
    public $references;

    public $q34aAnswer;
    public $q34bAnswer;
    public $q34bDetails;
    public $q35aAnswer;
    public $q35aDetails;
    public $q35bAnswer;
    public $q35bDate_filed;
    public $q35bStatus;
    public $q36aAnswer;
    public $q36aDetails;
    public $q37aAnswer;
    public $q37aDetails;
    public $q38aAnswer;
    public $q38aDetails;
    public $q38bAnswer;
    public $q38bDetails;
    public $q39aAnswer;
    public $q39aDetails;
    public $q40aAnswer;
    public $q40aDetails;
    public $q40bAnswer;
    public $q40bDetails;
    public $q40cAnswer;
    public $q40cDetails;
    public $editGovId;
    public $govId;
    public $idNumber;
    public $dateIssued;

    public function mount($userId)
    {
        $user = User::with([
            'position', 
            'officeDivision', 
            'officeDivisionUnit', 
            'userData', 
            'contracts',
            ])
            ->find($userId);

        if ($user) {  
            $this->selectedUser = $user; 
            
            $this->userData = $this->selectedUser->userData;
            $this->userSpouse = $this->selectedUser->employeesSpouse;
            $this->userMother = $this->selectedUser->employeesMother;
            $this->userFather = $this->selectedUser->employeesFather;
            $this->userChildren = $this->selectedUser->employeesChildren;
            $this->educBackground = $this->selectedUser->employeesEducation;
            $this->eligibility = $this->selectedUser->eligibility;
            $this->workExperience = $this->selectedUser->workExperience;
            $this->voluntaryWorks = $this->selectedUser->voluntaryWorks;
            $this->lds = $this->selectedUser->learningAndDevelopment;
            $this->skills = $this->selectedUser->skills;
            $this->hobbies = $this->selectedUser->hobbies;
            $this->non_acads_distinctions = $this->selectedUser->nonAcadDistinctions;
            $this->assOrgMemberships = $this->selectedUser->assOrgMembership;
            $this->references = $this->selectedUser->charReferences;

            $this->getC4Answers();
            $pdsGovId = PdsGovIssuedId::where('user_id', $user->id)->first();
            if($pdsGovId){
                $this->govId = $pdsGovId->gov_id;
                $this->idNumber = $pdsGovId->id_number;
                $this->dateIssued = $pdsGovId->date_of_issuance;
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.employee.pds');
    }

    public function getC4Answers(){
        try {
            $questions = [
                'q34a' => ['num' => 34, 'letter' => 'a', 'fields' => ['answer']],
                'q34b' => ['num' => 34, 'letter' => 'b', 'fields' => ['answer', 'details']],
                'q35a' => ['num' => 35, 'letter' => 'a', 'fields' => ['answer', 'details']],
                'q35b' => ['num' => 35, 'letter' => 'b', 'fields' => ['answer', 'date_filed', 'status']],
                'q36a' => ['num' => 36, 'letter' => 'a', 'fields' => ['answer', 'details']],
                'q37a' => ['num' => 37, 'letter' => 'a', 'fields' => ['answer', 'details']],
                'q38a' => ['num' => 38, 'letter' => 'a', 'fields' => ['answer', 'details']],
                'q38b' => ['num' => 38, 'letter' => 'b', 'fields' => ['answer', 'details']],
                'q39a' => ['num' => 39, 'letter' => 'a', 'fields' => ['answer', 'details']],
                'q40a' => ['num' => 40, 'letter' => 'a', 'fields' => ['answer', 'details']],
                'q40b' => ['num' => 40, 'letter' => 'b', 'fields' => ['answer', 'details']],
                'q40c' => ['num' => 40, 'letter' => 'c', 'fields' => ['answer', 'details']],
            ];
    
            foreach ($questions as $key => $question) {
                $answer = $this->getAnswer($question['num'], $question['letter']);
                
                foreach ($question['fields'] as $field) {
                    $fieldKey = $key . ucfirst($field);
                    if($answer && $field == "date_filed"){
                        $this->{$fieldKey} = $answer ? Carbon::parse($answer->{$field})->format('m-d-Y') : null;
                    }else{
                        $this->{$fieldKey} = $answer ? $answer->{$field} : null;
                    }
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getAnswer($qNum, $qLetter){
        return PdsC4Answers::where('user_id', $this->selectedUser->id)
            ->where('question_number', $qNum)
            ->where('question_letter', $qLetter)
            ->first();
    } 
    
    public function exportPDS(){
        try {
            $user = User::find($this->selectedUser->id);
            $pds = [
                'userData' => $user->userData,
                'userSpouse' => $user->employeesSpouse,
                'userMother' => $user->employeesMother,
                'userFather' => $user->employeesFather,
                'userChildren' => $user->employeesChildren,
                'educBackground' => $user->employeesEducation,
                'eligibility' => $user->eligibility,
                'workExperience' => $user->workExperience,
                'voluntaryWorks' => $user->voluntaryWorks,
                'lds' => $user->learningAndDevelopment,
                'skills' => $user->skills,
                'hobbies' => $user->hobbies,
                'non_acads_distinctions' => $user->nonAcadDistinctions,
                'assOrgMemberships' => $user->assOrgMembership,
                'references' => $user->charReferences,
                'pds_c4_answers' => $user->pdsC4Answers,
                'pds_gov_id' => $user->pdsGovIssuedId,
                'pds_photo' => $user->pdsPhoto,
            ];

            $exporter = new PDSExport($pds);
            $result = $exporter->export();

            return response()->streamDownload(function () use ($result) {
                echo $result['content'];
            }, $result['filename']);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
