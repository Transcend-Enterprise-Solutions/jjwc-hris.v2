<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SendTrainingNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $targetAudienceType;
    protected $targetAudienceIds;
    protected $adminEmail;
    protected $trainingInfo;

    /**
     * Create a new job instance.
     */
    public function __construct($targetAudienceType, $targetAudienceIds, $adminEmail, $trainingInfo)
    {
        $this->targetAudienceType = $targetAudienceType;
        $this->targetAudienceIds = $targetAudienceIds;
        $this->adminEmail = $adminEmail;
        $this->trainingInfo = $trainingInfo;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $emails = $this->getParticipantEmails();
        
        // Dispatch individual jobs for each email with delays
        foreach ($emails as $index => $email) {
            SendSingleTrainingNotificationJob::dispatch(
                $email, 
                $this->adminEmail, 
                $this->trainingInfo
            )->delay(now()->addSeconds($index * 2)); // 2 second delay between each email
        }
    }

    /**
     * Get participant emails based on target audience type
     */
    protected function getParticipantEmails()
    {
        $emails = [];
        
        switch ($this->targetAudienceType) {
            case 'all':
                // Get all active employees
                $emails = User::where('user_role', 'emp')
                             ->where('active_status', 1)
                             ->pluck('email')
                             ->toArray();
                break;
                
            case 'division':
                // Get users from selected divisions
                if ($this->targetAudienceIds && is_array($this->targetAudienceIds)) {
                    $emails = User::where('user_role', 'emp')
                                 ->where('active_status', 1)
                                 ->whereIn('office_division_id', $this->targetAudienceIds)
                                 ->pluck('email')
                                 ->toArray();
                }
                break;
                
            case 'unit':
                // Get users from selected units
                if ($this->targetAudienceIds && is_array($this->targetAudienceIds)) {
                    $emails = User::where('user_role', 'emp')
                                 ->where('active_status', 1)
                                 ->whereIn('unit_id', $this->targetAudienceIds)
                                 ->pluck('email')
                                 ->toArray();
                }
                break;
                
            case 'sg':
                // Get users by Salary Grade
                if ($this->targetAudienceIds && is_array($this->targetAudienceIds)) {
                    $userIds = collect();
                    
                    foreach ($this->targetAudienceIds as $salaryGrade) {
                        // Get plantilla employees from payrolls table
                        $plantillaUserIds = DB::table('payrolls')
                            ->join('user_data', 'payrolls.user_id', '=', 'user_data.user_id')
                            ->where('user_data.appointment', 'plantilla')
                            ->where(DB::raw('CAST(SUBSTRING_INDEX(payrolls.sg_step, "-", 1) AS UNSIGNED)'), $salaryGrade)
                            ->pluck('payrolls.user_id');
                        
                        // Get COS employees from cos_reg_payrolls table
                        $cosUserIds = DB::table('cos_reg_payrolls')
                            ->join('user_data', 'cos_reg_payrolls.user_id', '=', 'user_data.user_id')
                            ->where('user_data.appointment', 'cos')
                            ->where('cos_reg_payrolls.sg_step', $salaryGrade)
                            ->pluck('cos_reg_payrolls.user_id');
                        
                        $userIds = $userIds->merge($plantillaUserIds)->merge($cosUserIds);
                    }
                    
                    $emails = User::where('user_role', 'emp')
                                 ->where('active_status', 1)
                                 ->whereIn('id', $userIds->unique())
                                 ->pluck('email')
                                 ->toArray();
                }
                break;
                
            case 'employees':
                // Get specific selected employees
                if ($this->targetAudienceIds && is_array($this->targetAudienceIds)) {
                    $emails = User::where('user_role', 'emp')
                                 ->where('active_status', 1)
                                 ->whereIn('id', $this->targetAudienceIds)
                                 ->pluck('email')
                                 ->toArray();
                }
                break;
                
            default:
                $emails = [];
                break;
        }
        
        // Filter out null/empty emails and return unique emails
        return array_filter(array_unique($emails), function($email) {
            return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendTrainingNotificationJob failed: ' . $exception->getMessage());
    }
}
