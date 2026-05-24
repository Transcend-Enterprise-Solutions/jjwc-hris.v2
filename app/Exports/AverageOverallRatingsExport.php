<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Rating;
use App\Models\DocRequest;
use Carbon\Carbon;

class AverageOverallRatingsExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $month = $this->filters['month'] ?? now()->format('Y-m');
        $date = Carbon::createFromFormat('Y-m', $month);

        return Rating::selectRaw(
            'YEAR(ratings.created_at) as year,
             MONTH(ratings.created_at) as month,
             users.name as user_name,
             doc_requests.document_type as document_type,
             AVG(ratings.responsiveness) as avg_responsiveness,
             AVG(ratings.reliability) as avg_reliability,
             AVG(ratings.access_facilities) as avg_access_facilities,
             AVG(ratings.communication) as avg_communication,
             AVG(ratings.cost) as avg_cost,
             AVG(ratings.integrity) as avg_integrity,
             AVG(ratings.assurance) as avg_assurance,
             AVG(ratings.outcome) as avg_outcome,
             AVG(ratings.overall) as avg_overall'
        )
        ->leftJoin('doc_requests', 'ratings.doc_request_id', '=', 'doc_requests.id')
        ->leftJoin('users', 'ratings.user_id', '=', 'users.id')  // Join users table
        ->whereYear('ratings.created_at', $date->year)
        ->whereMonth('ratings.created_at', $date->month)
        ->groupBy('year', 'month', 'doc_requests.document_type', 'users.name')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get([
            'year',
            'month',
            'user_name',
            'document_type',
            'avg_responsiveness',
            'avg_reliability',
            'avg_access_facilities',
            'avg_communication',
            'avg_cost',
            'avg_integrity',
            'avg_assurance',
            'avg_outcome',
            'avg_overall',
        ]);
    }

    public function headings(): array
    {
        return [
            'Year',
            'Month',
            'User Name',
            'Document Type',
            'Responsiveness',
            'Reliability',
            'Access Facilities',
            'Communication',
            'Cost',
            'Integrity',
            'Assurance',
            'Outcome',
            'Average Overall Rating',
        ];
    }
}
