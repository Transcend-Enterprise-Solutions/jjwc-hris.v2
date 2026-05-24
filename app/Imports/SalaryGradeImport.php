<?php

namespace App\Imports;

use App\Models\SalaryGrade;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SalaryGradeImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return SalaryGrade::updateOrCreate(
            ['salary_grade' => $row['salary_grade']],
            [
                'step1' => $this->parseAmount($row['step_1']),
                'step2' => $this->parseAmount($row['step_2']),
                'step3' => $this->parseAmount($row['step_3']),
                'step4' => $this->parseAmount($row['step_4']),
                'step5' => $this->parseAmount($row['step_5']),
                'step6' => $this->parseAmount($row['step_6']),
                'step7' => $this->parseAmount($row['step_7']),
                'step8' => $this->parseAmount($row['step_8']),
            ]
        );
    }

    public function rules(): array
    {
        return [
            'salary_grade' => ['required', 'integer', 'min:1'],
            'step_1' => ['required', 'numeric', 'min:0'],
            'step_2' => ['required', 'numeric', 'min:0'],
            'step_3' => ['required', 'numeric', 'min:0'],
            'step_4' => ['required', 'numeric', 'min:0'],
            'step_5' => ['required', 'numeric', 'min:0'],
            'step_6' => ['required', 'numeric', 'min:0'],
            'step_7' => ['required', 'numeric', 'min:0'],
            'step_8' => ['required', 'numeric', 'min:0'],
        ];
    }

    private function parseAmount($value)
    {
        // Remove any non-numeric characters except decimal point and comma
        $cleanValue = preg_replace('/[^0-9.,]/', '', $value);
        // Replace comma with dot if comma is used as decimal separator
        $cleanValue = str_replace(',', '', $cleanValue);
        return (int) $cleanValue;
    }
}