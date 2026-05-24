<?php

namespace App\Exports;

use App\Models\AdminRoleAccess;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AdminRolesExport implements FromCollection, WithEvents
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
                $sheet->getColumnDimension('C')->setWidth(35);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(35);
                $sheet->getColumnDimension('F')->setWidth(35);
                $sheet->getColumnDimension('G')->setWidth(35);


                // Set row height for row 4
                $sheet->getRowDimension(4)->setRowHeight(40); 
; 

                // Merge cells A12 and A13
                $sheet->setCellValue('A4', '');
                $sheet->setCellValue('B4', 'ADMIN ROLE');
                $sheet->setCellValue('C4', 'NAME');
                $sheet->setCellValue('D4', 'EMPLOYEE NO.');
                $sheet->setCellValue('E4', 'WORK GROUP');
                $sheet->setCellValue('F4', 'UNIT');
                $sheet->setCellValue('G4', 'POSITION');

                // Apply word wrap
                $sheet->getStyle('A:G')->getAlignment()->setWrapText(true);

                // Column Header
                $sheet->getStyle('A1:G3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A4:G4')->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

                // Rows
                $sheet->getStyle('A4:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('C4:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    private function addCustomHeader(BeforeSheet $event){
        $sheet = $event->sheet;

        // Add custom header
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', "");

        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', "JUVENILE JUSTICE AND WELFARE COUNCIL");
        $sheet->mergeCells('A3:G3');

        $sheet->setCellValue('A3', "HRIS Admin Role List");

        // Apply some basic styling
        $sheet->getStyle('A1:G3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:G3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('A1:G3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);
        $sheet->getStyle('A4:G4')->getFont()->setBold(true);
        $sheet->getStyle('2:2')->getFont()->setSize(16);

        $sheet->getStyle('A4:G4')->applyFromArray([
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
        $role = function($value){
            $adminAccess = AdminRoleAccess::where('role_code', $value)->first();  
            return $adminAccess ? $adminAccess->role_name : '--';
        };

        $code = function($value){
            $c = explode('-', $value);
            return isset($c[1]) ? $c[1] : $value;
        };


        return $this->filters['admins']->get()
            ->map(function ($user) use ($role, $code) {
                $this->rowNumber++;
                return [
                    $this->rowNumber,
                    'Admin Role' => $role($user->user_role),
                    'Name' => $user->name,
                    'Employee ID' => $code($user->emp_code),
                    'Work Group' => $user->office_division ?? '--',
                    'Unit' => $user->unit ?? '--',
                    'Position' => $user->position,
                ];
            });
    }
}
