<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class ApprovedOvertimeExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $records;
    protected $month;
    protected $year;
    protected $statusFilter;

    public function __construct($records, $month, $year, $statusFilter = 'all')
    {
        $this->records = $records;
        $this->month = $month;
        $this->year = $year;
        $this->statusFilter = $statusFilter;
    }

    public function collection()
    {
        return $this->records->map(function ($record, $index) {
            return [
                'no' => $index + 1,
                'employee_name' => $record->user_name ?? 'N/A',
                'employee_id' => $record->emp_code ?? 'N/A',
                'date' => Carbon::parse($record->date)->format('M d, Y'),
                'day' => $record->day_of_week ?? 'N/A',
                'office_division' => $record->office_division ?? 'N/A',
                'morning_in' => $record->up_morning_in ?? $record->morning_in ?? '--:--',
                'morning_out' => $record->up_morning_out ?? $record->morning_out ?? '--:--',
                'noon_in' => $record->up_afternoon_in ?? $record->afternoon_in ?? '--:--',
                'afternoon_out' => $record->up_afternoon_out ?? $record->afternoon_out ?? '--:--',
                'overtime' => $record->up_ot ?? $record->overtime ?? '00:00',
                'ot_type' => $record->ot_type === 'night_differential' ? 'Night Differential' : ($record->ot_type === 'regular' ? 'Regular' : 'Not Set'),
                'hours_rendered' => $record->up_total_hours_rendered ?? $record->total_hours_rendered ?? '00:00',
                'status' => ucfirst($record->ot_approval_status ?? 'Pending'),
                'updated_by' => $record->updated_by ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No.',
            'Employee Name',
            'Employee ID',
            'Date',
            'Day',
            'Office Division',
            'Morning In',
            'Morning Out',
            'Noon In',
            'Afternoon Out',
            'Overtime',
            'OT Type',
            'Hours Rendered',
            'Status',
            'Updated By',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $lastRow = $this->records->count() + 1;
        $sheet->getStyle('A2:O' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D3D3D3'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D2:O' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach (range(1, $lastRow) as $row) {
            $sheet->getRowDimension($row)->setRowHeight(-1);
        }

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 25,
            'C' => 15,
            'D' => 15,
            'E' => 12,
            'F' => 20,
            'G' => 12,
            'H' => 12,
            'I' => 12,
            'J' => 15,
            'K' => 12,
            'L' => 18,
            'M' => 15,
            'N' => 12,
            'O' => 20,
        ];
    }

    public function title(): string
    {
        $monthName = Carbon::create($this->year, $this->month, 1)->format('F');
        $statusText = ucfirst($this->statusFilter === 'all' ? 'All' : $this->statusFilter);
        return "OT {$statusText} - {$monthName} {$this->year}";
    }
}
