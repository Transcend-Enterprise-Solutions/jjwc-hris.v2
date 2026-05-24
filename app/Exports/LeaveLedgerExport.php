<?php

namespace App\Exports;

use App\Models\LeaveApplication;
use App\Models\LeaveCredits;
use App\Models\LeaveCreditsCalculation;
use App\Models\Positions;
use App\Models\UserData;
use App\Models\OfficeDivisions;
use App\Models\MonetizationRequest;
use App\Models\MonthlyCredits;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveLedgerExport
{
    protected $leaveApplicationId;
    protected $startDate;
    protected $endDate;

    public function __construct($leaveApplicationId, $startDate, $endDate)
    {
        $this->leaveApplicationId = $leaveApplicationId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    private function setBoldLabelWithValue($sheet, $cell, $label, $value)
    {
        $richText = new RichText();
        $boldPart = $richText->createTextRun($label);
        $boldPart->getFont()->setBold(true);
        $richText->createText($value);
        $sheet->setCellValue($cell, $richText);
    }

    public function export(): StreamedResponse
    {
        // Get the user directly instead of through leave application
        $user = Auth::user();

        // Fetching user data (this part remains unchanged)
        $position = Positions::find($user->position_id)->position ?? 'N/A';
        $department = OfficeDivisions::find($user->office_division_id)->office_division ?? 'N/A';
        $dateHired = UserData::where('user_id', $user->id)->value('date_hired');

        if ($dateHired) {
            $formattedDateHired = \Carbon\Carbon::createFromFormat('Y-m-d', $dateHired)->format('m/d/Y');
        } else {
            $formattedDateHired = 'N/A';
        }

        $startDateCarbon = Carbon::createFromFormat('Y-m', $this->startDate)->startOfMonth();
        $endDateCarbon = Carbon::createFromFormat('Y-m', $this->endDate)->endOfMonth();

        $templatePath = storage_path('app/public/leave_template/LeaveLedgerTemplate.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Fill in the basic user information
        $this->setBoldLabelWithValue($sheet, 'E7', ': ', $user->name);
        $this->setBoldLabelWithValue($sheet, 'P7', ': ', $position);
        $this->setBoldLabelWithValue($sheet, 'E8', ': ', $formattedDateHired);
        $this->setBoldLabelWithValue($sheet, 'P8', ': ', $department);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="LeaveLedger.xlsx"',
        ]);
    }
}
