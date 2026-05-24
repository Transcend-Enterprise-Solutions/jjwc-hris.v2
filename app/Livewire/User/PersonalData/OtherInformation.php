<?php

namespace App\Livewire\User\PersonalData;

use App\Models\PdsC4Answers;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OtherInformation extends Component
{
    public $editQuestions = false;
    public $editAnswer = [
        'q34a' => false,
        'q34b' => false,
        'q35a' => false,
        'q35b' => false,
        'q36a' => false,
        'q37a' => false,
        'q38a' => false,
        'q38b' => false,
        'q39a' => false,
        'q40a' => false,
        'q40b' => false,
        'q40c' => false,
    ];
    
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
    
    public function mount(){
        $this->getC4Answers();
    }

    public function render()
    {
        
        return view('livewire.user.personal-data.other-information');
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
                        $this->{$fieldKey} = $answer ? Carbon::parse($answer->{$field})->format('m/d/Y') : null;
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
        $user = Auth::user();
        return PdsC4Answers::where('user_id', $user->id)
            ->where('question_number', $qNum)
            ->where('question_letter', $qLetter)
            ->first();
    }   

    public function editC4Question(){
        $this->editQuestions = true;
        $this->editAnswer = [
            'q34a' => true,
            'q34b' => true,
            'q35a' => true,
            'q35b' => true,
            'q36a' => true,
            'q37a' => true,
            'q38a' => true,
            'q38b' => true,
            'q39a' => true,
            'q40a' => true,
            'q40b' => true,
            'q40c' => true,
        ];
    }
    
    public function resetVariables(){
        $this->editQuestions = false;
        $this->editAnswer = [
            'q34a' => false,
            'q34b' => false,
            'q35a' => false,
            'q35b' => false,
            'q36a' => false,
            'q37a' => false,
            'q38a' => false,
            'q38b' => false,
            'q39a' => false,
            'q40a' => false,
            'q40b' => false,
            'q40c' => false,
        ];
        $this->q34aAnswer = null;
        $this->q34bAnswer = null;
        $this->q34bDetails = null;
        $this->q35aAnswer = null;
        $this->q35aDetails = null;
        $this->q35bAnswer = null;
        $this->q35bDate_filed = null;
        $this->q35bStatus = null;
        $this->q36aAnswer = null;
        $this->q36aDetails = null;
        $this->q37aAnswer = null;
        $this->q37aDetails = null;
        $this->q38aAnswer = null;
        $this->q38aDetails = null;
        $this->q38bAnswer = null;
        $this->q38bDetails = null;
        $this->q39aAnswer = null;
        $this->q39aDetails = null;
        $this->q40aAnswer = null;
        $this->q40aDetails = null;
        $this->q40bAnswer = null;
        $this->q40bDetails = null;
        $this->q40cAnswer = null;
        $this->q40cDetails = null;
        $this->getC4Answers();
    }

    public function saveC4Question(){
        try{
            $user = Auth::user();
            if(!$user){
                $this->dispatch('swal', [
                    'title' => "User not authenticated!",
                    'icon' => 'error'
                ]);
                return;
            }

            $questions = [
                ['num' => 34, 'letter' => 'a', 'answer' => 'q34aAnswer', 'details' => null],
                ['num' => 34, 'letter' => 'b', 'answer' => 'q34bAnswer', 'details' => 'q34bDetails'],
                ['num' => 35, 'letter' => 'a', 'answer' => 'q35aAnswer', 'details' => 'q35aDetails'],
                ['num' => 35, 'letter' => 'b', 'answer' => 'q35bAnswer', 'details' => null, 'special' => 'criminal'],
                ['num' => 36, 'letter' => 'a', 'answer' => 'q36aAnswer', 'details' => 'q36aDetails'],
                ['num' => 37, 'letter' => 'a', 'answer' => 'q37aAnswer', 'details' => 'q37aDetails'],
                ['num' => 38, 'letter' => 'a', 'answer' => 'q38aAnswer', 'details' => 'q38aDetails'],
                ['num' => 38, 'letter' => 'b', 'answer' => 'q38bAnswer', 'details' => 'q38bDetails'],
                ['num' => 39, 'letter' => 'a', 'answer' => 'q39aAnswer', 'details' => 'q39aDetails'],
                ['num' => 40, 'letter' => 'a', 'answer' => 'q40aAnswer', 'details' => 'q40aDetails'],
                ['num' => 40, 'letter' => 'b', 'answer' => 'q40bAnswer', 'details' => 'q40bDetails'],
                ['num' => 40, 'letter' => 'c', 'answer' => 'q40cAnswer', 'details' => 'q40cDetails'],
            ];

            $this->resetErrorBag();

            $hasErrors = false;
            foreach($questions as $q){
                $answerValue = $this->{$q['answer']};
                
                if($answerValue === null || $answerValue === ''){
                    continue;
                }

                if($answerValue == 1){
                    if(isset($q['special']) && $q['special'] === 'criminal'){
                        if(empty($this->q35bDate_filed)){
                            $this->addError('q35bDate_filed', 'Date filed is required when answer is Yes');
                            $hasErrors = true;
                        }
                        if(empty($this->q35bStatus)){
                            $this->addError('q35bStatus', 'Status of case is required when answer is Yes');
                            $hasErrors = true;
                        }
                    }

                    if($q['details'] !== null){
                        $detailsValue = $this->{$q['details']};
                        if(empty($detailsValue)){
                            $this->addError($q['details'], 'Details are required when answer is Yes');
                            $hasErrors = true;
                        }
                    }
                }
            }

            if($hasErrors){
                $this->dispatch('swal', [
                    'title' => "Validation Error",
                    'text' => "Please fill in all required fields marked below",
                    'icon' => 'error'
                ]);
                return;
            }

            foreach($questions as $q){
                $answerValue = $this->{$q['answer']};
                
                if($answerValue === null || $answerValue === ''){
                    continue;
                }

                $detailsValue = $q['details'] ? $this->{$q['details']} : null;
                
                if($answerValue == 0){
                    $detailsValue = null;
                    if($q['details']){
                        $this->{$q['details']} = null;
                    }
                }

                $dateFiled = null;
                $status = null;

                if(isset($q['special']) && $q['special'] === 'criminal' && $answerValue == 1){
                    $dateFiled = $this->q35bDate_filed;
                    $status = $this->q35bStatus;
                }

                PdsC4Answers::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'question_number' => $q['num'],
                        'question_letter' => $q['letter']
                    ],
                    [
                        'answer' => $answerValue,
                        'details' => $answerValue ? $detailsValue : null,
                        'date_filed' => $dateFiled,
                        'status' => $status,
                    ]
                );
            }

            $this->dispatch('swal', [
                'title' => 'Information saved successfully!',
                'icon' => 'success'
            ]);
            
            $this->resetVariables();
            $this->getC4Answers();
        }catch(Exception $e){
            $this->dispatch('swal', [
                'title' => "Update was unsuccessful!",
                'icon' => 'error'
            ]);
            throw $e;
        }
    }
}
