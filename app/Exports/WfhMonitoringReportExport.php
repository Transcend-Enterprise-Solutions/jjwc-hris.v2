<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WfhMonitoringReportExport implements FromArray, WithEvents, WithTitle
{
    public function __construct(
        protected Collection $rows,
        protected Carbon $startDate,
        protected Carbon $endDate,
        protected ?string $employeeFilter = null
    ) {
    }

    public function array(): array
    {
        return [
            ['JJWC HRIS - WFH Daily Monitoring Report'],
            ['Report period', $this->startDate->format('M d, Y').' to '.$this->endDate->format('M d, Y')],
            ['Employee filter', $this->employeeFilter ?: 'All employees'],
            ['Generated', now()->format('M d, Y h:i A')],
            [''],
            [
                'Date',
                'Day',
                'Employee ID',
                'Employee Name',
                'Sessions',
                'First Time In',
                'Last Time Out / Activity',
                'Online',
                'Active',
                'Idle',
                'Activity %',
                'Work Status',
                'Last Activity',
                'Latitude',
                'Longitude',
                'GPS Accuracy',
                'Browser',
                'Device',
                'Keystrokes',
                'Mouse Moves',
                'Clicks',
                'Touches',
            ],
            ...$this->rows->values()->all(),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = max(6, 6 + $this->rows->count());
                $lastColumn = 'V';

                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->freezePane('A7');
                $sheet->setAutoFilter("A6:{$lastColumn}{$lastRow}");
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(6)->setRowHeight(34);
                $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '17365D']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle('A2:B4')->applyFromArray([
                    'font' => ['color' => ['rgb' => '334155']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle('A2:A4')->getFont()->setBold(true);
                $sheet->getStyle("A6:{$lastColumn}6")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                ]);

                if ($lastRow >= 7) {
                    $sheet->getStyle("A7:{$lastColumn}{$lastRow}")->applyFromArray([
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                    ]);
                    $sheet->getStyle("A7:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E7:V{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    for ($row = 7; $row <= $lastRow; $row++) {
                        if ($row % 2 === 0) {
                            $sheet->getStyle("A{$row}:{$lastColumn}{$row}")
                                ->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setRGB('F8FAFC');
                        }
                    }
                }

                $widths = [
                    'A' => 13, 'B' => 11, 'C' => 15, 'D' => 28, 'E' => 10,
                    'F' => 15, 'G' => 22, 'H' => 13, 'I' => 13, 'J' => 13,
                    'K' => 13, 'L' => 18, 'M' => 20, 'N' => 14, 'O' => 14,
                    'P' => 15, 'Q' => 20, 'R' => 18, 'S' => 13, 'T' => 13,
                    'U' => 11, 'V' => 11,
                ];

                foreach ($widths as $column => $width) {
                    $sheet->getColumnDimension($column)->setWidth($width);
                }

                $sheet->getPageSetup()->setOrientation('landscape');
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.4)->setRight(0.3)->setBottom(0.4)->setLeft(0.3);
            },
        ];
    }

    public function title(): string
    {
        return 'WFH Daily Stats';
    }
}
