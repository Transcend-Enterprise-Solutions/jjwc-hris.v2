<?php

namespace App\Exports;

use App\Models\LeaveApplication;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class LeaveAvailmentExport implements FromCollection, WithEvents, WithStyles
{
    protected $month;

    public function __construct($month)
    {
        $this->month = $month;
    }

    // public function collection()
    // {
    //     // Extract the year and month from the selected month (format: YYYY-MM)
    //     $year = substr($this->month, 0, 4);
    //     $month = substr($this->month, 5, 2);

    //     // Filter leave applications by the selected month (using date_of_filing)
    //     return LeaveApplication::with(['user.userData'])
    //         ->whereYear('date_of_filing', $year)
    //         ->whereMonth('date_of_filing', $month)
    //         ->get()
    //         ->map(function ($leaveApplication) {
    //             return [
    //                 'Control No.' => $leaveApplication->id,
    //                 'Date Filed' => $this->formatDates($leaveApplication->date_of_filing ?? 'N/A'),
    //                 'Surname' => $leaveApplication->user->userData->surname ?? 'N/A',
    //                 'First Name' => $leaveApplication->user->userData->first_name ?? 'N/A',
    //                 'Division' => $leaveApplication->office_or_department ?? 'N/A',
    //                 'Sex' => $leaveApplication->user->userData->sex ?? 'N/A',
    //                 'Type of Leave' => $leaveApplication->type_of_leave ?? 'N/A',
    //                 'Date of Leave' => $this->formatDates($leaveApplication->list_of_dates ?? 'N/A'),
    //                 'Date Approved' => $this->getDateApprovedOrDC($leaveApplication),
    //                 'Remarks' => $leaveApplication->remarks ?? 'N/A',
    //             ];
    //         });
    // }
    public function collection()
    {
        list($year, $month) = explode('-', $this->month);

        return LeaveApplication::with(['user.userData'])
            ->whereYear('date_of_filing', $year)
            ->whereMonth('date_of_filing', $month)
            ->get()
            ->map(function ($leaveApplication) {
                return [
                    'Control No.' => $leaveApplication->id,
                    'Date Filed' => $leaveApplication->date_of_filing,
                    'Surname' => $leaveApplication->user->userData->surname ?? 'N/A',
                    'First Name' => $leaveApplication->user->userData->first_name ?? 'N/A',
                    'Division' => $leaveApplication->office_or_department ?? 'N/A',
                    'Sex' => $leaveApplication->user->userData->sex ?? 'N/A',
                    'Type of Leave' => $leaveApplication->type_of_leave ?? 'N/A',
                    'Date of Leave' => $leaveApplication->list_of_dates ?? 'N/A',
                    'Date Approved' => $leaveApplication->date_approved ?? 'N/A',
                    'Remarks' => $leaveApplication->remarks ?? 'N/A',
                ];
            });
    }

    private function getDateApprovedOrDC($leaveApplication)
    {
        if ($leaveApplication->status === 'Disapproved') {
            return 'Disapproved';
        }

        // Otherwise, return the formatted approved dates
        return $this->formatDates($leaveApplication->approved_dates ?? 'N/A');
    }

    private function formatDates($dates)
    {
        $dateArray = preg_split('/[\s,]+/', $dates);
    
        $parsedDates = array_map(function($date) {
            try {
                return Carbon::parse(trim($date));
            } catch (\Exception $e) {
                return null;
            }
        }, $dateArray);
    
        $parsedDates = array_filter($parsedDates);
    
        if (empty($parsedDates)) {
            return '';
        }
    
        $firstDate = $parsedDates[0];
    
        $sameMonth = collect($parsedDates)->every(function ($date) use ($firstDate) {
            return $date->format('Y-m') === $firstDate->format('Y-m');
        });
    
        if ($sameMonth) {
            $days = collect($parsedDates)->map(function($date) {
                return $date->format('d');
            })->implode(',');
    
            return $firstDate->format('M') . '. ' . $days . ', ' . $firstDate->format('Y');
        } else {
            return collect($parsedDates)->map(function ($date) {
                return $date->format('M d, Y');
            })->implode(', ');
        }
    }
    

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $year = substr($this->month, 0, 4);

                $sheet->setTitle($year);

                // Merge cells A1:J1 and set styles
                $sheet->mergeCells('A1:J1');
                $sheet->getStyle('A1:J1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 18,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_BOTTOM,
                    ],
                ]);
                $sheet->setCellValue('A1', 'REPORT ON LEAVE AVAILMENT');

                // Merge cells A2:J2 and set styles
                $sheet->mergeCells('A2:J2');
                $sheet->getStyle('A2:J2')->applyFromArray([
                    'font' => [
                        'bold' => false,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_BOTTOM,
                    ],
                ]);
                $sheet->setCellValue('A2', 'For the Year ' . $year);

                // Merge cells A3:J3 and set styles
                $sheet->mergeCells('A3:J3');
                $sheet->getStyle('A3:J3')->applyFromArray([
                    'font' => [
                        'bold' => false,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_BOTTOM,
                    ],
                ]);
                $sheet->setCellValue('A3', '');

                $sheet->getColumnDimension('A')->setWidth(10.89);
                $sheet->getColumnDimension('B')->setWidth(16.78);
                $sheet->getColumnDimension('C')->setWidth(19.89);
                $sheet->getColumnDimension('D')->setWidth(21.67);
                $sheet->getColumnDimension('E')->setWidth(21.67);
                $sheet->getColumnDimension('F')->setWidth(8.22);
                $sheet->getColumnDimension('G')->setWidth(23.78);
                $sheet->getColumnDimension('H')->setWidth(32.78);
                $sheet->getColumnDimension('I')->setWidth(32.78);
                $sheet->getColumnDimension('J')->setWidth(18);
                
                $sheet->getRowDimension(1)->setRowHeight(23.4);
                $sheet->getRowDimension(2)->setRowHeight(18);
                $sheet->getRowDimension(4)->setRowHeight(15.6);
                $sheet->getRowDimension(5)->setRowHeight(14.4);
                
                // Set column headers in row 4
                $headers = [
                    'A4' => 'Control No.',
                    'B4' => 'Date Filed',
                    'C4' => 'Surname',
                    'D4' => 'First Name',
                    'E4' => 'Division',
                    'F4' => 'Sex',
                    'G4' => 'Type of Leave',
                    'H4' => 'Date of Leave',
                    'I4' => 'Date Approved',
                    'J4' => 'Remarks',
                ];

                foreach ($headers as $cell => $header) {
                    $sheet->setCellValue($cell, $header);
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['argb' => '9bc2e6'],
                        ],
                    ]);
                }

                // Center all cells and wrap text for specific columns
                $sheet->getStyle('A5:J5' . $sheet->getHighestRow())->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                
                // Wrap text for Date of Leave, Type of Leave, Date Approved
                $sheet->getStyle('H5:H' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
                $sheet->getStyle('G5:G' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
                $sheet->getStyle('I5:I' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);

                $row = 5;
                foreach ($this->collection() as $leaveApplication) {
                    $sheet->setCellValue('A' . $row, $leaveApplication['Control No.']);
                    $sheet->setCellValue('B' . $row, $leaveApplication['Date Filed']);
                    $sheet->setCellValue('C' . $row, $leaveApplication['Surname']);
                    $sheet->setCellValue('D' . $row, $leaveApplication['First Name']);
                    $sheet->setCellValue('E' . $row, $leaveApplication['Division']);
                    $sheet->setCellValue('F' . $row, $leaveApplication['Sex']);
                    $sheet->setCellValue('G' . $row, $leaveApplication['Type of Leave']);
                    $sheet->setCellValue('H' . $row, $leaveApplication['Date of Leave']);
                    $sheet->setCellValue('I' . $row, $leaveApplication['Date Approved']);
                    $sheet->setCellValue('J' . $row, $leaveApplication['Remarks']);
                    $row++;
                }
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
        ];
    }
}
