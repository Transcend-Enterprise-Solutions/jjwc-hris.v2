<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffidavitTemplate extends Model
{
    use HasFactory;

    
     protected $fillable = [
        'name',
        'form_number',
        'content',
        'variables',
        'is_active'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Replace placeholders with actual values
     */
    public function fillTemplate(array $data): string
    {
        $content = $this->content;
        
        foreach ($data as $key => $value) {
            // Replace placeholders like {{employee_name}}, {{address}}, etc.
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }
        
        return $content;
    }

    /**
     * Extract variables from template content
     */
    public function extractVariables(): array
    {
        preg_match_all('/\{\{([^}]+)\}\}/', $this->content, $matches);
        return array_unique($matches[1]);
    }

    /**
     * Get default variables with descriptions
     */
    public static function getDefaultVariables(): array
    {
        return [
            'employee_name' => 'Employee Full Name',
            'civil_status' => 'Civil Status (single/married)',
            'address' => 'Full Address',
            'resignation_date' => 'Resignation Date',
            'current_date' => 'Current Date',
            'day' => 'Day',
            'month' => 'Month',
            'year' => 'Year',
            'city' => 'City',
            'identification_type' => 'ID Type',
            'identification_number' => 'ID Number',
            'identification_date' => 'ID Issue Date',
            'doc_number' => 'Document Number',
            'page_number' => 'Page Number',
            'book_number' => 'Book Number',
            'series_year' => 'Series Year'
        ];
    }
}