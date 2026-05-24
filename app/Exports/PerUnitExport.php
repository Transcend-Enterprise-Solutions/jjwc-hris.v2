<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PerUnitExport implements FromCollection, WithEvents
{
    use Exportable;

    protected $filters;
    protected $rowNumber = 0;

    public function __construct($filters){
        $this->filters = $filters;
    }

    public function registerEvents(): array{
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $this->addCustomHeader($event);
            },
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(4);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(30);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(30);
                $sheet->getColumnDimension('F')->setWidth(20);
                $sheet->getColumnDimension('G')->setWidth(20);
                $sheet->getColumnDimension('H')->setWidth(20);
                $sheet->getColumnDimension('I')->setWidth(10);
                $sheet->getColumnDimension('J')->setWidth(20);
                $sheet->getColumnDimension('K')->setWidth(20);
                $sheet->getColumnDimension('L')->setWidth(15);


                // Set row height for row 4
                $sheet->getRowDimension(4)->setRowHeight(20); 
; 

                // Merge cells A12 and A13
                $sheet->setCellValue('A4', '');
                $sheet->setCellValue('B4', 'NAME');
                $sheet->setCellValue('C4', 'EMAIL');
                $sheet->setCellValue('D4', 'EMPLOYEE NO.');
                $sheet->setCellValue('E4', 'POSITION');
                $sheet->setCellValue('F4', 'APPOINTMENT');
                $sheet->setCellValue('G4', 'WORK GROUP');
                $sheet->setCellValue('H4', 'UNIT');
                $sheet->setCellValue('I4', 'SG/STEP');
                $sheet->setCellValue('J4', 'RATE PER MONTH');
                $sheet->setCellValue('K4', 'DATE EMPLOYED');
                $sheet->setCellValue('L4', 'STATUS');

                // Apply word wrap
                $sheet->getStyle('A:L')->getAlignment()->setWrapText(true);

                // Column Header
                $sheet->getStyle('A1:L3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A4:L4')->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

                // Rows
                $sheet->getStyle('A4:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('C4:L' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    private function addCustomHeader(BeforeSheet $event){
        $sheet = $event->sheet;

        // Add custom header
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', "");

        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', "JUVENILE JUSTICE AND WELFARE COUNCIL");
        $sheet->mergeCells('A3:L3');
        $sheet->setCellValue('A3', $this->filters['office_division'] . " - ". $this->filters['unit'] ." (Employee List)");

        // Apply some basic styling
        $sheet->getStyle('A1:L3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:L3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('A1:L3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);
        $sheet->getStyle('A4:L4')->getFont()->setBold(true);
        $sheet->getStyle('2:2')->getFont()->setSize(16);

        $sheet->getStyle('A4:L4')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'], // Black color
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FFF0F0F0'], // Light gray background
            ],
        ]);
    }

    public function collection(){
        $formatDate = function($value) {
            $date = Carbon::parse($value)->format('F d, Y');
            return $date;
        };

        $formatCurrency = function($value) {
            if($value == 0 || $value == null){
                return "-";
            }
            return '₱ ' . number_format((float)$value, 2, '.', ',');
        };

        return $this->filters['users']->get()
            ->map(function ($user) use ($formatDate, $formatCurrency) {
                $this->rowNumber++;

                $status = null;
                switch($user->active_status){
                    case 0:
                        $status = 'Inactive';
                        break;
                    case 1:
                        $status = 'Active';
                        break;
                    case 2:
                        $status = 'Resigned';
                        break;
                    case 3:
                        $status = 'Retired';
                        break;
                }

                return [
                    $this->rowNumber,
                    'Name' => $user->surname . ', ' . $user->first_name . 
                        ($user->middle_name && $user->middle_name != 'N/A' ? ' ' . $user->middle_name : '') .
                        ($user->name_extension && $user->name_extension != 'N/A' ? ' ' . $user->name_extension : ''),
                    'Email' => $user->email,
                    'Employee ID' => $user->emp_code,
                    'Position' => $user->position,
                    'Appointment' => $user->appointment,
                    'Work Group' => $user->office_division,
                    'Unit' => $user->unit ?: '-',
                    'SG/STEP' => $user->sg ? $user->sg . ($user->step ? '-' . $user->step : '') : '-',
                    'Rate Per Month' => $formatCurrency($user->monthly_basic_salary),
                    'Date Employed' => $formatDate($user->date_hired),
                    'Status' => $status,
                ];
            });
    }
}
