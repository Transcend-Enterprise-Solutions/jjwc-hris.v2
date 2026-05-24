<?php

namespace App\Exports;

use App\Models\DocRequest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DocRequestExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    use Exportable;

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = DocRequest::query()
            ->join('users', 'doc_requests.user_id', '=', 'users.id')
            ->select('doc_requests.id', 'users.name as user_name', 'doc_requests.document_type', 'doc_requests.date_requested', 'doc_requests.status', 'doc_requests.date_completed');

        if (isset($this->filters['month'])) {
            $date = Carbon::createFromFormat('Y-m', $this->filters['month']);
            $query->whereYear('doc_requests.date_requested', $date->year)
                  ->whereMonth('doc_requests.date_requested', $date->month);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Employee Name',
            'Document Type',
            'Date Requested',
            'Status',
            'Date Completed',
        ];
    }

    public function map($docRequest): array
    {
        return [
            $docRequest->id,
            $docRequest->user_name,
            $docRequest->document_type,
            $docRequest->date_requested->format('m/d/Y'),
            $docRequest->status,
            $docRequest->date_completed ? Carbon::parse($docRequest->date_completed)->format('m/d/Y') : null,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header Row
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFF0F0F0']],
        ]);

        // Data Rows
        $dataRange = 'A2:F' . $sheet->getHighestRow();
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10, // ID
            'B' => 30, // User Name
            'C' => 30, // Document Type
            'D' => 20, // Date Requested
            'E' => 15, // Status
            'F' => 20, // Date Completed
        ];
    }

    public function title(): string
    {
        return 'Document Requests';
    }
}
