<?php

namespace App\Exports;

use App\Models\Eligibility;
use App\Models\EmployeesEducation;
use App\Models\LearningAndDevelopment;
use App\Models\Positions;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\DB;

class EmployeesExport implements FromCollection, WithEvents
{
    use Exportable;

    protected $filters;
    protected $selectedColumns;
    protected $rowNumber = 0;

    public function __construct($filters = [], $selectedColumns = [])
    {
        $this->filters = array_merge([
            'sex' => null,
            'civil_status' => [],
            'selectedProvince' => [],
            'selectedCity' => [],
            'selectedBarangay' => [],
            'selectedLD' => [],
            'selectedElig' => [],
            'office_division' => '',
            'unit' => '',
        ], $filters);

        $this->selectedColumns = $selectedColumns;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $this->addCustomHeader($event);
            },
            AfterSheet::class => function(AfterSheet $event) {
                $this->formatSheet($event);
            },
        ];
    }

    private function addCustomHeader(BeforeSheet $event)
    {
        $sheet = $event->sheet;
        $lastColumn = $this->getLastColumnLetter();

        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->setCellValue('A1', "");

        $sheet->mergeCells("A2:{$lastColumn}2");
        $sheet->setCellValue('A2', "JUVENILE JUSTICE AND WELFARE COUNCIL");
        
        $sheet->mergeCells("A3:{$lastColumn}3");
        $sheet->setCellValue('A3', "Employee List");

        // Add column headers in row 4
        $headers = $this->getColumnHeaders();
        foreach ($headers as $index => $header) {
            $column = $this->getColumnLetter($index + 1);
            $sheet->setCellValue("{$column}4", $header);
        }

        $sheet->getStyle('A1:' . $lastColumn . '4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:' . $lastColumn . '4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $lastColumn . '3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);
        $sheet->getStyle('2:2')->getFont()->setSize(16);
        $sheet->getStyle("A4:{$lastColumn}4")->getFont()->setBold(true);
    }

    private function formatSheet(AfterSheet $event)
    {
        $sheet = $event->sheet;
        $lastColumn = $this->getLastColumnLetter();
        $highestRow = $sheet->getHighestRow();

        // Set column widths
        $columnCount = count($this->getColumnHeaders());
        for ($i = 1; $i <= $columnCount; $i++) {
            $column = $this->getColumnLetter($i);
            if($column == 'A'){
                $sheet->getColumnDimension($column)->setWidth(5);
            }else{
                $sheet->getColumnDimension($column)->setWidth(20);
            }
        }

        // Format header row
        $sheet->getStyle("A4:{$lastColumn}4")->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FFF0F0F0'],
            ],
        ]);

        // Set text wrapping and alignment for all cells
        $sheet->getStyle("A4:{$lastColumn}{$highestRow}")->applyFromArray([
            'alignment' => [
                'wrapText' => true,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Center align all columns except 'Name'
        $nameColumnIndex = array_search('Name', $this->getColumnHeaders());
        foreach (range('A4', $lastColumn . '10000') as $column) {
            if ($column !== chr(65 + $nameColumnIndex)) { // 65 is ASCII for 'A'
                $sheet->getStyle("{$column}5:{$column}{$highestRow}")
                      ->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }
    }

    private function getLastColumnLetter()
    {
        return $this->getColumnLetter(count($this->getColumnHeaders()));
    }

    private function getColumnLetter($columnNumber){
        $columnLetter = '';
        while ($columnNumber > 0) {
            $modulo = ($columnNumber - 1) % 26;
            $columnLetter = chr(65 + $modulo) . $columnLetter;
            $columnNumber = (int)(($columnNumber - $modulo) / 26);
        }
        return $columnLetter;
    }

    public function collection()
    {
        $query = User::join('user_data', 'users.id', '=', 'user_data.user_id')
                ->leftJoin('employees_education', 'employees_education.user_id', 'users.id')
                ->leftJoin('learning_and_development', 'learning_and_development.user_id', 'users.id')
                ->leftJoin('positions', 'positions.id', 'users.position_id')
                ->leftJoin('eligibility', 'eligibility.user_id', 'users.id');
    
        $columnsToSelect = ['users.id'];
        $columnsToGroupBy = ['users.id'];

        $columnsToSelect[] = 'users.emp_code';
        $columnsToGroupBy []= 'users.emp_code';

        $columnsToSelect[] = 'users.position_id';
        $columnsToGroupBy []= 'users.position_id';
    
        $nameFields = ['surname', 'first_name', 'middle_name', 'name_extension'];
        $nameFieldsSelected = false;
    
        foreach ($this->selectedColumns as $column) {
            if ($column !== 'years_in_gov_service' 
                && $column !== 'learning_and_development'
                && $column !== 'eligibility'
                && $column !== 'position'
                && $column !== 'ld_title'
                && $column !== 'educational_background'
                && $column !== 'name_of_school'
                && $column !== 'course'
                && $column !== 'year_graduated') {

                if ($column == 'emp_code') {
                    continue;
                }


                if (in_array($column, $nameFields)) {
                    $columnsToSelect[] = "user_data.$column";
                    $columnsToGroupBy[] = "user_data.$column";
                    $nameFieldsSelected = true;
                } elseif ($column !== 'name') {
                    if ($column === 'active_status') {
                        $columnsToSelect[] = "users.$column";
                        $columnsToGroupBy[] = "users.$column";
                    } else {
                        $columnsToSelect[] = "user_data.$column";
                        $columnsToGroupBy[] = "user_data.$column";
                    }
                }
            }
        }
    
        if (!$nameFieldsSelected && in_array('name', $this->selectedColumns)) {
            foreach ($nameFields as $field) {
                $columnsToSelect[] = "user_data.$field";
                $columnsToGroupBy[] = "user_data.$field";
            }
        }
    
        $query->select($columnsToSelect);
    
        if (in_array('years_in_gov_service', $this->selectedColumns)) {
            $query->addSelect(DB::raw('(
                SELECT FLOOR(SUM(
                    CASE
                        WHEN work_experience.toPresent = "Present" THEN TIMESTAMPDIFF(MONTH, work_experience.start_date, CURDATE())
                        WHEN work_experience.end_date IS NOT NULL THEN TIMESTAMPDIFF(MONTH, work_experience.start_date, work_experience.end_date)
                        ELSE 0
                    END
                ) / 12)
                FROM work_experience
                WHERE work_experience.user_id = users.id AND work_experience.gov_service = 1
            ) as years_in_gov_service'));
        }
    
        // Apply filters
        if (!empty($this->filters['sex'])) {
            if($this->filters['sex'] == 'others'){
                $query->where('user_data.sex', '!=', 'Female')
                      ->where('user_data.sex', '!=', 'Male');
            } else {
                $query->where('user_data.sex', $this->filters['sex']);
            }
        }
        if (!empty($this->filters['civil_status'])) {
            $query->whereIn('user_data.civil_status', $this->filters['civil_status']);
        }
        if (!empty($this->filters['selectedProvince'])) {
            $query->whereIn('user_data.permanent_selectedProvince', $this->filters['selectedProvince']);
        }
        if (!empty($this->filters['selectedCity'])) {
            $query->whereIn('user_data.permanent_selectedCity', $this->filters['selectedCity']);
        }
        if (!empty($this->filters['selectedBarangay'])) {
            $query->whereIn('user_data.permanent_selectedBarangay', $this->filters['selectedBarangay']);
        }
        if (!empty($this->filters['selectedLD'])) {
            if($this->filters['selectedLD'] == ['Others']){
                $query->whereNotIn('learning_and_development.type_of_ld', ['Technical', 'Supervisory', 'Leadership']);
            }else{
                $query->whereIn('learning_and_development.type_of_ld', $this->filters['selectedLD']);
            }
            $columnsToSelect[] = 'learning_and_development.user_id as learning_and_development';
            $columnsToGroupBy[] = 'learning_and_development.user_id';
        }
        if (!empty($this->filters['selectedElig'])) {
            $query->whereIn('eligibility.eligibility', $this->filters['selectedElig']);
            $columnsToSelect[] = 'eligibility.eligibility as eligibility';
            $columnsToGroupBy[] = 'eligibility.eligibility';
        }
        if ($this->filters['appointment']) {
            switch ($this->filters['appointment']) {
                case 'plantilla':
                    $query->where('user_data.appointment', 'plantilla');
                    break;
                case 'cos':
                    $query->where('user_data.appointment', 'cos')
                    ->orWhere('user_data.appointment', 'ct');
                    break;
                case 'pa':
                    $query->where('user_data.appointment', 'pa');
                    break;
            }
        }
        if ($this->filters['posLevel']) {
            $query->where('positions.level', (int) $this->filters['posLevel']);
        }
        if (!empty($this->filters['selectedEduc'])) {
            $query->where(function($subQuery) {
                $isBachelor = in_array('b', $this->filters['selectedEduc']);
                $isMaster = in_array('m', $this->filters['selectedEduc']);
                $isDoctor = in_array('d', $this->filters['selectedEduc']);
        
                if ($isBachelor) {
                    $subQuery->orWhere('employees_education.is_bachelor', 1);
                    $columnsToSelect[] = 'employees_education.is_bachelor';
                    $columnsToGroupBy[] = 'employees_education.is_bachelor';
                }
                if ($isMaster) {
                    $subQuery->orWhere('employees_education.is_master', 1);
                    $columnsToSelect[] = 'employees_education.is_master';
                    $columnsToGroupBy[] = 'employees_education.is_master';
                }
                if ($isDoctor) {
                    $subQuery->orWhere('employees_education.is_doctor', 1);
                    $columnsToSelect[] = 'employees_education.is_doctor';
                    $columnsToGroupBy[] = 'employees_education.is_doctor';
                }
            });
        }
    
        $query->groupBy($columnsToGroupBy);

    
        return $query->get()
            ->map(function ($user) use ($nameFields, $nameFieldsSelected) {
                $this->rowNumber++;
                $userData = [$this->rowNumber];
    
                foreach ($this->selectedColumns as $column) {
                    $educs = EmployeesEducation::where('user_id', $user->id)->get();
                    $lds = LearningAndDevelopment::where('user_id', $user->id)->get();
                    $eligs = Eligibility::where('user_id', $user->id)->get();
                    $pos = Positions::where('id', $user->position_id)->first();

                    if ($column !== 'name' && $column !== 'id') {
                        if ($column === 'active_status') {
                            $statusMapping = [
                                0 => 'Inactive',
                                1 => 'Active',
                                2 => 'Retired',
                                3 => 'Resigned'
                            ];
                            $userData[] = $statusMapping[$user->active_status] ?? 'Unknown';
                        } elseif ($column === 'years_in_gov_service') {
                            $userData[] = $user->years_in_gov_service ?? '';
                        } elseif ($column === 'date_of_birth' || $column === 'date_hired') {
                            $userData[] = $user->$column ? Carbon::parse($user->$column)->format('F d, Y') : '';
                        } elseif ($column === 'sex') {
                            $userData[] = $user->$column == 'No' ? 'Prefer Not To Say' : $user->$column;
                        } elseif ($column === 'pwd') {
                            $userData[] = $user->$column ? 'Yes' : 'No';
                        } elseif ($column === 'is_solo_parent') {
                            $userData[] = $user->$column ? 'Yes' : 'No';
                        } elseif ($column === 'learning_and_development') {
                            if(!$lds->isEmpty()){
                                $leardDev = '';
                                foreach($lds as $ld){
                                    $leardDev = $leardDev . ("• " . $ld->type_of_ld . "\n");
                                }
                                $userData[] = $leardDev;
                            }else{
                                $userData[] = '';
                            }
                        } elseif ($column === 'ld_title') {
                            if(!$lds->isEmpty()){
                                $ldTitle = '';
                                foreach($lds as $ld){
                                    $ldTitle = $ldTitle . ("• " . $ld->title . "\n");
                                }
                                $userData[] = $ldTitle;
                            }else{
                                $userData[] = '';
                            }
                        } elseif ($column === 'eligibility') {
                            if(!$eligs->isEmpty()){
                                $elig = '';
                                foreach($eligs as $e){
                                    $elig = $elig . ("• " . $e->eligibility . "\n");
                                }
                                $userData[] = $elig;
                            }else{
                                $userData[] = '';
                            }
                        } elseif ($column === 'position') {
                            if($pos){
                                $userData[] = $pos->position;
                            }else{
                                $userData[] = '';
                            }
                        }elseif ($column === 'educational_background') {
                            if(!$educs->isEmpty()){
                                $degree = '';
                                foreach($educs as $ed){
                                    if($ed->is_bachelor){
                                        $degree = $degree . ("• Bachelor's Degree \n");
                                    }
                                    if($ed->is_master){
                                        $degree = $degree . ("• Master's Degree \n");
                                    }
                                    if($ed->is_doctor){
                                        $degree = $degree . ("• Doctorate Degree \n");
                                    }
                                }
                                $userData[] = $degree;
                            }else{
                                $userData[] = '';
                            }
                        } elseif ($column === 'course') {
                            if(!$educs->isEmpty()){
                                $course = '';
                                foreach($educs as $ed){
                                    if($ed->is_bachelor){
                                        $course = $course . ("• " . $ed->basic_educ_degree_course . "\n");
                                    }
                                    if($ed->is_master){
                                        $course = $course . ("• " . $ed->basic_educ_degree_course . "\n");
                                    }
                                    if($ed->is_doctor){
                                        $course = $course . ("• " . $ed->basic_educ_degree_course . "\n");
                                    }
                                }
                                $userData[] = $course;
                            }else{
                                $userData[] = '';
                            }
                        } elseif ($column === 'name_of_school') {
                            if(!$educs->isEmpty()){
                                $nameOfSchool = '';
                                foreach($educs as $ed){
                                    if($ed->is_bachelor){
                                        $nameOfSchool = $nameOfSchool . ("• " . $ed->name_of_school . "\n");
                                    }
                                    if($ed->is_master){
                                        $nameOfSchool = $nameOfSchool . ("• " . $ed->name_of_school . "\n");
                                    }
                                    if($ed->is_doctor){
                                        $nameOfSchool = $nameOfSchool . ("• " . $ed->name_of_school . "\n");
                                    }
                                }
                                $userData[] = $nameOfSchool;
                            }else{
                                $userData[] = '';
                            }
                        } elseif ($column === 'year_graduated') {
                            if(!$educs->isEmpty()){
                                $yearGraduated = '';
                                foreach($educs as $ed){
                                    if($ed->is_bachelor){
                                        $yearGraduated = $yearGraduated . ("• " . $ed->year_graduated . "\n");
                                    }
                                    if($ed->is_master){
                                        $yearGraduated = $yearGraduated . ("• " . $ed->year_graduated . "\n");
                                    }
                                    if($ed->is_doctor){
                                        $yearGraduated = $yearGraduated . ("• " . $ed->year_graduated . "\n");
                                    }
                                }
                                $userData[] = $yearGraduated;
                            }else{
                                $userData[] = '';
                            }
                        }else {
                            $userData[] = $user->$column ?? '';
                        }
                    }
                }
                return $userData;
            });
    }

    private function getColumnHeaders(): array
    {
        $headers = ['#'];
        foreach ($this->selectedColumns as $column) {
            if ($column !== 'id') {
                $headers[] = $this->getColumnHeader($column);
            }
        }
        return $headers;
    }

    private function getColumnHeader($column)
    {
        $headers = [
            'emp_code' => 'Employee Number',
            'surname' => 'Surname',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'name_extension' => 'Name Extension',
            'date_of_birth' => 'Birth Date',
            'place_of_birth' => 'Birth Place',
            'sex' => 'Sex',
            'citizenship' => 'Citizenship',
            'civil_status' => 'Civil Status',
            'height' => 'Height',
            'weight' => 'Weight',
            'blood_type' => 'Blood Type',
            'ethnicity' => 'Ethnicity',
            'is_solo_parent' => 'Is Solo Parent',
            'pwd' => 'PWD',
            'gsis' => 'GSIS ID No.',
            'pagibig' => 'PAGIBIG ID No.',
            'philhealth' => 'PhilHealth ID No.',
            'sss' => 'SSS No.',
            'tin' => 'TIN No.',
            'agency_employee_no' => 'Agency Employee No.',
            'tel_number' => 'Telephone No.',
            'mobile_number' => 'Mobile No.',
            'permanent_selectedProvince' => 'Permanent Address (Province)',
            'permanent_selectedCity' => 'Permanent Address (City)',
            'permanent_selectedBarangay' => 'Permanent Address (Barangay)',
            'p_house_street' => 'Permanent Address (Street)',
            'permanent_selectedZipcode' => 'Permanent Address (Zip Code)',
            'residential_selectedProvince' => 'Residential Address (Province)',
            'residential_selectedCity' => 'Residential Address (City)',
            'residential_selectedBarangay' => 'Residential Address (Barangay)',
            'r_house_street' => 'Residential Address (Street)',
            'residential_selectedZipcode' => 'Residential Address (Zip Code)',
            'active_status' => 'Active Status',
            'position' => 'Position',
            'appointment' => 'Nature of Appointment',
            'date_hired' => 'Date Hired',
            'years_in_gov_service' => 'Years in Government Service',
            'ld_title' => 'Learning and Development',
            'learning_and_development' => 'Type of LD',
            'eligibility' => 'Eligibility',
            'educational_background' => 'Degree',
            'course' => 'Course',
            'name_of_school' => 'Name of School',
            'year_graduated' => 'Year Graduated',
        ];

        return $headers[$column] ?? $column;
    }
}