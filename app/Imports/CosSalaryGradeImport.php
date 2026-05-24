<?php

namespace App\Imports;

use App\Models\CosSalaryGrade;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class CosSalaryGradeImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        // Skip rows without salary_grade
        if (empty($row['salary_grade'])) {
            return null;
        }

        return CosSalaryGrade::updateOrCreate(
            ['salary_grade' => $row['salary_grade']],
            [
                'step1' => $this->parseAmount($row['step_1'] ?? null),
                'step2' => $this->parseAmount($row['step_2'] ?? null),
                'step3' => $this->parseAmount($row['step_3'] ?? null),
                'step4' => $this->parseAmount($row['step_4'] ?? null),
                'step5' => $this->parseAmount($row['step_5'] ?? null),
                'step6' => $this->parseAmount($row['step_6'] ?? null),
                'step7' => $this->parseAmount($row['step_7'] ?? null),
                'step8' => $this->parseAmount($row['step_8'] ?? null),
            ]
        );
    }

    public function rules(): array
    {
        return [
            'salary_grade' => ['required', 'integer', 'min:1'],
            'step_1' => ['nullable', 'numeric', 'min:0'],
            'step_2' => ['nullable', 'numeric', 'min:0'],
            'step_3' => ['nullable', 'numeric', 'min:0'],
            'step_4' => ['nullable', 'numeric', 'min:0'],
            'step_5' => ['nullable', 'numeric', 'min:0'],
            'step_6' => ['nullable', 'numeric', 'min:0'],
            'step_7' => ['nullable', 'numeric', 'min:0'],
            'step_8' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    private function parseAmount($value)
    {
        // If value is null or empty string, return null
        if ($value === null || $value === '') {
            return null;
        }

        // Remove any non-numeric characters except decimal point and comma
        $cleanValue = preg_replace('/[^0-9.,]/', '', (string)$value);
        
        // If the cleaned value is empty, return null
        if (empty($cleanValue)) {
            return null;
        }
        
        // Replace comma with dot if comma is used as decimal separator
        $cleanValue = str_replace(',', '', $cleanValue);
        
        return (int) $cleanValue;
    }
}