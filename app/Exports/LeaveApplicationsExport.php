<?php

namespace App\Exports;

use App\Models\LeaveApplication;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class LeaveApplicationsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $statuses;

    public function __construct($statuses)
    {
        $this->statuses = $statuses;
    }

    public function query()
    {
        return LeaveApplication::query()
            ->whereIn('status', $this->statuses);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date Filed',
            'Name',
            'Office/Department',
            'Position',
            'Salary',
            'Type of Leave',
            'Details of Leave',
            'Requested Days',
            'Requested Date/s',
            'Commutation',
            'Approved Date/s',
            'Approved Day/s',
            'Remarks',
            'Status',
        ];
    }

    public function map($leaveApplication): array
    {
        return [
            $leaveApplication->id,
            Carbon::parse($leaveApplication->date_of_filing)->format('M d, Y'),
            $leaveApplication->name,
            $leaveApplication->office_or_department,
            $leaveApplication->position,
            $leaveApplication->salary,
            implode("\n", explode(',', $leaveApplication->type_of_leave)),
            $leaveApplication->details_of_leave,
            $leaveApplication->number_of_days,
            implode("\n", explode(',', $leaveApplication->list_of_dates)),
            $leaveApplication->commutation,
            $leaveApplication->approved_dates ?? 'N/A',
            $leaveApplication->approved_days !== null ? ($leaveApplication->approved_days === 0 ? '0' : $leaveApplication->approved_days) : 'N/A',
            $leaveApplication->remarks ?? 'N/A',
            $leaveApplication->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Styling headers with border, text alignment, and width
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
    
        // Apply border, text alignment, and wrapping to all data rows
        $sheet->getStyle('A2:O' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
    
        for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
            $sheet->getRowDimension($row)->setRowHeight(-1);
        }
    
        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setWidth(20);
        }
    
        return $sheet;
    }
}
