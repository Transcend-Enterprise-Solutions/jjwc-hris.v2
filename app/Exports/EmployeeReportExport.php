<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use DateTime;

class EmployeeReportExport implements FromCollection, WithEvents
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
                $sheet->getColumnDimension('H')->setWidth(10);
                $sheet->getColumnDimension('I')->setWidth(20);
                $sheet->getColumnDimension('J')->setWidth(20);
                $sheet->getColumnDimension('K')->setWidth(15);


                // Set row height for row 4
                $sheet->getRowDimension(4)->setRowHeight(40); 
; 

                // Merge cells A12 and A13
                $sheet->setCellValue('A4', '');
                $sheet->setCellValue('B4', 'NAME');
                $sheet->setCellValue('C4', 'EMAIL');
                $sheet->setCellValue('D4', 'EMPLOYEE NO.');
                $sheet->setCellValue('E4', 'POSITION');
                $sheet->setCellValue('F4', 'APPOINTMENT');
                $sheet->setCellValue('G4', 'WORK GROUP');
                $sheet->setCellValue('H4', 'SG/STEP');
                $sheet->setCellValue('I4', 'RATE PER MONTH');
                $sheet->setCellValue('J4', 'DATE EMPLOYED');
                $sheet->setCellValue('K4', 'STATUS');

                // Apply word wrap
                $sheet->getStyle('A:K')->getAlignment()->setWrapText(true);

                // Column Header
                $sheet->getStyle('A1:K3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A4:K4')->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

                // Rows
                $sheet->getStyle('A4:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('C4:K' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    private function addCustomHeader(BeforeSheet $event){
        $sheet = $event->sheet;

        // Add custom header
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', "");

        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2', "JUVENILE JUSTICE AND WELFARE COUNCIL");
        $sheet->mergeCells('A3:K3');

        $month = '';
        if (isset($this->filters['month']) && $this->filters['month']) {
            $dateTime = DateTime::createFromFormat('Y-m', $this->filters['month']);
            if ($dateTime !== false) {
                $month = " for the month of " . $dateTime->format('F Y');
            } else {
                // Handle invalid date format
                $month = 'Invalid Date';
            }
        }
        $sheet->setCellValue('A3', " Employee List" . $month);

        // Apply some basic styling
        $sheet->getStyle('A1:K3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:K3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('A1:K3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);
        $sheet->getStyle('A4:K4')->getFont()->setBold(true);
        $sheet->getStyle('2:2')->getFont()->setSize(16);

        $sheet->getStyle('A4:K4')->applyFromArray([
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

        return $this->filters['employees']->get()
            ->map(function ($user) use ($formatDate, $formatCurrency) {
                $this->rowNumber++;
                $sg_step = null;
                if($user->plantilla_sg_step){
                    $sg_step = $user->plantilla_sg_step;
                }else if($user->cos_reg_sg_step){
                    $sg_step = $user->cos_reg_sg_step;
                }else{
                    $sg_step = "-";
                }

                $rate = null;
                if($user->plantilla_rate){
                    $rate = $user->plantilla_rate;
                }else if($user->cos_reg_rate){
                    $rate = $user->cos_reg_rate;
                }

                $appointment = null;
                if($user->appointment != "cos" && $user->appointment != "ct"){
                    $appointment = explode(',', $user->appointment);
                    if($appointment[0] == 'pa'){
                        $appointment = 'Presidential Appointee';
                    }else{
                        $appointment = 'Plantilla';
                    }
                }else{
                    if($user->appointment == "ct"){
                        $appointment = 'Co-Terminus';
                    }else{
                        $appointment = 'COS';
                    }
                }

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
                    'Appointment' => $appointment,
                    'Work Group' => $user->office_division,
                    'G/STEP' => $sg_step,
                    'Rate Per Month' => $formatCurrency($rate),
                    'Date Employed' => $formatDate($user->date_hired),
                    'Status' => $status,
                ];
            });
    }
}
