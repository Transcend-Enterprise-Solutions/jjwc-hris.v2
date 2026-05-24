<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SalaryGradeExport implements FromCollection, WithEvents, WithHeadings
{
    use Exportable;

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $formatCurrency = function($value) {
            return number_format((float)$value, 0, '', ',');
        };

        return $this->filters['sgStep']->map(function ($sg) use ($formatCurrency) {
            return [
                $sg->salary_grade,
                $sg->step1,
                $sg->step2,
                $sg->step3,
                $sg->step4,
                $sg->step5,
                $sg->step6,
                $sg->step7,
                $sg->step8,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Salary Grade',
            'Step 1',
            'Step 2',
            'Step 3',
            'Step 4',
            'Step 5',
            'Step 6',
            'Step 7',
            'Step 8',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E0E0E0'],
                    ],
                ]);

                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getColumnDimension('A')->setWidth(15);
                for ($col = 'B'; $col <= $highestColumn; $col++) {
                    $sheet->getColumnDimension($col)->setWidth(20);
                }

                // Align all cells to center
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}