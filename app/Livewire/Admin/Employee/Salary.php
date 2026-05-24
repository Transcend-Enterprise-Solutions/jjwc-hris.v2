<?php

namespace App\Livewire\Admin\Employee;

use App\Models\CosSalaryGrade;
use App\Models\EmployeeSalary;
use App\Models\SalaryGrade;
use App\Models\User;
use Exception;
use Livewire\Component;

class Salary extends Component
{
    public $selectedUser;

    public $editSalary, $addSalary, $deleteId, $salaryId;
    public $sg;
    public $step = 1;
    public $basicSalary;
    public $pera;
    public $otherAllowances = [];

    public $salaryGrades;



    public function mount($userId)
    {
        $user = User::with(['salary', 'userData'])
            ->find($userId);

        if ($user) {
            $this->selectedUser = $user;
        }

        if($user->userData->appointment == 'cos'){
            $this->salaryGrades = CosSalaryGrade::all();
        }else{
            $this->salaryGrades = SalaryGrade::all();
        }
    }
    public function render()
    {
        $this->getRate();

        return view('livewire.admin.employee.salary');
    }

    public function getRate(){
        if ($this->sg && $this->step) {
            if($this->selectedUser->userData->appointment == 'plantilla'){
                $salaryGrades = SalaryGrade::where('salary_grade', $this->sg);
            }else{
                $salaryGrades = CosSalaryGrade::where('salary_grade', $this->sg);
            }
            switch ($this->step) {
                case 1:
                    $salaryGrade = $salaryGrades->select('step1 as step')->first();
                    break;
                case 2:
                    $salaryGrade = $salaryGrades->select('step2 as step')->first();
                    break;
                case 3:
                    $salaryGrade = $salaryGrades->select('step3 as step')->first();
                    break;
                case 4:
                    $salaryGrade = $salaryGrades->select('step4 as step')->first();
                    break;
                case 5:
                    $salaryGrade = $salaryGrades->select('step5 as step')->first();
                    break;
                case 6:
                    $salaryGrade = $salaryGrades->select('step6 as step')->first();
                    break;
                case 7:
                    $salaryGrade = $salaryGrades->select('step7 as step')->first();
                    break;
                case 8:
                    $salaryGrade = $salaryGrades->select('step8 as step')->first();
                    break;
                default:
                    $salaryGrade = null;
                    break;
            }
            if ($salaryGrade) {
                $this->basicSalary = $salaryGrade->step;
            } else {
                $this->basicSalary = 0;
            }
        }
    }

    public function toggleEditSalary($id = null){
        $salary = EmployeeSalary::where('user_id', $id)->first();
        if($salary){
            $this->salaryId = $salary->id;
            $this->sg = $salary->sg;
            $this->step = $salary->step;
            $this->basicSalary = $salary->monthly_basic_salary;
            $this->pera = $salary->pera;
            $this->otherAllowances = $salary->other_allowances ? json_decode($salary->other_allowances, true) : [];
            $this->addSalary = false;
        }else{
            $this->addSalary = true;
        }
        $this->editSalary = true;
    }

    public function saveSalary(){
        try{
            $data = [
                'user_id' => $this->selectedUser->id,
                'sg' => $this->sg,
                'step' => $this->sg ? $this->step : null,
                'monthly_basic_salary' => $this->basicSalary,
                'pera' => $this->pera,
                'other_allowances' => json_encode($this->otherAllowances ?? [], JSON_PRETTY_PRINT),
            ];
            if($this->salaryId){
                $salary = EmployeeSalary::find($this->salaryId);
                $salary->update($data);
            }else{
                EmployeeSalary::create($data);
            }

            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => 'Salary saved successfully',
                'icon' => 'success'
            ]);
        }catch(Exception $e){
            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => 'Something went wrong. Salary was not saved.',
                'icon' => 'error'
            ]);
            throw $e;
        }
    }

    public function toggleDelete($id){
        $this->deleteId = $id;
    }

    public function deleteData(){
        try {
            $salary = EmployeeSalary::where('id', $this->deleteId)->first();

            if (!$salary) {
                $this->dispatch('swal', [
                    'title' => "Salary not found!",
                    'icon' => 'error'
                ]);
                return;
            }

            $salary->delete();
            $message = "Salary deleted successfully!";

            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => $message,
                'icon' => 'success'
            ]);
        } catch (Exception $e) {
            $this->dispatch('swal', [
                'title' => "Deletion of salary was unsuccessful!",
                'icon' => 'error'
            ]);
            $this->resetVariables();
            throw $e;
        }
    }

    public function resetVariables()
    {
        $this->deleteId = null;
        $this->salaryId = null;
        $this->addSalary - null;
        $this->editSalary = null;
        $this->sg = null;
        $this->step = null;
        $this->pera = null;
        $this->basicSalary = null;
        $this->otherAllowances = [];
        $this->resetValidation();
    }
}
