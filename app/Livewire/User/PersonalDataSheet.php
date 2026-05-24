<?php

namespace App\Livewire\User;

use App\Exports\PDSExport;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
#[Title('Personal Data')]
class PersonalDataSheet extends Component
{
    #[Url(as: 'tab', except: 'employee-details')]
    public $tab = 'employee-details';

    public $pds;
    public $consentStatus = false;

    public function mount(){
        $consentStatus = Auth::user()->dataPrivacyConsent;
        $this->consentStatus = $consentStatus ? $consentStatus->consent_status : false;
    }

    public function render(){

        $user = Auth::user();
        $this->pds = [
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
        ];

        return view('livewire.user.personal-data-sheet', [
            'user' => $user,
        ]);
    }

    public function giveConsent(){
        $user = Auth::user();
        $user->dataPrivacyConsent()->updateOrCreate(
            ['user_id' => $user->id],
            ['consent_status' => true]
        );
        $this->consentStatus = true;
    }

    public function exportPDS(){
        try {
            $exporter = new PDSExport($this->pds);
            $result = $exporter->export();

            return response()->streamDownload(function () use ($result) {
                echo $result['content'];
            }, $result['filename']);
        } catch (Exception $e) {
            throw $e;
        }
    }
}