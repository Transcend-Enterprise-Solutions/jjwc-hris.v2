<?php

namespace App\Exports;

use App\Models\LeaveCredits;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
// use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class LeaveCreditsExport implements FromCollection, WithMapping, WithEvents
{
    protected $selectedLeaveTypes;

    public function __construct(array $selectedLeaveTypes)
    {
        $this->selectedLeaveTypes = $selectedLeaveTypes;
    }

    public function collection()
    {
        // $columns = ['user_id'];

        // if (in_array('Vacation Leave', $this->selectedLeaveTypes)) {
        //     $columns[] = 'vl_claimable_credits';
        // }

        // if (in_array('Sick Leave', $this->selectedLeaveTypes)) {
        //     $columns[] = 'sl_claimable_credits';
        // }

        // if (in_array('Special Privilege Leave', $this->selectedLeaveTypes)) {
        //     $columns[] = 'spl_claimable_credits';
        // }

        // return LeaveCredits::select($columns)->get();

        $columns = ['user_id', 'vl_claimable_credits', 'sl_claimable_credits', 'fl_claimable_credits', 'spl_claimable_credits', 'updated_at'];
        return LeaveCredits::select($columns)->get();
    }

    public function map($row): array
    {
        $user = User::find($row->user_id);

        $vlClaimable = number_format($row->vl_claimable_credits ?? 0, 3, '.', '');
        $slClaimable = number_format($row->sl_claimable_credits ?? 0, 3, '.', '');
        $flClaimable = number_format($row->fl_claimable_credits ?? 0, 3, '.', '');
        $splClaimable = number_format($row->spl_claimable_credits ?? 0, 3, '.', '');
        $totalClaimable = number_format(($row->vl_claimable_credits ?? 0) + ($row->sl_claimable_credits ?? 0), 3, '.', '');

        // $formattedDate = Carbon::parse($row->updated_at)->format('M j, Y');
        $formattedDate = Carbon::today()->format('M j, Y');

        return [
            $user ? $user->name : 'N/A',
            $user ? $user->emp_code : 'N/A',
            $vlClaimable,
            $slClaimable,
            $totalClaimable,
            $splClaimable,
            $flClaimable,
            $formattedDate,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:H1');
                $sheet->getStyle('A1:H1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 18,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_BOTTOM,
                    ],
                ]);
                $sheet->setCellValue('A1', 'REPORT ON LEAVE CREDITS');

                $sheet->mergeCells('A2:H2');
                $sheet->getStyle('A2:H2')->applyFromArray([
                    'font' => [
                        'bold' => false,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_BOTTOM,
                    ],
                ]);
                $sheet->setCellValue('A2', '');

                // Set column headers in row 3
                $headers = [
                    'A3' => 'NAME',
                    'B3' => 'EMPLOYEE ID',
                    'C3' => 'VL',
                    'D3' => 'SL',
                    'E3' => 'TOTAL',
                    'F3' => 'FL',
                    'G3' => 'SPL',
                    'H3' => 'UPDATED AS OF',
                ];

                foreach ($headers as $cell => $header) {
                    $sheet->setCellValue($cell, $header);
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['argb' => '9bc2e6'],
                        ],
                        'borders' => [
                            'outline' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                'color' => ['argb' => '000000'],
                            ],
                            'inside' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                    ]);
                }

                // Start inserting records from row 4
                $rowIndex = 4;
                foreach ($this->collection() as $row) {
                    $data = $this->map($row);
                    foreach ($data as $colIndex => $value) {
                        $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex, $value);
                    }
                    $rowIndex++;
                }

                $lastRow = $sheet->getHighestRow();
                $columnsToFormat = ['C', 'D', 'E', 'F', 'G'];
                foreach ($columnsToFormat as $column) {
                    $sheet->getStyle("{$column}4:{$column}{$lastRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.000');
                }

                // Center all cells and wrap text for specific columns
                $sheet->getStyle('A4:H' . $sheet->getHighestRow())->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('C4:H' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);

                // Set default column widths
                $sheet->getColumnDimension('A')->setWidth(35);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(15);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(20);
            },
        ];
    }
}
