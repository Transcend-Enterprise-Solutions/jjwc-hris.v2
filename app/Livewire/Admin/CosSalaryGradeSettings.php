<?php

namespace App\Livewire\Admin;

use App\Imports\CosSalaryGradeImport;
use App\Models\CosSalaryGrade;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalaryGradeExport;
use Livewire\WithFileUploads;
use Exception;

class CosSalaryGradeSettings extends Component
{
    use WithFileUploads;

    public $salaryGrades;
    public $editingId = null;
    public $isEditing = false;
    public $editedData = [];
    public $showSGModal = false;
    public $salaryGradeData = [
        'salary_grade' => '',
        'step1' => '', 'step2' => '', 'step3' => '', 'step4' => '',
        'step5' => '', 'step6' => '', 'step7' => '', 'step8' => '',
    ];

    public $deleteId;
    public $deleteMessage;
    public $data;
    public $file;

    public function mount()
    {
        $this->salaryGrades = CosSalaryGrade::orderBy('salary_grade')->get();
    }

    public function render()
    {
        if($this->file){
            $this->importFromExcel();
            $this->salaryGrades = CosSalaryGrade::orderBy('salary_grade')->get();
        }

        return view('livewire.admin.cos-salary-grade-settings');
    }

      public function editSG($id){
        $this->isEditing = true;
        $this->editingId = $id;
        $salaryGrade = $this->salaryGrades->firstWhere('id', $id);
        
        $this->salaryGradeData = [
            'salary_grade' => $salaryGrade->salary_grade,
            'step1' => $salaryGrade->step1,
            'step2' => $salaryGrade->step2,
            'step3' => $salaryGrade->step3,
            'step4' => $salaryGrade->step4,
            'step5' => $salaryGrade->step5,
            'step6' => $salaryGrade->step6,
            'step7' => $salaryGrade->step7,
            'step8' => $salaryGrade->step8,
        ];
        
        $this->showSGModal = true;
    }

    public function openSGModal(){
        $this->showSGModal = true;
    }

    public function saveSalaryGrade(){
        try{
            $message = null;
            $this->validate([
                'salaryGradeData.salary_grade' => 'required|integer',
                'salaryGradeData.step1' => 'required|numeric',
                'salaryGradeData.step2' => 'required|numeric',
                'salaryGradeData.step3' => 'required|numeric',
                'salaryGradeData.step4' => 'required|numeric',
                'salaryGradeData.step5' => 'required|numeric',
                'salaryGradeData.step6' => 'required|numeric',
                'salaryGradeData.step7' => 'required|numeric',
                'salaryGradeData.step8' => 'required|numeric',
            ]);
            if ($this->isEditing) {
                CosSalaryGrade::find($this->editingId)->update($this->salaryGradeData);
                $message = "Salary Grade updated successfully!";
            } else {
                CosSalaryGrade::create($this->salaryGradeData);
                $message = "Salary Grade added successfully!";
            }
            $this->resetVariables();
            $this->dispatch('swal', [
                'title' => $message,
                'icon' => 'success'
            ]);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function toggleDeleteSG($id, $data){
        $this->deleteId = $id;
        $this->data = $data;
        $this->deleteMessage = $data;
    }

    public function exportSalaryGrade(){
        $sgStep = CosSalaryGrade::all();
        $filters = [
            'sgStep' => $sgStep,
        ];
        return Excel::download(new SalaryGradeExport ($filters), 'Salary-Grades.xlsx');
    }

    public function importFromExcel(){
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            DB::beginTransaction();
            
            $import = new CosSalaryGradeImport();
            Excel::import($import, $this->file);
            
            DB::commit();
            
            // Check if any rows were skipped
            $skippedRows = $import->failures()->count();
            
            $message = "Salary Grade imported successfully!";
            if ($skippedRows > 0) {
                $message .= " {$skippedRows} rows were skipped due to errors.";
            }
            
            $this->dispatch('swal', [
                'title' => $message,
                'icon' => 'success'
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            $failures = $e->failures();
            $errorMessages = collect($failures)->map(function ($failure) {
                return "Row {$failure->row()}: {$failure->errors()[0]}";
            })->implode(', ');
            
            $this->dispatch('swal', [
                'title' => "Please upload the correct Salary Grade excel file!",
                'icon' => 'error'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', [
                'title' => 'An error occurred during import: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }

        $this->file = null;
    }

    public function resetVariables(){
        $this->resetValidation();
        $this->deleteId = null;
        $this->deleteMessage = null;
        $this->data = null;
        $this->showSGModal = null;
        $this->editingId = null;
        $this->salaryGradeData = [
            'salary_grade' => '',
            'step1' => '', 'step2' => '', 'step3' => '', 'step4' => '',
            'step5' => '', 'step6' => '', 'step7' => '', 'step8' => '',
        ];
    }
}
