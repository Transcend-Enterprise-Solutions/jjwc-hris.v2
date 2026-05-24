<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Mail;
use App\Mail\NewTrainingNotification;
use Illuminate\Support\Facades\Log;

class SendSingleTrainingNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $adminEmail;
    protected $trainingInfo;

    public function __construct($email, $adminEmail, $trainingInfo)
    {
        $this->email = $email;
        $this->adminEmail = $adminEmail;
        $this->trainingInfo = $trainingInfo;
    }

    public function handle(): void
    {
        Mail::to($this->email)->send(new NewTrainingNotification($this->adminEmail, $this->trainingInfo));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Failed to send training notification to {$this->email}: " . $exception->getMessage());
    }
}